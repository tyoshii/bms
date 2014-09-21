<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * Set error reporting and display errors settings.  You will want to change these when in production.
 */
error_reporting(-1);

if (isset($_SERVER['FUEL_ENV']) && $_SERVER['FUEL_ENV'] !== 'production')
{
  ini_set('display_errors', 1);
}
else
{
  ini_set('display_errors', 0);
}

/**
 * Website document root
 */
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);

/**
 * Path to the application directory.
 */
define('APPPATH', realpath(__DIR__.'/../fuel/app/').DIRECTORY_SEPARATOR);

/**
 * Path to the default packages directory.
 */
define('PKGPATH', realpath(__DIR__.'/../fuel/packages/').DIRECTORY_SEPARATOR);

/**
 * The path to the framework core.
 */
define('COREPATH', realpath(__DIR__.'/../fuel/core/').DIRECTORY_SEPARATOR);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Boot the app
require APPPATH.'bootstrap.php';

// Generate the request, execute it and send the output.
try
{
  // maintenance mode
  if (\Config::get('bms.maintenance') === 'on')
  {
	if (Auth::has_access('admin.admin'))
	{
	  echo "現在、メンテナンスモードです";
	  $response = Request::forge()->execute()->response();
	}
	else
	{
	  \Request::reset_request(true);
	  $response = Response::forge(View::forge('maintenance.twig'));
	}
  }
  else
  {
	$response = Request::forge()->execute()->response();
  }
} catch (HttpNotFoundException $e)
{
  \Request::reset_request(true);

  $route = array_key_exists('_404_', Router::$routes) ? Router::$routes['_404_']->translation : Config::get('routes._404_');

  if ($route instanceof Closure)
  {
	$response = $route();

	if (!$response instanceof Response)
	{
	  $response = Response::forge($response);
	}
  }
  elseif ($route)
  {
	$response = Request::forge($route, false)->execute()->response();
  }
  else
  {
	throw $e;
  }
}

// Render the output
$response->body((string)$response);

// This will add the execution time and memory usage to the output.
// Comment this out if you don't use it.
if (strpos($response->body(), '{exec_time}') !== false or strpos($response->body(), '{mem_usage}') !== false)
{
  $bm = Profiler::app_total();
  $response->body(
	  str_replace(
		  array('{exec_time}', '{mem_usage}'),
		  array(round($bm[0], 4), round($bm[1] / pow(1024, 2), 3)),
		  $response->body()
	  )
  );
}

$response->send(true);
