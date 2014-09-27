<?php

namespace Fuel\Migrations;

class Add_last_inning_to_games_runningscores
{
	public function up()
	{
		\DBUtil::add_fields('games_runningscores', array(
			'last_inning' => array('type' => 'tinyint', 'unsigned' => true),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games_runningscores', array(
			'last_inning'

		));
	}
}
