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

/**
 * Class ForumRepository
 *
 * @author Björn Bresser <bjoern.bresser@gmail.com>
 * @author Philipp Thiele <philipp.thiele@phth.de>
 * @package AgoraTeam\Agora\Domain\Repository
 */
class ForumRepository extends Repository
{

    /**
     * Find forums that have no parent and are therefore root forums
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findRootForums()
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('parent', 0)
        );
        $query->setOrderings(array('crdate' => QueryInterface::ORDER_DESCENDING));
        $forums = $query->execute();

        return $forums;
    }

    /**
     * Function findAccessibleUserForums
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAccessibleUserForums()
    {
        $constraints = array();
        $user = $this->getUser();
        $query = $this->createQuery();

        $constraints[] = $query->logicalAnd(
            $query->equals('groups_with_read_access', 0),
            $query->equals('users_with_read_access', 0)
        );

        if (is_a($user, '\AgoraTeam\Agora\Domain\Model\User')) {
            // Get the allowed forums for the logged in user
            $flattenedGroups = $user->getFlattenedGroupUids();
            $constraints[] = $query->logicalOr(
                $query->contains('groupsWithReadAccess', $flattenedGroups),
                $query->contains('usersWithReadAccess', $user->getUid())
            );
        }
        if (count($constraints) > 1) {
            $permissionConstraint = $query->logicalOr($constraints);
        } else {
            $permissionConstraint = current($constraints);
        }

        $query->matching($permissionConstraint);
        $forums = $query->execute();

        return $forums;
    }

    /**
     * Function findForumsWithDifferentId
     * find forums that have different id comparing with the given one
     *
     * @param $forum
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findForumsWithDifferentId($forum)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalNot($query->equals('uid', $forum->getUid()))
        );
        $query->setOrderings(array('title' => QueryInterface::ORDER_DESCENDING));
        $forums = $query->execute();

        return $forums;
    }

    /**
     * Function findAccessibleUserForumsAsMatrix
     * find forums and childrens and generate matrix
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAccessibleUserForumsAsMatrix()
    {
        $allAccessibleForums = $this->findVisibleRootForums();
        $matrix = [];

        foreach ($allAccessibleForums as $key => $value) {
            $matrix[$key]['item'] = $value;
            $matrix[$key]['children'] = $this->findAccessibleUserForumsByParent($value->getUid());
        }
        return $matrix;

    }

    /**
     * Find forums that have no parent and are therefore root forums
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findVisibleRootForums()
    {

        $query = $this->createQuery();

        $constraints = array();

        if ($this->getUser()) {
            $groupConstraints = array();
            foreach ($this->getUser()->getFlattenedGroups() as $group) {
                $groupConstraints[] = $query->contains('groupsWithReadAccess', $group);
            }
            $constraints[] = $query->logicalOr(
                $groupConstraints
            );
            $constraints[] = $query->contains('usersWithReadAccess', $this->getUser());
        }

        $constraints[] = $query->logicalAnd(
            array(
                $query->equals('groupsWithReadAccess', 0),
                $query->equals('usersWithReadAccess', 0)
            )
        );

        if (count($constraints) > 1) {
            $permissionConstraint = $query->logicalOr($constraints);
        } else {
            $permissionConstraint = current($constraints);
        }

        $query->matching(
            $query->logicalAnd(
                array(
                    $permissionConstraint,
                    $query->equals('parent', 0)
                )
            )
        );
        $query->setOrderings(array('crdate' => QueryInterface::ORDER_DESCENDING));
        $forums = $query->execute();

        return $forums;
    }

    /**
     * Function findAccessibleUserForumsByParent
     * find forums of specific parent
     * @param int $parent
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAccessibleUserForumsByParent($parent)
    {
        $constraints = array();
        $user = $this->getUser();
        $query = $this->createQuery();

        $constraints[] = $query->logicalAnd(
            $query->equals('groups_with_read_access', 0),
            $query->equals('users_with_read_access', 0),
            $query->equals('parent', $parent)
        );

        if (is_a($user, '\AgoraTeam\Agora\Domain\Model\User')) {
            // Get the allowed forums for the logged in user
            $flattenedGroups = $user->getFlattenedGroupUids();
            $constraints[] = $query->logicalOr(
                $query->contains('groupsWithReadAccess', $flattenedGroups),
                $query->contains('usersWithReadAccess', $user->getUid())
            );
        }
        if (count($constraints) > 1) {
            $permissionConstraint = $query->logicalOr($constraints);
        } else {
            $permissionConstraint = current($constraints);
        }

        $query->matching($permissionConstraint);
        $forums = $query->execute();

        return $forums;
    }
}
