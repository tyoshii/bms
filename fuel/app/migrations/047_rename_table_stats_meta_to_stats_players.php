<?php

namespace Fuel\Migrations;

class Rename_table_stats_meta_to_stats_players
{
  public function up()
  {
    \DBUtil::rename_table('stats_meta', 'stats_players');
  }

  public function down()
  {
    \DBUtil::rename_table('stats_players', 'stats_meta');
  }
}