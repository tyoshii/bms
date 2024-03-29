<?php

class Controller_Api_Deploy extends Controller
{
    public function action_index()
    {
        $data = json_decode(Input::post('payload'), true);

        // debug
        echo $data['ref']."\n";

        if ($data['ref'] === 'refs/heads/master') {
            self::_deploy(
                '/home/tyoshii/git/tyoshii/bms/',
                'master',
                'production',
                'bms.list'
            );
        }

        if ($data['ref'] === 'refs/heads/staging') {
            self::_deploy(
                '/home/tyoshii/git/tyoshii/bms_staging/',
                'staging',
                'staging',
                'bms_staging.list'
            );
        }
    }

    private static function _deploy($git_dir, $branch, $fuel_env, $deploy_list)
    {
        try {
            chdir($git_dir);

            // service out
            `FUEL_ENV={$fuel_env} /usr/bin/env php oil r service:out`;

            // git-pull
            `git checkout $branch`;
            `git pull origin $branch`;

            // submodule
            `git submodule update`;

            // oil
            // stagingとproductionで同じデータを見ている場合、
            // stagingでmigrateしたあとにproductionでmigrateするとエラーになる？
            `FUEL_ENV={$fuel_env} /usr/bin/env php oil r migrate -all`;

            // composer
            `/usr/bin/env php composer.phar update`;

            // deploy
            chdir($git_dir.'/deploy/');
            `/usr/bin/env perl deploy.pl {$deploy_list} force`;

            // service in
            chdir($git_dir);
            `FUEL_ENV={$fuel_env} /usr/bin/env php oil r service:in`;

            // version up
            `/usr/bin/env php oil r version:up`;
            `/usr/bin/env php oil r version`;

            echo 'DEPLOY DONE';
        } catch (Exception $e) {
            echo $e->getMessage();

            // service in
            chdir($git_dir);
            `FUEL_ENV={$fuel_env} /usr/bin/env php oil r service:in`;
        }
    }
}
