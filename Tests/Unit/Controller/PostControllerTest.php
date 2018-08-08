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

use AgoraTeam\Agora\Domain\Model\Post;

/**
 * Test case for class AgoraTeam\Agora\Controller\PostController.
 *
 * @author Philipp Thiele <philipp.thiele@phth.de>
 * @author Björn Christopher Bresser <bjoern.bresser@gmail.com>
 */
class PostControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \AgoraTeam\Agora\Controller\PostController
     */
    protected $subject = null;

    /**
     * @test
     */
    public function listActionFetchesAllPostsFromRepositoryAndAssignsThemToView()
    {

        $allPosts = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', false);

        $postRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\PostRepository', array('findAll'),
            array(), '', false);
        $postRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPosts));
        $this->inject($this->subject, 'postRepository', $postRepository);

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $view->expects($this->once())->method('assign')->with('posts', $allPosts);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenPostToView()
    {
        $post = new Post();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('post', $post);

        $this->subject->showAction($post);
    }

    /**
     * @test
     */
    public function newActionAssignsTheGivenPostToView()
    {
        $post = new Post();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $view->expects($this->once())->method('assign')->with('newPost', $post);
        $this->inject($this->subject, 'view', $view);

        $this->subject->newAction($post);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenPostToPostRepository()
    {
        $post = new Post();

        $postRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\PostRepository', array('add'), array(),
            '', false);
        $postRepository->expects($this->once())->method('add')->with($post);
        $this->inject($this->subject, 'postRepository', $postRepository);

        $this->subject->createAction($post);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenPostToView()
    {
        $post = new Post();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('post', $post);

        $this->subject->editAction($post);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenPostInPostRepository()
    {
        $post = new Post();

        $postRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\PostRepository', array('update'),
            array(), '', false);
        $postRepository->expects($this->once())->method('update')->with($post);
        $this->inject($this->subject, 'postRepository', $postRepository);

        $this->subject->updateAction($post);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenPostFromPostRepository()
    {
        $post = new Post();

        $postRepository = $this->getMock('AgoraTeam\\Agora\\Domain\\Repository\\PostRepository', array('remove'),
            array(), '', false);
        $postRepository->expects($this->once())->method('remove')->with($post);
        $this->inject($this->subject, 'postRepository', $postRepository);

        $this->subject->deleteAction($post);
    }

    protected function setUp()
    {
        $this->subject = $this->getMock('AgoraTeam\\Agora\\Controller\\PostController',
            array('redirect', 'forward', 'addFlashMessage'), array(), '', false);
    }

    protected function tearDown()
    {
        unset($this->subject);
    }
}
