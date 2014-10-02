<?php

namespace Fuel\Tasks;

class Backup
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r backup
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	public function run($args = null)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Backup:Run]";
		echo "\n-------------------------------------------\n\n";
	}

	public function mysql()
	{
		\Config::load('db', true);

		$name = \Config::get('db.active');

		$host = \Config::get("db.{$name}.connection.host");
		$port = \Config::get("db.{$name}.connection.port");
		$user = \Config::get("db.{$name}.backup.username");
		$pass = \Config::get("db.{$name}.backup.password");

		$backup_file = APPPATH.'/tmp/mysqldump_'.\Fuel::$env.'_'.date('Ymd');
		if (file_exists($backup_file))
			die("バックアップ先のファイルが既にあります");

		$mysqldump = shell_exec('which mysqldump');
		$mysqldump = trim($mysqldump);

		$cmd = "{$mysqldump} -u {$user} -h {$host} -P {$port}";
		if ($pass)
			$cmd .= " -p{$pass}";

		$cmd .= " bms > {$backup_file}";

		echo $cmd;
		`$cmd`;
	}

}
/* End of file tasks/backup.php */
