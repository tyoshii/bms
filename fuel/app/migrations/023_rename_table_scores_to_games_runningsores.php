<?php

namespace Fuel\Migrations;

class Rename_table_scores_to_games_runningsores
{
	public function up()
	{
		\DBUtil::rename_table('scores', 'games_runningsores');
	}

	public function down()
	{
		\DBUtil::rename_table('games_runningsores', 'scores');
	}
}