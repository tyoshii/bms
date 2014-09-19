<?php

namespace Fuel\Migrations;

class Add_url_path_to_teams
{
	public function up()
	{
		\DBUtil::add_fields('teams', array(
			'url_path' => array('constraint' => 64, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('teams', array(
			'url_path'

		));
	}
}