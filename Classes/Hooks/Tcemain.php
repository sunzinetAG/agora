<?php

namespace AgoraTeam\Agora\Hooks;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Philipp Thiele <philipp.thiele@phth.de>
 *           Björn Christopher Bresser <bjoern.bresser@gmail.com>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook into tcemain
 */
class Tcemain
{

    protected $preProcessValues = array();

    protected $accessToDelete = array();

    protected $queryBuilder = null;

    /**
     * @param string $table
     * @param string $uid
     * @return string
     */
    protected function getRecordKey($table, $uid)
    {
        return $table . '-' . $uid;
    }

    /**
     * processDatamap preProcessFieldArray
     * here we filter all groups and users with changed permissions and store them in $preProcessValues for later use
     * in afterDatabaseOperations
     *
     * @param array $fields fieldArray
     * @param string $table table name
     * @param integer $recordUid id of the record
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject parent Object
     * @return void
     */
    public function processDatamap_preProcessFieldArray(
        $fields,
        $table,
        $recordUid,
        \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject
    ) {
        if ($table === 'tx_agora_domain_model_forum') {
            $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['agora']);
            if ($settings['recursivePermissions'] !== '1') {
                return;
            }
            $forum = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tx_agora_domain_model_forum', $recordUid);

            $preProcessValueMapping = [
                'tx_agora_forum_userswithreadaccess_mm' => 'usersOfForumWithReadAccess',
                'tx_agora_forum_userswithwriteaccess_mm' => 'usersOfForumWithWriteAccess',
                'tx_agora_forum_userswithmodificationaccess_mm' => 'usersOfForumWithModificationAccess',
                'tx_agora_forum_groupswithreadaccess_mm' => 'groupsOfForumWithReadAccess',
                'tx_agora_forum_groupswithwriteaccess_mm' => 'groupsOfForumWithWriteAccess',
                'tx_agora_forum_groupswithmodificationaccess_mm' => 'groupsOfForumWithModificationAccess'
            ];

            foreach ($preProcessValueMapping as $mmTable => $value) {
                $recordKey = $this->getRecordKey($table, $recordUid);
                $accessValues = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid_foreign',
                    $mmTable,
                    'uid_local = "' . $forum['uid'] . '" ',
                    '',
                    '',
                    '',
                    'uid_foreign'
                );
                $this->preProcessValues[$recordKey][$value] = array_keys($accessValues);
            }
        }
    }

    /**
     * processDatamap after Database Operations
     *
     * @param string $status status
     * @param string $table table name
     * @param integer $recordUid id of the record
     * @param array $fields fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject parent Object
     * @return void
     */
    public function processDatamap_afterDatabaseOperations(
        $status,
        $table,
        $recordUid,
        array $fields,
        \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject
    ) {
        // set access rights on subforums for new forums recursively
        if ($table === 'tx_agora_domain_model_forum') {
            $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['agora']);
            if ($settings['recursivePermissions'] !== '1') {
                return;
            }
            $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tx_agora_domain_model_forum', $recordUid);
            $this->updateSubforums($record, true);
        }
    }

    /**
     * update subforums
     *
     * @todo: refactor with a more generic approach
     * @param array $forum forum
     * @param bool $isRootForum
     * @return void
     */
    protected function updateSubforums($forum, $isRootForum = false)
    {
        $this->accessToDelete = [];
        $preProcessValueMapping = [
            'tx_agora_forum_userswithreadaccess_mm' => 'usersOfForumWithReadAccess',
            'tx_agora_forum_userswithwriteaccess_mm' => 'usersOfForumWithWriteAccess',
            'tx_agora_forum_userswithmodificationaccess_mm' => 'usersOfForumWithModificationAccess',
            'tx_agora_forum_groupswithreadaccess_mm' => 'groupsOfForumWithReadAccess',
            'tx_agora_forum_groupswithwriteaccess_mm' => 'groupsOfForumWithWriteAccess',
            'tx_agora_forum_groupswithmodificationaccess_mm' => 'groupsOfForumWithModificationAccess'
        ];

        foreach ($preProcessValueMapping as $mmTable => $access) {
            $recordKey = $this->getRecordKey('tx_agora_domain_model_forum', $forum['uid']);
            $accessArr[$access] = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                'uid_foreign',
                $mmTable,
                'uid_local = "' . $forum['uid'] . '" ',
                '',
                '',
                '',
                'uid_foreign'
            );

            if ($isRootForum) {
                foreach ($this->preProcessValues[$recordKey][$access] as $userId) {
                    if (!array_key_exists($userId, $accessArr[$access])) {
                        $this->accessToDelete[$mmTable][] = $userId;
                    }
                }
            }
        }
        $subForums = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'tx_agora_domain_model_forum',
            'parent = "' . $forum['uid'] . '" '
        );

        foreach ($subForums as $subforum) {
            foreach ($preProcessValueMapping as $mmTable => $access) {
                // users_with_read_access
                $subForumRights = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid_foreign',
                    $mmTable,
                    'uid_local = "' . $subforum['uid'] . '" ',
                    '',
                    '',
                    '',
                    'uid_foreign'
                );
                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable($mmTable);

                foreach (array_keys($accessArr[$access]) as $userOfForumWithReadAccess) {
                    if (!array_key_exists($userOfForumWithReadAccess, $subForumRights)) {
                        $queryBuilder->insert($mmTable)->values(
                            array(
                                'uid_local' => $subforum['uid'],
                                'uid_foreign' => $userOfForumWithReadAccess
                            )
                        )->execute();
                    }
                }
                if (count($this->accessToDelete[$mmTable])) {
                    foreach ($this->accessToDelete[$mmTable] as $key => $value) {
                        $queryBuilder->delete($mmTable)->where(
                            $queryBuilder->expr()->eq('uid_local', $subforum['uid']),
                            $queryBuilder->expr()->eq('uid_foreign', $value)
                        )->execute();
                    }

                    //                    $GLOBALS['TYPO3_DB']->exec_DELETEquery(
                    //                        'tx_agora_forum_userswithreadaccess_mm',
                    //                        'uid_foreign IN (' . implode(',',
                    //                            $this->usersOfForumWithReadAccessToDelete) . ') AND uid_local = ' . $subforum['uid']
                    //                    );
                }
            }
        }

        foreach ($subForums as $subforum) {
            $this->updateSubforums($subforum);
        }
    }
}
