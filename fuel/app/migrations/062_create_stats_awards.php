<?php

namespace Fuel\Migrations;

class Create_stats_awards
{
	public function up()
	{
		\DBUtil::create_table('stats_awards', array(
			'id'                   => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'game_id'              => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'team_id'              => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'mvp_player_id'        => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'second_mvp_player_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'created_at'           => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at'           => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('stats_awards');
	}
}