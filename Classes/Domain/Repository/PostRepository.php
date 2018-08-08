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
use AgoraTeam\Agora\Domain\Model\Dto\ForumDemand;
use AgoraTeam\Agora\Domain\Model\Thread;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Posts
 *
 * Class PostRepository
 * @package AgoraTeam\Agora\Domain\Repository
 */
class PostRepository extends AbstractDemandedRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'publishing_date' => QueryInterface::ORDER_ASCENDING
    );

    /**
     * @param Thread $thread
     * @return mixed
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
            ->setOffset((integer)$offset)
            ->setLimit((integer)$limit)
            ->execute();

        return $result;
    }

    /**
     * Finds the latest Posts
     *
     * @param integer $limit The number of threads to return at max
     * @param $openUserForums
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findLatestPostsForUser($limit, $openUserForums)
    {
        $constraints = [];

        $query = $this->createQuery();
        $constraints['original_post'] = $query->equals('original_post', 0);
        if ($openUserForums && count($openUserForums) > 0) {
            $constraints['forum'] = $query->in('forum', $openUserForums);
        }
        $result = $query
            ->matching(
                $query->logicalAnd(
                    $constraints
                )
            )
            ->setOrderings(array('publishing_date' => QueryInterface::ORDER_DESCENDING))
            ->setLimit((integer)$limit)
            ->execute();

        return $result;
    }


    /**
     * @param $uid
     * @return mixed
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
     * @param int $thread
     * @param string $sword
     * @return mixed
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findFirstPostOnThread($thread, $sword)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('thread.uid', $thread),
                $query->like("text", '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($sword, '') . '%')
            ))->setOrderings(array('crdate' => QueryInterface::ORDER_ASCENDING));

        return $query->execute()->getFirst();
    }

    /**
     * Get the search constraints
     *
     * @param QueryInterface $query
     * @param ForumDemand $demand
     * @param ThreadRepository $threadRepository
     * @return array
     * @throws \Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function getSearchConstraints(
        QueryInterface $query,
        ForumDemand $demand,
        ThreadRepository $threadRepository
    ) {
        $constraints = [];

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

            $threadsWithSearchedWord = $threadRepository->findDemanded($demand);
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
