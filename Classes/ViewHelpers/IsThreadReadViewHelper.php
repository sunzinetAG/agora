<?php

namespace AgoraTeam\Agora\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Björn Christopher Bresser <bjoern.bresser@gmail.com>
 *           Fabian Staake <fabian.staake@sunzinet.com>
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

/**
 * Class IsThreadReadViewHelper
 * Checks rather a thread is read or not
 *
 * @package AgoraTeam\Agora\ViewHelpers
 */
class IsThreadReadViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Authentication service
     *
     * @var \AgoraTeam\Agora\Service\Authentication\AuthenticationService
     * @inject
     */
    protected $authenticationService;

    /**
     * Render the viewhelper
     *
     * @param \AgoraTeam\Agora\Domain\Model\Thread $thread
     * @param mixed $user
     * @param int $checkForLatestUpdate
     *
     * @return $content the rendered content
     */
    public function render(\AgoraTeam\Agora\Domain\Model\Thread $thread)
    {
        $content = '';

        if ($thread->hasBeenReadByFrontendUser($this->authenticationService->getUser())) {
            $content = $this->renderChildren();
        }

        return $content;
    }
}
