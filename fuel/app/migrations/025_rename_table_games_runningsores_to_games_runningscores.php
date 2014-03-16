<?php

namespace Fuel\Migrations;

class Rename_table_games_runningsores_to_games_runningscores
{
	public function up()
	{
		\DBUtil::rename_table('games_runningsores', 'games_runningscores');
	}

	public function down()
	{
		\DBUtil::rename_table('games_runningscores', 'games_runningsores');
	}
}