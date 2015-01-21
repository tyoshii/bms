<?php

namespace Fuel\Migrations;

class Create_conventions
{
	public function up()
	{
		\DBUtil::create_table('conventions', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 64, 'type' => 'varchar'),
			'kind' => array('constraint' => '"league","tournament"', 'type' => 'enum'),
			'published' => array('constraint' => '"true","false"', 'type' => 'enum'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('conventions');
	}
}
