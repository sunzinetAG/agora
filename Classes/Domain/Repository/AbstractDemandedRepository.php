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

use AgoraTeam\Agora\Domain\Model\DemandInterface;
use AgoraTeam\Agora\Domain\Repository\DemandedRepositoryInterface;

/**
 * Abstract demanded repository
 */
abstract class AbstractDemandedRepository extends Repository implements DemandedRepositoryInterface
{
     * Returns an array of constraints created from a given demand object.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     * @abstract
     */
    abstract protected function createConstraintsFromDemand(
        \TYPO3\CMS\Extbase\Persistence\QueryInterface $query,
        DemandInterface $demand
    );

    /**
     * Returns an array of orderings created from a given demand object.
     *
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     * @abstract
     */
    abstract protected function createOrderingsFromDemand(DemandInterface $demand);

    /**
     * Returns the objects of this repository matching the demand.
     *
     * @param DemandInterface $demand
     * @param bool $respectEnableFields
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findDemanded(DemandInterface $demand, $respectEnableFields = true)
    {
        $query = $this->generateQuery($demand, $respectEnableFields);

        return $query->execute();
    }

    /**
     * @param DemandInterface $demand
     * @param bool $respectEnableFields
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    protected function generateQuery(DemandInterface $demand, $respectEnableFields = true)
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
     * Returns the total number objects of this repository matching the demand.
     *
     * @param DemandInterface $demand
     * @return int
     */
    public function countDemanded(DemandInterface $demand)
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
