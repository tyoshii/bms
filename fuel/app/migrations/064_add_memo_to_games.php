<?php

namespace Fuel\Migrations;

class Add_memo_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
				'memo' => array('type' => 'text'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
				'memo'

		));
	}
}