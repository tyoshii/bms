<?php

return array(
		'default' => array(
				'connection' => array(
						'host'     => 'localhost',
						'port'     => '3306',
						'database' => 'bms',
						'username' => 'root',
						'password' => Config::get('password.mysql.development.root'),
				),
		),
);
