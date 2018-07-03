<?php
namespace AgoraTeam\Agora\Domain\Model\Dto;

/**
 * This file is part of the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use AgoraTeam\Agora\Domain\Model\DemandInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * News Demand object which holds all information to get the correct news records.
 */
class ForumDemand extends AbstractEntity implements DemandInterface
{
 
    /** @var \AgoraTeam\Agora\Domain\Model\Dto\Search */
    protected $search;

    /** @var string */
    protected $order;
    
    /** @var int */
    protected $limit;
    
    /** @var int */
    protected $offset;
 
    /** @var string */
    protected $action = '';

    /** @var string */
    protected $class = '';
    
    /** @var int */
    protected $storagePage;
 
    /**
     * Set order
     *
     * @param string $order order
     * @return ForumDemand
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }
 
    /**
     * Get search object
     *
     * @return \AgoraTeam\Agora\Domain\Model\Dto\Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set search object
     *
     * @param \AgoraTeam\Agora\Domain\Model\Dto\Search $search search object
     * @return ForumDemand
     */
    public function setSearch($search = null)
    {
        $this->search = $search;
        return $this;
    }
 
    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return ForumDemand
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Set limit
     *
     * @param int $limit limit
     * @return NewsDemand
     */
    public function setLimit($limit)
    {
    	$this->limit = (int)$limit;
    	return $this;
    }
    
    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
    	return $this->limit;
    }
    
    /**
     * Set offset
     *
     * @param int $offset offset
     * @return NewsDemand
     */
    public function setOffset($offset)
    {
    	$this->offset = (int)$offset;
    	return $this;
    }
    
    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset()
    {
    	return $this->offset;
    }

    /**
     * @param string $class
     * @return ForumDemand
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $action
     * @param string $controller
     * @return ForumDemand
     */
    public function setActionAndClass($action, $controller)
    {
        $this->action = $action;
        $this->class = $controller;
        return $this;
    }
    
    /**
     * Set list of storage pages
     *
     * @param string $storagePage storage page list
     * @return ForumDeman
     */
    public function setStoragePage($storagePage)
    {
    	$this->storagePage = $storagePage;
    	return $this;
    }
    
    /**
     * Get list of storage pages
     *
     * @return string
     */
    public function getStoragePage()
    {
    	return $this->storagePage;
    }
    
}
