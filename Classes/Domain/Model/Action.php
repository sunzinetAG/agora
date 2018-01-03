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

/**
 * Class Post
 *
 * @package AgoraTeam\Agora\Domain\Model
 */
class Action extends Entity
{

    /**
     * @var int
     */
    protected $type = 0;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $link = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $data = '';

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     */
    protected $user = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Model\Post $post
     */
    protected $post = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Model\thread $thread
     */
    protected $thread = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Model\Forum $forum
     */
    protected $forum = null;

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getUser(): \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return thread
     */
    public function getThread(): thread
    {
        return $this->thread;
    }

    /**
     * @param thread $thread
     */
    public function setThread(thread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return Forum
     */
    public function getForum(): Forum
    {
        return $this->forum;
    }

    /**
     * @param Forum $forum
     */
    public function setForum(Forum $forum)
    {
        $this->forum = $forum;
    }
}
