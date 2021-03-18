<?php

call_user_func(function () {
    $extension = 'zabbix_monitor';
    $extensionname = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extension);
    $plugin = 'Listview';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        $extensionname,
        $plugin,
        'Zabbix Monitor List',
        'EXT:zabbix_monitor/Resources/Public/Icons/Extension.png'
    );
});
