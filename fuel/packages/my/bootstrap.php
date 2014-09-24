<?php

Autoloader::add_core_namespace('My');

Autoloader::add_classes(array(
	'My\\Response' => __DIR__ . '/classes/my.php',
	'My\\MyException' => __DIR__ . '/classes/my.php',
	'My\\InputEx' => __DIR__ . '/classes/input_ex.php',
	'My\\FieldsetEx' => __DIR__ . '/classes/fieldset_ex.php',
));
