<?php
namespace AgoraTeam\Agora\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Philipp Thiele <philipp.thiele@phth.de>
 *           BjĂ¶rn Christopher Bresser <bjoern.bresser@gmail.com>
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
	/***
	 * Replace unwanted Characters
	 * 
	 * @param string $str
	 */
	public function replaceCharacters($string)
	{
		 $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ'; 
		 $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyybyRr'; 
		 $string = utf8_decode($string);     
		 $string = strtr($string, utf8_decode($a), $b); 
		 $string = strtolower($string);
		 return  utf8_encode($string);
	}
	
	/**
	 * Remove last word from the character
	 * @param string $data
	 * @param int $maxCharacters
	 *
	 * return string $data
	 */
	public function getRiddOfTheLastString($data, $maxCharacters) 
	{
		if ($maxCharacters > 0) {
			$data = trim(substr($data, 0, $maxCharacters));
		}
 		$dataAsArray = explode(" ", $data);
 		if (count($dataAsArray) > 5) {
 			array_pop($dataAsArray);
 		}
		foreach ($dataAsArray as $value) {
			$string .= $value ." ";
		}
 		return $string;
	}
	
    /**
     * Find and wrap sword
     *
     * @param string $sword
     * @param string $data
     * @param int $maxCharacters
     * @return string 
     */
    public function render($sword, $data, $maxCharacters)
    {	
       	$cleanedWord = $this->replaceCharacters($sword);
    	$cleanedData = $this->replaceCharacters($data);
      	$swordCount = substr_count(strtolower($cleanedData), strtolower($cleanedWord));
    	$swordLength = strlen($sword);
    	$textLength = strlen($data);
    	$searchedWordIsInRange = true;
    	
    	if ($maxCharacters > 0 && $swordCount > 0) {
    		// get position of the first sword letter in a string
    		$getFirstPosOfSwordInData = strpos(strtolower($cleanedData), strtolower($cleanedWord));
    		// Get the position of the last sword character
    		$posOfSwordLastChar = $getFirstPosOfSwordInData + $swordLength;
    		 
    		// check if whole sword is in the crop range
     		if ($posOfSwordLastChar > $maxCharacters) {
    			$searchedWordIsInRange = false;
    		}
    		 
    		// If the whole string is in the range, limit to maxcharacters
    		// and if is not, we should get the rid of the unwanted characters
    		// and keep the result of the sword and other text in the range
    		if ($searchedWordIsInRange === true && $textLength > $maxCharacters) {
    			$data = $this->getRiddOfTheLastString($data, $maxCharacters);      		
    			$data = $data . '...';
     		} else if ($searchedWordIsInRange === false && $textLength > $maxCharacters) {
      			$trim = ($maxCharacters - $swordLength) / 2; 
     			$trimFromTheFront = $getFirstPosOfSwordInData - $trim;
     			$trimFromTheBack = $textLength - ($posOfSwordLastChar + $trim);
     				
     			$data = substr($data, $trimFromTheFront);
     			$data = substr($data, 0, $maxCharacters);
					
     			$firstEmptySpaceIndex = stripos($data, ' ');
				$data = substr($data, $firstEmptySpaceIndex);
     				
				$data = $this->getRiddOfTheLastString($data, 0);
     			$data = "..." . $data . "...";
    		}
    	} else if ($maxCharacters > 0 && $swordCount == 0 && $textLength > $maxCharacters) {
     		$data = $this->getRiddOfTheLastString($data, $maxCharacters);     		
     		$data = $data . ' ...'; 
     	}
     	
 		return $data;
    }
}
