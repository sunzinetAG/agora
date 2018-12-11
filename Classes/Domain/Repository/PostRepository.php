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
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Domain\Model\DemandInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Posts
 */
class PostRepository extends AbstractDemandedRepository
{

    /**
     * ForumRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ForumRepository
     * @inject
     */
    protected $forumRepository;

    /**
     * threadRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ThreadRepository
     * @inject
     */
    protected $threadRepository;

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'publishing_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    /**
     * @param Thread $thread
     */
    public function findByThreadOnFirstLevel($thread)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('thread', $thread),
                $query->equals('quotedPost', 0)
            )
        );

        $result = $query->execute();
        return $result;
    }

    /**
     * @param Thread $thread
     * @return int
     */
    public function countPostsByThreadsOnFirstLevel(Thread $thread)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('thread', $thread),
                $query->equals('quotedPost', 0)
            )
        );
        $result = $query->count();

        return $result;
    }

    /**
     * @param Thread $thread
     * @param $offset
     * @param $limit
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByThreadPaginated(Thread $thread, $offset, $limit)
    {
        $query = $this->createQuery();
        $result = $query->matching(
            $query->logicalAnd(
                $query->equals('thread', $thread),
                $query->equals('quotedPost', 0)
            )
        )
        ->setOffset((integer) $offset)
        ->setLimit((integer) $limit)
        ->execute();

        return $result;
    }

    /**
     * Finds the latest Posts
     *
     * @param integer $limit The number of threads to return at max
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findLatestPostsForUser($limit)
    {
        $constraints = [];
        $openUserForums = $this->forumRepository->findAccessibleUserForums();
        $query = $this->createQuery();
        $constraints['original_post'] = $query->equals('original_post', 0);
        $constraints['thread_posts'] = $query->greaterThan('thread.posts', 1);
        if ($openUserForums && count($openUserForums) > 0) {
            $constraints['forum'] = $query->in('forum', $openUserForums);
        }
        $result = $query
            ->matching(
                $query->logicalAnd(
                    $constraints
                )
            )
            ->setOrderings(array('publishing_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING))
            ->setLimit((integer)$limit)
            ->execute();

        return $result;
    }

    /**
     * @param $uid
     */
    public function findPostByUid($uid)
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
     * @param string $sword
     * @param int $thread
     */
    public function findFirstPostOnThread($thread, $sword)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('thread.uid', $thread),
                $query->like("text", '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($sword, '') . '%')
            )
        )->setOrderings(array('crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));

        return $query->execute()->getFirst();
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
        $searchRadius = $searchObject->getRadius();

        if (!empty($searchForums)) {
            $constraints[] = $query->in('forum', $searchForums);
        }

        if (!is_null($searchRadius)) {
            $constraints[] = $query->equals('original_post', 0);
        }
        $constraints[] = $query->greaterThan('thread', 0);

        if ($searchRadius == 2) {
            $threadsWithSearchedWord = $this->threadRepository->findDemanded($demand);
            if (!count($threadsWithSearchedWord)) {
                $threadsWithSearchedWord = array(0 => '-1');
            }

            $constraints[] = $query->in('thread', $threadsWithSearchedWord);
            $constraints[] = $query->equals('historical_versions', 0);
        }

        if (!empty($searchSubject)) {
            $searchFields = array(0 => 'text');

            if (is_null($searchRadius) || $searchRadius == 2) {
                $searchFields = array(0 => 'text', 1 => 'thread.title');
            }

            if ($searchRadius == 3) {
                $searchFields = array(0 => 'thread.title');
            }

            $searchConstraints = [];

            if (count($searchFields) === 0) {
                throw new \UnexpectedValueException('No search fields defined', 1318497755);
            }

            $searchSubjectSplitted = GeneralUtility::trimExplode(' ', $searchSubject, true);
            if ($searchObject->isSplitSubjectWords() && count($searchSubjectSplitted) > 1) {
                foreach ($searchFields as $field) {
                    $subConstraints = [];
                    foreach ($searchSubjectSplitted as $searchSubjectSplittedPart) {
                        $subConstraints[] = $query->like($field, '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchSubjectSplittedPart, '') . '%');
                    }
                    $searchConstraints[] = $query->logicalAnd($subConstraints);
                }

                if (count($searchConstraints)) {
                    $constraints[] = $query->logicalOr($searchConstraints);
                }
            } else {
                foreach ($searchFields as $field) {
                    if (!empty($searchSubject)) {
                        $searchConstraints[] = $query->like($field, '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchSubject, '') . '%');
                    }
                }
                if (count($searchConstraints)) {
                    if ($searchRadius == 2) {
                        $constraints[] = $query->logicalAnd($searchConstraints);
                    } else {
                        $constraints[] = $query->logicalOr($searchConstraints);
                    }
                }
            }
        }

        return $constraints;
    }
}
