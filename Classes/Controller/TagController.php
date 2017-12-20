<?php

namespace AgoraTeam\Agora\Controller;

/***************************************************************
 *  Copyright notice
 *  (c) 2017 Philipp Thiele <philipp.thiele@phth.de>
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
 * TagController
 */
class TagController extends ActionController
{

    /**
     * @var \AgoraTeam\Agora\Domain\Repository\TagRepository
     * @inject
     */
    protected $tagRepository;

    /**
     * @param string $part
     * @return string
     */
    public function autocompleteAction($part = '')
    {
        $result = [];
        if ($part) {
            $tagObj = $this->tagRepository->findTagLikeTitle($part);
            foreach ($tagObj as $tag) {
                $result[] = $tag->getTitle();
            }
        }

        return json_encode($result);
    }

    /**
     * listAction
     * @return string
     */
    public function listAction()
    {
        $tagArr = [];
        $tagObj = $this->tagRepository->findAll();
        foreach ($tagObj as $tag) {
            $tagArr[] = $tag->getTitle();
        }
        return json_encode($tagArr);
    }
}
