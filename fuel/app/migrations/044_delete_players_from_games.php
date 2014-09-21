<?php

namespace Fuel\Migrations;

class Delete_players_from_games
{
	public function up()
	{
		\DBUtil::drop_fields('games', array(
				'players'

		));
	}

	public function down()
	{
		\DBUtil::add_fields('games', array(
				'players' => array('type' => 'text'),

		));
	}
}