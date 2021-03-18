<?php
defined('TYPO3_MODE') || die();

call_user_func(function() {

    $extensionKey = 'zabbix_monitor';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'Zabbix Monitor'
    );
});
