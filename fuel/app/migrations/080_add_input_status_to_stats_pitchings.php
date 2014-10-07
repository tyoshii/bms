<?php

namespace Fuel\Migrations;

class Add_input_status_to_stats_pitchings
{
	public function up()
	{
		\DBUtil::add_fields('stats_pitchings', array(
			'input_status' => array('constraint' => '"save","complete"', 'type' => 'enum'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('stats_pitchings', array(
			'input_status'

		));
	}
}