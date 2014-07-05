<?php
class Model_Score_Team extends \Orm\Model
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

  public static function getTeamScore()
  {
    $my_team_id = Model_Player::getMyTeamId();

    $query = <<<___QUERY___
SELECT
    sum(s.TPA) as TPA, sum(s.AB) as AB, sum(s.H) as H, sum(s.2B) as 2B, sum(s.3B) as 3B, sum(s.HR) as HR,sum(s.RBI) as RBI,sum(s.R) as R,sum(s.SO) as SO,sum(s.BB) as BB,sum(s.HBP) as HBP,sum(s.SAC) as SAC,sum(s.SF) as SF,sum(s.SB) as SB,(SELECT sum(E) from stats_fieldings where team_id = $my_team_id) as E
FROM
    stats_hittings AS s
LEFT JOIN
    players AS p
ON
    s.player_id = p.id
LEFT JOIN
    teams AS  t
ON
    t.id = p.team
WHERE
    p.status != -1
AND
    s.team_id = $my_team_id 
GROUP BY
    s.team_id
;
___QUERY___;

    $result= DB::query($query)->execute()->as_array();
    return $result[0];
  }

  public static function getTeamGameInfo(){
 
    $my_team_id = Model_Player::getMyTeamId();
    
    $query = <<<___QUERY___
SELECT
  gr.id,gr.tsum,gr.bsum,g.team_top,g.team_bottom,g.game_status,g.team_top_name,g.team_bottom_name,g.date
FROM
  games as g
LEFT JOIN
  games_runningscores as gr
ON
  g.id = gr.id
WHERE
  (g.team_top = $my_team_id or g.team_bottom = $my_team_id)
ORDER BY
  g.date DESC
;
___QUERY___;

    $result= DB::query($query)->execute()->as_array();

    return $result;
  }

  public static function getTeamWinLose($team_id,$infos){

    $ret = Array();
    $ret['win'] = 0;
    $ret['lose'] = 0;
    $ret['draw'] = 0;

    foreach ($infos as $info){
      if($info['tsum'] > $info['bsum']){
        if($info['team_top'] == $team_id){
          ++$ret['win'];
        }else{
          ++$ret['lose'];
        }
      }else if($info['tsum'] == $info['bsum']){
        ++$ret['draw'];
      }else{
         if($info['team_top'] == $team_id){
          ++$ret['lose'];
        }else{
          ++$ret['win'];
        } 
      }
    }
    return $ret;
  }
}
