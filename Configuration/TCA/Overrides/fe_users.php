<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tmpAgoraColumns = array(
    'signiture' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_user.signiture',
        'config' => array(
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim'
        )
    ),
    'posts' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_user.posts',
        'config' => array(
            'type' => 'inline',
            'foreign_table' => 'tx_agora_domain_model_post',
            'foreign_field' => 'creator',
            'maxitems' => 9999,
            'appearance' => array(
                'collapseAll' => 1,
                'levelLinksPosition' => 'top',
                'showSynchronizationLink' => 1,
                'showPossibleLocalizationRecords' => 1,
                'showAllLocalizationLink' => 1
            ),
        ),

    ),
    'favorite_posts' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_user.favorite_posts',
        'config' => array(
            'type' => 'select',
            'foreign_table' => 'tx_agora_domain_model_post',
            'MM' => 'tx_agora_feuser_post_mm',
            'size' => 5,
            'minitems' => 0,
            'maxitems' => 9999,
            'renderMode' => 'checkbox',
        ),
    ),
    'observed_threads' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_thread.user',
        'config' => array(
            'type' => 'select',
            'foreign_table' => 'tx_agora_domain_model_thread',
            'MM' => 'tx_agora_feuser_thread_mm',
            'size' => 5,
            'minitems' => 0,
            'maxitems' => 9999,
            'renderMode' => 'checkbox',
        ),
    ),

    'view' => array(
        'config' => array(
            'type' => 'passthrough',
        )
    )
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmpAgoraColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;Agora,signiture'
);
