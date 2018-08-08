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
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use AgoraTeam\Agora\Domain\Model\Dto\ForumDemand;
use AgoraTeam\Agora\Domain\Model\Forum;
use AgoraTeam\Agora\Domain\Model\Dto\Search;

/**
 * The repository for Threads
 *
 * Class ThreadRepository
 * @package AgoraTeam\Agora\Domain\Repository
 */
class ThreadRepository extends AbstractDemandedRepository
{

    /**
     * Find threads by forum
     *
     * @param Forum $forum
     * @return array|QueryResultInterface
     */
    public function findByForum(Forum $forum)
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
     * @param $limit
     * @param ForumRepository $forumRepository
     * @return array|QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findLatestThreadsForUser($limit, ForumRepository $forumRepository)
    {
        $openUserForums = $forumRepository->findAccessibleUserForums();

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
     * @return mixed
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
        $queryBuilder->update('tx_agora_domain_model_thread')
            ->where(
                $queryBuilder->expr()->eq('uid', $threadId)
            )
            ->set('views', $views + 1)
            ->execute();
    }

    /**
     * @param Forum $forum
     * @param $offset
     * @param $limit
     * @return array|QueryResultInterface
     */
    public function findByThreadPaginated(Forum $forum, $offset, $limit)
    {
        $query = $this->createQuery();
        $result = $query->matching(
            $query->equals('forum', $forum)
        )
            ->setOrderings(array('tstamp' => QueryInterface::ORDER_DESCENDING))
            ->setOffset((integer)$offset)
            ->setLimit((integer)$limit)
            ->execute();

        return $result;
    }

    /**
     * Get the search constraints
     *
     * @param QueryInterface $query
     * @param ForumDemand $demand
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function getSearchConstraints(QueryInterface $query, ForumDemand $demand)
    {
        $constraints = [];

        if ($demand->getSearch() === null) {
            return $constraints;
        }

        /* @var $searchObject Search */
        $searchObject = $demand->getSearch();
        $searchSubject = $searchObject->getSword();
        $searchForums = $searchObject->getThemes();

        if (!empty($searchForums)) {
            $constraints[] = $query->in('forum', $searchForums);
        }

        if (!empty($searchSubject)) {
            $searchConstraints = [];
            $searchFields = array(0 => 'title');

            if (count($searchFields) === 0) {
                throw new \UnexpectedValueException('No search fields defined', 1318497755);
            }

            $searchSubjectSplitted = GeneralUtility::trimExplode(' ', $searchSubject, true);
            if ($searchObject->isSplitSubjectWords() && count($searchSubjectSplitted) > 1) {
                foreach ($searchFields as $field) {
                    $subConstraints = [];
                    foreach ($searchSubjectSplitted as $searchSubjectSplittedPart) {
                        $subConstraints[] = $query->like($field,
                            '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchSubjectSplittedPart, '') . '%');
                    }
                    $searchConstraints[] = $query->logicalAnd($subConstraints);
                }

                if (count($searchConstraints)) {
                    $constraints[] = $query->logicalOr($searchConstraints);
                }
            } else {
                foreach ($searchFields as $field) {
                    if (!empty($searchSubject)) {
                        $searchConstraints[] = $query->like($field,
                            '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchSubject, '') . '%');
                    }
                }

                if (count($searchConstraints)) {
                    $constraints[] = $query->logicalOr($searchConstraints);
                }
            }
        }

        return $constraints;
    }


}
