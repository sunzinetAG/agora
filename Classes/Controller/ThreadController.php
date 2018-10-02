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
use AgoraTeam\Agora\Domain\Service\MailService;
use AgoraTeam\Agora\Service\TagService;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;

/**
 * ThreadController
 */
class ThreadController extends ActionController
{

    /**
     * forumRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ForumRepository
     * @inject
     */
    protected $forumRepository = null;

    /**
     * threadRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ThreadRepository
     * @inject
     */
    protected $threadRepository = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Service\AuthorizationService
     * @inject
     */
    protected $authorizationService;

    /**
     * action list
     *
     * @param \AgoraTeam\Agora\Domain\Model\Forum $forum
     * @param string $page
     * @return void
     */
    public function listAction(\AgoraTeam\Agora\Domain\Model\Forum $forum, $page = 1)
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
     * @param \AgoraTeam\Agora\Domain\Model\Thread $thread
     * @return void
     */
    public function showAction(\AgoraTeam\Agora\Domain\Model\Thread $thread)
    {
        $this->view->assign('thread', $thread);
    }

    /**
     * action new
     *
     * @param \AgoraTeam\Agora\Domain\Model\Forum $forum
     * @param \AgoraTeam\Agora\Domain\Model\Thread $thread
     * @param string $text
     * @return void
     */
    public function newAction(
        \AgoraTeam\Agora\Domain\Model\Forum $forum,
        \AgoraTeam\Agora\Domain\Model\Thread $thread = null,
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
     * @param \AgoraTeam\Agora\Domain\Model\Forum $forum
     * @param \AgoraTeam\Agora\Domain\Model\Thread $thread
     * @param string $text
     * @param string $tags
     * @validate $text notEmpty
     * @return void
     */
    public function createAction(
        \AgoraTeam\Agora\Domain\Model\Forum $forum,
        \AgoraTeam\Agora\Domain\Model\Thread $thread,
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

        /** @var \AgoraTeam\Agora\Domain\Model\Post $post */
        $post = new \AgoraTeam\Agora\Domain\Model\Post;
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
     * @return void
     */
    public function listLatestAction()
    {
        $user = $this->authenticationService->getUser();
        $limit = intval($this->settings['limit']);
        if ($limit === 0) {
            $limit = $this->settings['thread']['numberOfItemsInLatestView'];
        }
        $latestThreads = $this->threadRepository->findLatestThreadsForUser($limit);

        $this->view->assign('user', $user);
        $this->view->assign('latestThreads', $latestThreads);
    }
}
