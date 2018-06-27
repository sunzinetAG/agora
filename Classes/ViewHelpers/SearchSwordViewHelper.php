<?php
namespace AgoraTeam\Agora\ViewHelpers;

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

/**
 * Class searchSwordViewHelper
 * Find sword in the text
 *
 * @package AgoraTeam\Agora\ViewHelpers
 */

class SearchSwordViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Find and wrap sword
     *
     * @param string $sword
     * @param string $data
     * @param int $corpMaxWords
     * @return string 
     */
    public function render($sword, $data, $maxCharacters)
    {	
    	
    	// Check for the cmlcase of the search word.
     	$swordCount = substr_count(strtolower($data), strtolower($sword));
    	$swordLength = strlen($sword);
    	$textLength = strlen($data);
    	$searchedWordIsInRange = true;
    	
    	if ($maxCharacters > 0 && $swordCount > 0) {
    		// get position of the first sword letter 
    		$getFirstPosOfSwordInData = stripos(strtolower($data), strtolower($sword));

    		// if the position of the 'sword' is higher than zero trim, calculate how much characters it is 
     		if ($getFirstPosOfSwordInData > 0) {
    			$getInFront = substr($data, 0, $getFirstPosOfSwordInData);
    			$length_getInFront = strlen($getInFront); 
    		}
     		
    		// Get the position of the last sword character
    		$posOfSwordLastChar = $getFirstPosOfSwordInData + $swordLength;
    		 
    		// check if whole sword is in the crop range
     		if ($posOfSwordLastChar > $maxCharacters) {
    			$searchedWordIsInRange = false;
    		}
    		 
    		// If the whole string is in the range, limit to maxcharacters
    		// and if is not, we should get the rid of the unwanted characters
    		// and keep the result of the sword and other text in the range
    		if ($searchedWordIsInRange === true) {
    			$data = substr($data, 0, $maxCharacters-3);
    		} else {
    			// check we have more that it needs $removeCharsFromStartCount
				 $removeCharsFromStartCount = $posOfSwordLastChar - $maxCharacters-3;
    			 $trimCharMoreThenSwordLastLetter = substr($data, 0, $posOfSwordLastChar);
    			 $countTrimmedSword = strlen($trimCharMoreThenSwordLastLetter);
    			 $data = substr($trimCharMoreThenSwordLastLetter, $removeCharsFromStartCount, $countTrimmedSword);
    		}
    		 
    		// wrap sword with marker
    		$data = str_replace($sword, "###SWORD_WITH_WRAP###", $data);
    		$swordWraped = "<span class='searched-sword'>". $sword ."</span>";
    		$data = str_replace("###SWORD_WITH_WRAP###", $swordWraped, $data);
    		
    	} else if ($maxCharacters > 0 && $swordCount == 0) {
     		$data = substr($data, 0, $maxCharacters -3);
     	} else if ($maxCharacters == 0 || $swordCount == 0 ){
    		
    		$data = str_replace($sword, "###SWORD_WITH_WRAP###", $data);
    		$swordWraped = "<span class='searched-sword'>". $sword ."</span>";
    		$data = str_replace("###SWORD_WITH_WRAP###", $swordWraped, $data);
    	}
    	
 		return $data;
    }

}
