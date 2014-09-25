<?php

namespace Fuel\Migrations;

class Add_order_to_stats_pitchings
{
	public function up()
	{
		\DBUtil::add_fields('stats_pitchings', array(
			'order' => array('type' => 'tinyint', 'unsigned' => true, 'default' => '0'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('stats_pitchings', array(
			'order'

		));
	}
}