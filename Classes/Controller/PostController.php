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
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Domain\Model\User;
use AgoraTeam\Agora\Service\TagService;
use AgoraTeam\Agora\Utility\QuoteUtility;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;

/**
 * PostController
 */
class PostController extends ActionController
{

    const QUOTE_MODE = 'quote';

    /**
     * postService
     *
     * @var \AgoraTeam\Agora\Domain\Service\PostService
     * @inject
     */
    protected $postService;

    /**
     * threadService
     *
     * @var \AgoraTeam\Agora\Service\MailService
     * @inject
     */
    protected $mailService;

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
     * @var \AgoraTeam\Agora\Domain\Service\AuthorizationService
     * @inject
     */
    protected $authorizationService;

    /**
     * action list
     *
     * @param \AgoraTeam\Agora\Domain\Model\Thread $thread
     * @param string $page
     * @return void
     */
    public function listAction(\AgoraTeam\Agora\Domain\Model\Thread $thread, $page = 1)
    {
        try {
            $this->authenticationService->assertReadAuthorization($thread);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        $observedThread = [];
        $paginator = '';

        // Calculate everything for the pagination
        $itemsPerPage = ($this->settings['post']['numberOfItemsPerPage']) ?
            $this->settings['post']['numberOfItemsPerPage'] : 20;

        // Count all results
        $countPosts = $this->postRepository->countPostsByThreadsOnFirstLevel($thread);

        // Fetch data
        $offset = ($page - 1) * $itemsPerPage;
        $limit = $itemsPerPage;


        if ($countPosts > $itemsPerPage) {
            $totalPages = ceil($countPosts / $itemsPerPage);
            $paginator = $this->paginationService->build($page, $totalPages);
        }

        if ($totalPages && $totalPages < $page) {
            throw new TargetNotFoundException('Page was not found');
        }
        $posts = $this->postRepository->findByThreadPaginated($thread, $offset, $limit);
        // We need to calculate the beginning of the first post
        $postnumber = ($page * $itemsPerPage) - $itemsPerPage;

        /** @var User $user */
        $user = $this->authenticationService->getUser();
        $this->increaseThreadView($thread);

        $firstPost = $this->postRepository->findByThread($thread)->getFirst();

        if (is_a($user, '\AgoraTeam\Agora\Domain\Model\User')) {
            if (!$thread->hasBeenReadByFrontendUser($user)) {
                $user->addReadThread($thread);
                $this->userRepository->update($user);
            }
            if ($user->getObservedThreads() !== null) {
                $observedThread = $user->getObservedThreads()->offsetExists($thread);
            }
        }

        $this->view->assignMultiple(
            array(
                'thread' => $thread,
                'posts' => $posts,
                'page' => $page,
                'postnumber' => $postnumber,
                'paginator' => $paginator,
                'totalPostAmount' => $countPosts,
                'firstPost' => $firstPost,
                'user' => $user,
                'observedThread' => $observedThread
            )
        );
    }

    /**
     * action show
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $post
     * @return void
     */
    public function showAction(\AgoraTeam\Agora\Domain\Model\Post $post)
    {
        try {
            $this->authenticationService->assertReadAuthorization($post);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        $user = $this->authenticationService->getUser();
        $this->view->assign('post', $post);
        $this->view->assign('user', $user);
    }

    /**
     * action showHistory
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $post
     * @return void
     */
    public function showHistoryAction(\AgoraTeam\Agora\Domain\Model\Post $post)
    {
        try {
            $this->authenticationService->assertReadAuthorization($post);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }
        $this->view->assign('post', $post);
    }

    /**
     * action new
     *
     * @param string $mode
     * @param \AgoraTeam\Agora\Domain\Model\Post $newPost
     * @param \AgoraTeam\Agora\Domain\Model\Thread $thread
     * @param \AgoraTeam\Agora\Domain\Model\Post $quotedPost
     * @ignorevalidation $newPost
     * @return void
     */
    public function newAction(
        string $mode = '',
        \AgoraTeam\Agora\Domain\Model\Post $newPost = null,
        \AgoraTeam\Agora\Domain\Model\Post $quotedPost = null,
        \AgoraTeam\Agora\Domain\Model\Thread $thread = null
    ) {
        try {
            $this->authenticationService->assertNewPostAuthorization($thread);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }
        $quote = '';
        if (self::QUOTE_MODE == $mode) {
            $qpCreator = $quotedPost->getCreator();
            $qpAuthorName = $this->settings['post']['defaultCreatorName'];
            if (!is_null($qpCreator)) {
                /* @todo specifiy the name to display by typoscript */
                $qpAuthorName = $qpCreator->getFirstName() . ' ' . $qpCreator->getLastName();
            }
            $quote = QuoteUtility::create(
                $quotedPost->getText(),
                $qpAuthorName,
                $quotedPost->getCrdate()
            );
            $quotedPost = null;
        }
        $this->view->assignMultiple([
            'mode' => $mode,
            'quote' => $quote,
            'newPost' => $newPost,
            'quotedPost' => $quotedPost,
            'thread' => $thread,
        ]);
    }

    /**
     * action create
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $newPost
     * @return void
     */
    public function createAction(\AgoraTeam\Agora\Domain\Model\Post $newPost)
    {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        try {
            $this->authenticationService->assertNewPostAuthorization($newPost->getThread());
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        $newPost->setForum($newPost->getThread()->getForum());
        $user = $this->authenticationService->getUser();
        $now = new \DateTime();

        $newPost->setCreator($user);
        $newPost->setPublishingDate($now);
        $this->postRepository->add($newPost);

        $thread = $newPost->getThread();
        $thread->addPost($newPost);

        $this->threadRepository->update($thread);

        /* Force the post to persist for the dispatcher */
        $this->persistenceManager->persistAll();

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'postCreated',
            ['post' => $newPost]
        );

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'tx_agora_domain_model_post.flashMessages.created',
                'agora'
            ),
            '',
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );

        // Build up the redirect
        $uri = $this->generatePostUri($newPost, true);
        $this->redirectToUri($uri);
    }

