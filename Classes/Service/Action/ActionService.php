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
use AgoraTeam\Agora\Controller\RatingController;
use AgoraTeam\Agora\Domain\Model\AccessibleInterface;
use AgoraTeam\Agora\Domain\Model\Action;
use AgoraTeam\Agora\Domain\Model\NotifiableInterface;
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
     * @param string $additionalValue
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @return void
     */
    public function process(
        NotifiableInterface $notificationObject,
        $type = 0,
        $additionalValue = ''
    ) {
        $action = null;
        if ($this->settings['notifications']['enable'] != 1) {
            return;
        }

        switch ($type) {
            case NotificationService::NEW_THREAD:
                $action = $this->addThreadAction($notificationObject, $type);
                break;
            case NotificationService::NEW_POST:
            case NotificationService::NEW_QUOTED_POST:
            case NotificationService::UPDATE_POST:
            case NotificationService::DELETE_POST:
                $action = $this->addPostAction($notificationObject, $type);
                break;
            case NotificationService::DELETE_THREAD:
                $action = $this->addThreadAction($notificationObject, $type);
                break;
            case NotificationService::OTHER:
                $action = $this->addOtherAction($notificationObject);
                break;
            case NotificationService::NEW_RATING:
                $action = $this->addNewRatingAction($notificationObject, $type, $additionalValue);
                break;
            default:
                break;
        }
        if (!is_null($action)) {
            $action->setHash();
            $persistedAction = $this->actionRepository->findOneByHash($action->getHash());
            if (is_null($persistedAction)) {
                $this->actionRepository->add($action);
            } else {
                $persistedAction->setTstamp(new \DateTime());
                $this->actionRepository->update($persistedAction);
            }
        }
    }

    /**
     * @param $post
     * @param $type
     * @param $ratingValue
     */
    private function addNewRatingAction($post, $type, $ratingValue)
    {
        if ($ratingValue == RatingController::RATE_TYPE_UP) {
            $type = NotificationService::POSITIVE_RATING;
        } elseif ($ratingValue == RatingController::RATE_TYPE_DOWN) {
            $type = NotificationService::NEGATIVE_RATING;
        }

        $action = new Action();
        $action->setType($type);
        $action->setTitle($post->getThread()->getTitle());
        $action->setThread($post->getThread()->getUid());
        $action->setUser($GLOBALS['TSFE']->fe_user->user['uid']);
        $action->setPage($GLOBALS['TSFE']->id);
        $action->setPost($post->getUid());

        return $action;
    }

    /**
     * @param NotifiableInterface $thread
     * @param integer $type
     * @return Action $action
     */
    private function addThreadAction($thread, $type)
    {
        $action = new Action();
        $action->setType($type);
        $action->setThread($thread->getUid());
        $action->setTitle($thread->getTitle());
        $action->setUser($thread->getCreator()->getUid());
        $action->setPage($GLOBALS['TSFE']->id);

        return $action;
    }

    /**
     * @param NotifiableInterface $post
     * @param integer $type
     * @return AccessibleInterface $action
     */
    private function addPostAction($post, $type)
    {
        // @todo there is bug wthin the function for updates
        $action = new Action();
        $action->setType($type);
        $action->setTitle($post->getThread()->getTitle());
        $action->setThread($post->getThread()->getUid());
        $action->setUser($post->getCreator()->getUid());
        $action->setPage($GLOBALS['TSFE']->id);
        if ($type == NotificationService::NEW_QUOTED_POST) {
            $action->setPost($post->getQuotedPost()->getUid());
        } elseif ($type == NotificationService::UPDATE_POST) {
            $action->setPost($post->getOriginalPost()->getUid());
        } else {
            $action->setPost($post->getUid());
        }

        return $action;
    }

    /**
     * @param NotifiableInterface $other
     */
    private function addOtherAction($other)
    {
        $action = new Action();
        $action->setType($other->getType());
        $action->setTitle($other->getTitle());
        $action->setDescription($other->getDescription());
        $action->setData(json_encode($other->getData()));
        $action->setPage($GLOBALS['TSFE']->id);

        return $action;
    }
}
