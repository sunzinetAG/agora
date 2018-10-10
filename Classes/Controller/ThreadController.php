<?php

namespace AgoraTeam\Agora\Controller;

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

use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use AgoraTeam\Agora\Domain\Model\Forum;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Service\TagService;
use AgoraTeam\Agora\Domain\Repository\ForumRepository;
use AgoraTeam\Agora\Domain\Repository\ThreadRepository;

/**
 * Class ThreadController
 * @package AgoraTeam\Agora\Controller
 */
class ThreadController extends ActionController
{

    /**
     * forumRepository
     *
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository = null;

    /**
     * threadRepository
     *
     * @var ThreadRepository
     * @inject
     */
    protected $threadRepository = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Service\AuthorizationService
     * @inject
     */
    protected $authorizationService;

    /**
     * Action list
     *
     * @param Forum $forum
     * @param int $page
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException
     */
    public function listAction(Forum $forum, $page = 1)
    {
        $this->authenticationService->assertReadAuthorization($forum);
        $paginator = '';
        // Calculate everything for the pagination
        $itemsPerPage = ($this->settings['thread']['numberOfItemsPerPage']) ?
            $this->settings['thread']['numberOfItemsPerPage'] : 10;

        // Count all results
        $countThreads = $this->threadRepository->countByForum($forum);

        // Fetch data
        $offset = ($page - 1) * $itemsPerPage;
        $limit = $itemsPerPage;
        if ($countThreads > $itemsPerPage) {
            $totalPages = ceil($countThreads / $itemsPerPage);
            $paginator = $this->paginationService->build($page, $totalPages);
        }

        if ($totalPages && $totalPages < $page) {
            throw new TargetNotFoundException('Page was not found');
        }

        $threads = $this->threadRepository->findByThreadPaginated($forum, $offset, $limit);

        $this->view->assignMultiple(
            array(
                'forum' => $forum,
                'paginator' => $paginator,
                'page' => $page,
                'totalThreadAmount' => $countThreads,
                'threads' => $threads
            )
        );
    }

    /**
     * action show
     *
     * @param Thread $thread
     * @return void
     */
    public function showAction(Thread $thread)
    {
        $this->view->assign('thread', $thread);
    }

    /**
     * action new
     *
     * @param Forum $forum
     * @param Thread|null $thread
     * @param string $text
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     * @return void
     */
    public function newAction(
        Forum $forum,
        Thread $thread = null,
        $text = ''
    ) {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        $this->authenticationService->assertNewThreadAuthorization($forum);

        $this->view->assign('forum', $forum)
            ->assign('thread', $thread)
            ->assign('text', $text);
    }

    /**
     * action create
     *
     * @param Forum $forum
     * @param Thread $thread
     * @param string $text
     * @param string $tags
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @validate $text notEmpty
     * @return void
     */
    public function createAction(
        Forum $forum,
        Thread $thread,
        $text,
        $tags = ''
    ) {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        $this->authenticationService->assertNewThreadAuthorization($forum);

        if ($tags) {
            /** @var TagService $tagService */
            $tagService = $this->objectManager->get(TagService::class);
            $tags = $tagService->prepareTags($tags);
            $thread->setTags($tags);
        }

        /** @var Post $post */
        $post = new Post;
        $post->setTopic($thread->getTitle());
        $post->setText($text);
        $post->setForum($forum);
        $now = new \DateTime();
        $post->setPublishingDate($now);

        if (is_a($this->authenticationService->getUser(), '\AgoraTeam\Agora\Domain\Model\User')) {
            $post->setCreator($this->authenticationService->getUser());
        }

        $thread->setForum($forum);
        $thread->addPost($post);
        if (is_a($this->authenticationService->getUser(), '\AgoraTeam\Agora\Domain\Model\User')) {
            $thread->setCreator($this->authenticationService->getUser());
        }
        $forum->addThread($thread);
        $this->forumRepository->update($forum);

        $this->addLocalizedFlashmessage('tx_agora_domain_model_thread.flashMessages.created');

        /* Force the thread to persist for the dispatcher */
        $this->persistenceManager->persistAll();
        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'threadCreated',
            ['thread' => $thread]
        );

        $this->redirect(
            'list',
            'Post',
            'Agora',
            array('thread' => $thread)
        );
    }

    /**
     * action listLatest
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @return void
     */
    public function listLatestAction()
    {
        $user = $this->authenticationService->getUser();
        $limit = intval($this->settings['limit']);
        if ($limit === 0) {
            $limit = $this->settings['thread']['numberOfItemsInLatestView'];
        }
        $latestThreads = $this->threadRepository->findLatestThreadsForUser($limit, $this->forumRepository);

        $this->view->assign('user', $user);
        $this->view->assign('latestThreads', $latestThreads);
    }
}
