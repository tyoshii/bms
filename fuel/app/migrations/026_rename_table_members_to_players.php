<?php

namespace Fuel\Migrations;

class Rename_table_members_to_players
{
	public function up()
	{
		\DBUtil::rename_table('members', 'players');
	}

	public function down()
	{
		\DBUtil::rename_table('players', 'members');
	}
}