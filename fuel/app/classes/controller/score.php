<?php

class Controller_Score extends Controller_Base
{
  public function action_index()
  {
    return Response::forge( View::forge('score/index.twig') );
  }
}
