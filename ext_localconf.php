<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function() {
    $extension = 'zabbix_monitor';
    $extensionname = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extension);
    $plugin = 'Listview';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionname,
        $plugin,
        [
            // Allowed Controllers and there Actions = ControllerName => Actions
            \HauerHeinrich\ZabbixMonitor\Controller\Typo3InformationController::class => 'list, show'
        ],
        // non-cacheable actions
        [
            // ControllerName => Actions
            \HauerHeinrich\ZabbixMonitor\Controller\Typo3InformationController::class => 'list, show'
        ]
    );

    // Register custom cache
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['zabbixmonitor_apidata'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['zabbixmonitor_apidata'] = [];
    }

    // Add configuration for the logging API
    $GLOBALS['TYPO3_CONF_VARS']['LOG'][$extensionname]['Controller']['writerConfiguration'] = [
        // configuration for ERROR level log entries
        \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
            // add a FileWriter
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                // configuration for the writer
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/typo3_examples.log',
            ]
        ],

        \TYPO3\CMS\Core\Log\LogLevel::WARNING => [
            // add a SyslogWriter
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                // configuration for the writer
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/typo3_examples.log',
            ]
        ],
    ];
});
