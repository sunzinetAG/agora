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

use AgoraTeam\Agora\Domain\Repository\ForumRepository;

/**
 * Class ForumController
 * @package AgoraTeam\Agora\Controller
 */
class ForumController extends ActionController
{

    /**
     * ForumRepository
     *
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository = null;

    /**
     * Action list
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @return void
     */
    public function listAction()
    {
        $forums = $this->forumRepository->findVisibleRootForums();
        $this->view->assignMultiple(
            array(
                'forums' => $forums,
                'user' => $this->authenticationService->getUser()
            )
        );
    }
}
