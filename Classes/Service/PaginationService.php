<?php

namespace AgoraTeam\Agora\Service;

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
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class PaginationService
 *
 * @package AgoraTeam\Agora\Service
 */
class PaginationService implements SingletonInterface
{

    /**
     * postRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * threadRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ThreadRepository
     * @inject
     */
    protected $threadRepository;

    /**
     * @param integer $actualPage
     * @param integer $totalPages
     * @param integer $pageLinks Amount of links inside the pagination
     * @return array
     */
    public function build($actualPage, $totalPages, $pageLinks = 7)
    {
        $i = 1;
        $pagination = [];

        /* Show all pages directly inside the pagination
        Pagination: | 1 | 2 | 3 | 4 | 5 | 6 | 7 */

        if ($totalPages <= $pageLinks) {
            while ($i <= $totalPages) {
                $pagination[$i]['value'] = $i;
                if ($i == $actualPage) {
                    $pagination[$i]['class'] = 'active';
                }
                $i++;
            }

            return $pagination;
        }

        /* The current page is less 4 - start with page 1
        Pagination: | 1 | 2 | 3 | 4 | 5 | 6 | 7 >>*/
        if ($actualPage < 4) {
            while ($i <= $pageLinks) {
                $pagination[$i]['value'] = $i;
                if ($i == $actualPage) {
                    $pagination[$i]['class'] = 'active';
                }
                $i++;
            }
            // link to last page
            $pagination[$i]['value'] = $totalPages;
            $pagination[$i]['class'] = 'last';

            return $pagination;
        }

        /* The current page is somewhere in between
        Pagination: << | 5 | 6 | 7 | 8 | 9 | 10 | 11 | >>*/
        if ($actualPage >= 4 && $actualPage < $totalPages - 3) {
            // link to first page
            $pagination[0]['value'] = 1;
            $pagination[0]['class'] = 'first';

            //beginn der Paginierung berechnen:
            $i = $actualPage - 3;
            $end = $i + $pageLinks - 1;

            while ($i <= $end) {
                $pagination[$i]['value'] = $i;
                if ($i == $actualPage) {
                    $pagination[$i]['class'] = 'active';
                }
                $i++;
            }
            // link to last page
            $pagination[$i]['value'] = $totalPages;
            $pagination[$i]['class'] = 'last';

            return $pagination;
        }

        /* The current page is one of the last three
        Pagination: << | 15 | 16 | 17 | 18 | 19 | 20 | 21 */
        if ($actualPage >= $totalPages - 3) {
            // link to first page
            $pagination[0]['value'] = 1;
            $pagination[0]['class'] = 'first';

            //beginn der Paginierung berechnen:
            $i = $totalPages - $pageLinks;
            while ($i <= $totalPages) {
                $pagination[$i]['value'] = $i;
                if ($i == $actualPage) {
                    $pagination[$i]['class'] = 'active';
                }
                $i++;
            }

            return $pagination;
        }
    }

    /**
     * @param Post $post
     * @return integer $page
     */
    public function getPostPagePosition(Post $post, $settings)
    {
        // Check if posts is a quotedPosts
        if (!is_null($post->getQuotedPost())) {
            $post = $post->getQuotedPost();
        }
        $itemsPerPage = ($settings['post']['numberOfItemsPerPage']) ?
            $settings['post']['numberOfItemsPerPage'] : 10;

        $thread = $post->getThread();

        /** @var QueryResult $posts */
        $posts = $this->postRepository->findByThreadOnFirstLevel($thread)->toArray();
        $position = array_search($post, $posts) + 1;
        if (false == $position) {
            return 0;
        }

        $page = ceil($position / $itemsPerPage);

        return $page;
    }

    /**
     * @param Thread $thread
     * @return integer $page
     */
    public function getThreadPagePosition(Thread $thread, $settings)
    {
        $itemsPerPage = ($settings['thread']['numberOfItemsPerPage']) ?
            $settings['thread']['numberOfItemsPerPage'] : 10;

        /** @var QueryResult $posts */
        $threads = $this->threadRepository->findByForum($thread->getForum())->toArray();
        $position = array_search($thread, $threads) + 1;
        if (false == $position) {
            return 0;
        }

        $page = ceil($position / $itemsPerPage);

        return $page;
    }
}
