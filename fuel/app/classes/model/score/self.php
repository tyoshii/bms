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
    // userIDからユーザ名(uniq)を取得
    // select username from users where id = <ID>;

    // user名からチームIDを取得 
    // select team from players where username = <USERNAME>;

    // チームIDから試合IDを取得(複数ある)
    // select id,team_top,team_bottom from games where team_top = <TEAM_ID> OR team_bottom = <TEAM_ID>;

    // （打撃結果）stats_hittingdetailsから結果を持ってくる
    // 打率ならresult_idがあればよい
    // select * from stats_hittingdetails where game_id in <GAME_ID> and team_id = <TEAM_ID>

    // 自分自身の成績の場合は、ユーザ名からplayer_idを拾えばよい
    // 個人毎の成績ならplayer_idでselectする

    $result = "";
    return $result;
  }

}
