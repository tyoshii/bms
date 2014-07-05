<?php

class Controller_Score extends Controller_Base
{
  public function action_record_team()
  {
    $view = View::forge('score/record_team.twig');

    $game = Model_Score_Self::getSelfScores();
    //$score = Model_Games_Runningscore::find(Input::param('id'), array(
    //  'related' => array('games'),
    //));

    //$score = Model_Score_Self::find(1,array());
    //var_dump($score);

    //$view->game_info = $team_score;

    return Response::forge( $view );
  }
  public function action_record_self()
  {
    $view = View::forge('score/record_self.twig');

    $view->stats = Model_Score_Self::getSelfScores();

    $view->dispname = Common::get_dispname();
 
    return Response::forge( $view );
  }
}
