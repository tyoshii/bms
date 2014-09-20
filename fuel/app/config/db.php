<?php

return array(
    'default' => array(
        'connection' => array(
            'dsn'      => 'mysql:host=localhost;dbname=bms;port=3306',
            'username' => 'root',
            'password' => Config::get('password.mysql'),
        ),
    ),
);
