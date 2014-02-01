<?php

namespace Fuel\Migrations;

class Rename_table_stamen_to_stamens
{
	public function up()
	{
		\DBUtil::rename_table('stamen', 'stamens');
	}

	public function down()
	{
		\DBUtil::rename_table('stamens', 'stamen');
	}
}