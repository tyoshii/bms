<?php

namespace Fuel\Migrations;

class Create_score_meta
{
	public function up()
	{
		\DBUtil::create_table('score_meta', array(
				'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'player_id'  => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
				'game_id'    => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
				'order'      => array('type' => 'tinyint', 'unsigned' => true),
				'position'   => array('constraint' => 32, 'type' => 'varchar'),
				'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
				'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('score_meta');
	}
}