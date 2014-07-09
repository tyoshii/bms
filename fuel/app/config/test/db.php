<?php

return array(
	'default' => array(
      'connection'  => array(
      'host'       => 'localhost',
      'port'       => '3306',
			'dsn'        => 'mysql:host=localhost;dbname=bms_test;port=3306',
			'username'   => 'root',
			'password'   => Config::get('password.mysql.test.root'),
		),
	),
);
