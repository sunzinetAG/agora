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
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Notification;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Domain\Repository\NotificationRepository;
use AgoraTeam\Agora\Service\MailService;
use AgoraTeam\Agora\Service\NotificationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ActionController
 */
class NotificationCommandController extends CommandController
{

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
     * @var \AgoraTeam\Agora\Service\MailService
     */
    protected $mailService;

    /**
     * The settings.
     *
     * @var array
     */
    protected $settings = array();

    /**
     * NotificationCommandController constructor.
     */
    protected function __construct()
    {
        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
        // get settings
        $this->settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'agora'
        );
    }

    /**
     * Convert actions to notifications to save performance
     */
    public function convertCommand()
    {
    }

    /**
     * MailCommandController
     *      This task will send notification emails to the users
     *
     * @param int $storagePid
     * @param int $taskLimit
     * @return bool
     * @throws \Exception
     */
    public function mailCommand($storagePid, $taskLimit)
    {
    }
}
