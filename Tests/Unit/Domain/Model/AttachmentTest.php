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

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use AgoraTeam\Agora\Domain\Model\Attachment;

/**
 * Test case for class \AgoraTeam\Agora\Domain\Model\Attachment.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Philipp Thiele <philipp.thiele@phth.de>
 * @author Björn Christopher Bresser <bjoern.bresser@gmail.com>
 */
class AttachmentTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \AgoraTeam\Agora\Domain\Model\Attachment
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
    public function getFileReturnsInitialValueForFileReference()
    {
        $this->assertEquals(
            null,
            $this->subject->getFile()
        );
    }

    /**
     * @test
     */
    public function setFileForFileReferenceSetsFile()
    {
        $fileReferenceFixture = new FileReference();
        $this->subject->setFile($fileReferenceFixture);

        $this->assertAttributeEquals(
            $fileReferenceFixture,
            'file',
            $this->subject
        );
    }

    protected function setUp()
    {
        $this->subject = new Attachment();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }
}
