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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Forum
 * @package AgoraTeam\Agora\Domain\Model
 */
class Forum extends Entity implements AccessibleInterface
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * parent
     *
     * @var Forum
     * @lazy
     */
    protected $parent = null;

    /**
     * parent
     *
     * @var ObjectStorage<Forum>
     * @cascade remove
     * @lazy
     */
    protected $subForums = null;

    /**
     * threads
     *
     * @var ObjectStorage<Thread>
     * @cascade remove
     * @lazy
     */
    protected $threads = null;

    /**
     * groupsWithReadAccess
     *
     * @var ObjectStorage<Group>
     * @lazy
     */
    protected $groupsWithReadAccess = null;

    /**
     * groupsWithWriteAccess
     *
     * @var ObjectStorage<Group>
     * @lazy
     */
    protected $groupsWithWriteAccess = null;

    /**
     * groupsWithModificationAccess
     *
     * @var ObjectStorage<Group>
     * @lazy
     */
    protected $groupsWithModificationAccess = null;

    /**
     * usersWithReadAccess
     *
     * @var ObjectStorage<User>
     * @lazy
     */
    protected $usersWithReadAccess = null;

    /**
     * usersWithWriteAccess
     *
     * @var ObjectStorage<User>
     * @lazy
     */
    protected $usersWithWriteAccess = null;

    /**
     * usersWithModificationAccess
     *
     * @var ObjectStorage<User>
     * @lazy
     */
    protected $usersWithModificationAccess = null;

    /**
     * rootline
     *
     * @var array
     */
    protected $rootline = array();

    /**
     * __construct
     */
    public function __construct()
    {
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
        $this->subForums = new ObjectStorage();
        $this->threads = new ObjectStorage();
        $this->groupsWithReadAccess = new ObjectStorage();
        $this->groupsWithWriteAccess = new ObjectStorage();
        $this->groupsWithModificationAccess = new ObjectStorage();
        $this->usersWithReadAccess = new ObjectStorage();
        $this->usersWithWriteAccess = new ObjectStorage();
        $this->usersWithModificationAccess = new ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Adds a SubForum
     *
     * @param Forum $subForum
     * @return void
     */
    public function addSubForum(Forum $subForum)
    {
        $this->subForums->attach($subForum);
    }

    /**
     * Removes a Forum
     *
     * @param Forum $subForumToRemove The SubForum to be removed
     * @return void
     */
    public function removeSubForum(Forum $subForumToRemove)
    {
        $this->subForums->detach($subForumToRemove);
    }

    /**
     * Returns the subForums
     *
     * @return ObjectStorage<Forum> $parent
     */
    public function getSubForums()
    {
        return $this->subForums;
    }

    /**
     * Sets the subForums
     *
     * @param ObjectStorage<Forum> $subForums
     * @return void
     */
    public function setSubForums(ObjectStorage $subForums)
    {
        $this->subForums = $subForums;
    }

    /**
     * Adds a Thread
     *
     * @param Thread $thread
     * @return void
     */
    public function addThread(Thread $thread)
    {
        $this->threads->attach($thread);
    }

    /**
     * Removes a Thread
     *
     * @param Thread $threadToRemove The Thread to be removed
     * @return void
     */
    public function removeThread(Thread $threadToRemove)
    {
        $this->threads->detach($threadToRemove);
    }

    /**
     * Returns the threads
     *
     * @return ObjectStorage<Thread> $threads
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * Sets the threads
     *
     * @param ObjectStorage<Thread> $threads
     * @return void
     */
    public function setThreads(ObjectStorage $threads)
    {
        $this->threads = $threads;
    }

    /**
     * Returns the latest thread
     *
     * @return \boolean|Thread $latestThread
     */
    public function getLatestThread()
    {
        $latestThread = false;
        if ($this->threads->count()) {
            $threads = $this->threads->toArray();
            $latestThread = $threads[$this->threads->count() - 1];
        }
        return $latestThread;
    }

    /**
     * Adds a Group
     *
     * @param Group $groupsWithReadAccess
     * @return void
     */
    public function addGroupsWithReadAccess(Group $groupsWithReadAccess)
    {
        $this->groupsWithReadAccess->attach($groupsWithReadAccess);
    }

    /**
     * Removes the Group
     *
     * @param Group $groupsWithReadAccessToRemove The Group to be removed
     * @return void
     */
    public function removeGroupsWithReadAccess(Group $groupsWithReadAccessToRemove)
    {
        $this->groupsWithReadAccess->detach($groupsWithReadAccessToRemove);
    }

    /**
     * Adds the Group
     *
     * @param Group $groupWithWriteAccess
     * @return void
     */
    public function addGroupWithWriteAccess(Group $groupWithWriteAccess)
    {
        $this->groupsWithWriteAccess->attach($groupWithWriteAccess);
    }

    /**
     * Removes the groupsWithWriteAccess
     *
     * @param Group $groupWithWriteAccessToRemove The Group to be removed
     * @return void
     */
    public function removeGroupWithWriteAccess(Group $groupWithWriteAccessToRemove)
    {
        $this->groupsWithWriteAccess->detach($groupWithWriteAccessToRemove);
    }

    /**
     * Adds a Group
     *
     * @param Group $groupWithModificationAccess
     * @return void
     */
    public function addGroupWithModificationAccess(Group $groupWithModificationAccess)
    {
        $this->groupsWithModificationAccess->attach($groupWithModificationAccess);
    }

    /**
     * Removes a Group
     *
     * @param Group $groupWithModificationAccessToRemove The Group to be removed
     * @return void
     */
    public function removeGroupWithModificationAccess(
        Group $groupWithModificationAccessToRemove
    ) {
        $this->groupsWithModificationAccess->detach($groupWithModificationAccessToRemove);
    }

    /**
     * Adds a User
     *
     * @param User $userWithReadAccess
     * @return void
     */
    public function addUserWithReadAccess(User $userWithReadAccess)
    {
        $this->usersWithReadAccess->attach($userWithReadAccess);
    }

    /**
     * Removes a User
     *
     * @param User $userWithReadAccessToRemove The User to be removed
     * @return void
     */
    public function removeUserWithReadAccess(User $userWithReadAccessToRemove)
    {
        $this->usersWithReadAccess->detach($userWithReadAccessToRemove);
    }

    /**
     * Adds a User
     *
     * @param User $userWithWriteAccess
     * @return void
     */
    public function addUserWithWriteAccess(User $userWithWriteAccess)
    {
        $this->usersWithWriteAccess->attach($userWithWriteAccess);
    }

    /**
     * Removes a User
     *
     * @param User $userWithWriteAccessToRemove The User to be removed
     * @return void
     */
    public function removeUserWithWriteAccess(User $userWithWriteAccessToRemove)
    {
        $this->usersWithWriteAccess->detach($userWithWriteAccessToRemove);
    }

    /**
     * Adds a User
     *
     * @param User $userWithModificationAccess
     * @return void
     */
    public function addUserWithModificationAccess(User $userWithModificationAccess)
    {
        $this->usersWithModificationAccess->attach($userWithModificationAccess);
    }

    /**
     * Removes a User
     *
     * @param User $userWithModificationAccessToRemove The User to be removed
     * @return void
     */
    public function removeUserWithModificationAccess(
        User $userWithModificationAccessToRemove
    ) {
        $this->usersWithModificationAccess->detach($userWithModificationAccessToRemove);
    }

    /**
     * Returns the boolean state of the modify protected flag
     *
     * @return boolean
     */
    public function isModifyProtected()
    {
        return $this->getModifyProtected();
    }

    /**
     * Returns the modify protected flag
     *
     * @return boolean $modifyProtected
     */
    public function getModifyProtected()
    {
        $modifyProtected = false;
        if ($this->getUsersWithModificationAccess()->count() > 0) {
            $modifyProtected = true;
        }
        if ($this->getGroupsWithModificationAccess()->count() > 0) {
            $modifyProtected = true;
        }

        return $modifyProtected;
    }

    /**
     * Returns the usersWithModificationAccess
     *
     * @return ObjectStorage<User> $usersWithModificationAccess
     */
    public function getUsersWithModificationAccess()
    {
        return $this->usersWithModificationAccess;
    }

    /**
     * Sets the usersWithModificationAccess
     *
     * @param ObjectStorage<User> $usersWithModificationAccess
     * @return void
     */
    public function setUsersWithModificationAccess(
        ObjectStorage $usersWithModificationAccess
    ) {
        $this->usersWithModificationAccess = $usersWithModificationAccess;
    }

    /**
     * Returns the groupsWithModificationAccess
     *
     * @return ObjectStorage<Group> $groupsWithModificationAccess
     */
    public function getGroupsWithModificationAccess()
    {
        return $this->groupsWithModificationAccess;
    }

    /**
     * Sets the groupsWithModificationAccess
     *
     * @param ObjectStorage<Group> $groupsWithModificationAccess
     * @return void
     */
    public function setGroupsWithModificationAccess(
        ObjectStorage $groupsWithModificationAccess
    ) {
        $this->groupsWithModificationAccess = $groupsWithModificationAccess;
    }

    /**
     * Returns the rootline
     *
     * @return array
     */
    public function getRootline()
    {
        if (empty($this->rootline)) {
            $this->fetchNextRootlineLevel();
        }

        return $this->rootline;
    }

    /**
     * fetches next rootline level recursively
     *
     * @return void
     */
    public function fetchNextRootlineLevel()
    {

        if (empty($this->rootline)) {
            if (is_object($this->getParent())) {
                array_push($this->rootline, current($this->getParent()->getRootline()));
                array_push($this->rootline, $this);
            } else {
                array_push($this->rootline, $this);
            }
        }
    }

    /**
     * Returns the parent
     *
     * @return Forum $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent
     *
     * @param Forum $parent
     * @return void
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param User|null $user
     * @param string $accessType
     * @return bool
     */
    public function checkAccess(User $user = null, $accessType = self::TYPE_READ)
    {
        switch ($accessType) {
            case self::TYPE_EDIT_POST:
            case self::TYPE_NEW_POST:
            case self::TYPE_WRITE:
                return $this->isWritableForUser($user);
            default:
                return $this->isAccessibleForUser($user);
        }
    }

    /**
     * checks if the forum is writable for the given user
     *
     * @param mixed $user
     * @return bool
     */
    public function isWritableForUser($user)
    {
        $isWritable = false;

        if ($this->isWriteProtected()) {
            if (is_a($user, '\AgoraTeam\Agora\Domain\Model\User')) {
                if ($this->getUsersWithWriteAccess()->count() > 0) {
                    foreach ($this->getUsersWithWriteAccess() as $currentUser) {
                        if ($user->getUid() == $currentUser->getUid()) {
                            $isWritable = true;
                            break;
                        }
                    }
                }
                // the comparision on group level is expensive, so check and double-check if this is really necessary
                if ($isWritable !== true) {
                    if ($this->getGroupsWithWriteAccess()->count() > 0) {
                        foreach ($this->getGroupsWithWriteAccess() as $groupWithAccess) {
                            foreach ($user->getFlattenedGroups() as $group) {
                                if ($groupWithAccess->getUid() == $group->getUid()) {
                                    $isWritable = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $isWritable = true;
        }

        return $isWritable;
    }

    /**
     * Returns the boolean state of the write protected flag
     *
     * @return boolean
     */
    public function isWriteProtected()
    {
        return $this->getWriteProtected();
    }

    /**
     * Returns the write protected flag
     *
     * @return boolean $writeProtected
     */
    public function getWriteProtected()
    {
        $writeProtected = false;
        if ($this->getUsersWithWriteAccess()->count() > 0) {
            $writeProtected = true;
        }
        if ($this->getGroupsWithWriteAccess()->count() > 0) {
            $writeProtected = true;
        }

        return $writeProtected;
    }

    /**
     * Returns the usersWithWriteAccess
     *
     * @return ObjectStorage<User> $usersWithWriteAccess
     */
    public function getUsersWithWriteAccess()
    {
        return $this->usersWithWriteAccess;
    }

    /**
     * Sets the usersWithWriteAccess
     *
     * @param ObjectStorage<User> $usersWithWriteAccess
     * @return void
     */
    public function setUsersWithWriteAccess(ObjectStorage $usersWithWriteAccess)
    {
        $this->usersWithWriteAccess = $usersWithWriteAccess;
    }

    /**
     * Returns the groupsWithWriteAccess
     *
     * @return ObjectStorage<Group> $groupsWithWriteAccess
     */
    public function getGroupsWithWriteAccess()
    {
        return $this->groupsWithWriteAccess;
    }

    /**
     * Sets the groupsWithWriteAccess
     *
     * @param ObjectStorage<Group> $groupsWithWriteAccess
     * @return void
     */
    public function setGroupsWithWriteAccess(ObjectStorage $groupsWithWriteAccess)
    {
        $this->groupsWithWriteAccess = $groupsWithWriteAccess;
    }

    /**
     * checks if the forum is accessible for the given user
     *
     * @param mixed $user
     * @return bool
     */
    public function isAccessibleForUser($user)
    {
        $isAccessible = false;

        if ($this->isReadProtected()) {
            if (is_a($user, '\AgoraTeam\Agora\Domain\Model\User')) {
                if ($this->getUsersWithReadAccess()->count() > 0) {
                    foreach ($this->getUsersWithReadAccess() as $currentUser) {
                        if ($user->getUid() == $currentUser->getUid()) {
                            $isAccessible = true;
                            break;
                        }
                    }
                }
                // the comparision on group level is expensive, so check and double-check if this is really necessary
                if ($isAccessible != true) {
                    if ($this->getGroupsWithReadAccess()->count() > 0) {
                        foreach ($this->getGroupsWithReadAccess() as $groupWithAccess) {
                            foreach ($user->getFlattenedGroups() as $group) {
                                if ($groupWithAccess->getUid() == $group->getUid()) {
                                    $isAccessible = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $isAccessible = true;
        }

        return $isAccessible;
    }

    /**
     * Returns the boolean state of the read protected flag
     *
     * @return boolean
     */
    public function isReadProtected()
    {
        return $this->getReadProtected();
    }

    /**
     * Returns the read protected flag
     *
     * @return boolean $readProtected
     */
    public function getReadProtected()
    {
        $readProtected = false;
        if ($this->getUsersWithReadAccess()->count() > 0) {
            $readProtected = true;
        }
        if ($this->getGroupsWithReadAccess()->count() > 0) {
            $readProtected = true;
        }

        return $readProtected;
    }

    /**
     * Returns the usersWithReadAccess
     *
     * @return ObjectStorage<User> $usersWithReadAccess
     */
    public function getUsersWithReadAccess()
    {
        return $this->usersWithReadAccess;
    }

    /**
     * Sets the usersWithReadAccess
     *
     * @param ObjectStorage<User> $usersWithReadAccess
     * @return void
     */
    public function setUsersWithReadAccess(ObjectStorage $usersWithReadAccess)
    {
        $this->usersWithReadAccess = $usersWithReadAccess;
    }

    /**
     * Returns the groupsWithReadAccess
     *
     * @return ObjectStorage<Group> $groupsWithReadAccess
     */
    public function getGroupsWithReadAccess()
    {
        return $this->groupsWithReadAccess;
    }

    /**
     * Sets the groupsWithReadAccess
     *
     * @param ObjectStorage<Group> $groupsWithReadAccess
     * @return void
     */
    public function setGroupsWithReadAccess(ObjectStorage $groupsWithReadAccess)
    {
        $this->groupsWithReadAccess = $groupsWithReadAccess;
    }
}
