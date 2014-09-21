<?php

namespace Fuel\Migrations;

class Create_batter_results
{
	public function up()
	{
		\DBUtil::create_table('batter_results', array(
				'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'result'     => array('constraint' => 32, 'type' => 'varchar'),
				'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
				'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('batter_results');
	}
}