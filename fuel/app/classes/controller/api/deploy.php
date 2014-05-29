<?php

class Controller_Api_Deploy extends Controller
{
  public function action_index()
  {
    $data = json_decode(Input::post('payload'), true);

    echo $data['ref']."\n";
    if ( $data['ref'] === 'refs/heads/master' )
    {
      try {
        chdir("/home/tyoshii/git/tyoshii/bms/");

        // service out
        `/usr/bin/env php oil r service:out`;

        // git-pull
        `git checkout master`;
        `git pull origin master`;

        // oil
        `/usr/bin/env php oil r migrate:current`;

        // deploy
        chdir("/home/tyoshii/git/tyoshii/bms/deploy/");
        `/usr/bin/env perl deploy.pl force`;

        // service in
        chdir("/home/tyoshii/git/tyoshii/bms/");
        `/usr/bin/env php oil r service:in`;

        echo "DEPLOY DONE";
      
      } catch (Exception $e) {
        echo $e->getMessage();

        // service in
        chdir("/home/tyoshii/git/tyoshii/bms/");
        `/usr/bin/env php oil r service:in`;
      }
    }
  }
}
