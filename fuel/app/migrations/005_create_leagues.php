<?php

namespace Fuel\Migrations;

class Create_leagues
{
	public function up()
	{
		\DBUtil::create_table('leagues', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'       => array('constraint' => 64, 'type' => 'varchar'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('leagues');
	}
}
