<?php

namespace Fuel\Migrations;

class Drop_stamens
{
	public function up()
	{
		\DBUtil::drop_table('stamens');
	}

	public function down()
	{
		\DBUtil::create_table('stamens', array(
			'id'         => array('type' => 'int unsigned', 'null' => true, 'auto_increment' => true),
			'game_id'    => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'data'       => array('type' => 'text', 'null' => true),
			'created_at' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'updated_at' => array('type' => 'int', 'null' => true, 'constraint' => 11),

		), array('id'));

	}
}