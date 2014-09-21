<?php

namespace Fuel\Migrations;

class Add_status_to_stats_hittings
{
	public function up()
	{
		\DBUtil::add_fields('stats_hittings', array(
				'status' => array('constraint' => 11, 'type' => 'int'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('stats_hittings', array(
				'status'

		));
	}
}