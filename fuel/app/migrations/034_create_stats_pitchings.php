<?php

namespace Fuel\Migrations;

class Create_stats_pitchings
{
	public function up()
	{
		\DBUtil::create_table('stats_pitchings', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'player_id'  => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'game_id'    => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'W'          => array('type' => 'tinyint', 'unsingned' => true),
			'L'          => array('type' => 'tinyint', 'unsingned' => true),
			'HLD'        => array('type' => 'tinyint', 'unsingned' => true),
			'SV'         => array('type' => 'tinyint', 'unsingned' => true),
			'IP'         => array('type' => 'tinyint', 'unsingned' => true),
			'H'          => array('type' => 'tinyint', 'unsingned' => true),
			'SO'         => array('type' => 'tinyint', 'unsingned' => true),
			'BB'         => array('type' => 'tinyint', 'unsingned' => true),
			'HB'         => array('type' => 'tinyint', 'unsingned' => true),
			'ER'         => array('type' => 'tinyint', 'unsingned' => true),
			'R'          => array('type' => 'tinyint', 'unsingned' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('stats_pitchings');
	}
}