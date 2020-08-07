<?php

return [
    'debug' => env('SMS_DEBUG', false),

    // 模板 ID
    'template' => [
        'register' => 'SMS_198927244',
    ],

    // EasySms 构造函数对应参数
    'config' => [
        // HTTP 请求的超时时间（秒）
        'timeout' => 5.0,

        // 默认发送配置
        'default' => [
            // 网关调用策略，默认：顺序调用
            'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

            // 默认可用的发送网关
            'gateways' => [
                'aliyun',
            ],
        ],
        // 可用的网关配置
        'gateways' => [
            'errorlog' => [
                'file' => storage_path('logs/easy-sms.log'),
            ],
            'aliyun' => [
                'access_key_id' => env('SMS_ALIYUN_ACCESSKEY_ID'),
                'access_key_secret' => env('SMS_ALIYUN_ACCESSKEY_SECRET'),
                'sign_name' => 'Lara02',
            ],
            //...
        ],
    ],
];