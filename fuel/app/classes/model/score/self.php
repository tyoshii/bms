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
    
    $username = Auth::get_screen_name();

    $team_id = Model_Team::get_teams();

    //$team_id = Model_Team::find_by_username($username)->team;

    // model名はテーブル名とあわせて規則性がある
    
    // Model_Team <- teamsテーブルのモデル
    // Model_Player <- playersテーブルのモデル

    // 全部のテーブルにはidからがある

    // selectはfind
    $res = Model_Team::find(1);

    // findは find_by_id と同じ意味
    // つまり find_by_カラム名　でselectのクエリが発行可能
    // select * from teams where カラム名 = 1;
    $res = Model_Team::find_by_username('sonuma');

    // findで取得したormオブジェクトはそこから　更新が可能（update
    $res->username('tyoshii'); //tyoshiiに書き換え
    $res->save(); //保存

    // 削除も可能
    $res->delete();

    // 新規insertはforge（newの意味)で新しく作ることで可能
    $team = Model_Team::forge();
    $team->username = 'sonuma';
    $team->number   = 19;
    $team->save();

    // ドキュメント見るのが一番早い
    // https://www.google.co.jp/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#q=fuelphp+orm&safe=off&spell=1
    
    // ちな
    Common::debug($res);

    // debugメソッド用意してある
    // <pre> をはいてvar_dumpしてexitしてくれる。

    // user名からチームIDを取得 
    // select team from players where username = <USERNAME>;

    // チームIDから試合IDを取得(複数ある)
    // select id,team_top,team_bottom from games where team_top = <TEAM_ID> OR team_bottom = <TEAM_ID>;

    // （打撃結果）stats_hittingdetailsから結果を持ってくる
    // 打率ならresult_idがあればよい
    // select * from stats_hittingdetails where game_id in <GAME_ID> and team_id = <TEAM_ID>

    // 自分自身の成績の場合は、ユーザ名からplayer_idを拾えばよい
    // 個人毎の成績ならplayer_idでselectする
    return $res;
  }
}
