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
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Threads
 */
class ThreadRepository extends Repository
{

    /**
     * Find threads by forum
     *
     * @param \AgoraTeam\Agora\Domain\Model\Forum $forum
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByForum(\AgoraTeam\Agora\Domain\Model\Forum $forum)
    {
        $query = $this->createQuery();

        $result = $query
            ->matching(
                $query->equals('forum', $forum)
            )
            ->setOrderings(array('tstamp' => QueryInterface::ORDER_DESCENDING))
            ->execute();

        return $result;
    }

    /**
     * Finds the latest Threads
     *
     * @param integer $limit The number of threads to return at max
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findLatestThreadsForUser($limit)
    {
        $openUserForums = $this->forumRepository->findAccessibleUserForums();

        $query = $this->createQuery();

        $result = $query
            ->matching(
                $query->in('forum', $openUserForums)
            )
            ->setOrderings(array('crdate' => QueryInterface::ORDER_DESCENDING))
            ->setLimit((integer)$limit)
            ->execute();

        return $result;
    }

    /**
     * @param $uid
     */
    public function findThreadByUid($uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setIncludeDeleted(true);
        $result = $query
            ->matching(
                $query->equals('uid', $uid)
            )
            ->execute()->getFirst();

        return $result;
    }

    /**
     * We need to do this by the queryBuilder, otherwise
     * the tstamp would be updated and the sorting would be destroyed
     *
     * @param $threadId
     * @param $views
     */
    public function increaseViews($threadId, $views)
    {
        // We need to use the queryBuilder because we want to group the notifications by users
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_agora_domain_model_thread'
        );
        $statement = $queryBuilder->update('tx_agora_domain_model_thread')
            ->where(
                $queryBuilder->expr()->eq('uid', $threadId)
            )
            ->set('views', $views + 1)
            ->execute();
    }

}
