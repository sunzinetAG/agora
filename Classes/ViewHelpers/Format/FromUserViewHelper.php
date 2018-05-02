<?php

namespace AgoraTeam\Agora\ViewHelpers\Format;

/*  | This extension is part of the TYPO3 project. The TYPO3 project is
 *  | free software and is licensed under GNU General Public License.
 *  |
 *  | (c) 2018 Björn Bresser <bjoern.bresser@sunzinet.com>
 */
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Formats a unix timestamp to a human-readable, relative string
 *
 * @package Sunzinet\SzNotification
 */
class FromUserViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var bool $dateIsAbsolute
     */
    protected $dateIsAbsolute = false;

    /**
     * Render the supplied unix timestamp in a localized human-readable string.
     *
     * @param int $user The user Id
     * @param array $amount notifications
     * @return string Formated text
     */
    public function render($user, $amount = [])
    {
        $counter = count($amount) - 1;
        $string = '';
        foreach ($amount as $v) {
            if ($v->getUser() == $user) {
                $counter = $counter - 1;
            }
        }

        $username = $this->getUserByUid($user);
        if (!$username) {
            return $string;
        }
        $fromTranslation = LocalizationUtility::translate('tx_agora.from', 'Agora');
        if ($amount == 1) {
            $translation = LocalizationUtility::translate('tx_agora.oneAdditionalPerson', 'Agora');
            $string = $username . ' ' . $translation;
        } elseif ($counter >= 2) {
            $translation = LocalizationUtility::translate(
                'tx_agora.multiAdditionalPerson',
                'Agora',
                [0 => $counter]
            );
            $string = $username . ' ' . $translation;
        } else {
            $string = $username;
        }

        return $fromTranslation . $string;
    }

    /**
     * @param $user
     */
    private function getUserByUid($user)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');
        $userName = $queryBuilder->select('username')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq('uid', $user)
            )->execute()->fetchColumn(0);

        return $userName;
    }
}
