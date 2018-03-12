<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=pg;dbname=ww_dev',
    'username' => 'ww_user',
    'password' => 'ww_pw',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public'
        ]
    ],

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
