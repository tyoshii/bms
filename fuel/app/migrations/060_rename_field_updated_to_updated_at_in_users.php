<?php

namespace Fuel\Migrations;

class Rename_field_updated_to_updated_at_in_users
{
	public function up()
	{
		\DBUtil::modify_fields('users', array(
				'updated' => array('name' => 'updated_at', 'type' => 'int', 'constraint' => 11, 'default' => 0)
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('users', array(
				'updated_at' => array('name' => 'updated', 'type' => 'int', 'constraint' => 11, 'default' => 0)
		));
	}
}
