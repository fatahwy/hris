<?php

$env = require __DIR__ . '/env.php';

return [
    'class' => 'yii\db\Connection',
    'charset' => 'utf8',

    'dsn' => 'pgsql:host=' . $env['db_host'] . ';port=5432;dbname=' . $env['db_name'],
    'username' => $env['db_username'],
    'password' => $env['db_password'],

    'attributes' => [
        PDO::ATTR_PERSISTENT => true,
    ],

    // Schema cache options (for production environment)
    // 'enableSchemaCache' => true,
    // 'schemaCacheDuration' => 3600,
    // 'schemaCache' => 'cache',
];