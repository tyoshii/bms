<?php

namespace Fuel\Migrations;

class Create_scores
{
	public function up()
	{
		\DBUtil::create_table('scores', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			't1' => array('type' => 'tinyint', 'unsigned' => true),
			't2' => array('type' => 'tinyint', 'unsigned' => true),
			't3' => array('type' => 'tinyint', 'unsigned' => true),
			't4' => array('type' => 'tinyint', 'unsigned' => true),
			't5' => array('type' => 'tinyint', 'unsigned' => true),
			't6' => array('type' => 'tinyint', 'unsigned' => true),
			't7' => array('type' => 'tinyint', 'unsigned' => true),
			't8' => array('type' => 'tinyint', 'unsigned' => true),
			't9' => array('type' => 'tinyint', 'unsigned' => true),
			't10' => array('type' => 'tinyint', 'unsigned' => true),
			't11' => array('type' => 'tinyint', 'unsigned' => true),
			't12' => array('type' => 'tinyint', 'unsigned' => true),
			't13' => array('type' => 'tinyint', 'unsigned' => true),
			't14' => array('type' => 'tinyint', 'unsigned' => true),
			't15' => array('type' => 'tinyint', 'unsigned' => true),
			't16' => array('type' => 'tinyint', 'unsigned' => true),
			't17' => array('type' => 'tinyint', 'unsigned' => true),
			't18' => array('type' => 'tinyint', 'unsigned' => true),
			'tsum' => array('type' => 'tinyint', 'unsigned' => true),
			'b1' => array('type' => 'tinyint', 'unsigned' => true),
			'b2' => array('type' => 'tinyint', 'unsigned' => true),
			'b3' => array('type' => 'tinyint', 'unsigned' => true),
			'b4' => array('type' => 'tinyint', 'unsigned' => true),
			'b5' => array('type' => 'tinyint', 'unsigned' => true),
			'b6' => array('type' => 'tinyint', 'unsigned' => true),
			'b7' => array('type' => 'tinyint', 'unsigned' => true),
			'b8' => array('type' => 'tinyint', 'unsigned' => true),
			'b9' => array('type' => 'tinyint', 'unsigned' => true),
			'b10' => array('type' => 'tinyint', 'unsigned' => true),
			'b11' => array('type' => 'tinyint', 'unsigned' => true),
			'b12' => array('type' => 'tinyint', 'unsigned' => true),
			'b13' => array('type' => 'tinyint', 'unsigned' => true),
			'b14' => array('type' => 'tinyint', 'unsigned' => true),
			'b15' => array('type' => 'tinyint', 'unsigned' => true),
			'b16' => array('type' => 'tinyint', 'unsigned' => true),
			'b17' => array('type' => 'tinyint', 'unsigned' => true),
			'b18' => array('type' => 'tinyint', 'unsigned' => true),
			'bsum' => array('type' => 'tinyint', 'unsigned' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('scores');
	}
}