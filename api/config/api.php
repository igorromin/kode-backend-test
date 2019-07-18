<?php

$db     = require(__DIR__ . '/../../config/db.php');

$config = [
    'id' => 'basic',
    'name' => 'api',
    // Need to get one level up:
    'basePath' => dirname(__DIR__).'/..',
    'bootstrap' => ['log'],
    'aliases' => [
        '@main' => '@vendor/../web',
        '@mainweb' => '/web',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\api\modules\v1\Module',
        ],
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl' => '/api',
            'enableCsrfCookie' => false,
            'enableCsrfValidation' => false
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],

                    'logFile' => '@app/runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/user'],
                    'pluralize' => false,
                    'patterns' => [
                        'POST login' => 'login',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/post'],
                    'extraPatterns' => [
                        'PUT <id>/like' => 'like',
                    ],
                ],
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => 'json',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $data = $response->data;
                    // Error handle
                    $error = '';
                    if( ! $response->isSuccessful) {
                        if(isset($data['message'])) {
                            $error = $data['message'];
                        } elseif(isset(current($data)['message'])) {
                            $error = current($data)['message'];
                        }
                    }
                    $response->data = [
                        'status' => $response->isSuccessful,
                        'code' => $response->statusCode,
                        'error' => $error,
                    ];
                    if($response->isSuccessful) {
                        $response->data['data'] = $data;
                    }
                }
            },
        ],
        'db' => $db,
    ],
    'params' => $params,
];

return $config;