<?php

class Log extends \Fuel\Core\Log
{
    public static function trace($status = 999, $msg = '')
    {
        $msg = sprintf('IP:%s username:%s uri:%s status:%s %s',
            Input::real_ip(),
            Auth::get('username', '(no login'),
            Uri::current(),
            $status,
            $msg
        );

        static::warning($msg, 'TRACE');
    }

    public static function get_log_path()
    {
        $y = date('Y');
        $m = date('m');
        $d = date('d');

        return sprintf('%s/logs/%s/%s/%s.php', APPPATH, $y, $m, $d);
    }
}
