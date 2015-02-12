<?php

namespace Fuel\Migrations;

class Create_conventions_games
{
	public function up()
	{
		\DBUtil::create_table('conventions_games', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'convention_id' => array('constraint' => 11, 'type' => 'int'),
			'game_id' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('conventions_games');
	}
}