<?php

namespace AgoraTeam\Agora\Command;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Philipp Thiele <philipp.thiele@phth.de>
 *           BjÃ¶rn Christopher Bresser <bjoern.bresser@gmail.com>
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
use AgoraTeam\Agora\Domain\Model\Action;
use AgoraTeam\Agora\Domain\Model\Notification;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Domain\Model\User;
use AgoraTeam\Agora\Service\Notification\NotificationService;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

/**
 * ActionController
 */
class ActionConverterCommandController extends CommandController
{

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * notificationRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\NotificationRepository
     * @inject
     */
    protected $notificationRepository = null;

    /**
     * ActionRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ActionRepository
     * @inject
     */
    protected $actionRepository = null;

    /**
     * ThreadRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ThreadRepository
     * @inject
     */
    protected $threadRepository = null;

    /**
     * PostRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\PostRepository
     * @inject
     */
    protected $postRepository = null;

    /**
     * UserRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository = null;

    /**
     * The settings.
     *
     * @var array
     */
    protected $settings = array();

    /**
     * NotificationCommandController constructor.
     */
    protected function initialize()
    {
        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
        // get settings
        $this->settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'agora'
        );
    }

    /**
     * @param \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * Convert actions to notifications to save performance
     */
    public function actionConverterCommand()
    {
        $this->initialize();

        // Get all new actions
        $actions = $this->actionRepository->findAll()->toArray();
        $notifications = $this->getNotificationsFromActions($actions);
        $trimmedNotifications = $this->trimDuplications($notifications);

        foreach ($actions as $action) {
            $this->actionRepository->remove($action);
        }
        foreach ($trimmedNotifications as $trimmedNotification) {
            $this->notificationRepository->add($trimmedNotification);
        }

        return true;
    }

    /**
     * @param $notifications
     * @return array
     */
    private function trimDuplications($notifications)
    {
        $alreadyChecked = [];
        $tmpNotifications = $notifications;
        $trimmedNotifications = [];
        foreach ($notifications as $key => $notificationAction) {
            $noDuplications = true;
            $type = $notificationAction->getType();
            $page = $notificationAction->getPage();
            $title = $notificationAction->getTitle();
            $owner = $notificationAction->getOwner();
            $user = $notificationAction->getUser();
            $post = $notificationAction->getPost();
            $thread = $notificationAction->getThread();
            $data = $notificationAction->getData();

            if (array_key_exists($key, $alreadyChecked)) {
                continue;
            }

            foreach ($tmpNotifications as $k => $v) {
                // Check if owner has duplications
                if ($type == $v->getType() &&
                    $title == $v->getTitle() &&
                    $page == $v->getPage() &&
                    $owner == $v->getOwner() &&
                    $user == $v->getUser() &&
                    $post == $v->getPost() &&
                    $thread == $v->getThread() &&
                    $data == $v->getData() &&
                    $k != $key
                ) {
                    $trimmedNotifications[$key] = $notificationAction;
                    $alreadyChecked[$k] = $k;
                    $noDuplications = false;
                    unset($tmpNotifications[$k]);
                }
            }
            if ($noDuplications) {
                $alreadyChecked[$key] = $key;
                $trimmedNotifications[$key] = $notificationAction;
            }
        }
        // Remove notifications where the user equals owner
        foreach ($trimmedNotifications as $key => $trimmedNotification) {
            if ($trimmedNotification->getUser() == $trimmedNotification->getOwner()) {
                unset($trimmedNotifications[$key]);
            }
        }

        return $trimmedNotifications;
    }

    /**
     * Get the notifications from the actions. Type "Other" actions will not
     * be transformed to agora notifications. Type "NEW_THREAD" notification will be
     * global for the first time
     *
     * @param array $actions
     * @return array
     */
    private function getNotificationsFromActions($actions)
    {
        $notifications = [];

        /** @var Action $action * */
        foreach ($actions as $key => $action) {
            $quotedPost = null;
            $observerNotifications = [];

            $thread = $this->threadRepository->findThreadByUid($action->getThread());
            $post = $this->postRepository->findPostByUid($action->getPost());

            switch ($action->getType()) {
                case NotificationService::NEW_THREAD:
                    // Handle other actions by yourself ;)
                    $this->signalSlotDispatcher->dispatch(
                        __CLASS__,
                        'notificationOnNewThread',
                        ['action' => $action]
                    );
                    break;
                case NotificationService::NEW_POST:
                    $notifications[] = $this->getThreadOwnerNotification($action, $thread);
                    if ($quotedPost = $this->getQuotedPostOwnerNotification($action, $post)) {
                        $notifications[] = $quotedPost;
                    }
                    $observerNotifications = $this->getObserverNotification($action, $thread, $post);
                    break;
                case NotificationService::NEW_QUOTED_POST:
                    $notifications[] = $this->getThreadOwnerNotification($action, $thread);
                    if ($quotedPost = $this->getQuotedPostOwnerNotification($action, $post)) {
                        $notifications[] = $quotedPost;
                    }
                    $observerNotifications = $this->getObserverNotification($action, $thread, $post);
                    break;
                case NotificationService::UPDATE_POST:
                    $notifications[] = $this->getThreadOwnerNotification($action, $thread);
                    if ($quotedPost = $this->getQuotedPostOwnerNotification($action, $post)) {
                        $notifications[] = $quotedPost;
                    }
                    $observerNotifications = $this->getObserverNotification($action, $thread, $post);
                    break;
                case NotificationService::DELETE_POST:
                    $this->signalSlotDispatcher->dispatch(
                        __CLASS__,
                        'notificationOnPostDelete',
                        ['action' => $action, 'notifications' => &$notifications]
                    );
                    break;
                case NotificationService::DELETE_THREAD:
                    $this->signalSlotDispatcher->dispatch(
                        __CLASS__,
                        'notificationOnThreadDelete',
                        ['action' => $action, 'notifications' => &$notifications]
                    );
                    break;
                case NotificationService::NEW_RATING:
                case NotificationService::POSITIVE_RATING:
                case NotificationService::NEGATIVE_RATING:
                    $notifications[] = $this->getPostOwnerNotification($action, $post);
                    break;
                case NotificationService::USER_DEFINED:
                    // This could not only return one single notification!!
                    $userDefinedNotifications = $this->getUserDefinedNotifications($action);
                    $notifications = array_merge(
                        array_values($notifications),
                        array_values($userDefinedNotifications)
                    );
                    break;
                default:
                    // Handle other actions by yourself ;)
                    $this->signalSlotDispatcher->dispatch(
                        __CLASS__,
                        'notificationFromOtherActions',
                        ['action' => $action, 'notifications' => &$notifications]
                    );
            }
            if ($observerNotifications) {
                foreach ($observerNotifications as $observerNotification) {
                    $notifications[] = $observerNotification;
                }
            }
        }
        return $notifications;
    }

    /**
     * @param Action $action
     * @return mixed
     */
    private function getUserDefinedNotifications($action)
    {
        $notifications = [];
        $title = $action->getTitle();
        $description = $action->getDescription();
        $link = $action->getLink();
        $type = $action->getType();
        $tstamp = $action->getTstamp();

        if ($action->getUser()) {
            $notification = new Notification();
            $notification->setType($type);
            $notification->setOwner($action->getUser());
            $notification->setTitle($title);
            $notification->setDescription($description);
            $notification->setLink($link);
            $notification->setTstamp($tstamp);
            $notification->setCrdate($tstamp);

            $notifications[] = $notification;
        } else {
            // We need to get all users by the set groups
            $users = $this->userRepository->findByUsergroups($action->getGroups());
            foreach ($users as $user) {
                $notification = new Notification();
                $notification->setType($type);
                $notification->setOwner($user->getUid());
                $notification->setTitle($title);
                $notification->setDescription($description);
                $notification->setLink($link);
                $notification->setTstamp($tstamp);
                $notification->setCrdate($tstamp);

                $notifications[] = $notification;
            }
        }
        return $notifications;
    }

    /**
     * @param Action $action
     * @param Post $post
     * @return Notification
     */
    private function getPostOwnerNotification($action, $post)
    {
        $notification = new Notification();
        $notification->setType($action->getType());
        $notification->setUser($action->getUser());
        $notification->setOwner($post->getCreator()->getUid());
        $notification->setThread($action->getThread());
        $notification->setPost($action->getPost());
        $notification->setPage($action->getPage());
        $notification->setTitle($action->getTitle());
        $notification->setTstamp($action->getTstamp());

        return $notification;
    }

    /**
     * @param Action $action
     * @param Thread $thread
     * @return Notification
     */
    private function getThreadOwnerNotification($action, $thread)
    {
        $notification = new Notification();
        $notification->setType($action->getType());
        $notification->setUser($action->getUser());
        $notification->setOwner($thread->getCreator()->getUid());
        $notification->setThread($action->getThread());
        $notification->setPost($action->getPost());
        $notification->setPage($action->getPage());
        $notification->setTitle($action->getTitle());
        $notification->setTstamp($action->getTstamp());

        return $notification;
    }

    /**
     * @param Action $action
     * @param Post $post
     * @return Notification
     */
    private function getQuotedPostOwnerNotification($action, $post)
    {
        $notification = null;
        if (!is_null($post)) {
            if ($post->getQuotedPost()) {
                $notification = new Notification();
                $notification->setType($action->getType());
                $notification->setTitle($action->getTitle());
                $notification->setUser($action->getUser());
                $notification->setOwner($post->getQuotedPost()->getCreator()->getUid());
                $notification->setThread($post->getThread()->getUid());
                $notification->setPost($post->getUid());
                $notification->setPage($action->getPage());
                $notification->setTstamp($action->getTstamp());
            }
        }

        return $notification;
    }

    /**
     * @param Action $action
     * @param Thread $thread
     * @param Post $post
     * @return array
     */
    private function getObserverNotification($action, $thread, $post)
    {
        $notifications = [];

        if ($thread) {
            $observers = $thread->getObservers();
            /** @var User $observer */
            foreach ($observers as $observer) {
                $notification = new Notification();
                $notification->setType($action->getType());
                $notification->setTitle($action->getTitle());
                $notification->setUser($action->getUser());
                $notification->setOwner($observer->getUid());
                $notification->setThread($thread->getUid());
                $notification->setPage($action->getPage());
                $notification->setTstamp($action->getTstamp());

                if ($post) {
                    $notification->setPost($post->getUid());
                }

                $notifications[] = $notification;
                unset($notification);
            }
        }

        return $notifications;
    }
}
