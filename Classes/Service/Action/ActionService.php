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
use AgoraTeam\Agora\Domain\Model\Action;
use AgoraTeam\Agora\Domain\Model\Dto\ActionDemand;
use AgoraTeam\Agora\Domain\Model\NotifiableInterface;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Service\Notification\NotificationService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class NotificationService
 *
 * @package AgoraTeam\Agora\Service
 */
class ActionService implements SingletonInterface
{

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * TaskRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ActionRepository
     * @inject
     */
    protected $actionRepository = null;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     */
    protected $configurationManager = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager'
        );
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    /**
     * @param NotifiableInterface $notificationObject
     * @param $type
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @return void
     */
    public function process(
        NotifiableInterface $notificationObject,
        $type = 0
    ) {
        $action = null;
        if ($this->settings['notifications']['enable'] != 1) {
            return;
        }

        switch ($type) {
            case NotificationService::NEW_THREAD:
                $action = $this->addThreadAction($notificationObject);
                break;
            case NotificationService::NEW_POST:
            case NotificationService::UPDATE_POST:
            case NotificationService::DELETE_POST:
                $action = $this->addPostAction($notificationObject, $type);
                break;
            case NotificationService::OTHER:
                $action = $this->addOtherAction($notificationObject);
                break;
            default:
                break;
        }
        if (!is_null($action)) {
            $this->actionRepository->add($action);
        }
    }

    /**
     * @param Thread $thread
     */
    private function addThreadAction($thread)
    {
        $action = new Action();
        $action->setType(NotificationService::NEW_THREAD);
        $action->setThread($thread);
        $action->setUser($thread->getCreator());

        return $action;
    }

    /**
     * @param Post $post
     * @param $integer $type
     * @return Action $action
     */
    private function addPostAction($post, $type)
    {
        $action = new Action();
        $action->setType($type);
        $action->setPost($post);
        $action->setUser($post->getCreator());

        return $action;
    }

    /**
     * @param ActionDemand $other
     */
    private function addOtherAction($other)
    {
        $action = new Action();
        $action->setType($other->getType());
        $action->setTitle($other->getTitle());
        $action->setDescription($other->getDescription());
        $action->setData(json_encode($other->getData()));

        return $action;
    }

}
