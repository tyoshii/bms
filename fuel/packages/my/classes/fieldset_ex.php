<?php

namespace My;

class FieldsetEX extends \Fuel\Core\Fieldset
{
	public static function reset()
	{
		parent::$_instances = array();
	}
}
