<?php

use kartik\export\ExportMenu;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use kartik\mpdf\Pdf;
use yii\bootstrap5\LinkPager as LinkPager5;
use yii\widgets\LinkPager;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$env = require __DIR__ . '/env.php';

$name = 'PRANATA HR';
$config = [
    'id' => 'basic',
    'name' => $name,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'admin', 'queue'],
    'language' => 'id',
    'timezone' => 'Asia/Jakarta',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu', // avaliable value 'left-menu', 'right-menu' and 'top-menu'
            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'app\models\User',
                    'idField' => 'id_user'
                ]
            ],
            'menus' => [
                'assignment' => [
                    'label' => 'Grand Access' // change label
                ],
                // 'route' => null, // disable menu
            ],
            'aliases' => [
                '@mdm/admin/views/layouts' => '@vendor/mdmsoft/yii2-admin/views/layouts',
            ],
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
            'bsVersion' => 5,
            // enter optional module parameters below - only if you need to  
            // use your own export download action or custom translation 
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'asdj12nkasdu1289asdh1283y7as8das7d8asdb1h2jvd7612391326$$%%##@@',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'authTimeout' => 3600,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@runtime/queue',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'scheme' => 'smtp',
                'host' => $env['mail_host'],
                'username' => $env['mail_username'],
                'password' => $env['mail_password'],
                'port' => 465,
                'encryption' => 'ssl',
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 100,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'exportInterval' => 100
                ],
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' => 'mailer',
                    'levels' => ['error'],
                    // 'categories' => [
                    //     'yii\db\*',
                    //     'yii\web\HttpException:500',
                    // ],
                    'except' => [
                        'yii\web\HttpException:400', // bad request
                        'yii\web\HttpException:403', // forbidden
                        'yii\web\HttpException:404', // not found
                        'yii\web\HttpException:405', // method not allowed
                    ],
                    'message' => [
                        'from' => [$params['senderEmail'] => $name],
                        'to' => [$params['adminEmail']],
                        'subject' => "$name : Error Log",
                    ],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login' => 'site/login',
                'register' => 'site/register',
                'forgot-password' => 'site/forgot-password',
                'new-password' => 'site/new-password',
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '-',
            'defaultTimeZone' => 'Asia/Jakarta',
            'dateFormat' => 'php:d F Y',
            'datetimeFormat' => 'php:d F Y H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'Rp.',
            'locale' => 'id-ID',
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 2,
                NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
            'cache' => 'cache',
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'debug/*',
            'site/*',
            'gii/*',
            'admin/*'
        ]
    ],
    'params' => $params,
    'container' => [
        'definitions' => [
            GridView::class => $params['gridConfig'],
            ExportMenu::class => $params['exportConfig'],
            LinkPager::class => LinkPager5::class,
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

Yii::$container->set(ActiveForm::class, [
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);

Yii::$container->set(\yii\widgets\DetailView::class, [
    'options' => ['class' => 'table table-hover table-borderless table-modern-detail'],
]);

return $config;