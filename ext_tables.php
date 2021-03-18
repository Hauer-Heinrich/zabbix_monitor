<?php
defined('TYPO3_MODE') || die();

call_user_func(static function() {
    $extension = 'zabbix_monitor';
    $extensionname = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extension);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_zabbixmonitor_domain_model_clientinfo', 'EXT:zabbix_monitor/Resources/Private/Language/locallang_csh_tx_zabbixmonitor_domain_model_clientinfo.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_zabbixmonitor_domain_model_clientinfo');

});
