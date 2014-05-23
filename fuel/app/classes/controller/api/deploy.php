<?php

class Controller_Api_Deploy extends Controller
{
  public function action_index()
  {
    $data = json_decode(Input::post('payload'), true);

    echo $data['ref']."\n";
    if ( $data['ref'] === 'refs/heads/master' )
    {
        // git-pull
        chdir("/home/tyoshii/git/tyoshii/bms/");
        `git checkout master`;
        `git pull origin master`;

        // oil
        `/usr/bin/env php oil r migrate:current`;

        // deploy
        chdir("/home/tyoshii/git/tyoshii/bms/deploy/");
        `/usr/bin/env perl deploy.pl force`;

        echo "DEPLOY DONE";
    }
  }
}
