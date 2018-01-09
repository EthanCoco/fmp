<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
Yii::$classMap['PHPZip'] = '@app/libs/zip.php';
Yii::$classMap['ValidateCode'] = '@app/libs/validateCode/ValidateCode.class.php';
Yii::$classMap['TCPDF'] = '@app/libs/tcpdf/tcpdf.php';

$config = [
    'id' => 'formhelp',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','formdsn'],
    'defaultRoute' => 'index/index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'f17aaabc20bfe045075927934fed52d2',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
//      'urlManager' => [
//          'enablePrettyUrl' => true,
//          'showScriptName' => false,
//          'rules' => [
//          ],
//      ],
		'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'suffix'=>'.html',
            'rules' => [
				'/' => '/index/index',
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                "<controller:\w+>/<action:\w+>"=>"<controller>/<action>",
            ],
        ],
    ],
    
    'params' => $params,
    
	'modules' => [
        'formdsn' => [
            'class' => 'app\modules\formdsn\Module',
//          'allowedIPs' => ['*'],
        ],
    ],
    
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
          'allowedIPs' => ['127.0.0.1', '::1','192.168.144.132'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
          'allowedIPs' => ['*'],
    ];
}

return $config;
