<?php

namespace Fuel\Migrations;

class Add_category_to_batter_results
{
	public function up()
	{
		\DBUtil::add_fields('batter_results', array(
			'category' => array('constraint' => 32, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('batter_results', array(
			'category'

		));
	}
}