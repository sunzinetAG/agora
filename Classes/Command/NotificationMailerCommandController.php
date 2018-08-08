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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\RequiredArgumentMissingException;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use AgoraTeam\Agora\Domain\Repository\UserRepository;
use AgoraTeam\Agora\Service\Notification\NotificationService;
use AgoraTeam\Agora\Domain\Repository\NotificationRepository;
use AgoraTeam\Agora\Service\MailService;

/**
 * Class NotificationMailerCommandController
 *
 * @package AgoraTeam\Agora\Command
 */
class NotificationMailerCommandController extends CommandController
{

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;
    /**
     * userRepository
     *
     * @var UserRepository
     * @inject
     */
    protected $userRepository = null;
    /**
     * userRepository
     *
     * @var NotificationRepository
     * @inject
     */
    protected $notificationRepository = null;
    /**
     * @var MailService
     * @inject
     */
    protected $mailService;
    /**
     * @var NotificationService
     * @inject
     */
    protected $notificationService;

    /**
     * @param Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * Assamble the commands and send them by mail
     *
     * @param string $userStorage StoragePid of the user datasets
     * @param string $start Earliest time to execute the sheduler task (Format hh:mm pm)
     * @param int $duration Period for the execution of the sheduler task in hours
     * @param int $amountOfUsersPerRun Amount of users to notificate per run
     * @return bool
     * @throws RequiredArgumentMissingException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function notificationCommand($userStorage, $start, $duration, $amountOfUsersPerRun = 50)
    {
        if ($this->checkExecutionTime($start, $duration)) {
            return true;
        }

        $configurationManager = GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager'
        );
        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        if (empty($userStorage) || is_null($userStorage)) {
            throw new RequiredArgumentMissingException(
                'Missing required argument userStorage',
                1518774162
            );
        }

        $usersIds = $this->notificationRepository->findUserListFromNotifcationsByLimit($amountOfUsersPerRun);
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
                    $this->mailService->sendMail(
                        [$user->getEmail() => $user->getLastName()],
                        [$settings['email']['defaultEmailAdress'] => $settings['email']['defaultEmailUserName']],
                        $settings['email']['notificationSubject'],
                        'Notification',
                        ['groupedNotifications' => $groupedNotifications, 'user' => $user]
                    );
                }
            }
            //  Even if the users email is not set, dump the notifications
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
