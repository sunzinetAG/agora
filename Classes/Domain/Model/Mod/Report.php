<?php

namespace AgoraTeam\Agora\Domain\Model\Mod;

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

use AgoraTeam\Agora\Domain\Model\Entity;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\User;

/**
 * Class Report
 *
 * @package AgoraTeam\Agora\Domain\Model
 */
class Report extends Entity
{

    /**
     * @var \AgoraTeam\Agora\Domain\Model\User $feuser
     */
    protected $feuser = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Model\Post $post
     * @lazy
     */
    protected $post = null;

    /**
     * @var int
     */
    protected $type = 1;

    /**
     * @var \AgoraTeam\Agora\Domain\Model\User $feuser
     */
    protected $reporter = null;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @return \AgoraTeam\Agora\Domain\Model\User
     */
    public function getFeuser()
    {
        return $this->feuser;
    }

    /**
     * @param \AgoraTeam\Agora\Domain\Model\User $feuser
     */
    public function setFeuser(\AgoraTeam\Agora\Domain\Model\User $feuser)
    {
        $this->feuser = $feuser;
    }

    /**
     * @return Post
     */
    public function getPost()
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
     * @return int
     */
    public function getType()
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
     * @return \AgoraTeam\Agora\Domain\Model\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param \AgoraTeam\Agora\Domain\Model\User $reporter
     */
    public function setReporter(\AgoraTeam\Agora\Domain\Model\User $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }
}
