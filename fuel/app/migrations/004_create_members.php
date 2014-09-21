<?php

namespace Fuel\Migrations;

class Create_members
{
	public function up()
	{
		\DBUtil::create_table('members', array(
				'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'team'       => array('constraint' => 11, 'type' => 'int'),
				'name'       => array('constraint' => 64, 'type' => 'varchar'),
				'number'     => array('constraint' => 11, 'type' => 'int'),
				'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
				'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('members');
	}
}