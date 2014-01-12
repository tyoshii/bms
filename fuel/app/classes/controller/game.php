<?php

class Controller_Game extends Controller_Base
{
  public function action_list()
  {
    // disp game list

    return Response::forge( View::forge('game/list.twig') ) ;
  } 

  public function post_create()
  {
    // regist db

    // success
    Response::redirect(Uri::create('game/list'));
  }

	public function action_create()
	{
    // form disp

    // validation

    return Response::forge( View::forge('game/create.twig') );
	}

	public function action_edit()
	{
    if (Input::post())
    {
      // 成績登録処理
    }

    // ゲームデータ表示
    // 権限のあるチームのみ表示

    // 複数権限もっている場合はタブで両方表示

    return Response::forge( View::forge('game/edit.twig') );
	}

	public function action_delete()
	{
    // Fieldset::forge

    return Response::forge( View::forge('game/delete.twig') );
	}

  public function post_delete()
  {
    // validation

    // delete

    Response::redirect(Uri::create('/game/list'));
  }

}
