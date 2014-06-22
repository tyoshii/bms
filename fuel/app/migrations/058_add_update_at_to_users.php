<?php

namespace Fuel\Migrations;

class Add_update_at_to_users
{
	public function up()
	{
		\DBUtil::add_fields('users', array(
			'update_at' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('users', array(
			'update_at'

		));
	}
}