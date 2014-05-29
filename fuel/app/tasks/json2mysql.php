<?php

namespace Fuel\Tasks;

class Json2mysql
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r json2mysql
	 *
	 * @return string
	 */
	public function run($game_id = NULL)
	{
    // 対象game_idを取得
    $game_ids = '';
    if ( $game_id )
    {
      $game_ids = array($game_id);
    }
    else
    {
      $game_ids = \DB::select('id')->from('games')->execute()->as_array();
    }

    foreach ( $game_ids as $id )
    {
      // ミラー対象のデータを取得
      $results = \DB::select('game_id', 'team_id', 'players', 'pitchers', 'batters')
                  ->from('games_stats')
                  ->execute()->as_array();

      // 1つずつパースしてregist
      foreach ( $results as $result )
      {
        $ids = array(
          'game_id' => $result['game_id'],
          'team_id' => $result['team_id'],
        );
        
        // players
        if ( $players = json_decode($result['players'], true) )
        {
          // データ整形
          foreach ( $players as $key => $val )
          {
            $players[$key]['player_id'] = $val['member_id'];
          }

          // regist
          \Model_Stats_Player::registPlayer($ids, $players);
        }    

        // pitchers
        if ( $stats = json_decode($result['pitchers'], true) )
        {
          $require_keys = array(
            'result',
            'inning_int', 'inning_frac',
            'hianda', 'sanshin', 'shishikyuu',
            'earned_runs', 'runs',
          );

          // データ整形
          foreach ( $stats as $player_id => $stat )
          {
            if ( ! $stat ) continue;

            foreach ( $require_keys as $key )
            {
              if ( array_key_exists($key, $stat) )
                $stats[$player_id][$key] = $stat[$key];
              else
                $stats[$player_id][$key] = 0;
            }
          }

          // regist
          \Model_Stats_Pitching::replaceAll($ids, $stats);
        }

        // batters
        if ( $stats = json_decode($result['batters'], true) )
        {
          // regist
          \Model_Stats_Hitting::replaceAll($ids, $stats);
        }
      }
    }

    echo "DONE !!";
	}

}
/* End of file tasks/json2mysql.php */
