<?php

class Controller_Deploy extends Controller
{
  public function action_index()
  {
    $data = json_decode(Input::post('payload'), true);

    echo $data['ref'];
    if ( $data['ref'] === 'refs/heads/test' )
    {
        chdir("/home/tyoshii/git/tyoshii/bms/");
        `git checkout master`;
        `git pull origin mater`;
    }
  }
}
