<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'zh-CN',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => require __DIR__ . '/main/modules.php',
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => 'redisCache',
        ],
        'config' => [ 
            'class'        => 'cando\config\Config',
            'dir'          => 'config/config.d',
            'systemConfig' => [
                'modelClass' => 'core\models\Config',
                'filename'   => 'system',
            ],
        ],
        'linkable' => [
            'class' => 'cando\link\Manager',
        ],
        'storage' => [
            'class'   => 'cando\storage\local\Manager',
            'path'    => '@media',
            'baseUrl' => '@mediaUrl',
        ],
        'authManager' => [
            'class' => 'cando\rbac\DbManager',
        ],
        'formatter' => [
            'class'           => 'yii\i18n\Formatter',
            'timeZone'        => 'Asia/Shanghai',
            'defaultTimeZone' => 'Asia/Shanghai',
            'datetimeFormat'  => 'php:Y-m-d H:i:s',
            'dateFormat'      => 'php:Y-m-d',
            'timeFormat'      => 'php:H:i:s',
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@locale',
                ],
                '*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@locale',
                ],
            ],
        ],
        'helper' => [
            'class' => 'cando\components\Helper',
            'helpers' => [
                'hash' => [
                    'class' => 'core\helpers\HashHelper',
                    'singleton' => true,
                ],
            ],
        ],
    ],
];
