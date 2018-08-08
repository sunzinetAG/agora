<?php

namespace AgoraTeam\Agora\Tests\Unit\Controller;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Philipp Thiele <philipp.thiele@phth.de>
 *            Björn Christopher Bresser <bjoern.bresser@gmail.com>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use AgoraTeam\Agora\Domain\Model\Forum;

/**
 * Test case for class AgoraTeam\Agora\Controller\ForumController.
 *
 * @author Philipp Thiele <philipp.thiele@phth.de>
 * @author Björn Christopher Bresser <bjoern.bresser@gmail.com>
 */
class ForumControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \AgoraTeam\Agora\Controller\ForumController
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = $this->getMock('AgoraTeam\\Agora\\Controller\\ForumController',
            array('redirect', 'forward', 'addFlashMessage'), array(), '', false);
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function listActionFetchesAllForumsFromRepositoryAndAssignsThemToView()
    {

        $allForums = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', false);

        $forumRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\ForumRepository', array('findAll'),
            array(), '', false);
        $forumRepository->expects($this->once())->method('findAll')->will($this->returnValue($allForums));
        $this->inject($this->subject, 'forumRepository', $forumRepository);

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $view->expects($this->once())->method('assign')->with('forums', $allForums);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenForumToView()
    {
        $forum = new Forum();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('forum', $forum);

        $this->subject->showAction($forum);
    }

    /**
     * @test
     */
    public function newActionAssignsTheGivenForumToView()
    {
        $forum = new Forum();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $view->expects($this->once())->method('assign')->with('newForum', $forum);
        $this->inject($this->subject, 'view', $view);

        $this->subject->newAction($forum);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenForumToForumRepository()
    {
        $forum = new Forum();

        $forumRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\ForumRepository', array('add'),
            array(), '', false);
        $forumRepository->expects($this->once())->method('add')->with($forum);
        $this->inject($this->subject, 'forumRepository', $forumRepository);

        $this->subject->createAction($forum);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenForumToView()
    {
        $forum = new Forum();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('forum', $forum);

        $this->subject->editAction($forum);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenForumInForumRepository()
    {
        $forum = new Forum();

        $forumRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\ForumRepository', array('update'),
            array(), '', false);
        $forumRepository->expects($this->once())->method('update')->with($forum);
        $this->inject($this->subject, 'forumRepository', $forumRepository);

        $this->subject->updateAction($forum);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenForumFromForumRepository()
    {
        $forum = new Forum();

        $forumRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\ForumRepository', array('remove'),
            array(), '', false);
        $forumRepository->expects($this->once())->method('remove')->with($forum);
        $this->inject($this->subject, 'forumRepository', $forumRepository);

        $this->subject->deleteAction($forum);
    }
}
