<?php

namespace Fuel\Migrations;

class Add_updated_to_users
{
	public function up()
	{
		\DBUtil::add_fields('users', array(
				'updated' => array('constraint' => 11, 'type' => 'int', 'default' => 0),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('users', array(
				'updated'

		));
	}
}
