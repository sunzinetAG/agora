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
     * @var int
     */
    protected $user = 0;

    /**
     * @var string
     */
    protected $groups = '';

    /**
     * @var int
     */
    protected $post = 0;

    /**
     * @var int
     */
    protected $thread = 0;

    /**
     * @var int
     */
    protected $forum = 0;

    /**
     * @var string
     */
    protected $crdate;

    /**
     * @var int
     */
    protected $page = 0;

    /**
     * @var string
     */
    protected $hash = '';

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
     * @return int
     */
    public function getUser(): int
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser(int $user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getPost(): int
    {
        return $this->post;
    }

    /**
     * @param int $post
     */
    public function setPost(int $post)
    {
        $this->post = $post;
    }

    /**
     * @return int
     */
    public function getThread(): int
    {
        return $this->thread;
    }

    /**
     * @param int $thread
     */
    public function setThread(int $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return int
     */
    public function getForum(): int
    {
        return $this->forum;
    }

    /**
     * @param int $forum
     */
    public function setForum(int $forum)
    {
        $this->forum = $forum;
    }

    /**
     * @return string
     */
    public function getCrdate(): string
    {
        return $this->crdate;
    }

    /**
     * @param string $crdate
     */
    public function setCrdate(string $crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @return \DateTime
     */
    public function getTstamp(): \DateTime
    {
        return $this->tstamp;
    }

    /**
     * @param \DateTime $tstamp
     */
    public function setTstamp(\DateTime $tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getGroups(): string
    {
        return $this->groups;
    }

    /**
     * @param string $groups
     */
    public function setGroups(string $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash = '')
    {
        if ($hash == '') {
            $objVars = get_object_vars($this);
            $hash = md5(serialize($objVars));
        }
        $this->hash = $hash;
    }
}
