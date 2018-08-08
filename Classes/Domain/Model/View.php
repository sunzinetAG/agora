<?php

namespace AgoraTeam\Agora\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class View
 * @package AgoraTeam\Agora\Domain\Model
 */
class View extends AbstractEntity
{

    /**
     * thread
     *
     * @var Thread
     */
    protected $thread = null;

    /**
     * user
     *
     * @var ObjectStorage<User>
     * @cascade remove
     */
    protected $user = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->user = new ObjectStorage();
    }

    /**
     * Returns the thread
     *
     * @return Thread $thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Sets the thread
     *
     * @param Thread $thread
     * @return void
     */
    public function setThread(Thread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Adds a User
     *
     * @param User $user
     * @return void
     */
    public function addUser(User $user)
    {
        $this->user->attach($user);
    }

    /**
     * Removes a User
     *
     * @param User $userToRemove The User to be removed
     * @return void
     */
    public function removeUser(User $userToRemove)
    {
        $this->user->detach($userToRemove);
    }

    /**
     * Returns the user
     *
     * @return ObjectStorage<User> $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user
     *
     * @param ObjectStorage<User> $user
     * @return void
     */
    public function setUser(ObjectStorage $user)
    {
        $this->user = $user;
    }

}
