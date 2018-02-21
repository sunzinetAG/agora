<?php

namespace AgoraTeam\Agora\Service\Notification;

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
use AgoraTeam\Agora\Domain\Model\Notification;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class NotificationService
 *
 * @package AgoraTeam\Agora\Service
 */
class NotificationService implements SingletonInterface
{

    const OTHER = 0;

    const NEW_THREAD = 1;

    const NEW_POST = 2;

    const UPDATE_POST = 3;

    const DELETE_POST = 4;

    const DELETE_THREAD = 5;

    const USER_DEFINED = 6;

    const NEW_QUOTED_POST = 7;

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @var \AgoraTeam\Agora\Domain\Repository\NotificationRepository
     * @inject
     */
    protected $notificationRepository = null;

    /**
     * @param \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * Assamble the notifications for the user to reduce
     * the length of the list the user gets
     *
     * @param array $notifications
     * @return array
     */
    public function assambleNotifications($notifications)
    {
        $assambledNotifi = [];
        $tmpNotifications = $notifications;
        $alreadyMatched = [];

        /** @var Notification $notification */
        foreach ($notifications as $key => $notification) {
            $type = $notification->getType();
            $page = $notification->getPage();
            $thread = $notification->getThread();
            $title = $notification->getTitle();
            $uid = $notification->getUid();

            if (array_key_exists($key, $alreadyMatched)) {
                continue;
            }

            // First put the notification in his own assamble part
            $assambledNotifi[$key]['notifications'][] = $notification;
            $assambledNotifi[$key]['count'] = 1;
            $assambledNotifi[$key]['crdate'] = $notification->getCrdate();
            $assambledNotifi[$key]['user'] = $notification->getUser();
            $assambledNotifi[$key]['type'] = $notification->getType();

            // Iterate with the current notification through each other
            // to find duplications and put them in the $assambleNotifi
            foreach ($tmpNotifications as $k => $v) {
                if ($type == $v->getType() &&
                    $title == $v->getTitle() &&
                    $page == $v->getPage() &&
                    $thread == $v->getThread() &&
                    $uid != $v->getUid()
                ) {
                    $assambledNotifi[$key]['notifications'][] = $v;
                    $assambledNotifi[$key]['count'] = count($assambledNotifi[$key]['notifications']);

                    if ($v->getCrdate() > $notification->getCrdate()) {
                        $assambledNotifi[$key]['crdate'] = $v->getCrdate();
                        $assambledNotifi[$key]['user'] = $v->getUser();
                    }

                    $alreadyMatched[$k] = $k;
                    unset($tmpNotifications[$k]);
                }
            }
        }

        return $assambledNotifi;
    }

    /**
     * @param array $notifications
     * @return array
     */
    public function groupNotificationsByType($notifications)
    {
        $groupedNotifications = [];
        foreach ($notifications as $k => $notification) {
            $type = $notification['type'];
            $groupedNotifications[$type][] = $notification;
        }
        ksort($groupedNotifications);
        return $groupedNotifications;
    }

    /**
     * @param $user
     * @return array
     */
    public function getGroupedNotificationsByUser($user)
    {
        $groupedNotifications = [];
        $checkUser = true;

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'beforeGettingUserNotifications',
            [$user, &$checkUser]
        );

        if ($checkUser) {
            $userNotifications = $this->notificationRepository->findByOwner($user)->toArray();
            if ($userNotifications) {
                $notifiations = $this->assambleNotifications($userNotifications);
                $groupedNotifications = $this->groupNotificationsByType($notifiations);
            }
        }

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'afterGroupingUserNotifications',
            [$user, &$groupedNotifications]
        );

        return $groupedNotifications;
    }

    /**
     * @param $groupedNotifications
     * @return array
     */
    public function flattenGroupedNotifications($groupedNotifications)
    {
        $flattenedNotifications = call_user_func_array('array_merge', $groupedNotifications);
        return $flattenedNotifications;
    }

    /**
     * @param $notifications
     */
    public function markUserNotificationsAsSent($user)
    {
        $notifications = $this->notificationRepository->findByOwner($user);
        foreach ($notifications as $notification) {
            $notification->setSent(1);
            $this->notificationRepository->update($notification);
        }
    }


    /**
     * @param int $page
     * @param string $type
     * @param string $title
     * @param $owner
     * @param string $description
     * @param Post $post
     * @param Thread $thread
     * @param string $link
     * @param array $data
     */
    public function createNewNotification(
        int $page,
        string $type,
        string $title,
        $owner,
        string $description = '',
        Post $post = null,
        Thread $thread = null,
        string $link = '',
        array $data = []
    ) {
        $userId = $this->getUser();

        /** @var Notification $notification */
        $notification = new Notification();
        $notification->setPid(0);
        $notification->setType($type);
        $notification->setOwner($owner);
        $notification->setUser($userId);
        $notification->setPage($page);
        $notification->setTitle($title);
        $notification->setDescription($description);
        $notification->setPost($post);
        $notification->setThread($thread);
        $notification->setLink($link);
        $notification->setData($data);

        $this->notificationRepository->add($notification);
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return (int)$GLOBALS['TSFE']->fe_user->user['uid'];
    }

}
