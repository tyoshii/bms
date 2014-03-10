<?php

namespace Fuel\Migrations;

class Add_user_id_to_players
{
	public function up()
	{
		\DBUtil::add_fields('players', array(
			'user_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('players', array(
			'user_id'

		));
	}
}