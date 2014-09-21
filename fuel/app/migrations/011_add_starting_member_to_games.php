<?php

namespace Fuel\Migrations;

class Add_starting_member_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
				'starting_member' => array('type' => 'text'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
				'starting_member'

		));
	}
}