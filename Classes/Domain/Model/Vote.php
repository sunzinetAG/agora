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
 * Class Vote
 * @package AgoraTeam\Agora\Domain\Model
 */
class Vote extends AbstractEntity
{

    /**
     * voting
     *
     * @var Voting
     */
    protected $voting = null;

    /**
     * votingAnswers
     *
     * @var ObjectStorage<VotingAnswer>
     * @cascade remove
     */
    protected $votingAnswers = null;

    /**
     * user
     *
     * @var User
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
        $this->votingAnswers = new ObjectStorage();
    }

    /**
     * Returns the voting
     *
     * @return Voting $voting
     */
    public function getVoting()
    {
        return $this->voting;
    }

    /**
     * Sets the voting
     *
     * @param Voting $voting
     * @return void
     */
    public function setVoting(Voting $voting)
    {
        $this->voting = $voting;
    }

    /**
     * Adds a VotingAnswer
     *
     * @param VotingAnswer $votingAnswer
     * @return void
     */
    public function addVotingAnswer(VotingAnswer $votingAnswer)
    {
        $this->votingAnswers->attach($votingAnswer);
    }

    /**
     * Removes a VotingAnswer
     *
     * @param VotingAnswer $votingAnswerToRemove The VotingAnswer to be removed
     * @return void
     */
    public function removeVotingAnswer(VotingAnswer $votingAnswerToRemove)
    {
        $this->votingAnswers->detach($votingAnswerToRemove);
    }

    /**
     * Returns the votingAnswers
     *
     * @return ObjectStorage<VotingAnswer> $votingAnswers
     */
    public function getVotingAnswers()
    {
        return $this->votingAnswers;
    }

    /**
     * Sets the votingAnswers
     *
     * @param ObjectStorage<VotingAnswer> $votingAnswers
     * @return void
     */
    public function setVotingAnswers(ObjectStorage $votingAnswers)
    {
        $this->votingAnswers = $votingAnswers;
    }

    /**
     * Returns the user
     *
     * @return User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user
     *
     * @param User $user
     * @return void
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

}
