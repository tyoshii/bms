<?php

Autoloader::add_core_namespace('Agent');

Autoloader::add_classes(array(
	'Agent\\Agent' => __DIR__ . '/classes/agent.php',
	'Agent\\AgentException' => __DIR__ . '/classes/agent.php',
));
