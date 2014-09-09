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

	public function games2games_teams()
	{
		echo "\n===========================================";
		echo "\nRunning task [migration:runningscore_id2game_id]";
		echo "\n-------------------------------------------\n\n";

		$games = \DB::select()->from('games')->execute();

		foreach ( $games as $game_id => $game )
		{
			if ( \Model_Games_Team::find_by_game_id($game_id) )
			{
				echo 'Skip : '.$game_id."\n";
				continue;
			}

			$props = array('game_id' => $game_id);

			if ( $game['team_top'] === '0' )
			{
				$props = $props + array(
					'team_id' => $game['team_bottom'],
					'order'   => 'bottom',
					'opponent_team_name' => $game['team_top_name'],
				);
			}
			else
			{
				$props = $props + array(
					'team_id' => $game['team_top'],
					'order'   => 'top',
					'opponent_team_name' => $game['team_bottom_name'],
				);
			}

			\Model_Games_Team::regist($props);

			echo 'migration : '.$game_id."\n";
		} 

		echo "DONE";
	}

  public function runningscore_id2game_id()
  {
		echo "\n===========================================";
		echo "\nRunning task [migration:runningscore_id2game_id]";
		echo "\n-------------------------------------------\n\n";

    $scores = \Model_Games_Runningscore::find('all');

    foreach ( $scores as $score )
    {
      $score->game_id = $score->id;
      $score->save();
    }

    echo "DONE";
  }

  public function position_remove_zero($args = NULL)
  {
		echo "\n===========================================";
		echo "\nRunning task [migration:position_remove_zero stats]";
		echo "\n-------------------------------------------\n\n";

    $result = \Model_Stats_Player::find('all');

    foreach ( $result as $res )
    {
      $positions = explode(',', $res->position);

      while(true)
      {
        if ( count($positions) == 0 )
        {
          break;
        }

        if ( $positions[count($positions)-1] == 0 )
        {
          array_pop($positions);
        }
        else
        {
          break;
        }
      }

      $res->position = implode(',', $positions);
      $res->save();
    }

    echo "DONE!!";
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
