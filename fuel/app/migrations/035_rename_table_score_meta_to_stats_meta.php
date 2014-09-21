<?php

namespace Fuel\Migrations;

class Rename_table_score_meta_to_stats_meta
{
	public function up()
	{
		\DBUtil::rename_table('score_meta', 'stats_meta');
	}

	public function down()
	{
		\DBUtil::rename_table('stats_meta', 'score_meta');
	}
}