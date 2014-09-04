<?php

namespace Fuel\Tasks;

class Dbinit
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r dbinit
	 *
	 * @return string
	 */
	public function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Dbinit:Run]";
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
	 * php oil r dbinit:batter_result "arguments"
	 *
	 * @return string
	 */
  // default result
  public static $_results = array(
    array(
      'id' => '11',
      'category_id' => 1,
      'category' => '打者アウト',
      'result'   => 'アウト',
    ),
    array(
      'id' => '12',
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '単打',
    ),
    array(
      'id' => '13',
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '二塁打',
    ),
    array(
      'id' => '14',
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '三塁打',
    ),
    array(
      'id' => '15',
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '本塁打',
    ),
    array(
      'id' => '16',
      'category_id' => 3,
      'category' => '犠打',
      'result' => '犠打',
    ),
    array(
      'id' => '17',
      'category_id' => 3,
      'category' => '犠打',
      'result' => '犠飛',
    ),
    array(
      'id' => '18',
      'category_id' => 4,
      'category' => '三振',
      'result' => '見逃し三振',
    ),
    array(
      'id' => '19',
      'category_id' => 4,
      'category' => '三振',
      'result' => '空振り三振',
    ),
    array(
      'id' => '20',
      'category_id' => 5,
      'category' => '四球',
      'result' => '四球',
    ),
    array(
      'id' => '21',
      'category_id' => 5,
      'category' => '四球',
      'result' => '死球',
    ),
    array(
      'id' => '22',
      'category_id' => 6,
      'category' => 'その他',
      'result' => '打撃妨害',
    ),
    array(
      'id' => '23',
      'category_id' => 6,
      'category' => 'その他',
      'result' => '守備妨害',
    ),
  );

	public function batter_result($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Dbinit:Batter result]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/

    $br = \Model_Batter_Result::find('all');
    foreach ( $br as $b )
      $b->delete();

    foreach ( self::$_results as $res )
    {
      $res['order'] = 0;
      $br = \Model_Batter_Result::forge($res);
      $br->save();
    }

    echo "Finish!!";
	}


  /**
   *
   */
  public function insert_data_for_travis()
  {
    // team
    $team1_id = \Model_Team::regist('テストチーム1');
    $team2_id = \Model_Team::regist('テストチーム2');

    // game
    // TODO: createNewGameは新規ゲーム追加の修正で変更の可能性あり
    $data = array(
      'date'   => date('Y-m-d'),
      'top'    => $team1_id,
      'bottom' => $team2_id,
    );
    \Model_Game::createNewGame($data);

    // player
    $props = array(
      'team'     => $team1_id,
      'name'     => '選手A',
      'number'   => 1,
      'username' => 'player1',
    );
    \Model_Player::regist($props);

    // user
    $data = array(
      # username  => group
      'admin'     => 100,
      'moderator' => 50,
      'user'      => 1,
      'banned'    => -1,
      'player1'   => 1,
    );

    foreach ( $data as $username => $group )
    {
      \Auth::delete_user($username);
      \Auth::create_user($username, 'password', "{$username}@bm-s.info", $group);
    }

    // config
    $ids = \Config::get('bms.moderator_team_ids');
    if ( count($ids) === 0 )
    {
      \Config::set('bms.moderator_team_ids', array(1));
      \Config::save('bms', 'bms');
    }
  }
}
/* End of file tasks/dbinit.php */
