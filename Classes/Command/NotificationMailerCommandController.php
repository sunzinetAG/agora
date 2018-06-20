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
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Mvc\Exception\RequiredArgumentMissingException;

/**
 * Class NotificationMailerCommandController
 *
 * @package AgoraTeam\Agora\Command
 */
class NotificationMailerCommandController extends CommandController
{

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @param \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * userRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository = null;

    /**
     * userRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\NotificationRepository
     * @inject
     */
    protected $notificationRepository = null;

    /**
     * @var \AgoraTeam\Agora\Service\MailService
     * @inject
     */
    protected $mailService;

    /**
     * @var \AgoraTeam\Agora\Service\Notification\NotificationService
     * @inject
     */
    protected $notificationService;

    /**
     * Assamble the commands and send them by mail
     *
     * @param string $userStorage StoragePid of the user datasets
     * @param string $start Earliest time to execute the sheduler task (Format hh:mm pm)
     * @param integer $duration Period for the execution of the sheduler task in hours
     * @param integer $amounfOfUsersPerRun Amout of users to notificate per run
     */
    public function notificationCommand($userStorage, $start, $duration, $amounfOfUsersPerRun = 50)
    {
        if ($this->checkExecutionTime($start, $duration)) {
            return true;
        } else {
            return true;
        }
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager'
        );
        $settings = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        if (empty($userStorage) || is_null($userStorage)) {
            throw new RequiredArgumentMissingException(
                'Missing required argument userStorage',
                1518774162
            );
        }

        $usersIds = $this->notificationRepository->findUserListFromNotifcationsByLimit($amounfOfUsersPerRun);
        foreach ($usersIds as $key => $val) {
            $user = $this->userRepository->findByUid($val);
            if ($user->getEmail()) {
                $userNotifications = $this->notificationService->getNotificationsByUser($user);
                $this->signalSlotDispatcher->dispatch(
                    __CLASS__,
                    'afterGettingUserNotifications',
                    [$user, &$userNotifications]
                );
                if (!empty($userNotifications)) {
                    $groupedNotifications = $this->notificationService->groupNotificationsByType($userNotifications);

                    $mailSent = $this->mailService->sendMail(
                        [$user->getEmail() => $user->getLastName()],
                        [$settings['email']['defaultEmailAdress'] => $settings['email']['defaultEmailUserName']],
                        $settings['email']['notificationSubject'],
                        'Notification',
                        ['groupedNotifications' => $groupedNotifications, 'user' => $user]
                    );
                }
            }
            // Even if the users email is not set, dump the notifications
            $this->notificationService->markUserNotificationsAsSent($user);
        }

        return true;
    }

    /**
     * @param $start
     * @param $duration
     * @return bool
     */
    public function checkExecutionTime($start, $duration)
    {
        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime("+ $duration hours", $startTimestamp);

        if (time() > $startTimestamp && time() < $endTimestamp) {
            return false;
        } else {
            return true;
        }
    }
}
