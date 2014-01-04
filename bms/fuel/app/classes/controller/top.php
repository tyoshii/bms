<?php

class Controller_Top extends Controller
{

	public function action_index()
	{
		return Response::forge(View::forge('top/index.twig'));
	}

}
