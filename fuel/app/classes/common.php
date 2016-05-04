<?php

class Common
{
    /**
     * generate random string.
     *
     * @return string
     */
    public static function random_string()
    {
        return time().rand(1000, 9999).rand(1000, 9999);
    }

    /**
     * check crypt time.
     *
     * @return bool
     */
    public static function check_crypt_time($time)
    {
        return time() - $time > 60 * 60;
    }

    public static function db_clean($table, $where)
    {
        $query = DB::delete($table);

        if ($where) {
            $query->where($where);
        }

        $query->execute();
    }

    public static function debug($v)
    {
        echo '<pre>';
        print_r($v);
        exit;
    }

    public static function redirect($uri)
    {
        $redirect_to = Session::get('redirect_to');
        if (!$redirect_to) {
            $redirect_to = $uri ? $uri : Uri::current();
        }

        Response::redirect(Uri::create($redirect));
    }

    public static function get_dispname()
    {
        $info = Auth::get_profile_fields();
        $name = isset($info['dispname']) ? $info['dispname'] : Auth::get_screen_name();

        return $name;
    }

    public static function update_user($props)
    {
        $info = Auth::get_profile_fields();

        foreach ($props as $key => $val) {
            $info[$key] = $val;
        }

        Auth::update_user($info, Auth::get_screen_name());
    }

    public static function get_usericon_url()
    {
        $email = md5(Auth::get_email());

        $gravatar_url = 'http://www.gravatar.com/avatar/'.$email.'.jpg';
        $bms_url = Uri::base(false).'image/usericon/default.jpg';

        return sprintf('%s?d=%s', $gravatar_url, $bms_url);
    }

    /**
     * ログイン後にリダイレクトする先のURL.
     */
    public static function get_url_redirect_after_login()
    {
        // セッションにredirectURLが入っている場合
        if ($url = Session::get('redirect_to')) {
            Session::delete('redirect_to');

            return $url;
        }

        // 所属チームがある場合は、チームページへredirect
        $team = Model_Team::get_belong_team();
        if ($team) {
            return '/team/'.$team->url_path;
        }

        // default is top page
        return '/';
    }
}
