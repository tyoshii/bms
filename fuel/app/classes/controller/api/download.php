<?php

class Controller_Api_Download extends Controller_Rest
{
	public function before()
	{
		parent::before();
	}

	/**
	 * 成績のダウンロード
	 * (IT LEAGUE成績提出のため)
	 */
	public function action_stats()
	{
		// validation
		$val = Validation::forge();
		$val->add_field('game_id', '', 'required');
		$val->add_field('team_id', '', 'required');

		if ( ! $val->run(Input::get()))
		{
			Session::set_flash('error', '不正なパラメーターです');
			return Response::redirect('error/400');
		}

		// load and create sheet
		$book = new PHPExcel();
		$book->createSheet();
		$book->createSheet();
		$book->createSheet();
		
		// 選手
		$book->setActiveSheetIndex(0);
		$sheet = $book->getActiveSheet();
		self::_set_player_stats($sheet);
		
		// 試合
		$book->setActiveSheetIndex(1);
		$sheet = $book->getActiveSheet();
		self::_set_game_stats($sheet);
		
		// 打撃
		$book->setActiveSheetIndex(2);
		$sheet = $book->getActiveSheet();
		self::_set_hitting_stats($sheet);

		// 投手
		$book->setActiveSheetIndex(3);
		$sheet = $book->getActiveSheet();
		self::_set_pitching_stats($sheet);

		// Excel2003形式で出力する
		$date = Model_Game::find(Input::get('game_id'))->date;
		$team = Model_Team::find(Input::get('team_id'))->name;
		$filename = sprintf('%s_stats_%s.xls', $team, $date);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer = PHPExcel_IOFactory::createWriter($book, "Excel5");
		$writer->save('php://output');  
	}
	
	/**
	 * 選手データ
	 */
	private static function _set_player_stats(&$sheet)
	{
		// sheet name
		$sheet->setTitle('選手DB');

		// set header to excel
		$sheet->setCellValue('A1', '背番号');
		$sheet->setCellValue('B1', '名前');

		// player data
		$players = Model_Player::get_players(Input::get('team_id'));

		// set data to excel
		foreach ($players as $index => $player)
		{
			$sheet->setCellValue('A'.($index+2), $player['number']);
			$sheet->setCellValue('B'.($index+2), $player['name']);
		}
	}

	/**
	 * 試合に関する成績を付与
	 */
	private static function _set_game_stats(&$sheet)
	{
		// sheet name
		$sheet->setTitle('試合DB');

		// set header to excel
		$sheet->setCellValue('A1', '日付');
		$sheet->setCellValue('B1', '相手');
		$sheet->setCellValue('C1', '勝敗');
		$sheet->setCellValue('D1', 'スコア');
		$sheet->setCellValue('E1', '練習試合/公式戦');
		$sheet->setCellValue('F1', '球場');
		$sheet->setCellValue('G1', '試合ID');
		$sheet->setCellValue('H1', '勝利投手');
		$sheet->setCellValue('I1', '敗戦投手');
		$sheet->setCellValue('J1', '勝利打点');
		$sheet->setCellValue('K1', 'セーブ');
		$sheet->setCellValue('L1', '備考');
		$sheet->setCellValue('M1', '先攻/後攻');

		// get game data
		$game = self::_get_game_data();
		
		// create data
		$datas = array(
			$game->date,
			$game->games_teams->opponent_team_name,
		);

		// 勝敗
		$score = $game->games_runningscores;
		$result = '分';

		if ($score->tsum < $score->bsum)
		{
			$result = $game->games_teams->order === 'top' ? '負' : '勝';
		}
		
		if ($score->tsum > $score->bsum)
		{
			$result = $game->games_teams->order === 'top' ? '勝' : '負';
		}

		$datas[] = $result;

		// スコア
		if ($game->games_teams->order === 'top')
		{
			$datas[] = $score->tsum.' - '.$score->bsum;
		}
		else
		{
			$datas[] = $score->bsum.' - '.$score->tsum;
		}

		// 公式戦/球場/試合ID
		$datas[] = '公式戦';
		$datas[] = $game->stadium;
		$datas[] = Input::get('game_id');

		// 勝利投手/敗戦投手/勝利打点/セーブ
		$win  = '';
		$lose = '';
		$save = '';
		$pitchings = Model_Stats_Pitching::get_stats_by_playeds(Input::get('game_id'), Input::get('team_id'));

		foreach ($pitchings as $pitcher)
		{
			$win  = $pitcher['W']  === '1' ? $pitcher['name'] : '';
			$lose = $pitcher['L']  === '1' ? $pitcher['name'] : '';
			$save = $pitcher['SV'] === '1' ? $pitcher['name'] : '';
		}

		$datas[] = $win;
		$datas[] = $lose;
		$datas[] = '';
		$datas[] = $save;

		// 備考（MIP）
		$award = Model_Stats_Award::get_stats(Input::get('game_id'), Input::get('team_id'));
		$mip = $award['mvp_player_name'];
		if ($award['second_mvp_player_name'] !== '')
		{
			 $mip .= '、'.$award['second_mvp_player_name'];
		}

		$datas[] = $mip;

		// 先攻/後攻
		$datas[] = $game->games_teams->order === 'top' ? '先攻' : '後攻';

		// set data to excel
		foreach ($datas as $index => $data)
		{
			$sheet->setCellValue(chr(97+$index).'2', $data);
		}

		return true;
	}
	
