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
	 * @param null $args
	 *
	 * @return string
	 */
	public function run($args = null)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Service:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 * Put in TASK DETAILS HERE
		 **************************/
		if (\Config::get('bms.maintenance') === 'on')
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
	 * @param null $args
	 *
	 * @return string
	 */
	public function out($args = null)
	{
		echo "\n===========================================";
		echo "\nRunning task [Service:Out]";
		echo "\n-------------------------------------------\n\n";

		\Config::set('bms.maintenance', 'on');
		\Config::save('bms', 'bms');

		echo 'service out... go to maintenance mode';
	}

	/*
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r service:in "arguments"
	 *
	 * @return string
	 */
	public function in($args = null)
	{
		echo "\n===========================================";
		echo "\nRunning task [Service:In]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 * Put in TASK DETAILS HERE
		 **************************/
		\Config::set('bms.maintenance', 'off');
		\Config::save('bms', 'bms');

		echo 'service in... start !!';
	}

}
/* End of file tasks/service.php */
