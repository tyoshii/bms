<?php

namespace Fuel\Migrations;

class Delete_status_from_stats_pitchings
{
	public function up()
	{
		\DBUtil::drop_fields('stats_pitchings', array(
			'status'

		));
	}

	public function down()
	{
		\DBUtil::add_fields('stats_pitchings', array(
			'status' => array('constraint' => 11, 'type' => 'int'),

		));
	}
}