	/**
	 * 打撃に関する成績を付与
	 */
	private static function _set_hitting_stats(&$sheet)
	{
		// sheet name
		$sheet->setTitle('打撃DB');

		// set header	
		$sheet->setCellValue('A1', '試合ID');
		$sheet->setCellValue('B1', '選手ID');
		$sheet->setCellValue('C1', '選手');
		$sheet->setCellValue('D1', '内野フライ');
		$sheet->setCellValue('E1', '内野ゴロ');
		$sheet->setCellValue('F1', '外野フライ');
		$sheet->setCellValue('G1', '三振');
		$sheet->setCellValue('H1', '四球');
		$sheet->setCellValue('I1', '死球');
		$sheet->setCellValue('J1', '送りバント');
		$sheet->setCellValue('K1', '犠打');
		$sheet->setCellValue('L1', 'ヒット');
		$sheet->setCellValue('M1', '二塁打');
		$sheet->setCellValue('N1', '三塁打');
		$sheet->setCellValue('O1', 'HR');
		$sheet->setCellValue('P1', '打点');
		$sheet->setCellValue('Q1', '盗塁');

		// get stats
		$hittings = Model_Stats_Hitting::get_stats_by_playeds(Input::get('game_id'), Input::get('team_id'));

		// set to excel
		foreach ($hittings as $index => $hitting)
		{
			// 試合ID/選手ID/選手
			$data = array();
			$data[] = Input::get('game_id');
			$data[] = $hitting['number'];
			$data[] = $hitting['name'];

			// 打撃成績     0     1     2     3     4     5     6     7     8   9   10  11
			//              内フ, 内ゴ, 外フ, 三振, 四球, 死球, 送バ, 犠打, H,  2B, 3B, HR
			$result = array('',   '',   '',   '',   '',   '',   '',   '',   '', '', '', '');

			foreach ($hitting['details'] as $detail)
			{
				switch ($detail['result_id'])
				{
					// 凡打
					case '11':
						// 外野
						if ($detail['direction'] >= 7 and $detail['direction'] <= 9)
						{
							$result[2]++;
						}
						else //内野
						{
							$detail['kind'] === '1' ? $result[1]++ : $result[0]++;
						}
					break;

					// 三振
					case '18':
					case '19':
						$result[3]++;
					break;

					// 四球
					case '20':
						$result[4]++;
					break;

					// 死球
					case '21':
						$result[5]++;
					break;

					// 送りバント
					case '16':
						$result[6]++;
					break;

					// 犠打
					case '17':
						$result[7]++;
					break;

					// ヒット
					case '12':
						$result[8]++;
					break;

					// 二塁打
					case '13':
						$result[9]++;
					break;

					// 三塁打
					case '14':
						$result[10]++;
					break;

					// HR
					case '15':
						$result[11]++;
					break;
				}
			}

			$data = array_merge($data, $result);

			// 打点/盗塁
			$data[] = $hitting['RBI'] !== '0' ? $hitting['RBI'] : '';
			$data[] = $hitting['SB']  !== '0' ? $hitting['SB']  : '';

			// set to excel
			foreach ($data as $i => $d)
			{
				$sheet->setCellValue(chr(97+$i).($index+2), $d);
			}
		}
	}

	/**
	 * 投手に関する成績を付与
	 */
	private static function _set_pitching_stats(&$sheet)
	{
		// sheet name
		$sheet->setTitle('投手DB');

		// set header
		$sheet->setCellValue('A1', '試合ID');
		$sheet->setCellValue('B1', '選手ID');
		$sheet->setCellValue('C1', '選手');
		$sheet->setCellValue('D1', '完投');
		$sheet->setCellValue('E1', '完封');
		$sheet->setCellValue('F1', '勝');
		$sheet->setCellValue('G1', '負');
		$sheet->setCellValue('H1', 'セーブ');
		$sheet->setCellValue('I1', '投球回');
		$sheet->setCellValue('J1', '投球回1/3');
		$sheet->setCellValue('K1', '奪三振');
		$sheet->setCellValue('L1', '失点');

		// get stats
		$pitchings = Model_Stats_Pitching::get_stats_by_playeds(Input::get('game_id'), Input::get('team_id'));

		foreach ($pitchings as $row => $pitching)
		{
			// 試合ID/選手ID/選手
			$data = array();
			$data[] = Input::get('game_id');
			$data[] = $pitching['number'];
			$data[] = $pitching['name'];

			// 完投/完封
			if (count($pitchings) === 1)
			{
				$data[] = 1;
				$data[] = $pitching['R'] === '0' ? 1 : '';
			}
			else
			{
				$data[] = '';
				$data[] = '';
			}

			// 勝/負/セーブ
			$data[] = $pitching['W']  ? 1 : '';
			$data[] = $pitching['L']  ? 1 : '';
			$data[] = $pitching['SV'] ? 1 : '';

			// 投球回/奪三振/失点
			$data[] = $pitching['IP'];
			$data[] = $pitching['IP_frac'];
			$data[] = $pitching['SO'];
			$data[] = $pitching['R'];
			
			// set to excel
			foreach ($data as $col => $d)
			{
				$sheet->setCellValue(chr(97+$col).($row+2), $d);
			}
		}
	}

	/**
	 * 試合情報の取得（将来的にはモデルへ）
	 */
	private static function _get_game_data()
	{
		// TODO: Model_Gameを整理して、そっちにロジック移動したい
		$query = Model_Game::query()->where('id', Input::get('game_id'));
		$query->related('games_teams', array(
			'where' => array(
				array('team_id', '=', Input::get('team_id')),
			),
		));

		$game = $query->get_one();

		if ( ! $game)
		{
			throw new Exception('データが存在しません。');
		}

		return $game;
	}
}
