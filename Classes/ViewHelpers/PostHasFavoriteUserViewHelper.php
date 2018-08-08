<?php

namespace AgoraTeam\Agora\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Björn Christopher Bresser <bjoern.bresser@gmail.com>
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

use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\User;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PostHasFavoriteUserViewHelper
 * @package AgoraTeam\Agora\ViewHelpers
 */
class PostHasFavoriteUserViewHelper extends AbstractViewHelper
{

    /**
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public function render(Post $post, User $user)
    {
        if (!$user) {
            return false;
        }

        return $user->getFavoritePosts()->offsetExists($post);
    }
}
