<?php

return array(
    'default' => array(
        'connection' => array(
            'host'     => 'bm-s.info',
            'port'     => '3306',
            'dsn'      => 'mysql:host=bm-s.info;dbname=bms;port=3306',
            'username' => 'root',
            'password' => Config::get('password.mysql.staging.root'),
        ),
    ),
);
