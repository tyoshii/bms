<?php

namespace Fuel\Migrations;

class Create_stats_hittings
{
	public function up()
	{
		\DBUtil::create_table('stats_hittings', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'player_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'game_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'TPA' => array('type' => 'tinyint', 'unsingned' => true),
			'AB' => array('type' => 'tinyint', 'unsingned' => true),
			'H' => array('type' => 'tinyint', 'unsingned' => true),
			'2B' => array('type' => 'tinyint', 'unsingned' => true),
			'3B' => array('type' => 'tinyint', 'unsingned' => true),
			'HR' => array('type' => 'tinyint', 'unsingned' => true),
			'SO' => array('type' => 'tinyint', 'unsingned' => true),
			'BB' => array('type' => 'tinyint', 'unsingned' => true),
			'HBP' => array('type' => 'tinyint', 'unsingned' => true),
			'SAC' => array('type' => 'tinyint', 'unsingned' => true),
			'SF' => array('type' => 'tinyint', 'unsingned' => true),
			'RBI' => array('type' => 'tinyint', 'unsingned' => true),
			'R' => array('type' => 'tinyint', 'unsingned' => true),
			'SB' => array('type' => 'tinyint', 'unsingned' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('stats_hittings');
	}
}