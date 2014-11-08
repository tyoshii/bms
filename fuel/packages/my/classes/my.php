<?php

namespace My;

class MyException extends \FuelException {}

class Agent extends \Fuel\Core\Agent
{
	public static function _init()
	{
		// fetch and store the user agent
		static::$user_agent = \Input::server('http_user_agent', '');

		// fetch and process the configuration
		\Config::load('agent', true);

		static::$config = array_merge(static::$defaults, \Config::get('agent', array()));

		// validate the browscap configuration
		if ( ! is_array(static::$config['browscap']))
		{
			static::$config['browscap'] = static::$defaults['browscap'];
		}
		else
		{
			if ( ! array_key_exists('enabled', static::$config['browscap']) or ! is_bool(static::$config['browscap']['enabled']))
			{
				static::$config['browscap']['enabled'] = true;
			}

			if ( ! array_key_exists('url', static::$config['browscap']) or ! is_string(static::$config['browscap']['url']))
			{
				static::$config['browscap']['url'] = static::$defaults['browscap']['url'];
			}

			if ( ! array_key_exists('file', static::$config['browscap']) or ! is_string(static::$config['browscap']['file']))
			{
				static::$config['browscap']['file'] = static::$defaults['browscap']['file'];
			}

			if ( ! array_key_exists('method', static::$config['browscap']) or ! is_string(static::$config['browscap']['method']))
			{
				static::$config['browscap']['method'] = static::$defaults['browscap']['method'];
			}
			static::$config['browscap']['method'] = strtolower(static::$config['browscap']['method']);
		}

		// validate the cache configuration
		if ( ! is_array(static::$config['cache']))
		{
			static::$config['cache'] = static::$defaults['cache'];
		}
		else
		{
			if ( ! array_key_exists('driver', static::$config['cache']) or ! is_string(static::$config['cache']['driver']))
			{
				static::$config['cache']['driver'] = static::$defaults['cache']['driver'];
			}

			if ( ! array_key_exists('expiry', static::$config['cache']) or ! is_numeric(static::$config['cache']['expiry']) or static::$config['cache']['expiry'] < 7200)
			{
				static::$config['cache']['expiry'] = static::$defaults['cache']['expiry'];
			}

			if ( ! array_key_exists('identifier', static::$config['cache']) or ! is_string(static::$config['cache']['identifier']))
			{
				static::$config['cache']['identifier'] = static::$defaults['cache']['identifier'];
			}
		}

		// do we have a user agent?
		if (static::$user_agent)
		{
			// try the build in get_browser() method
			if (ini_get('browscap') == '' or false === $browser = get_browser(static::$user_agent, true))
			{
				// use Crossjoin\Browscap
				$browscap = new \Crossjoin\Browscap\Browscap();
				$browser = (array) $browscap->getBrowser()->getData();

				// if it fails, emulate get_browser()
				// $browser = static::get_from_browscap();
			}

			if ($browser)
			{
				// save it for future reference
				static::$properties = array_change_key_case($browser);
			}
		}
	}
}

class Response extends \Fuel\Core\Response
{
    /**
     * Override Fuel\Core\Response redirect method
     *
     * @param   string  $url     The url
     * @param   string  $method  The redirect method
     * @param   int     $code    The redirect status code
     *
     * @return  void
     */
    public static function redirect($url = '', $method = 'location', $code = 302)
    {
        $response = new static;
        $response->set_status($code);

        if (strpos($url, '://') === false)
        {
            $url = $url !== '' ? \Uri::create($url) : \Uri::base();
        }

        strpos($url, '*') !== false and $url = \Uri::segment_replace($url);

        if ($method == 'location')
        {
            $response->set_header('Location', $url);
        }
        elseif ($method == 'refresh')
        {
            $response->set_header('Refresh', '0;url='.$url);
        }
        else
        {
            return;
        }

        if (\Fuel\Core\Fuel::$env != 'test')
        {
            $response->send(true);
            exit;
        }

        $response->send(true);

        return $response;
    }
}
