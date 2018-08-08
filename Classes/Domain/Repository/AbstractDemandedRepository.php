<?php

namespace AgoraTeam\Agora\Domain\Repository;

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
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use AgoraTeam\Agora\Domain\Model\Dto\ForumDemand;

/**
 * Abstract demanded repository
 */
abstract class AbstractDemandedRepository extends Repository
{

    /**
     * Returns the objects of this repository matching the demand.
     *
     * @param ForumDemand $demand
     * @param bool $respectEnableFields
     * @return array|QueryResultInterface
     * @throws \Exception
     */
    public function findDemanded(ForumDemand $demand, $respectEnableFields = true)
    {
        $query = $this->generateQuery($demand, $respectEnableFields);

        return $query->execute();
    }

    /**
     * @param ForumDemand $demand
     * @param bool $respectEnableFields
     * @return QueryInterface
     * @throws \Exception
     */
    protected function generateQuery(ForumDemand $demand, $respectEnableFields = true)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = $this->createConstraintsFromDemand($query, $demand);

        if ($respectEnableFields === false) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
            $constraints[] = $query->equals('deleted', 0);
        }

        if (!empty($constraints)) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        if ($orderings = $this->createOrderingsFromDemand($demand)) {
            $query->setOrderings($orderings);
        }

        if ($demand->getLimit() != null) {
            $query->setLimit((int)$demand->getLimit());
        }

        if ($demand->getOffset() != null) {
            if (!$query->getLimit()) {
                $query->setLimit(PHP_INT_MAX);
            }
            $query->setOffset((int)$demand->getOffset());
        }

        return $query;
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param ForumDemand $demand
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array<ConstraintInterface>
     */
    protected function createConstraintsFromDemand(QueryInterface $query, ForumDemand $demand)
    {
        /** @var ForumDemand $demand */
        $constraints = [];

        // storage page
        if ($demand->getStoragePage() != 0) {
            $pidList = GeneralUtility::intExplode(',', $demand->getStoragePage(), true);
            $constraints['pid'] = $query->in('pid', $pidList);
        }

        // Search
        $searchConstraints = $this->getSearchConstraints($query, $demand);
        if (!empty($searchConstraints)) {
            $constraints['search'] = $query->logicalAnd($searchConstraints);
        }

        // Clean not used constraints
        foreach ($constraints as $key => $value) {
            if (is_null($value)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    /**
     * Returns an array of orderings created from a given demand object.
     *
     * @param ForumDemand $demand
     * @return array<ConstraintInterface>
     */
    protected function createOrderingsFromDemand(ForumDemand $demand)
    {

        $orderings = [];
        $orderField = $demand->getSearch()->getOrder();

        if (!isset($orderField)) {
            $orderField = $demand->getOrder();
        }

        $orderList = GeneralUtility::trimExplode(',', $orderField, true);

        if (!empty($orderList)) {
            // go through every order statement
            foreach ($orderList as $orderItem) {
                list($orderField, $ascDesc) = GeneralUtility::trimExplode(' ', $orderItem, true);
                // count == 1 means that no direction is given
                if ($ascDesc) {
                    $orderings[$orderField] = ((strtolower($ascDesc) == 'desc') ?
                        QueryInterface::ORDER_DESCENDING :
                        QueryInterface::ORDER_ASCENDING);
                } else {
                    $orderings[$orderField] = QueryInterface::ORDER_ASCENDING;
                }
            }
        }

        return $orderings;
    }

    /**
     *  Returns the total number objects of this repository matching the demand.
     *
     * @param ForumDemand $demand
     * @return int
     * @throws \Exception
     */
    public function countDemanded(ForumDemand $demand)
    {
        $query = $this->createQuery();

        if ($constraints = $this->createConstraintsFromDemand($query, $demand)) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        $result = $query->execute();

        return $result->count();
    }
}
