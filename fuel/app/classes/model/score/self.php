<?php
class Model_Score_Self extends \Orm\Model
{

	protected static $_properties = array(
		'id',
		'date',
		'team_top',
		'team_bottom',
		'game_status',
		'players',
		'pitchers',
		'batters',
		'created_at',
		'updated_at',
	);

  public static function getSelfScores()
  {
    /*
    $query = DB::select(
      array( 'g.id', 'id' ),
      'g.id',
      'g.date',
      'g.game_status',
      'g.team_top',
      'g.team_bottom',
      'games_runningscores.tsum',
      'games_runningscores.bsum',
      DB::expr('(select name from teams as t where t.id = g.team_top) as team_top_name'),
      DB::expr('(select name from teams as t where t.id = g.team_bottom) as team_bottom_name')
    )->from(array('games', 'g'));

    $query->join('games_runningscores')->on('g.id', '=', 'games_runningscores.id');
  
    $query->where('game_status', '!=', 0);
    $query->order_by('date', 'desc');
    
    $result = $query->execute()->as_array();

    // ログインしている場合、自分のチームの試合にflag
    if ( Auth::check() && $team_id = Model_Player::getMyTeamId() )
    {
      foreach ( $result as $index => $res )
      {
        if ( $res['team_top']    == $team_id ||
             $res['team_bottom'] == $team_id )
        {
          $result[$index]['own'] = 1;
        }
        else
        {
          $result[$index]['own'] = 0;
        }
      }
    }
    */
    $result = "";
    return $result;
  }

}
