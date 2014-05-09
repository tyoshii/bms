<?php

namespace Fuel\Migrations;

class Add_team_id_to_stats_hittingdetails
{
	public function up()
	{
		\DBUtil::add_fields('stats_hittingdetails', array(
			'team_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('stats_hittingdetails', array(
			'team_id'

		));
	}
}