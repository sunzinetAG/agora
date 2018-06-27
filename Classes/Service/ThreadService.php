<?php

namespace AgoraTeam\Agora\Service;

/***************************************************************
 *  Copyright notice
 *  (c) 2018 Björn Christopher Bresser <bjoern.bresser@gmail.com>
 *           Fabian Staake <fabian.staake@sunzinet.com>
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
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use AgoraTeam\Agora\Domain\Model\Thread;
use AgoraTeam\Agora\Domain\Model\User;

/**
 * Class ThreadService
 *
 * @package AgoraTeam\Agora\Service
 */
class ThreadService implements SingletonInterface
{

    /**
     * Mark a thread as read by a user.
     *
     * @param Thread $thread
     * @param User $user
     */
    public function markAsRead(Thread $thread, User $user)
    {
        if ($this->isRead($thread, $user)) {
            $this->updateReadThreadTimestamp($thread, $user);
        } else {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tx_agora_readthreads');
            $queryBuilder->insert('tx_agora_readthreads')
                ->values([
                    'uid_feuser' => $user->getUid(),
                    'uid_thread' => $thread->getUid(),
                    'timestamp' => time()
                ])
                ->execute();
        }
    }

    /**
     * Check whether a thread was read by a user.
     *
     * @param Thread $thread The thread
     * @param User $user The user
     * @return bool
     */
    public function isRead(Thread $thread, User $user)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_agora_readthreads');
        $count = $queryBuilder->count('*')
            ->from('tx_agora_readthreads')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_feuser',
                    $queryBuilder->createNamedParameter($user->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'uid_thread',
                    $queryBuilder->createNamedParameter($thread->getUid(), \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchColumn(0);
        return $count > 0;
    }

    /**
     * Check whether the latest update of a thread was read by a user.
     *
     * @param Thread $thread
     * @param User $user
     * @return bool
     */
    public function isLatestThreadUpdateRead(Thread $thread, User $user)
    {
        $latestPost = $thread->getLatestPost();
        $latestUpdate = $latestPost ? $latestPost->getCrdate() : $thread->getCrdate();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_agora_readthreads');
        $count = $queryBuilder->count('*')
            ->from('tx_agora_readthreads')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_feuser',
                    $queryBuilder->createNamedParameter($user->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'uid_thread',
                    $queryBuilder->createNamedParameter($thread->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->gte(
                    'timestamp',
                    $queryBuilder->createNamedParameter($latestUpdate, \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchColumn(0);

        return $count > 0;
    }

    /**
     * Update the timestamp of a read thread.
     *
     * @param Thread $thread
     * @param User $user
     */
    protected function updateReadThreadTimestamp(Thread $thread, User $user)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_agora_readthreads');
        $queryBuilder->update('tx_agora_readthreads')
            ->set('timestamp', time())
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_feuser',
                    $queryBuilder->createNamedParameter($user->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'uid_thread',
                    $queryBuilder->createNamedParameter($thread->getUid(), \PDO::PARAM_INT)
                )
            )
            ->execute();
    }
}
