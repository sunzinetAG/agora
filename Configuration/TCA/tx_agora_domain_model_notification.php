<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$lll = 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => array(
        'title' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_notification',
        'label' => 'crdate',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'hideTable' => true,
        'versioning_followPages' => true,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'answer,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('agora') .
            'Resources/Public/Icons/tx_agora_domain_model_notification.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, data, sent',
    ),
    'types' => array(
        '1' => array(
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, data,
		sent, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'
        ),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(

        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
                ),
            ),
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_agora_domain_model_notification',
                'foreign_table_where' => 'AND tx_agora_domain_model_notification.pid=###CURRENT_PID### AND tx_agora_domain_model_notification.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),

        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),

        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'crdate' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_notification.crdate',
            'config' => array(
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date'
            )
        ),
        'tstamp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_notification.tstamp',
            'config' => array(
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date'
            )
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_notification.type',
            'config' => array(
                'type' => 'input',
                'size' => 5,
                'eval' => 'trim'
            ),
        ),
        'sent' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_notification.sent',
            'config' => array(
                'type' => 'input',
                'size' => 5,
                'eval' => 'trim',
                'readOnly' => true
            ),
        ),
        'thread' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.thread',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'tx_agora_domain_model_thread',
                'size' => 1
            ),
        ),
        'page' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.page',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'post' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.post',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'tx_agora_domain_model_post',
                'size' => 1
            ),
        ),
        'user' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.user',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'fe_users',
                'size' => 1
            ),
        ),
        'owner' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.owner',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'fe_users',
                'size' => 1
            ),
        ),
        'title' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.title',
            'config' => array(
                'type' => 'select',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'link' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.link',
            'config' => array(
                'type' => 'select',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.description',
            'config' => array(
                'type' => 'text',
            ),
        ),
        'data' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_notification.data',
            'config' => array(
                'type' => 'text',
            ),
        ),
    ),
];
