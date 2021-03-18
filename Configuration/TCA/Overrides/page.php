<?php
defined('TYPO3_MODE') || die();

call_user_func(function() {

    $extensionKey = 'zabbix_monitor';

    // make PageTsConfig selectable
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
        $extensionKey,
        'Configuration/TsConfig/AllPage.typoscript',
        'Zabbix Monitor'
    );
});
