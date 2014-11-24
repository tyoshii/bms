<?php

Autoloader::add_core_namespace('Test');

Autoloader::add_classes(array(
	'Test\\Response' => __DIR__ . '/classes/test.php',
	'Test\\TestException' => __DIR__ . '/classes/test.php',
	'Test\\InputEx' => __DIR__ . '/classes/input_ex.php',
	'Test\\FieldsetEx' => __DIR__ . '/classes/fieldset_ex.php',
));
