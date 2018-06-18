<?php

namespace AgoraTeam\Agora\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *  (c) 2017 Philipp Thiele <philipp.thiele@phth.de>
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
use AgoraTeam\Agora\Domain\Model\User;
use Doctrine\DBAL\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TaskRepository
 *
 * @package AgoraTeam\Agora\Domain\Repository
 */
class NotificationRepository extends Repository
{

    // Order by BE sorting
    protected $defaultOrderings = array(
        'tstamp' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );

    /**
     * @param User|int $owner
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByOwner($owner)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('owner', $owner),
                $query->equals('sent', 0)
            )
        );

        return $query->execute();
    }

    /**
     * @param $limit
     * @return array
     */
    public function findUserListFromNotifcationsByLimit($limit)
    {
        // We need to use the queryBuilder because we want to group the notifications by users
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_agora_domain_model_notification'
        );

        $statement = $queryBuilder
            ->select('owner')
            ->from('tx_agora_domain_model_notification')
            ->where(
                $queryBuilder->expr()->eq('sent', 0)
            )
            ->groupBy('owner')
            ->setMaxResults($limit)
            ->execute()->fetchAll();

        return $statement;
    }
}
