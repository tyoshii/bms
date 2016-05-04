<?php

class Controller_Game extends Controller_Base
{
    public $_team = array();
    public $_player = array();
    public $_alerts = array();
    public $_team_admin = false;

    public function before()
    {
        parent::before();

        $this->set_global_game_object();
    }

    /**
     * game detail page.
     */
    public function action_detail()
    {
        // score
        if ($this->game->games_runningscore->last_inning < 7) {
            $this->game->games_runningscore->last_inning = 7;
        }

        // 対戦チーム名
        $games_team = $this->game->games_team;

        $temp1_id = $games_team->team_id;
        $temp1_name = Model_Team::find($games_team->team_id)->name;
        $temp2_id = $games_team->opponent_team_id;
        $temp2_name = $games_team->opponent_team_name;

        if ($games_team->order === 'top') {
            $team = array(
                'top_id' => $temp1_id,
                'top_name' => $temp1_name,
                'bottom_id' => $temp2_id,
                'bottom_name' => $temp2_name,
            );
        } else {
            $team = array(
                'top_id' => $temp2_id,
                'top_name' => $temp2_name,
                'bottom_id' => $temp1_id,
                'bottom_name' => $temp1_name,
            );
        }

        $this->view->team = $team;

        // stats
        $this->view->stats = array(
            'top' => array(
                'hitting' => array(
                    'players' => Model_Stats_Hitting::get_stats_by_playeds($this->game->id, $team['top_id']),
                    'total' => Model_Stats_Hitting::get_stats_total($this->game->id, $team['top_id']),
                ),
                'pitching' => array(
                    'players' => Model_Stats_Pitching::get_stats_by_playeds($this->game->id, $team['top_id']),
                    'total' => array(),
                ),
            ),
            'bottom' => array(
                'hitting' => array(
                    'players' => Model_Stats_Hitting::get_stats_by_playeds($this->game->id, $team['bottom_id']),
                    'total' => Model_Stats_Hitting::get_stats_total($this->game->id, $team['bottom_id']),
                ),
                'pitching' => array(
                    'players' => Model_Stats_Pitching::get_stats_by_playeds($this->game->id, $team['bottom_id']),
                    'total' => array(),
                ),
            ),
        );

        return Response::forge($this->view);
    }
}