    /**
     * action edit
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $originalPost
     * @param \AgoraTeam\Agora\Domain\Model\Post $post
     * @ignorevalidation $post
     * @return void
     */
    public function editAction(
        \AgoraTeam\Agora\Domain\Model\Post $originalPost,
        \AgoraTeam\Agora\Domain\Model\Post $post = null
    ) {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        try {
            $this->authenticationService->assertEditPostAuthorization($originalPost);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        $isFirstPost = false;
        if ($post === null) {
            $post = $this->postService->copy($originalPost);
        }

        // To update the tstamp of the thread we've to update the thread-object
        // just by adding the new post to the thread
        $thread = $originalPost->getThread();
        $this->threadRepository->update($thread);
        $firstPost = $this->postRepository->findByThread($thread)->getFirst();
        $firstPostUid = ($firstPost ? $firstPost->getUid() : null);

        if ($firstPostUid === $originalPost->getUid()) {
            $isFirstPost = true;
        }

        $this->view->assign('isFirstPostInThread', $isFirstPost);
        $this->view->assign('originalPost', $originalPost);
        $this->view->assign('post', $post);
    }

    /**
     * action update
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $originalPost
     * @param \AgoraTeam\Agora\Domain\Model\Post $post
     * @param string $tags
     * @return void
     */
    public function updateAction(
        \AgoraTeam\Agora\Domain\Model\Post $originalPost,
        \AgoraTeam\Agora\Domain\Model\Post $post,
        string $tags = ''
    ) {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        try {
            $this->authenticationService->assertEditPostAuthorization($originalPost);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        $thread = $originalPost->getThread();
        // Only process if there are changes within the text
        if ($originalPost->getText() !== $post->getText()) {
            $newPost = $this->postService->copy($originalPost);

            $newPost->setTopic($post->getTopic());
            $newPost->setText($post->getText());
            $newPost->setForum($post->getThread()->getForum());
            $newPost->setOriginalPost($originalPost);

            /* Move all replies to the newPost and update the postReposiory */
            foreach ($originalPost->getReplies()->toArray() as $reply) {
                $newPost->addReply($reply);
                $reply->setQuotedPost($newPost);
                $this->postRepository->update($reply);
            }

            $this->postService->archive($originalPost);
            $newPost->addHistoricalVersion($originalPost);

            $this->postRepository->update($originalPost);
            $this->postRepository->add($newPost);
            $thread = $newPost->getThread();
        }

        // Update the tags for the thread due to changes on the first post
        if ($tags) {
            /** @var TagService $tagService */
            $tagService = $this->objectManager->get(TagService::class);
            $tags = $tagService->prepareTags($tags);
            $thread->setTags($tags);
            $this->threadRepository->update($thread);
        }

        /* Force the post to persist for the dispatcher */
        $this->persistenceManager->persistAll();

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'tx_agora_domain_model_post.flashMessages.updated',
                'agora'
            ),
            '',
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'postUpdated',
            ['post' => $newPost]
        );

        // Build up the redirect
        $uri = $this->generatePostUri($newPost, true);
        $this->redirectToUri($uri);
    }

