<?php

namespace Fuel\Migrations;

class Create_games_teams
{
	public function up()
	{
		\DBUtil::create_table('games_teams', array(
				'id'               => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'game_id'          => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
				'team_id'          => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
				'order'            => array('constraint' => '"top","bottom"', 'type' => 'enum'),
				'opponent_team_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
				'input_status'     => array('constraint' => '"save","complete"', 'type' => 'enum'),
				'created_at'       => array('constraint' => 11, 'type' => 'int', 'null' => true),
				'updated_at'       => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('games_teams');
	}
}