<?php

class Controller_Api_Stats extends Controller_Api_Base
{
    public function before()
    {
        parent::before();

        $this->game_id = Input::get('game_id', null);
        $this->team_id = Input::get('team_id', null);

        $this->game = Model_Game::find($this->game_id);
        $this->team = Model_Team::find($this->team_id);
    }

    /**
     * 入力された成績にエラーが無いかをチェックするAPI
     * @get integer game_id
     * @get integer team_id(optional)
     */
    public function get_check()
    {
        $message = $this->_validation();
        if (is_string($message))
        {
            return $this->error(400, $message);
        }

        // チェック対象のチーム情報を取得
        $teams = $this->_get_stats_check_teams();

        // check logic
        // エラーが有った場合は $response に保存
        $response = array();

        // チームごとのチェック
        // 三振数は保存しておいて、後で比較
        $Ks = array();

        foreach ($teams as $team)
        {
            $team_id   = $team['id'];
            $team_name = $team['name'];

            // 三振数のメタ情報
            $Ks[$team_id] = array(
                'name'    => $team_name,
                'batter'  => 0,
                'pitcher' => 0,
            );

            // 配列の最後でresponseへマージする
            $errors    = array();

            $players = Model_Stats_Player::get_participate_players($this->game_id, $this->team_id);
            $stats_hitting  = Model_Stats_Hitting::get_stats($this->game_id, $this->team_id);
            $stats_pitching = Model_Stats_Pitching::get_stats($this->game_id, $this->team_id);

            // 成績入力がされているかどうか。
            if (count($stats_hitting) === 0 or count($stats_pitching) === 0)
            {
                $errors[] = '野手成績/投手成績の入力がされていません。';
                goto team_check_end;
            }

            // 前後の打席数があっているか
            $TPAs[] = array();
            $pre_order = 0;
            foreach ($players as $index => $player)
            {
                if (array_key_exists($player['id'], $stats_hitting))
                {
                    $stats = $stats_hitting[$player['id']];

                    // 打席数と三振数を記録
                    $tpa = $stats['TPA'];
                    $Ks[$team_id]['batter'] += $stats['SO'];
                }

                $order = $player['order'];

                if ($order == 0)
                {
                    $TPAs[$pre_order] += $tpa;
                }
                else
                {
                    $TPAs[$order] = $tpa;

                    // 前後の打席数をチェック
                    // １番打者の時はチェックしない
                    if ($order != 1)
                    {
                        if ($TPAs[$pre_order] - $TPAs[$order] < 0)
                        {
                            $errors[] = $order.'番打者の打席数が多いです。';
                        }
                    }

                    $pre_order = $order;
                }
            }

            // 1番打者とラストバッターの打席の差を比較
            $last  = end($TPAs);
            $first = $TPAs[1];

            if ($first - $last >= 2)
            {
                $errors[] = 'トップバッターとラストバッターの打席差が2以上あります。';
            }

            // 投球回数と、実施イニングがあっているかどうか。
            $last_inning = $this->game->games_runningscore->last_inning;

            $IP = 0;
            $IP_frac = 0;
            foreach ($stats_pitching as $stats)
            {
                $IP += $stats['IP'];    

                $frac = substr($stats['IP_frac'], 0, 1);
                $IP_frac += $frac;

                // 奪三振数を記録
                $Ks[$team_id]['pitcher'] += $stats['SO'];
            }

            // サヨナラゲームなどを考慮して1イニング以上としている
            if ($last_inning - ($IP + $IP_frac / 3) > 1)
            {
                $errors[] = '投手成績の投球回数が少ないです。';
            }
            if ($last_inning - ($IP + $IP_frac / 3) < 0)
            {
                $errors[] = '投手成績の投球回数が多いです。';
            }

team_check_end:
            if (count($errors) !== 0)
            {
                $response[] = array(
                    'item' => array(
                        'team_id'   => $this->team_id,
                        'team_name' => $team_name,
                        'errors'    => $errors,
                    ),
                );
            }
        }

        // 両チームでの差分チェック

        // 三振数はあっているか
        // 2チーム登録してある試合でのみチェックする
        if (count($Ks) === 2)
        {
            $team1_K = end($Ks);
            $team2_K = reset($Ks);
    
            if ($team1_K['batter'] !== $team2_K['pitcher'])
            {
                $response['common'][] = $team1_K['name'].'の打者三振数と'.
                                                                $team2_K['name'].'の投手奪三振数ががあいません。';
            }
            if ($team2_K['batter'] !== $team1_K['pitcher'])
            {
                $response['common'][] = $team2_K['name'].'の打者三振数と'.
                                                                $team1_K['name'].'の投手奪三振数ががあいません。';
            }
        }

        return $this->success($response);
    }

    /**
     * api/stats/check のvalidation
     *
     * TODO: メソッド名、取り急ぎ_validation
     *       他のエントリーポイントなど出てきたら、汎用的なvalidationモジュールへ
     *
     * @return mixed true or error message
     */
    private function _validation()
    {
        // game_id validation
        if (is_null($this->game_id) or ! $this->game)
        {
            $message = 'game_idが正しく指定されていません。';
            Log::error($message);
            return $message;
        }

        // team_id validation, if specify
        if ($this->team_id and ! $this->team)
        {
            $message = '指定されたteam_idが正しくありません';
            Log::error($message);
            return $message;
        }

        // login状態でのアクセスであれば、権限のある試合/チームであること
        if (Auth::check())
        {
            // TODO:
        }

        return true;
    }

    /**
     * api/stats/check で成績チェックするチームを取得
     *
     * @return array array(
     *                array('id' => 'team_id', 'name' => 'team_name'),
     *              [ array('id' => 'team_id', 'name' => 'team_name'), ]
     *							);
     */
    private function _get_stats_check_teams()
    {
        $teams = array();

        if ($this->team)
        {
            $teams[] = array(
                'id'   => $this->team->id,
                'name' => $this->team->name,
            );
        }
        else
        {
            $teams[] = array(
                'id'   => $this->game->games_team->team_id,
                'name' => Model_Team::find($this->game->games_team->team_id)->name,
            );
    
            if ($this->game->games_team->opponent_team_id != 0)
            {
                $teams[] = array(
                    'id'   => $this->game->games_team->opponent_team_id,
                    'name' => $this->game->games_team->opponent_team_name,
                );
            }
        }

        return $teams;
    }
}
