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
use AgoraTeam\Agora\Domain\Model\Forum;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use AgoraTeam\Agora\Domain\Model\DemandInterface;

/**
 * The repository for Threads
 */
class ThreadRepository extends \AgoraTeam\Agora\Domain\Repository\AbstractDemandedRepository
{

    /**
     * ForumRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ForumRepository
     * @inject
     */
    protected $forumRepository;

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
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand)
    {
        /** @var ForumDeman $demand */
        $constraints = [];

        // storage page
        if ($demand->getStoragePage() != 0) {
            $pidList = GeneralUtility::intExplode(',', $demand->getStoragePage(), true);
            $constraints['pid'] = $query->in('pid', $pidList);
        }

        // Search
        $searchConstraints = $this->getSearchConstraints($query, $demand);
        if (!empty($searchConstraints)) {
            $constraints['search'] = $query->logicalAnd($searchConstraints);
        }

        // Clean not used constraints
        foreach ($constraints as $key => $value) {
            if (is_null($value)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    /**
     * Returns an array of orderings created from a given demand object.
     *
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createOrderingsFromDemand(DemandInterface $demand)
    {

        $orderings = [];
        $orderField = $demand->getSearch()->getOrder();

        if (!isset($orderField)) {
            $orderField = $demand->getOrder();
        }

        $orderList = GeneralUtility::trimExplode(',', $orderField, true);

        if (!empty($orderList)) {
            // go through every order statement
            foreach ($orderList as $orderItem) {
                list($orderField, $ascDesc) = GeneralUtility::trimExplode(' ', $orderItem, true);
                // count == 1 means that no direction is given
                if ($ascDesc) {
                    $orderings[$orderField] = ((strtolower($ascDesc) == 'desc') ?
                        QueryInterface::ORDER_DESCENDING :
                        QueryInterface::ORDER_ASCENDING);
                } else {
                    $orderings[$orderField] = QueryInterface::ORDER_ASCENDING;
                }
            }
        }

        return $orderings;
    }

    /**
     * Get the search constraints
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return array
     * @throws \UnexpectedValueException
     */
    protected function getSearchConstraints(QueryInterface $query, DemandInterface $demand)
    {
        $constraints = [];
        $openUserForums = $this->forumRepository->findAccessibleUserForums();

        if ($demand->getSearch() === null) {
            return $constraints;
        }

        /* @var $searchObject \AgoraTeam\Agora\Domain\Model\Dto\Search */
        $searchObject = $demand->getSearch();
        $searchSubject = $searchObject->getSword();
        $searchForums = $searchObject->getThemes();

        if (!empty($searchForums)) {
            $constraints[] = $query->in('forum', $searchForums);
        }

        if (!empty($searchSubject)) {
            $searchConstraints = [];
            $searchRadius = $searchObject->getRadius();
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
