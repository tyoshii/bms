<?php

namespace Fuel\Tasks;

class Migration
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r migration
	 *
	 * @return string
	 */
	public function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Migration:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/
	}



	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r migration:games2games_stats "arguments"
	 *
	 * @return string
	 */
	public function games2games_stats($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Migration:Games2games stats]";
		echo "\n-------------------------------------------\n\n";

    $games = \DB::select_array()->from('games')->execute();

    foreach ( $games as $game )
    {
      $props = array(
        'game_id'  => $game['id'],
        'players'  => $game['players'],
        'pitchers' => $game['pitchers'],
        'batters'  => $game['batters'],
        'others'   => '',
      );
      
      foreach ( array('top' => $game['team_top'], 'bottom' => $game['team_bottom']) as $order => $team_id )
      { 
        $stat = \Model_Games_Stat::forge($props);
        $stat->order   = $order;
        $stat->team_id = $team_id;
        $stat->save();
      }
    }
	}
}
/* End of file tasks/migration.php */
