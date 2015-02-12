<?php

class Controller_Player extends Controller_Base
{
	public function action_index()
	{
		$id = $this->param('player_id');

		if ($player = Model_Player::find($id))
		{
			$url = sprintf('/team/%s/player/%s', $player->team->url_path, $id);
			return Response::redirect($url);
		}

		Session::set_flash('error', '存在しない選手です');
		return Response::redirect('/');
	}
}
