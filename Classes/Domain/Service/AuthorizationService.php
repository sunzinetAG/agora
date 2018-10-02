<?php

namespace AgoraTeam\Agora\Domain\Service;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class AuthorizationService
 *
 * @package AgoraTeam\Agora\Domain\Service
 */
class AuthorizationService implements SingletonInterface
{
    /**
     * Determines whether the currently logged in FE user has writable access
     *
     * @param string $userWritableAccessUid
     *
     * @return boolean TRUE if the currently logged in
     * FE user has writable access
     */
    public function hasUserWritableAccess($userWritableAccessUid)
    {
        if (!in_array($userWritableAccessUid, $GLOBALS['TSFE']->fe_user->groupData['uid'])) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }

        return true;
    }
}
