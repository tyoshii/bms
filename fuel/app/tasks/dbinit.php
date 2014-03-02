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
      'category_id' => 1,
      'category' => '打者アウト',
      'result'   => '凡打',
    ),
    array(
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '単打',
    ),
    array(
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '二塁打',
    ),
    array(
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '三塁打',
    ),
    array(
      'category_id' => 2,
      'category' => 'ヒット',
      'result' => '本塁打',
    ),
    array(
      'category_id' => 3,
      'category' => '犠打',
      'result' => '犠打',
    ),
    array(
      'category_id' => 3,
      'category' => '犠打',
      'result' => '犠飛',
    ),
    array(
      'category_id' => 4,
      'category' => '三振',
      'result' => '見逃し三振',
    ),
    array(
      'category_id' => 4,
      'category' => '三振',
      'result' => '空振り三振',
    ),
    array(
      'category_id' => 5,
      'category' => '四球',
      'result' => '四球',
    ),
    array(
      'category_id' => 5,
      'category' => '四球',
      'result' => '死球',
    ),
    array(
      'category_id' => 6,
      'category' => 'その他',
      'result' => '打撃妨害',
    ),
    array(
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
    if ( count($br) )
      $br->delete();

    foreach ( self::$_results as $res )
    {
      $res['order'] = 0;
      $br = \Model_Batter_Result::forge($res);
      $br->save();
    }

    echo "Finish!!";
	}

}
/* End of file tasks/dbinit.php */
