<?php

namespace Fuel\Migrations;

class Rename_field_update_at_to_updated_at_in_users
{
	public function up()
	{
		\DBUtil::modify_fields('users', array(
			'update_at' => array('name' => 'updated_at', 'type' => 'int unsigned')
		));
	}

	public function down()
	{
	\DBUtil::modify_fields('users', array(
			'updated_at' => array('name' => 'update_at', 'type' => 'int unsigned')
		));
	}
}