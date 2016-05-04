<?php

class Controller_Convention extends Controller_Base
{
    public $view = '';

    public function before()
    {
        parent::before();

        // view set
        $action = Request::main()->action;
        $this->view = View::forge('convention/'.$action.'.twig');

        // global value
        $this->convention = Model_Convention::find($this->param('convention_id'));
        $this->game = Model_Game::find($this->param('game_id'));

        $this->set_global('convention', $this->convention);
        $this->set_global('game', $this->game);
    }

    /**
     * display convention list.
     */
    public function action_index()
    {
        $this->view->lists = Model_Convention::get_own_list();

        return Response::forge($this->view);
    }

    /**
     * add convention.
     */
    public function action_add()
    {
        // form
        $form = Model_Convention::get_form();
        $this->view->set_safe('form', $form->build(Uri::current()));

        return Response::forge($this->view);
    }

    public function post_add()
    {
        $form = Model_Convention::get_form();
        $val = $form->validation();

        if ($val->run()) {
            Model_Convention::regist($val->validated());

            Session::set_flash('info', '新しく大会を登録しました。');

            return Response::redirect(Uri::create('/convention'));
        } else {
            Session::set_flash('error', $val->show_errors());
        }

        $form->repopulate();
        $this->view->set_safe('form', $form->build(Uri::current()));

        return Response::forge($this->view);
    }

    /**
     * convention detail.
     */
    public function action_detail()
    {
        $id = $this->convention->id;

        $this->view->teams = Model_Conventions_Team::get_entried_teams($id);
        $this->view->games = Model_Conventions_Game::get_all_games($id);

        return Response::forge($this->view);
    }

    /**
     * convention update.
     */
    public function action_update()
    {
        $form = Model_Convention::get_form($this->convention);
        $this->view->set_safe('form', $form->build(Uri::current()));

        return Response::forge($this->view);
    }
    public function post_update()
    {
        return Response::forge($this->view);
    }

    /**
     * convention stats.
     */
    public function action_stats()
    {
        return Response::forge($this->view);
    }

    /**
     * convention games list.
     */
    public function action_games()
    {
        return Response::forge($this->view);
    }
}
