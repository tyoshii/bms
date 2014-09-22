<?php

namespace Fuel\Migrations;

class Delete_updated_at_from_users
{
	public function up()
	{
		\DBUtil::drop_fields('users', array(
			'updated_at'

		));
	}

	public function down()
	{
		\DBUtil::add_fields('users', array(
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		));
	}
}
