<?php

namespace AgoraTeam\Agora\Controller;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Dinis Alexandru Catalin <dinisalexandrucatalin@gmail.com>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use AgoraTeam\Agora\Domain\Model\Forum;
use AgoraTeam\Agora\Domain\Repository\ForumRepository;
use AgoraTeam\Agora\Domain\Repository\GroupRepository;

/**
 * Class ForumAdminController
 *
 * @package AgoraTeam\Agora\Controller
 */
class ForumAdminController extends ActionController
{

    /**
     * ForumRepository
     *
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository = null;

    /**
     * GroupRepository
     *
     * @var GroupRepository
     * @inject
     */
    protected $groupRepository = null;

    /**
     * Keeps the selected page id
     *
     * @var $id
     */
    protected $id;

    /**
     * Action list
     *
     * @return void
     */
    public function listAction()
    {
        $forums = $this->forumRepository->findByPid($this->id);
        $this->view->assign('forums', $forums);
    }

    /**
     * Action show
     *
     * @param Forum $forum
     * @return void
     */
    public function showAction(Forum $forum)
    {
        $this->view->assign('forum', $forum);
    }

    /**
     * Action new
     *
     * @param Forum $newForum
     * @return void
     */
    public function newAction(Forum $newForum = null)
    {
        $users = $this->userRepository->findAll();
        $groups = $this->groupRepository->findAll();
        $forums = $this->forumRepository->findAll();
        $this->view->assignMultiple(
            array(
                'newForum' => $newForum,
                'users' => $users,
                'groups' => $groups,
                'forums' => $forums
            )
        );
    }

    /**
     * Action create
     *
     * @param Forum $newForum
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function createAction(Forum $newForum)
    {
        $this->forumRepository->add($newForum);
        $this->addFlashMessage('The form was succesfully created!!');
        $this->redirect('list');
    }

    /**
     * Action edit
     *
     * @param Forum $forum
     * @ignorevalidation $forum
     * @return void
     */
    public function editAction(Forum $forum)
    {
        $users = $this->userRepository->findAll();
        $groups = $this->groupRepository->findAll();
        $forums = $this->forumRepository->findForumsWithDifferentId($forum);
        $this->view->assignMultiple(
            array(
                'forum' => $forum,
                'users' => $users,
                'groups' => $groups,
                'forums' => $forums
            )
        );
    }

    /**
     * Action update
     *
     * @param Forum $forum
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @return void
     */
    public function updateAction(Forum $forum)
    {
        $this->forumRepository->update($forum);
        $this->addFlashMessage('The forum was succesfully edited!!');
        $this->redirect('list');
    }

    /**
     *  Action delete
     *
     * @param Forum $forum
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function deleteAction(Forum $forum)
    {

        if (($forum->getThreads()->count() == 0) && ($forum->getSubForums()->count() == 0)) {
            $this->forumRepository->remove($forum);
            $this->redirect('list');
        } else {
            $this->addFlashMessage(
                'You cannot delete the forum beacause it has subforums or threads!',
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('list');
        }
    }

    /**
     * Function initializeAction
     *
     * @return void
     */
    protected function initializeAction()
    {
        $this->id = (int)GeneralUtility::_GP('id');
    }
}
