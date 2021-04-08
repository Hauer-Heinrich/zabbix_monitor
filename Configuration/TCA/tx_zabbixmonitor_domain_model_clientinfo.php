<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:zabbix_monitor/Resources/Private/Language/locallang_db.xlf:title',
        'label' => 'title',
        'label_alt' => 'api_url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,api_url',
        'iconfile' => 'EXT:zabbix_monitor/Resources/Public/Icons/tx_zabbixmonitor_domain_model_clientinfo.gif',
        'hideAtCopy' => true,
    ],
    'palettes' => [
        'api' => [
            'showitem' => 'api_url, api_key'
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                hidden,
                title,
                --palette--;;api,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                    starttime,
                    endtime
            '
        ],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'tstamp' => [
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'description' => 'also used for validThrough date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:zabbix_monitor/Resources/Private/Language/locallang_db.xlf:tca.field.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'api_url' => [
            'exclude' => false,
            'label' => 'LLL:EXT:zabbix_monitor/Resources/Private/Language/locallang_db.xlf:tca.field.api_url',
            'description' => 'LLL:EXT:zabbix_monitor/Resources/Private/Language/locallang_db.xlf:tca.field.api_url.description',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'trim,uniqueInPid,required',
                'autocomplete' => true,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkOptions' => 'mail,page,spec,folder,telephone,file',
                            'blindLinkFields' => 'class,params,target,title',
                        ],
                    ],
                ],
            ],
        ],
        'api_key' => [
            'exclude' => false,
            'label' => 'LLL:EXT:zabbix_monitor/Resources/Private/Language/locallang_db.xlf:tca.field.api_key',
            'description' => 'LLL:EXT:zabbix_monitor/Resources/Private/Language/locallang_db.xlf:tca.field.api_key.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],

    ],
];
