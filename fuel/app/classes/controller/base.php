<?php

class Controller_Base extends Controller
{
    public $_global = array();

    public function before()
    {
        // global value
        View::set_global('fuel_env', Fuel::$env);
        View::set_global('usericon', Common::get_usericon_url());
        View::set_global('is_mobile', Agent::is_mobiledevice());

        // default view
        $path = implode('/', Request::main()->route->segments);
        $path .= '.twig';

        if (is_file(APPPATH.DS.'views'.DS.$path)) {
            $this->view = View::forge($path);
        }
    }

    public function after($res)
    {
        // set global method
        View::set_global('global', $this->_global);

        // trace log
        $status = $res ? $res->status : '999';
        Log::trace($status);

        return $res;
    }

    public function get_global($key)
    {
        return array_key_exists($key, $this->_global) ? $this->_global[$key] : null;
    }

    public function set_global($key, $val)
    {
        $this->_global[$key] = $val;
    }

    /**
     * game object set to global.
     *
     * @param string game_id
     *
     * @return bool
     */
    public function set_global_game_object($game_id = false)
    {
        $game_id = $game_id ?: $this->param('game_id');

        if (!$game_id) {
            return false;
        }

        if (!$this->game = Model_Game::find($game_id)) {
            Session::set_flash('error', '試合情報が取得できませんでした。');

            return Response::redirect('/');
        }

        // set global
        $this->set_global('game', $this->game);

        return true;
    }
}
