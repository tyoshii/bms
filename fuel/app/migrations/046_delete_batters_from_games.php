<?php

namespace Fuel\Migrations;

class Delete_batters_from_games
{
	public function up()
	{
		\DBUtil::drop_fields('games', array(
				'batters'

		));
	}

	public function down()
	{
		\DBUtil::add_fields('games', array(
				'batters' => array('type' => 'text'),

		));
	}
}