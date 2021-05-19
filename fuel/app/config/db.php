<?php

return array(
	'default' => array(
		'connection' => array(
			'dsn'      => 'mysql:host=127.0.0.1;dbname=bms;port=3306',
			'username' => 'root',
			'password' => Config::get('password.mysql'),
		),
	),
);
