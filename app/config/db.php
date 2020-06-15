<?php

use yii\db\Connection;

$database = getenv('MYSQL_DATABASE') ?: 'parser';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_ROOT_PASSWORD') ?: 'verysecret';

return [
    'class' => Connection::class,
    'dsn' => "mysql:host=mysql;dbname={$database}",
    'username' => $user,
    'password' => $password,
    'charset' => 'utf8',
    'enableProfiling' => true,
    'enableLogging' => true,
    // Schema cache options (for production environment)
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 6000,
    'schemaCache' => 'cache',

    'enableQueryCache'=>true,
    'queryCacheDuration'=>200,
];
