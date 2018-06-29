<?php

namespace AgoraTeam\Agora\Utility;

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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class QuoteUtility
 *
 * @package AgoraTeam\Agora\Utility
 */
class QuoteUtility implements SingletonInterface
{

    /**
     * @param $text
     * @param $author
     * @param string $quotedMarker
     * @return string
     */
    public static function create($text, $author, $time = 0, $quotedMarker = '>')
    {
        $headline = LocalizationUtility::translate('tx_agora_domain_model_post.quoted_headline', 'agora', [$author]);
        $textArray = preg_split('#(\r\n?|\n)+#', $text);

        $dataHolder = [];
        $splitTextArray = [
            '> **' . $headline . '**'
        ];

        $pattern = '/^( *)\[\d+\]:+(.*)$/i';

        foreach ($textArray as $key => $paragraph) {
            // Check if double Quote!
            if (preg_match($pattern, $paragraph)) {
                $dataHolder[] = $paragraph;
            } else {
                if ($paragraph != $quotedMarker) {
                    $splitTextArray[] = $quotedMarker . ' ' . $paragraph;
                    $splitTextArray[] = $quotedMarker;
                }
            }
        }

        // Remove last element of array if it is the quotedMarket
        if ($quotedMarker == end($splitTextArray)) {
            array_pop($splitTextArray);
        }

        $post = implode("\n", $splitTextArray);
        $dataHolder = implode("\n", $dataHolder);

        $text = $post . "\n\r\n\r\n\r" . $dataHolder;

        return $text;
    }
}
