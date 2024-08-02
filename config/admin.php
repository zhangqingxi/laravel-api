<?php

return [
    'event' => [
        /**
         * 模型观察者.
         */
        'observers' => [
            \App\Models\Admin\Admin::class,
            \App\Models\Admin\Menu::class,
            \App\Models\Admin\Role::class,
            \App\Models\Admin\File::class,
            \App\Models\Admin\RecycleBin::class,
        ],
    ],

    'route_prefix' => env('ADMIN_URL_PREFIX', 'admin'),
    'url' => env('ADMIN_URL', ''),
    'enable_encryption' => env('ADMIN_ENABLE_ENCRYPTION', true), // 是否启用加密'
    'ws' => [
        'url' => env('ADMIN_WS_URL', ''),
        'port' => env('ADMIN_WS_PORT', ''),
    ],
    'login' => [
        // 最大尝试登录次数，超过这个次数将导致账户被锁定
        'max_attempts' => 5,
        // 账户锁定时间，单位为分钟。在锁定时间内，用户无法尝试登录
        'lockout_time' => 15,
        // 登录令牌的过期时间，单位为分钟。登录令牌用于验证用户身份
        'token_expiration' => 24 * 60 * 60,
        // 免登录
        'no_login' => (7 * 24 * 60) + 60,
    ],

    'encryption' => [
        'rsa_public_key' => storage_path('keys/admin_public.pem'),
        'rsa_private_key' => storage_path('keys/admin_private.pem'),
    ],

    'request' => [
        // 最大请求次数限制，用于防止过多的请求导致服务器负担过重
        'max_requests' => 60,
        // 请求频率过期时间，单位为分钟，用于监控单位时间内请求上限
        'time_limit' => 1,
        // 请求的过期时间，单位为分钟，用于清理过期的请求记录
        'expired_time' => 10,
        // 同一请求的最小间隔时间，单位为秒，用于防止过于频繁的相同请求
        'duplicate_time' => 30,
    ],

    'mail' => [
        // 验证码过期时间，单位为分钟
        'code_expiration' => 5,
        // 验证码长度
        'code_length' => 6,
        // 验证码缓存键
        'code_key' => 'mail_verify_code',
    ]
];
