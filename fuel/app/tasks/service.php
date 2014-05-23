<?php

namespace Fuel\Tasks;

class Service
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r service
	 *
	 * @return string
	 */
	public function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Service:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/
    \Config::load('maintenance', 'm');
    if ( \Config::get('m.mode') === 'on' )
    {
      echo "現在メンテナンスモードです";
    }
    else
    {
      echo "サービスインしています";
    }
	}



	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r service:out "arguments"
	 *
	 * @return string
	 */
	public function out($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Service:Out]";
		echo "\n-------------------------------------------\n\n";

    \Config::load('maintenance', 'm');
    \Config::set('m.mode', 'on');
    \Config::save('maintenance', 'm');

    echo 'service out... go to maintenance mode';
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r service:in "arguments"
	 *
	 * @return string
	 */
	public function in($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Service:In]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/
    \Config::load('maintenance', 'm');
    \Config::set('m.mode', 'off');
    \Config::save('maintenance', 'm');

    echo 'service in... start !!';
	}

}
/* End of file tasks/service.php */
