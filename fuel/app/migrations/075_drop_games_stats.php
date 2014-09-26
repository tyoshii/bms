<?php

namespace Fuel\Migrations;

class Drop_games_stats
{
	public function up()
	{
		\DBUtil::drop_table('games_stats');
	}

	public function down()
	{
		\DBUtil::create_table('games_stats', array(
			'id' => array('type' => 'int unsigned', 'null' => true, 'auto_increment' => true),
			'game_id' => array('type' => 'int unsigned', 'null' => true),
			'order' => array('type' => 'varchar', 'null' => true, 'constraint' => 8),
			'team_id' => array('type' => 'int unsigned', 'null' => true),
			'players' => array('type' => 'text', 'null' => true),
			'pitchers' => array('type' => 'text', 'null' => true),
			'batters' => array('type' => 'text', 'null' => true),
			'others' => array('type' => 'text', 'null' => true),
			'created_at' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'updated_at' => array('type' => 'int', 'null' => true, 'constraint' => 11),

		), array('id'));

	}
}