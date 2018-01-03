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
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ActionController
 */
abstract class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * persistenceManager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * userRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * @var \AgoraTeam\Agora\Service\Authentication\AuthenticationService
     * @inject
     */
    protected $authenticationService;

    /**
     * @return array
     */
    protected function getPostsDefaultSender()
    {
        return array(
            $this->settings['post']['defaultPostEmailAdress']
            => $this->settings['post']['defaultPostEmailUserName']
        );
    }

    /**
     * @return array
     */
    protected function getThreadDefaultSender()
    {
        return array(
            $this->settings['thread']['defaultThreadEmailAdress']
            => $this->settings['thread']['defaultThreadEmailUserName']
        );
    }

    /**
     * @param $key
     * @param array $arguments
     * @param null $titleKey
     * @param int $severity
     */
    protected function addLocalizedFlashmessage(
        $key,
        array $arguments = [],
        $titleKey = null,
        $severity = FlashMessage::OK
    ) {
        $this->addFlashMessage(
            LocalizationUtility::translate($key, 'agora', $arguments),
            LocalizationUtility::translate($titleKey, 'agora'),
            $severity
        );
    }
}
