<?php

return array(
  'default' => array(
    'connection'  => array(
      'host'       => 'bm-s.info',
      'port'       => '3306',
      'database'   => 'bms',
      'username'   => 'root',
      'password'   => Config::get('password.mysql.production.root'),
    ),
    'backup'  => array(
      'username'   => 'bms_backup',
    ),
  ),
);
