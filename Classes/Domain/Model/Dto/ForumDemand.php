<?php

namespace AgoraTeam\Agora\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Forum Demand object which holds all information to get the correct Forum records.
 *
 * Class ForumDemand
 * @package AgoraTeam\Agora\Domain\Model\Dto
 */
class ForumDemand extends AbstractEntity
{

    /** @var Search */
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
     * Get order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

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
     * Get search object
     *
     * @return Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set search object
     *
     * @param Search $search search object
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
     * @param string $class
     * @return ForumDemand
     */
    public function setClass($class)
    {
        $this->class = $class;
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
     * Set limit
     *
     * @param int $limit limit
     * @return ForumDemand
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
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
     * Set offset
     *
     * @param int $offset offset
     * @return ForumDemand
     */
    public function setOffset($offset)
    {
        $this->offset = (int)$offset;
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
     * Get list of storage pages
     *
     * @return string
     */
    public function getStoragePage()
    {
        return $this->storagePage;
    }

    /**
     * Set list of storage pages
     *
     * @param string $storagePage storage page list
     * @return $this
     */
    public function setStoragePage($storagePage)
    {
        $this->storagePage = $storagePage;
        return $this;
    }

}
