<?php

namespace Fuel\Tasks;

class Version
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r version
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	public function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Version:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 * Put in TASK DETAILS HERE
		 **************************/
		echo "Version   : ".\Config::get('system.version')."\n";
		echo "Update At : ".\Config::get('system.update_at')."\n";
	}


	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r version:index "arguments"
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	public function up($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Version:up]";
		echo "\n-------------------------------------------\n\n";

		$CHANGELOG = DOCROOT.'/CHANGELOG.md';
		$version = `cat $CHANGELOG | grep Version | head -1 | awk '{ print $3}'`;

		\Config::set('system.version', trim($version));
		\Config::set('system.update_at', date('Y/m/d H:i:s'));

		\Config::save('system', 'system');

		echo "version up done. config/system.php update";
	}

}
/* End of file tasks/version.php */
