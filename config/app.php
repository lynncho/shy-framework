<?php

return [

    /*
    | Base Url
    |
    | main domain. (optional)
    */

    'base_url' => '',

    /*
    | Environment
    |
    | for error reporting.
    | options: development, production.
    */

    'env' => 'development',

    /*
    | Time Zone
    |
    | for time() date()...
    | options: PRC, Asia/Shanghai, Asia/Tokyo...
    */

    'timezone' => 'PRC',

    /*
    | Default language
    */

    'default_lang' => 'zh-cn',

    /*
    | WorkerMan Http
    */

    'worker_man_http' => [
        'port' => 2348,
        'worker' => 2
    ],

    /*
    | WorkerMan Socket
    */
    'worker_man_socket' => [
        'chat' => [
            'address' => 'websocket://127.0.0.1:2349',
            'worker' => 2,
            'onConnect' => ['app\socket\example' => 'onConnect'],
            'onMessage' => ['app\socket\example' => 'onMessage'],
            'onClose' => ['app\socket\example' => 'onClose']
        ],
    ],

    /*
    | Slow Log
    */

    'slow_log' => true,

    /*
    | Slow Log Limit (second)
    */

    'slow_log_limit' => 1,

    /*
    | Route By Config
    */

    'route_by_config' => true,

    /*
    | Route By Path
    */

    'route_by_path' => true,

    /*
    | Default Controller
    */

    'default_controller' => 'home',

    /*
    | Illuminate Database
    */

    'illuminate_database' => true,

    /*
    | Smarty
    */

    'smarty' => true,

    /*
    | Smarty Config
    */

    'smarty_config' => [
        'left_delimiter' => '',
        'right_delimiter' => '',
        'caching' => false,
        'cache_lifetime' => 120 //seconds
    ]

];
