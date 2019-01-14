<?php
defined('TYPO3_MODE') || die();

call_user_func(function ($table) {
    $llPrefix = 'LLL:EXT:access_restriction/Resources/Private/Language/locallang_be.xlf:';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
        'tx_accessrestriction_restrictions' => [
            'exclude' => true,
            'label'   => $llPrefix . 'tx_accessrestriction_restrictions',
            'config'  => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ],
        ],
    ]);

    $feGroupTCA = &$GLOBALS['TCA']['fe_groups'];
    if (isset($feGroupTCA['ctrl']['type']) && $feGroupTCA['ctrl']['type'] === 'tx_extbase_type') {

        // add our own groups type to TCA item list
        $feGroupTCA['columns']['tx_extbase_type']['config']['items'][] = [
            $llPrefix . 'recordtype',
            'access_restriction',
        ];

        // define our own show item type list
        $feGroupTCA['types']['access_restriction']['showitem'] = '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                tx_extbase_type, title, tx_accessrestriction_restrictions,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                hidden,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description
        ';
    } else {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'fe_groups',
            'tx_accessrestriction_restrictions'
        );
    }

}, 'fe_groups');

