<?php

class Controller_Convention_Game extends Controller_Convention
{
    public $view = '';

    public function before()
    {
        parent::before();

        // view set
        $action = Request::main()->action;
        $this->view = View::forge('convention/game/'.$action.'.twig');
    }

    /**
     * display convention game list.
     */
    public function action_index()
    {
        return Response::forge($this->view);
    }

    /**
     * display convention game detail.
     */
    public function action_detail()
    {
        return Response::forge($this->view);
    }

    /**
     * add convention game.
     */
    public function action_add()
    {
        $form = Model_Game::get_regist_form_convention($this->convention->id);
        $this->view->set_safe('form', $form->build());

        return Response::forge($this->view);
    }

    public function post_add()
    {
        $form = Model_Game::get_regist_form_convention($this->convention->id);
        $val = $form->validation();

        if ($val->run()) {
            // 同じチーム同士の対戦だったらエラー
            $top = Input::post('top');
            $bottom = Input::post('bottom');
            if ($top === $bottom) {
                Session::set_flash('error', '対戦チームが同じチーム同士です。');
            } else {
                if (Model_Conventions_Game::regist($this->convention->id)) {
                    Session::set_flash('info', '試合を追加しました。');
                } else {
                    Session::set_flash('error', '試合の追加に失敗しました。システムエラーです。');
                }

                // 成功しても失敗しても、大会トップに戻す
                $url = '/convention/'.$this->convention->id.'/detail';

                return Response::redirect($url);
            }
        } else {
            Session::set_flash('error', $val->show_errors());
        }

        $form->repopulate();
        $this->view->set_safe('form', $form->build());

        return Response::forge($this->view);
    }

    /**
     * update convention game.
     */
    public function action_update()
    {
        return Response::forge($this->view);
    }

    public function post_update()
    {
        return Response::forge($this->view);
    }
}
