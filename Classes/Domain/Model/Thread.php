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
 * Class Thread
 *
 * @package AgoraTeam\Agora\Domain\Model
 */
class Thread extends Entity implements AccessibleInterface, NotifiableInterface
{

    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * solved
     *
     * @var boolean
     */
    protected $solved = false;

    /**
     * closed
     *
     * @var boolean
     */
    protected $closed = false;

    /**
     * sticky
     *
     * @var boolean
     */
    protected $sticky = false;

    /**
     * creator
     * may be NULL if post is anonymous
     *
     * @var \AgoraTeam\Agora\Domain\Model\User
     */
    protected $creator = null;

    /**
     * posts
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\Post>
     * @cascade remove
     * @lazy
     */
    protected $posts = null;

    /**
     * posts
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\Tag>
     * @cascade remove
     * @lazy
     */
    protected $tags = null;

    /**
     * views
     *
     * @var integer
     */
    protected $views = null;

    /**
     * forum
     *
     * @var \AgoraTeam\Agora\Domain\Model\Forum
     */
    protected $forum;

    /**
     * observers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\User>
     */
    protected $observers = null;

    /**
     * readers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\User>
     */
    protected $readers = null;

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
        $this->posts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->user = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->observers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->tags = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->readers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Returns the solved
     *
     * @return boolean $solved
     */
    public function getSolved()
    {
        return $this->solved;
    }

    /**
     * Sets the solved
     *
     * @param boolean $solved
     * @return void
     */
    public function setSolved($solved)
    {
        $this->solved = $solved;
    }

    /**
     * Returns the boolean state of solved
     *
     * @return boolean
     */
    public function isSolved()
    {
        return $this->solved;
    }

    /**
     * Returns the closed
     *
     * @return boolean $closed
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Sets the closed
     *
     * @param boolean $closed
     * @return void
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * Returns the boolean state of closed
     *
     * @return boolean
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * Returns the sticky
     *
     * @return boolean $sticky
     */
    public function getSticky()
    {
        return $this->sticky;
    }

    /**
     * Sets the sticky
     *
     * @param boolean $sticky
     * @return void
     */
    public function setSticky($sticky)
    {
        $this->sticky = $sticky;
    }

    /**
     * Returns the boolean state of sticky
     *
     * @return boolean
     */
    public function isSticky()
    {
        return $this->sticky;
    }

    /**
     * Returns the creator
     *
     * @return mixed $creator
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Sets the creator
     *
     * @param mixed $creator
     * @return void
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * Adds an observer
     *
     * @param \AgoraTeam\Agora\Domain\Model\User $observer
     */
    public function addObserver(\AgoraTeam\Agora\Domain\Model\User $observer)
    {
        $this->observers->attach($observer);
    }

    /**
     * Removes an observer
     *
     * @param \AgoraTeam\Agora\Domain\Model\User $observerToRemove $observerToRemove The observer to be removed
     * @return void
     */
    public function removeObserver(\AgoraTeam\Agora\Domain\Model\User $observerToRemove)
    {
        $this->posts->detach($observerToRemove);
    }

    /**
     * Returns the observers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\User> $observers
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Sets the observers
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\User> $observers
     * @return void
     */
    public function setObservers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $observers)
    {
        $this->observers = $observers;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasBeenReadByFrontendUser(User $user = null)
    {
        return $user ? $this->readers->contains($user) : true;
    }

    /**
     * Add a reader
     *
     * @param User $user
     * @return void
     */
    public function addReader(User $user)
    {
        $this->readers->attach($user);
    }

    /**
     * Remove all readers to mark the thread as unread
     *
     * @return void
     */
    public function removeReaders()
    {
        $this->readers = new ObjectStorage();
    }

    /**
     * Adds a Post
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $post
     * @return void
     */
    public function addPost(\AgoraTeam\Agora\Domain\Model\Post $post)
    {
        $this->posts->attach($post);
        $this->removeReaders();
    }

    /**
     * Removes a Post
     *
     * @param \AgoraTeam\Agora\Domain\Model\Post $postToRemove The Post to be removed
     * @return void
     */
    public function removePost(\AgoraTeam\Agora\Domain\Model\Post $postToRemove)
    {
        $this->posts->detach($postToRemove);
    }

    /**
     * Returns the posts
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\Post> $posts
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Sets the posts
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgoraTeam\Agora\Domain\Model\Post> $posts
     * @return void
     */
    public function setPosts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return \AgoraTeam\Agora\Domain\Model\Post $post
     */
    public function getFirstPost()
    {
        $post = $this->getPosts()->current();

        return $post;
    }

    /**
     * Returns the latest post
     *
     * @return \boolean|\AgoraTeam\Agora\Domain\Model\Post $latestPost
     */
    public function getLatestPost()
    {
        $latestPost = false;
        $posts = $this->getPosts()->toArray();
        if ($posts) {
            $latestPost = end($posts);
        }

        return $latestPost;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getTags(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->tags;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $tags
     */
    public function setTags(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getTaglist()
    {
        $tagArr = $this->tags->toArray();
        foreach ($tagArr as $tag) {
            $resultArr[] = $tag->getTitle();
        }
        $tagList = implode(',', $resultArr);

        return $tagList;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function increaseViews()
    {
        $this->views = $this->getViews() + 1;
    }

    /**
     * Returns the forum
     *
     * @return \AgoraTeam\Agora\Domain\Model\Forum $forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Sets the forum
     *
     * @param \AgoraTeam\Agora\Domain\Model\Forum $forum
     * @return void
     */
    public function setForum(\AgoraTeam\Agora\Domain\Model\Forum $forum)
    {
        $this->forum = $forum;
    }

    /**
     * @param User|null $user
     * @param string $accessType
     * @return bool
     */
    public function checkAccess(User $user = null, $accessType = self::TYPE_READ)
    {
        switch ($accessType) {
            default:
                $return = $this->forum->checkAccess($user, $accessType);
        }

        if ($this->isClosed()) {
            $return = false;
        }

        return $return;
    }
}
