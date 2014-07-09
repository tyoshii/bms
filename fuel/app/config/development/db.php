<?php

return array(
	'default' => array(
		'connection'  => array(
      'host'       => 'localhost',
      'port'       => '3306',
			'dsn'        => 'mysql:host=localhost;dbname=bms;port=3306;unix_socket=/tmp/mysql.sock',
			'username'   => 'root',
			'password'   => Config::get('password.mysql.development.root'),
		),
	),
);
