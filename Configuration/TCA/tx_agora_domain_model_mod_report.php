<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_mod_report',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'searchFields' => '',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('agora') .
            'Resources/Public/Icons/tx_agora_domain_model_mod_report.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, feuser, post, type, reporter, text',
    ),
    'types' => array(
        '1' => array(
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource;;1,
                            feuser, post, type, reporter, text'
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
                'foreign_table' => 'tx_agora_domain_model_mod_report',
                'foreign_table_where' => 'AND tx_agora_domain_model_mod_report.pid=###CURRENT_PID### AND tx_agora_domain_model_mod_report.sys_language_uid IN (-1,0)',
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

        'post' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_mod_report.post',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'readOnly' => 1,
                'foreign_table' => 'tx_agora_domain_model_post'
            ),
        ),
        'feuser' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_mod_report.feuser',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'fe_users'
            ),
        ),
        'reporter' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_mod_report.reporter',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'fe_users'
            ),
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_mod_report.type',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ),
        ),
        'text' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:agora/Resources/Private/Language/locallang_db.xlf:tx_agora_domain_model_mod_report.text',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ),
        )
    ),
);
