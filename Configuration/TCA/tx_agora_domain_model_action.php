<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$lll = 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => array(
        'title' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_action',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'answer,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('agora') .
            'Resources/Public/Icons/tx_agora_domain_model_action.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, description',
    ),
    'types' => array(
        '1' => array(
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,
             type, title, description, link, user, groups, 
             --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'
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
                'foreign_table' => 'tx_agora_domain_model_action',
                'foreign_table_where' => 'AND tx_agora_domain_model_action.pid=###CURRENT_PID### AND tx_agora_domain_model_action.sys_language_uid IN (-1,0)',
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
        'crdate' => array(
            'label' => 'crdate',
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
        'tstamp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_action.tstamp',
            'config' => array(
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date'
            )
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_action.type',
            'config' => array(
                'type' => 'input',
                'size' => 5,
                'readOnly' => true,
                'default' => \AgoraTeam\Agora\Service\Notification\NotificationService::USER_DEFINED,
                'eval' => 'trim'
            ),
        ),
        'sent' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_action.sent',
            'config' => array(
                'type' => 'input',
                'size' => 5,
                'eval' => 'trim',
                'readOnly' => true
            ),
        ),
        'thread' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.thread',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'tx_agora_domain_model_thread',
                'size' => 1
            ),
        ),
        'post' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.post',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'tx_agora_domain_model_post',
                'size' => 1
            ),
        ),
        'user' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.user',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', '0'],
                ],
                'foreign_table' => 'fe_users',
                'size' => 1
            ),
        ),
        'groups' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.user',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'size' => 5
            ),
        ),
        'page' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.page',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'count' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.count',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'title' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required'
            ),
        ),
        'hash' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.hash',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'link' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.link',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.description',
            'config' => array(
                'type' => 'text',
                'eval' => 'required'
            ),
        ),
        'data' => array(
            'exclude' => 1,
            'label' => $lll . 'tx_agora_domain_model_action.data',
            'config' => array(
                'type' => 'text',
            ),
        ),
    ),
];
