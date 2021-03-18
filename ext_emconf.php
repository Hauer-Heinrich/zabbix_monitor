<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "zabbix_monitor"
 *
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['zabbix_monitor'] = [
    'title' => 'TYPO3 Zabbix Monitor',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Christian Hackl',
    'author_email' => 'hackl.chris@googlemail.com',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4',
            'fluid_styled_content' => '10.4'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\ZabbixMonitor\\' => 'Classes',
        ],
    ],
];
