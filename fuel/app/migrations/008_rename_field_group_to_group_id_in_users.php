<?php

namespace Fuel\Migrations;

class Rename_field_group_to_group_id_in_users
{
	public function up()
	{
		\DBUtil::modify_fields('users', array(
				'group' => array('name' => 'group_id', 'type' => 'int', 'constraint' => 11)
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('users', array(
				'group_id' => array('name' => 'group', 'type' => 'int', 'constraint' => 11)
		));
	}
}