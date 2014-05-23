<?php

class Controller_Deploy extends Controller
{
	public function action_index()
	{
    error_log(var_export(Input::post(), true));
  }
}
