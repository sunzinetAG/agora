<?php

namespace AgoraTeam\Agora\Domain\Repository;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The repository for Users
 */
class UserRepository extends Repository
{

    /**
     * @param int $uid
     * @return object
     */
    public function findByUid($uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setRespectStoragePage(false);
        $and = array(
            $query->equals('uid', $uid),
            $query->equals('deleted', 0)
        );
        $object = $query->matching($query->logicalAnd($and))->execute()->getFirst();

        return $object;
    }

    /**
     * @param string $groups
     * @return array|QueryResultInterface
     */
    public function findByUsergroups(string $groups)
    {
        $usergroups = GeneralUtility::trimExplode(',', $groups);
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->in('groups.uid', $usergroups)
        );

        return $query->execute();
    }

    /**
     * @param string $storage
     * @param int $amount
     * @return QueryResultInterface
     */
    public function findByStorage($storage)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds(explode(',', $storage));
        $query->getQuerySettings()->setIgnoreEnableFields(false);

        return $query->execute();
    }

    /**
     * @param string $storage
     * @param int $amount
     * @return QueryResultInterface
     */
    public function findLimitedByStorage($storage, $amount)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds(explode(',', $storage));
        $query->setLimit($amount);

        return $query->execute();
    }
}
