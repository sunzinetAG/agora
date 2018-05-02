<?php

namespace AgoraTeam\Agora\Service\Action;

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
use AgoraTeam\Agora\Domain\Model\Dto\ActionDemand;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Service\Notification\NotificationService;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class ActionListener
 * Handle the additation of new actions via the actionService
 *
 * @package AgoraTeam\Agora\Service
 */
class ActionListener implements SingletonInterface
{

    /**
     * @var \AgoraTeam\Agora\Service\Action\ActionService
     * @inject
     */
    protected $actionService = null;

    /**
     * Setup an action on post creation
     * @param Post $post
     * @return void
     */
    public function onPostCreated(Post $post)
    {
        if ($post->getQuotedPost()) {
            $this->actionService->process($post, NotificationService::NEW_QUOTED_POST);
        } else {
            $this->actionService->process($post, NotificationService::NEW_POST);
        }
    }

    /**
     * Setup an action if a post got updated
     * @param Post $post
     * @return void
     */
    public function onPostUpdated(Post $post)
    {
        $this->actionService->process($post, NotificationService::UPDATE_POST);
    }

    /**
     * Setup an action if a post got deleted
     * @param $post
     * @return void
     */
    public function onPostDeleted(Post $post)
    {
        $this->actionService->process($post, NotificationService::DELETE_POST);
    }

    /**
     * Setup an action if a post got deleted
     * @param $thread
     * @return void
     */
    public function onThreadDeleted(Thread $thread)
    {
        $this->actionService->process($thread, NotificationService::DELETE_THREAD);
    }

    /**
     * Setup an action if a new thread got created
     * @param Thread $thread
     * @return void
     */
    public function onThreadCreated(Thread $thread)
    {
        $this->actionService->process($thread, NotificationService::NEW_THREAD);
    }

    /**
     * Setup an action if a new thread got created
     * @param Post $post
     * @param string $rating
     * @return void
     */
    public function onConfirmedRating(Post $post, string $rating)
    {
        $this->actionService->process($post, NotificationService::NEW_RATING, $rating);
    }

    /**
     * Add another action to the actionlist which is not related to agora
     *
     * @param $title
     * @param $description
     * @param null $user
     * @param array $data
     * @param int $type
     * @return void
     */
    public function onOther($title, $description, $user = null, $data = [], $type = NotificationService::OTHER)
    {
        /** @var ActionDemand $actionDemand */
        $actionDemand = new ActionDemand();
        $actionDemand->setTitle($title);
        $actionDemand->setDescription($description);
        $actionDemand->setUser($user);
        $actionDemand->setData($data);
        $actionDemand->setType($type);

        $this->actionService->process($actionDemand, $type);
    }
}
