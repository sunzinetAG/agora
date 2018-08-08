<?php

namespace AgoraTeam\Agora\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Math: Sum
 *
 * Class SumViewHelper
 * @package AgoraTeam\Agora\ViewHelpers\Math
 */
class SumViewHelper extends AbstractViewHelper
{
    /**
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     */
    protected static function render($a, $b)
    {
        return $a + $b;
    }
}
