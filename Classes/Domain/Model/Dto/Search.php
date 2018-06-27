<?php
namespace AgoraTeam\Agora\Domain\Model\Dto;

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

class Search extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Basic search word
     *
     * @var string
     */
    protected $sword;
 	
    /**
     * Themes
     *
     * @var array
     */
    protected $themes;
    
    /**
     * Radius
     *
     * @var int
     */
    protected $radius;
    
    /**
     * orderBy
     *
     * @var string
     */
    protected $orderBy;
    
    /**
     * orderDirection
     *
     * @var string
     */
    protected $orderDirection;
    
    /**
     * Order
     *
     * @var string
     */
    protected $order;
    
    /** @var bool */
    protected $splitSubjectWords = false;
 
    /**
     * Get the sword
     *
     * @return string
     */
    public function getSword()
    {
        return $this->sword;
    }

    /**
     * Set sword
     *
     * @param string $sword
     */
    public function setSword($sword)
    {
        $this->sword = $sword;
    }
    
    /**
     * @param array $themes
     */
    public function setThemes($themes)
    {
    	$this->themes = $themes;
    }
    
    /**
     * @return array
     */
    public function getThemes()
    {
    	return $this->themes;
    }
    
    /**
     * @param int $radius
     */
    public function setRadius($radius)
    {
    	$this->radius = $radius;
    }
    
    /**
     * @return int
     */
    public function getRadius()
    {
    	return $this->radius;
    }
 
    /**
     * @return string
     */
    public function getOrderDirection()
    {
    	return $this->orderDirection;
    }
    
    /**
     * @param string $orderDirection
     */
    public function setOrderDirection($orderDirection)
    {
    	$this->orderDirection = $orderDirection;
    }
 
    /**
     * @return string
     */
    public function getOrderBy()
    {
    	return $this->orderBy;
    }
 
    /**
     * @param string $orderBy
     */
    public function setOrderBy($orderBy)
    {
    	$this->orderBy = $orderBy;
    }
    
    /**
     * @return string
     */
    public function getOrder()
    {	
    	return $this->order;
    }
   
    /**
     * @param string $order
     */
    public function setOrder($order)
    {
    	$this->order = $order;
    }
    
    /**
     * @return bool
     */
    public function isSplitSubjectWords()
    {
    	return $this->splitSubjectWords;
    }
    
    /**
     * @param bool $splitSubjectWords
     */
    public function setSplitSubjectWords($splitSubjectWords)
    {
    	$this->splitSubjectWords = $splitSubjectWords;
    }
}
