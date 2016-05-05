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

        $backup_file = APPPATH.'/tmp/mysqldump_'.\Fuel::$env.'_'.date('Ymd').'_'.time();
        if (file_exists($backup_file)) {
            echo $backup_file."\n";
            die('バックアップ先のファイルが既にあります');
        }

        $mysqldump = shell_exec('which mysqldump');
        $mysqldump = trim($mysqldump);

        $cmd = "{$mysqldump} -u {$user} -h {$host} -P {$port}";
        if ($pass and $pass !== '') {
            $cmd .= " -p{$pass}";
        }

        $cmd .= " bms > {$backup_file}";

        echo $cmd."\n";
        `$cmd`;

        // backup_fileをgzip
        $gzip = `which gzip`;
        $gzip = trim($gzip);
        `$gzip $backup_file`;
        $backup_file .= '.gz';

        echo "backup_file gzip : $backup_file\n";

        // バックアップ（gmailへの添付ファイル）
        $to = 'tyoshii716@gmail.com';
        $subject = 'mysql bakcup '.\Fuel::$env;
        $body = '';
        \Common_Email::sendmail($to, $subject, $body, $backup_file);
    }
}
/* End of file tasks/backup.php */
