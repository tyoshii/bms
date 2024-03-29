<?php

class Controller_Top extends Controller_Base
{
    /**
     * トップページ.
     */
    public function action_index()
    {
        $view = Theme::instance()->view('top.twig');

        $view->teams = Model_Team::query()->order_by('updated_at', 'DESC')->get();

        if (Auth::check()) {
            // 所属チーム
            $view->my_teams = Model_Team::get_belong_teams();
        } else {
            Auth::logout();
        }

        return Response::forge($view);
    }

    /**
     * login.
     */
    public function action_login()
    {
        if (Auth::check()) {
            return Response::redirect('/');
        }

        if (Input::get('url')) {
            Session::set('redirect_to', Input::get('url'));
        }

        $view = View::forge('login.twig');

        return Response::forge($view);
    }

    /**
     * logout.
     */
    public function action_logout()
    {
        // TODO: OpenID連携の場合は don't remember したほうがいい？
        Auth::logout();

        return Response::redirect(Uri::create('/'));
    }

    public function action_404()
    {
        return Response::forge(View::forge('errors/404.twig'), 404);
    }

    /**
     * force login page. only development/test.
     */
    public function action_force_login()
    {
        if (Fuel::$env === 'test' or Fuel::$env === 'development') {
            $username = $this->param('username');

            if ($user = Model_User::find_by_username($username)) {
                Auth::force_login($user->id);
            } else {
                Session::set_flash('error', '存在しないユーザー名です。');
            }
        } else {
            Session::set_flash('error', '不正なURLです');
        }

        return Response::redirect('/');
    }

    /**
     * about page.
     */
    public function action_about()
    {
        $view = View::forge('about.twig');

        return Response::forge($view);
    }
}
