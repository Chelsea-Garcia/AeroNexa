<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        // 1. DEFAULT MYSQL (System tables, Users, etc.)
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'aeronexa_main', // Or your main DB name
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ],

        // 2. AURELIYA (Hotels) - MYSQL
        'aureliya' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'aureliya', // MUST MATCH NAVICAT NAME
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ],

        // 3. GENERIC MONGODB (The Fix for Disappearing List)
        'mongodb' => [
            'driver' => 'mongodb',
            'host' => '127.0.0.1',
            'port' => 27017,
            'database' => 'philippineskyairway',
            'username' => '',
            'password' => '',
            'options' => ['database' => 'admin'],
        ],

        // 4. PSA (Flights) - MONGODB (Specific)
        'mongodb_psa' => [
            'driver' => 'mongodb',
            'host' => '127.0.0.1',
            'port' => 27017,
            'database' => 'philippineskyairway',
            'username' => '',
            'password' => '',
            'options' => ['database' => 'admin'],
        ],

        // 5. SKYROUTE (Transport) - MONGODB
        'mongodb_skyroute' => [
            'driver' => 'mongodb',
            'host' => '127.0.0.1',
            'port' => 27017,
            'database' => 'skyroute',
            'username' => '',
            'password' => '',
        ],

        // TRUTRAVEL (PACKAGES) - MYSQL
        'trutravel' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'trutravel', // Must match your Navicat database name
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ],

        // 6. AEROPAY - MYSQL
        'aeropay' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'aeropay',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],

    // ... Standard Laravel Settings ...
    'migrations' => ['table' => 'migrations', 'update_date_on_publish' => true],
    'redis' => [
        'client' => 'phpredis',
        'options' => ['cluster' => 'redis', 'prefix' => 'laravel_database_'],
        'default' => ['url' => env('REDIS_URL'), 'host' => '127.0.0.1', 'port' => '6379', 'database' => '0'],
        'cache' => ['url' => env('REDIS_URL'), 'host' => '127.0.0.1', 'port' => '6379', 'database' => '1'],
    ],
    'options' => [
        'mongodb' => [
            'use_mongo_id' => true,
        ]
    ],
];