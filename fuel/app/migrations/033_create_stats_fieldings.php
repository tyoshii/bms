<?php

namespace Fuel\Migrations;

class Create_stats_fieldings
{
	public function up()
	{
		\DBUtil::create_table('stats_fieldings', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'player_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'game_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'E' => array('type' => 'tinyint', 'unsingned' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('stats_fieldings');
	}
}