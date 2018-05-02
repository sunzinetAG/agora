<?php

namespace AgoraTeam\Agora\Service;

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
use AgoraTeam\Agora\Domain\Model\Tag;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class TagService
 *
 * @package AgoraTeam\Agora\Service
 */
class TagService implements SingletonInterface
{

    /**
     * An instance of the Extbase object manager.
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager = null;

    /**
     * @var \AgoraTeam\Agora\Domain\Repository\TagRepository
     * @inject
     */
    protected $tagRepository;

    /**
     * @param string $tags
     * @return ObjectStorage
     */
    public function prepareTags($tags)
    {
        $tagsObj = new ObjectStorage();

        // Remove possible duplicates from the string
        $tagsArr = array_unique(GeneralUtility::trimExplode(',', $tags));

        // Lookup each tags to check if exists
        foreach ($tagsArr as $tag) {
            $result = $this->tagRepository->findOneByTitle($tag);

            if (is_a($result, Tag::class)) {
                $tagsObj->attach($result);
            } else {
                /** @var Tag $newTag */
                $newTag = $this->objectManager->get(Tag::class);
                $newTag->setTitle($tag);
                $newTag->setCrdate(new \DateTime());

                $this->tagRepository->add($newTag);

                $tagsObj->attach($newTag);
            }
        }

        return $tagsObj;
    }
}
