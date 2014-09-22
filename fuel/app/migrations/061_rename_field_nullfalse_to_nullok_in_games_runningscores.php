<?php

namespace Fuel\Migrations;

class Rename_field_nullfalse_to_nullok_in_games_runningscores
{
	public function up()
	{
		$param = array('name' => '', 'type' => 'tinyint unsigned', 'null' => true);

		$fields = array();
		for ($i = 1; $i <= 18; $i++)
		{
			$param['name'] = 't'.$i;
			$fields['t'.$i] = $param;

			$param['name'] = 'b'.$i;
			$fields['b'.$i] = $param;
		}

		\DBUtil::modify_fields('games_runningscores', $fields);
	}

	public function down()
	{
		$param = array('name' => '', 'type' => 'tinyint unsigned');

		$fields = array();
		for ($i = 1; $i <= 18; $i++)
		{
			$param['name'] = 't'.$i;
			$fields['t'.$i] = $param;

			$param['name'] = 'b'.$i;
			$fields['b'.$i] = $param;
		}

		\DBUtil::modify_fields('games_runningscores', $fields);
	}
}
