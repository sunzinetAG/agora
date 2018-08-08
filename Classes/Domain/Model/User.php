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
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * Class User
 * @package AgoraTeam\Agora\Domain\Model
 */
class User extends AbstractEntity
{

    /**
     * signiture
     *
     * @var string
     * @var string
     */
    protected $signiture = '';

    /**
     * favoritePosts
     *
     * @var ObjectStorage<Post>
     */
    protected $favoritePosts = null;

    /**
     * observedThreads
     *
     * @var ObjectStorage<Thread>
     * @lazy
     */
    protected $readThreads = null;

    /**
     * observedThreads
     *
     * @var ObjectStorage<Thread>
     * @lazy
     */
    protected $observedThreads = null;

    /**
     * spamPosts
     *
     * @var ObjectStorage<Post>
     */
    protected $spamPosts = null;

    /**
     * groups
     *
     * @var ObjectStorage<Group>
     * @lazy
     */
    protected $groups = null;

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $firstName = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var FileReference
     */
    protected $image;

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
        $this->favoritePosts = new ObjectStorage();
        $this->readThreads = new ObjectStorage();
        $this->observedThreads = new ObjectStorage();
        $this->spamPosts = new ObjectStorage();
        $this->groups = new ObjectStorage();
    }

    /**
     * Returns the signiture
     *
     * @return string $signiture
     */
    public function getSigniture()
    {
        return $this->signiture;
    }

    /**
     * Sets the signiture
     *
     * @param string $signiture
     * @return void
     */
    public function setSigniture($signiture)
    {
        $this->signiture = $signiture;
    }


    /**
     * Adds a Thread
     *
     * @param Post $favoritePost
     * @return void
     */
    public function addFavoritePost(Post $favoritePost)
    {
        $this->favoritePosts->attach($favoritePost);
    }

    /**
     * Removes a Thread
     *
     * @param Post $favoritePostToRemove The Thread to be removed
     * @return void
     */
    public function removeFavoritePost(Post $favoritePostToRemove)
    {
        $this->favoritePosts->detach($favoritePostToRemove);
    }

    /**
     * Returns the favoritePosts
     *
     * @return ObjectStorage<Post> $favoritePosts
     */
    public function getFavoritePosts()
    {
        return $this->favoritePosts;
    }

    /**
     * Sets the favoritePosts
     *
     * @param ObjectStorage<Post> $favoritePosts
     * @return void
     */
    public function setFavoritePosts(ObjectStorage $favoritePosts)
    {
        $this->favoritePosts = $favoritePosts;
    }

    /**
     * Adds a Thread
     *
     * @param Thread $thread
     * @return void
     */
    public function addReadThread(Thread $thread)
    {
        $this->readThreads->attach($thread);
    }

    /**
     * Adds a Thread
     *
     * @param Thread $observedThread
     * @return void
     */
    public function addObservedThread(Thread $observedThread)
    {
        $this->observedThreads->attach($observedThread);
    }

    /**
     * Removes a Thread
     *
     * @param Thread $observedThreadToRemove The Thread to be removed
     * @return void
     */
    public function removeObservedThread(Thread $observedThreadToRemove)
    {
        $this->observedThreads->detach($observedThreadToRemove);
    }

    /**
     * Returns the observedThreads
     *
     * @return ObjectStorage<Thread> $observedThreads
     */
    public function getObservedThreads()
    {
        return $this->observedThreads;
    }

    /**
     * Sets the observedThreads
     *
     * @param ObjectStorage<Thread> $observedThreads
     * @return void
     */
    public function setObservedThreads(ObjectStorage $observedThreads)
    {
        $this->observedThreads = $observedThreads;
    }

    /**
     * Adds a Post
     *
     * @param Post $spamPost
     * @return void
     */
    public function addSpamPost(Post $spamPost)
    {
        $this->spamPosts->attach($spamPost);
    }

    /**
     * Removes a Post
     *
     * @param Post $spamPostToRemove The Post to be removed
     * @return void
     */
    public function removeSpamPost(Post $spamPostToRemove)
    {
        $this->spamPosts->detach($spamPostToRemove);
    }

    /**
     * Returns the spamPosts
     *
     * @return ObjectStorage<Post> $spamPosts
     */
    public function getSpamPosts()
    {
        return $this->spamPosts;
    }

    /**
     * Sets the spamPosts
     *
     * @param ObjectStorage<Post> $spamPosts
     * @return void
     */
    public function setSpamPosts(ObjectStorage $spamPosts)
    {
        $this->spamPosts = $spamPosts;
    }

    /**
     * Adds a Group
     *
     * @param Group $group
     * @return void
     */
    public function addGroup(Group $group)
    {
        $this->groups->attach($group);
    }

    /**
     * Removes a Group
     *
     * @param Group $groupToRemove The Group to be removed
     * @return void
     */
    public function removeGroup(Group $groupToRemove)
    {
        $this->groups->detach($groupToRemove);
    }

    /**
     * Returns the flattened groups
     *
     * @return array $groups
     */
    public function getFlattenedGroupUids()
    {
        $flattenedGroupUids = array();
        foreach ($this->getFlattenedGroups() as $group) {
            $flattenedGroupUids[] = (int)$group->getUid();
        }

        return $flattenedGroupUids;
    }

    /**
     * Returns the flattened groups
     *
     * @return array $groups
     */
    public function getFlattenedGroups()
    {
        $flattenedGroups = array();
        foreach ($this->getGroups() as $group) {
            $flattenedGroups[(string)$group] = $group;
            $flattenedGroups = array_merge($flattenedGroups, $group->getFlattenedSubgroups());
        }

        return $flattenedGroups;
    }

    /**
     * Returns the groups
     *
     * @return ObjectStorage<Group> $groups
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Sets the groups
     *
     * @param ObjectStorage<Group> $groups
     * @return void
     */
    public function setGroups(ObjectStorage $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        $displayNameParts = array();

        if ($this->getFirstName()) {
            $displayNameParts[] = $this->getFirstName();
        }
        if ($this->getLastName()) {
            $displayNameParts[] = $this->getLastName();
        }
        if (count($displayNameParts) > 0) {
            $displayName = implode(' ', $displayNameParts);
            //$displayName .= ' ('.$this->getUsername().')';
        } else {
            $displayName = $this->getUsername();
        }

        return $displayName;
    }

    /**
     * Returns the firstName value
     *
     * @return string
     * @api
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the firstName value
     *
     * @param string $firstName
     * @return void
     * @api
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Returns the lastName value
     *
     * @return string
     * @api
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the lastName value
     *
     * @param string $lastName
     * @return void
     * @api
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
}
