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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Voting;
use AgoraTeam\Agora\Domain\Model\Attachment;
use AgoraTeam\Agora\Domain\Model\User;

/**
 * Test case for class \AgoraTeam\Agora\Domain\Model\Post.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Philipp Thiele <philipp.thiele@phth.de>
 * @author Björn Christopher Bresser <bjoern.bresser@gmail.com>
 */
class PostTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \AgoraTeam\Agora\Domain\Model\Post
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new Post();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTopicReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTopic()
        );
    }

    /**
     * @test
     */
    public function setTopicForStringSetsTopic()
    {
        $this->subject->setTopic('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'topic',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTextReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getText()
        );
    }

    /**
     * @test
     */
    public function setTextForStringSetsText()
    {
        $this->subject->setText('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'text',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getQuotedPostsReturnsInitialValueForPost()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getQuotedPosts()
        );
    }

    /**
     * @test
     */
    public function setQuotedPostsForObjectStorageContainingPostSetsQuotedPosts()
    {
        $quotedPost = new Post();
        $objectStorageHoldingExactlyOneQuotedPosts = new ObjectStorage();
        $objectStorageHoldingExactlyOneQuotedPosts->attach($quotedPost);
        $this->subject->setQuotedPosts($objectStorageHoldingExactlyOneQuotedPosts);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneQuotedPosts,
            'quotedPosts',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addQuotedPostToObjectStorageHoldingQuotedPosts()
    {
        $quotedPost = new Post();
        $quotedPostsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $quotedPostsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($quotedPost));
        $this->inject($this->subject, 'quotedPosts', $quotedPostsObjectStorageMock);

        $this->subject->addQuotedPost($quotedPost);
    }

    /**
     * @test
     */
    public function removeQuotedPostFromObjectStorageHoldingQuotedPosts()
    {
        $quotedPost = new Post();
        $quotedPostsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $quotedPostsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($quotedPost));
        $this->inject($this->subject, 'quotedPosts', $quotedPostsObjectStorageMock);

        $this->subject->removeQuotedPost($quotedPost);
    }

    /**
     * @test
     */
    public function getVotingReturnsInitialValueForVoting()
    {
        $this->assertEquals(
            null,
            $this->subject->getVoting()
        );
    }

    /**
     * @test
     */
    public function setVotingForVotingSetsVoting()
    {
        $votingFixture = new Voting();
        $this->subject->setVoting($votingFixture);

        $this->assertAttributeEquals(
            $votingFixture,
            'voting',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAttachmentsReturnsInitialValueForAttachment()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getAttachments()
        );
    }

    /**
     * @test
     */
    public function setAttachmentsForObjectStorageContainingAttachmentSetsAttachments()
    {
        $attachment = new Attachment();
        $objectStorageHoldingExactlyOneAttachments = new ObjectStorage();
        $objectStorageHoldingExactlyOneAttachments->attach($attachment);
        $this->subject->setAttachments($objectStorageHoldingExactlyOneAttachments);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneAttachments,
            'attachments',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addAttachmentToObjectStorageHoldingAttachments()
    {
        $attachment = new Attachment();
        $attachmentsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $attachmentsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($attachment));
        $this->inject($this->subject, 'attachments', $attachmentsObjectStorageMock);

        $this->subject->addAttachment($attachment);
    }

    /**
     * @test
     */
    public function removeAttachmentFromObjectStorageHoldingAttachments()
    {
        $attachment = new Attachment();
        $attachmentsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $attachmentsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($attachment));
        $this->inject($this->subject, 'attachments', $attachmentsObjectStorageMock);

        $this->subject->removeAttachment($attachment);
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
    public function getHistoricalVersionsReturnsInitialValueForPost()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getHistoricalVersions()
        );
    }

    /**
     * @test
     */
    public function setHistoricalVersionsForObjectStorageContainingPostSetsHistoricalVersions()
    {
        $historicalVersion = new Post();
        $objectStorageHoldingExactlyOneHistoricalVersions = new ObjectStorage();
        $objectStorageHoldingExactlyOneHistoricalVersions->attach($historicalVersion);
        $this->subject->setHistoricalVersions($objectStorageHoldingExactlyOneHistoricalVersions);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneHistoricalVersions,
            'historicalVersions',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addHistoricalVersionToObjectStorageHoldingHistoricalVersions()
    {
        $historicalVersion = new Post();
        $historicalVersionsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'), array(), '', false);
        $historicalVersionsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($historicalVersion));
        $this->inject($this->subject, 'historicalVersions', $historicalVersionsObjectStorageMock);

        $this->subject->addHistoricalVersion($historicalVersion);
    }

    /**
     * @test
     */
    public function removeHistoricalVersionFromObjectStorageHoldingHistoricalVersions()
    {
        $historicalVersion = new Post();
        $historicalVersionsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'), array(), '', false);
        $historicalVersionsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($historicalVersion));
        $this->inject($this->subject, 'historicalVersions', $historicalVersionsObjectStorageMock);

        $this->subject->removeHistoricalVersion($historicalVersion);
    }
}
