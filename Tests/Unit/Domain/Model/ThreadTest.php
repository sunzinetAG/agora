<?php

namespace AgoraTeam\Agora\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Philipp Thiele <philipp.thiele@phth.de>
 *           Björn Christopher Bresser <bjoern.bresser@gmail.com>
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

use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Domain\Model\User;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Group;
use AgoraTeam\Agora\Domain\Model\View;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case for class \AgoraTeam\Agora\Domain\Model\Thread.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Philipp Thiele <philipp.thiele@phth.de>
 * @author Björn Christopher Bresser <bjoern.bresser@gmail.com>
 */
class ThreadTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \AgoraTeam\Agora\Domain\Model\Thread
     */
    protected $subject = null;

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSolvedReturnsInitialValueForBoolean()
    {
        $this->assertSame(
            false,
            $this->subject->getSolved()
        );
    }

    /**
     * @test
     */
    public function setSolvedForBooleanSetsSolved()
    {
        $this->subject->setSolved(true);

        $this->assertAttributeEquals(
            true,
            'solved',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getClosedReturnsInitialValueForBoolean()
    {
        $this->assertSame(
            false,
            $this->subject->getClosed()
        );
    }

    /**
     * @test
     */
    public function setClosedForBooleanSetsClosed()
    {
        $this->subject->setClosed(true);

        $this->assertAttributeEquals(
            true,
            'closed',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStickyReturnsInitialValueForBoolean()
    {
        $this->assertSame(
            false,
            $this->subject->getSticky()
        );
    }

    /**
     * @test
     */
    public function setStickyForBooleanSetsSticky()
    {
        $this->subject->setSticky(true);

        $this->assertAttributeEquals(
            true,
            'sticky',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCreatorReturnsInitialValueForUser()
    {
        $this->assertEquals(
            null,
            $this->subject->getCreator()
        );
    }

    /**
     * @test
     */
    public function setCreatorForUserSetsCreator()
    {
        $creatorFixture = new User();
        $this->subject->setCreator($creatorFixture);

        $this->assertAttributeEquals(
            $creatorFixture,
            'creator',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPostsReturnsInitialValueForPost()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getPosts()
        );
    }

    /**
     * @test
     */
    public function setPostsForObjectStorageContainingPostSetsPosts()
    {
        $post = new Post();
        $objectStorageHoldingExactlyOnePosts = new ObjectStorage();
        $objectStorageHoldingExactlyOnePosts->attach($post);
        $this->subject->setPosts($objectStorageHoldingExactlyOnePosts);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOnePosts,
            'posts',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addPostToObjectStorageHoldingPosts()
    {
        $post = new Post();
        $postsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'),
            array(), '', false);
        $postsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($post));
        $this->inject($this->subject, 'posts', $postsObjectStorageMock);

        $this->subject->addPost($post);
    }

    /**
     * @test
     */
    public function removePostFromObjectStorageHoldingPosts()
    {
        $post = new Post();
        $postsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'),
            array(), '', false);
        $postsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($post));
        $this->inject($this->subject, 'posts', $postsObjectStorageMock);

        $this->subject->removePost($post);
    }

    /**
     * @test
     */
    public function getViewsReturnsInitialValueFor()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getViews()
        );
    }

    /**
     * @test
     */
    public function setViewsForObjectStorageContainingSetsViews()
    {
        $view = new View();
        $objectStorageHoldingExactlyOneViews = new ObjectStorage();
        $objectStorageHoldingExactlyOneViews->attach($view);
        $this->subject->setViews($objectStorageHoldingExactlyOneViews);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneViews,
            'views',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addViewToObjectStorageHoldingViews()
    {
        $view = new View();
        $viewsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'),
            array(), '', false);
        $viewsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($view));
        $this->inject($this->subject, 'views', $viewsObjectStorageMock);

        $this->subject->addView($view);
    }

    /**
     * @test
     */
    public function removeViewFromObjectStorageHoldingViews()
    {
        $view = new View();
        $viewsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'),
            array(), '', false);
        $viewsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($view));
        $this->inject($this->subject, 'views', $viewsObjectStorageMock);

        $this->subject->removeView($view);
    }

    /**
     * @test
     */
    public function getGroupsWithReadAccessReturnsInitialValueForGroup()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getGroupsWithReadAccess()
        );
    }

    /**
     * @test
     */
    public function setGroupsWithReadAccessForObjectStorageContainingGroupSetsGroupsWithReadAccess()
    {
        $groupsWithReadAcces = new Group();
        $objectStorageHoldingExactlyOneGroupsWithReadAccess = new ObjectStorage();
        $objectStorageHoldingExactlyOneGroupsWithReadAccess->attach($groupsWithReadAcces);
        $this->subject->setGroupsWithReadAccess($objectStorageHoldingExactlyOneGroupsWithReadAccess);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneGroupsWithReadAccess,
            'groupsWithReadAccess',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addGroupsWithReadAccesToObjectStorageHoldingGroupsWithReadAccess()
    {
        $groupsWithReadAcces = new Group();
        $groupsWithReadAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $groupsWithReadAccessObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($groupsWithReadAcces));
        $this->inject($this->subject, 'groupsWithReadAccess', $groupsWithReadAccessObjectStorageMock);

        $this->subject->addGroupsWithReadAcces($groupsWithReadAcces);
    }

    /**
     * @test
     */
    public function removeGroupsWithReadAccesFromObjectStorageHoldingGroupsWithReadAccess()
    {
        $groupsWithReadAcces = new Group();
        $groupsWithReadAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $groupsWithReadAccessObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($groupsWithReadAcces));
        $this->inject($this->subject, 'groupsWithReadAccess', $groupsWithReadAccessObjectStorageMock);

        $this->subject->removeGroupsWithReadAcces($groupsWithReadAcces);
    }

    /**
     * @test
     */
    public function getGroupWithWriteAccesssReturnsInitialValueForGroup()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getGroupWithWriteAccesss()
        );
    }

    /**
     * @test
     */
    public function setGroupWithWriteAccesssForObjectStorageContainingGroupSetsGroupWithWriteAccesss()
    {
        $groupWithWriteAccess = new Group();
        $objectStorageHoldingExactlyOneGroupWithWriteAccesss = new ObjectStorage();
        $objectStorageHoldingExactlyOneGroupWithWriteAccesss->attach($groupWithWriteAccess);
        $this->subject->setGroupWithWriteAccesss($objectStorageHoldingExactlyOneGroupWithWriteAccesss);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneGroupWithWriteAccesss,
            'groupWithWriteAccess',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addGroupWithWriteAccessToObjectStorageHoldingGroupWithWriteAccesss()
    {
        $groupWithWriteAccess = new Group();
        $groupWithWriteAccesssObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $groupWithWriteAccesssObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($groupWithWriteAccess));
        $this->inject($this->subject, 'groupWithWriteAccess', $groupWithWriteAccesssObjectStorageMock);

        $this->subject->addGroupWithWriteAccess($groupWithWriteAccess);
    }

    /**
     * @test
     */
    public function removeGroupWithWriteAccessFromObjectStorageHoldingGroupWithWriteAccesss()
    {
        $groupWithWriteAccess = new Group();
        $groupWithWriteAccesssObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $groupWithWriteAccesssObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($groupWithWriteAccess));
        $this->inject($this->subject, 'groupWithWriteAccess', $groupWithWriteAccesssObjectStorageMock);

        $this->subject->removeGroupWithWriteAccess($groupWithWriteAccess);
    }

    /**
     * @test
     */
    public function getGroupsWithModificationAccessReturnsInitialValueForGroup()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getGroupsWithModificationAccess()
        );
    }

    /**
     * @test
     */
    public function setGroupsWithModificationAccessForObjectStorageContainingGroupSetsGroupsWithModificationAccess()
    {
        $groupsWithModificationAcces = new Group();
        $objectStorageHoldingExactlyOneGroupsWithModificationAccess = new ObjectStorage();
        $objectStorageHoldingExactlyOneGroupsWithModificationAccess->attach($groupsWithModificationAcces);
        $this->subject->setGroupsWithModificationAccess($objectStorageHoldingExactlyOneGroupsWithModificationAccess);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneGroupsWithModificationAccess,
            'groupsWithModificationAccess',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addGroupsWithModificationAccesToObjectStorageHoldingGroupsWithModificationAccess()
    {
        $groupsWithModificationAcces = new Group();
        $groupsWithModificationAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $groupsWithModificationAccessObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($groupsWithModificationAcces));
        $this->inject($this->subject, 'groupsWithModificationAccess', $groupsWithModificationAccessObjectStorageMock);

        $this->subject->addGroupsWithModificationAcces($groupsWithModificationAcces);
    }

    /**
     * @test
     */
    public function removeGroupsWithModificationAccesFromObjectStorageHoldingGroupsWithModificationAccess()
    {
        $groupsWithModificationAcces = new Group();
        $groupsWithModificationAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $groupsWithModificationAccessObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($groupsWithModificationAcces));
        $this->inject($this->subject, 'groupsWithModificationAccess', $groupsWithModificationAccessObjectStorageMock);

        $this->subject->removeGroupsWithModificationAcces($groupsWithModificationAcces);
    }

    /**
     * @test
     */
    public function getUsersWithReadAccessReturnsInitialValueForUser()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getUsersWithReadAccess()
        );
    }

    /**
     * @test
     */
    public function setUsersWithReadAccessForObjectStorageContainingUserSetsUsersWithReadAccess()
    {
        $usersWithReadAcces = new User();
        $objectStorageHoldingExactlyOneUsersWithReadAccess = new ObjectStorage();
        $objectStorageHoldingExactlyOneUsersWithReadAccess->attach($usersWithReadAcces);
        $this->subject->setUsersWithReadAccess($objectStorageHoldingExactlyOneUsersWithReadAccess);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneUsersWithReadAccess,
            'usersWithReadAccess',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addUsersWithReadAccesToObjectStorageHoldingUsersWithReadAccess()
    {
        $usersWithReadAcces = new User();
        $usersWithReadAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $usersWithReadAccessObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($usersWithReadAcces));
        $this->inject($this->subject, 'usersWithReadAccess', $usersWithReadAccessObjectStorageMock);

        $this->subject->addUsersWithReadAcces($usersWithReadAcces);
    }

    /**
     * @test
     */
    public function removeUsersWithReadAccesFromObjectStorageHoldingUsersWithReadAccess()
    {
        $usersWithReadAcces = new User();
        $usersWithReadAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $usersWithReadAccessObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($usersWithReadAcces));
        $this->inject($this->subject, 'usersWithReadAccess', $usersWithReadAccessObjectStorageMock);

        $this->subject->removeUsersWithReadAcces($usersWithReadAcces);
    }

    /**
     * @test
     */
    public function getUsersWthWriteAccessiiReturnsInitialValueForUser()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getUsersWthWriteAccessii()
        );
    }

    /**
     * @test
     */
    public function setUsersWthWriteAccessiiForObjectStorageContainingUserSetsUsersWthWriteAccessii()
    {
        $usersWthWriteAccessii = new User();
        $objectStorageHoldingExactlyOneUsersWthWriteAccessii = new ObjectStorage();
        $objectStorageHoldingExactlyOneUsersWthWriteAccessii->attach($usersWthWriteAccessii);
        $this->subject->setUsersWthWriteAccessii($objectStorageHoldingExactlyOneUsersWthWriteAccessii);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneUsersWthWriteAccessii,
            'usersWthWriteAccessii',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addUsersWthWriteAccessiiToObjectStorageHoldingUsersWthWriteAccessii()
    {
        $usersWthWriteAccessii = new User();
        $usersWthWriteAccessiiObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $usersWthWriteAccessiiObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($usersWthWriteAccessii));
        $this->inject($this->subject, 'usersWthWriteAccessii', $usersWthWriteAccessiiObjectStorageMock);

        $this->subject->addUsersWthWriteAccessii($usersWthWriteAccessii);
    }

    /**
     * @test
     */
    public function removeUsersWthWriteAccessiiFromObjectStorageHoldingUsersWthWriteAccessii()
    {
        $usersWthWriteAccessii = new User();
        $usersWthWriteAccessiiObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $usersWthWriteAccessiiObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($usersWthWriteAccessii));
        $this->inject($this->subject, 'usersWthWriteAccessii', $usersWthWriteAccessiiObjectStorageMock);

        $this->subject->removeUsersWthWriteAccessii($usersWthWriteAccessii);
    }

    /**
     * @test
     */
    public function getUsersWithModificationAccessReturnsInitialValueForUser()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getUsersWithModificationAccess()
        );
    }

    /**
     * @test
     */
    public function setUsersWithModificationAccessForObjectStorageContainingUserSetsUsersWithModificationAccess()
    {
        $usersWithModificationAcces = new User();
        $objectStorageHoldingExactlyOneUsersWithModificationAccess = new ObjectStorage();
        $objectStorageHoldingExactlyOneUsersWithModificationAccess->attach($usersWithModificationAcces);
        $this->subject->setUsersWithModificationAccess($objectStorageHoldingExactlyOneUsersWithModificationAccess);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneUsersWithModificationAccess,
            'usersWithModificationAccess',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addUsersWithModificationAccesToObjectStorageHoldingUsersWithModificationAccess()
    {
        $usersWithModificationAcces = new User();
        $usersWithModificationAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $usersWithModificationAccessObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($usersWithModificationAcces));
        $this->inject($this->subject, 'usersWithModificationAccess', $usersWithModificationAccessObjectStorageMock);

        $this->subject->addUsersWithModificationAcces($usersWithModificationAcces);
    }

    /**
     * @test
     */
    public function removeUsersWithModificationAccesFromObjectStorageHoldingUsersWithModificationAccess()
    {
        $usersWithModificationAcces = new User();
        $usersWithModificationAccessObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $usersWithModificationAccessObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($usersWithModificationAcces));
        $this->inject($this->subject, 'usersWithModificationAccess', $usersWithModificationAccessObjectStorageMock);

        $this->subject->removeUsersWithModificationAcces($usersWithModificationAcces);
    }

    protected function setUp()
    {
        $this->subject = new Thread();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }
}