    /**
     * action delete
     * @todo Redirect to the right paginated page
     * @param \AgoraTeam\Agora\Domain\Model\Post $post
     * @return void
     */
    public function deleteAction(\AgoraTeam\Agora\Domain\Model\Post $post)
    {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        try {
            $this->authenticationService->assertDeletePostAuthorization($post);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        // check if post is first post
        $firstPost = $this->postRepository->findByThread($post->getThread())->getFirst();
        $firstPostUid = ($firstPost ? $firstPost->getUid() : null);
        if ($firstPostUid === $post->getUid()) {
            $forum = $post->getThread()->getForum();
            $this->threadRepository->remove($post->getThread());
            $this->addLocalizedFlashmessage('tx_agora_domain_model_thread.flashMessages.deleted');
            $arguments = ['forum' => $forum];
            $this->signalSlotDispatcher->dispatch(
                __CLASS__,
                'threadDeleted',
                ['thread' => $post->getThread()]
            );
            $this->redirect(
                'list',
                'Thread',
                'Agora',
                ['forum' => $forum]
            );
        } else {
            $thread = $post->getThread();
            $this->postRepository->remove($post);
            $this->addLocalizedFlashmessage('tx_agora_domain_model_post.flashMessages.deleted');
            $this->signalSlotDispatcher->dispatch(
                __CLASS__,
                'postDeleted',
                ['post' => $post]
            );
            $this->redirect(
                'list',
                'Post',
                'Agora',
                ['thread' => $thread]
            );
        }
    }

    /**
     * @param Thread $thread
     */
    protected function increaseThreadView(Thread $thread)
    {
        if ($this->settings['thread']['views']['delay'] != 0) {
            $delay = $this->settings['thread']['views']['delay']['time'];

            $tsfe = $this->getTypoScriptFrontendController();
            $sessionData = $tsfe->fe_user->getKey('ses', 'tx_agora_views');
            if (is_null($sessionData)) {
                $sessionData = [];
            } else {
                $sessionData = json_decode($sessionData, true);
            }
            if (array_key_exists($thread->getUid(), $sessionData)) {
                if (time() - $delay < $sessionData[$thread->getUid()]) {
                    return false;
                }
            }
            $sessionData[$thread->getUid()] = time();
            $tsfe->fe_user->setKey('ses', 'tx_agora_views', json_encode($sessionData));
        }
        $this->threadRepository->increaseViews($thread->getUid(), $thread->getViews());
    }

    /**
     * @param Post $post
     */
    public function confirmDeleteAction(Post $post)
    {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        try {
            $this->authenticationService->assertDeletePostAuthorization($post);
        } catch (\TYPO3\CMS\Core\Error\Http\PageNotFoundException $exception) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        $this->view->assign('post', $post);
        $this->view->assign('user', $this->authenticationService->getUser());
    }

    /**
     * action listLatest
     *
     * @return void
     */
    public function listLatestAction()
    {
        $limit = intval($this->settings['limit']);
        if ($limit === 0) {
            $limit = $this->settings['post']['numberOfItemsInLatestView'];
        }
        $latestPosts = $this->postRepository->findLatestPostsForUser($limit);
        $this->view->assign('latestPosts', $latestPosts);
    }
}
