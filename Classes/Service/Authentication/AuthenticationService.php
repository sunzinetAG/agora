<?php

namespace AgoraTeam\Agora\Service\Authentication;

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
use AgoraTeam\Agora\Domain\Model\AccessibleInterface;
use AgoraTeam\Agora\Domain\Model\Forum;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Thread;
use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class AuthenticationService
 *
 * @package AgoraTeam\Agora\Service\Authentication
 */
class AuthenticationService implements SingletonInterface
{

    /**
     * @var int
     */
    protected $user = -1;

    /**
     * userRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * @param AccessibleInterface $cObject
     */
    public function assertReadAuthorization(AccessibleInterface $cObject)
    {
        if ($cObject->checkAccess($this->getUser(), AccessibleInterface::TYPE_READ) === false) {
            throw new PageNotFoundException('NOPE ' . __FUNCTION__);
        }
    }

    /**
     * @param Thread $thread
     */
    public function assertNewThreadAuthorization(Forum $forum)
    {
        $this->assertReadAuthorization($forum);
        if ($forum->checkAccess($this->getUser(), AccessibleInterface::TYPE_WRITE) === false) {
            throw new PageNotFoundException('NOPE ' . __FUNCTION__);
        }
    }

    /**
     * @param Thread $thread
     */
    public function assertNewPostAuthorization(Thread $thread)
    {
        $this->assertReadAuthorization($thread);
        if ($thread->checkAccess($this->getUser(), AccessibleInterface::TYPE_NEW_POST) === false) {
            throw new PageNotFoundException('NOPE ' . __FUNCTION__);
        }
    }

    /**
     * @param Post $post
     */
    public function assertEditPostAuthorization(Post $post)
    {
        $this->assertReadAuthorization($post);
        if ($post->checkAccess($this->getUser(), AccessibleInterface::TYPE_EDIT_POST) === false) {
            throw new PageNotFoundException('NOPE ' . __FUNCTION__);
        }
    }

    /**
     * @param Post $post
     */
    public function assertDeletePostAuthorization(Post $post)
    {
        $this->assertReadAuthorization($post);
        if ($post->checkAccess($this->getUser(), AccessibleInterface::TYPE_DELETE_POST) === false) {
            throw new PageNotFoundException('NOPE ' . __FUNCTION__);
        }
    }

    /**
     * @return int|object
     */
    public function getUser()
    {
        if (!is_a($this->user, '\AgoraTeam\Agora\Domain\Model\User')) {
            $this->user = $this->userRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        }

        return $this->user;
    }

    /**
     * Get Usergroups from current user
     *
     * @return array
     */
    public function getCurrentUsergroupUids()
    {
        $currentUser = $this->getUser();
        $usergroupUids = array();
        if ($currentUser !== null) {
            foreach ($currentUser->getUsergroup() as $usergroup) {
                $usergroupUids[] = $usergroup->getUid();
            }
        }

        return $usergroupUids;
    }
}
