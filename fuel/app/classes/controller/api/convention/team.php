<?php

class Controller_Api_Convention_Team extends Controller_Rest
{
    /**
     * add convention team
     */
    public function post_add()
    {
        // validation
        // :p

        $convention_id = Input::post('convention_id');
        $team_id = Input::post('team_id');

        Model_Conventions_Team::add($convention_id, $team_id);    

    return $this->response(array(
      'status' => 200,
      'message' => 'OK',
    ));
    }
    
    /**
     * remove team from convention
     */
    public function post_remove()
    {
        // validation
        // :p

        $convention_id = Input::post('convention_id');
        $team_id = Input::post('team_id');

        Model_Conventions_Team::remove($convention_id, $team_id);    

    return $this->response(array(
      'status' => 200,
      'message' => 'OK',
    ));
    }
}
