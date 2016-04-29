<?php

class Controller_Team extends Controller_Base
{
    public $_team = array();
    public $_player = array();
    public $_alerts = array();
    public $_team_admin = false;

    public function before()
    {
        parent::before();

        // team 情報
        if ($url_path = $this->param('url_path')) {
            if (!$this->_team = Model_Team::find_by_url_path($url_path)) {
                Session::set_flash('error', $url_path.'正しいチーム情報が取得できませんでした。');

                return Response::redirect('error/404');
            }
        }

        if ($this->_team) {
            // チーム管理者権限があるかどうか
            if (Model_Player::has_team_admin($this->_team->id)) {
                $this->_team_admin = true;
            }

            // チームページへのURL
            $this->_team->href = '/team/'.$this->_team->url_path;
        }

        // ログイン中ユーザーの選手情報/alert情報
        if (Auth::check() and $this->_team) {
            $this->_player = Model_Player::query()->where(array(
                array('team_id', $this->_team->id),
                array('username', Auth::get('username')),
            ))->get_one();

            if ($this->_player) {
                $this->_alerts = Model_Stats_Common::get_stats_alerts($this->_team->id, $this->_player->id);
            }
        }

        // set_global
        $this->set_global('team', $this->_team);
        $this->set_global('team_admin', $this->_team_admin);
        $this->set_global('player', $this->_player);
        $this->set_global('alerts', $this->_alerts);

        // postメソッドであれば、teamのupdated_atを更新
        // - トップページのsort keyに利用
        if (Input::post() and $this->_team) {
            $this->_team->updated_at = time();
            $this->_team->save();
        }
    }

    /**
     * チームページトップ.
     */
    public function action_index()
    {
        $view = View::forge('team/index.twig');

        // set view
        $view->games = Model_Game::get_info_by_team_id($this->_team->id);

        return Response::forge($view);
    }

    /**
     * チーム検索画面.
     */
    public function action_search()
    {
        $view = View::forge('team/search.twig');

        $query = Model_Team::query()->order_by('created_at');

        if ($q = Input::get('query')) {
            $query->where('name', 'LIKE', '%'.$q.'%');
        }

        $view->teams = $query->get();

        return Response::forge($view);
    }

    /**
     * チーム、新規登録.
     */
    public function action_regist()
    {
        $view = View::forge('team/regist.twig');

        // form
        $form = Model_Team::get_regist_form();

        if (Input::post()) {
            $val = $form->validation();

            if ($val->run()) {
                $url_path = Input::post('url_path');

                // duplicate url_path check
                // TODO: validation model or javascript check
                if (Model_Team::find_by_url_path($url_path)) {
                    Session::set_flash('error', 'その英語名は既に登録されています。');
                } else {
                    if (Model_Team::regist(Input::post())) {
                        Session::set_flash('info', '新しくチームを作成しました。');

                        return Response::redirect('/team/'.$url_path);
                    } else {
                        Session::set_flash('error', 'システムエラーが発生しました。');

                        return Response::redirect('/');
                    }
                }
            } else {
                Session::set_flash('error', $val->show_errors());
            }
        }

        $form->repopulate();
        $view->set_safe('form', $form->build(Uri::current()));

        return Response::forge($view);
    }

    /**
     * 選手一覧/個人.
     */
    public function action_player()
    {
        if ($player_id = $this->param('player_id')) {
            // 個人
            $view = View::forge('team/player/personal.twig');
            if (!$view->player = Model_Player::find($player_id)) {
                Session::set_flash('error', '選手情報が取得できませんでした');

                return Response::redirect($this->_team->href);
            }

            // 試合ごとの野手成績
            $view->stats_per_games = Model_Stats_Hitting::get_stats_per_game($player_id);
        } else {
            // 選手一覧
            $view = View::forge('team/player/list.twig');
            if (!$view->players = Model_Player::get_players($this->_team->id)) {
                Session::set_flash('error', '選手情報が取得できませんでした');

                return Response::redirect($this->_team->href);
            }
        }

        return Response::forge($view);
    }

    /**
     * 成績.
     */
    public function action_stats()
    {
        $view = View::forge('team/stats.twig');

        $view->result = Model_Score_Team::get_team_win_lose($this->_team->id, array());
        $view->stats = array(
            'teams' => Model_Score_Team::get_team_score($this->_team->id),
            'selfs' => Model_Score_Self::get_self_scores($this->_team->id),
        );

        return Response::forge($view);
    }

    /**
     * このチームに入る、っていうオファー
     */
    public function action_offer()
    {
				if (Input::post()) {

					// 未ログインの場合は、ログインページヘ
					if (! Auth::check()) {
						return Response::redirect('/login?url='.Uri::current());
					}

					// 加入者にメール
					$time = time();
					$username = Auth::get('username');
					$crypt = Crypt::encode($time.$username);
					$offer_confirm_url = sprintf('%soffer/confirm?t=%s&u=%s&c=%s', Uri::base(false), $time, $username, $crypt);

					$subject = '入部オファーが届いています';
					$body = <<<__BODY__
チーム「{$this->_team->name}」に入部オファーが届いています。
以下のURLから入部オファーを確認してください。

$offer_confirm_url
__BODY__;

					$admins = Model_Team::get_admins($this->_team->id);
					foreach ($admins as $admin) {
						Common_Email::sendmail(Model_Player::get_player_email($admin->id), $subject, $body);
					}

					// 成功ページ
					Session::set_flash('info', 'チーム管理者にチーム加入リクエストを送付しました。');
					return Response::redirect($this->_team->href);
				}

        $view = View::forge('team/offer.twig');
        return Response::forge($view);
    }
}
