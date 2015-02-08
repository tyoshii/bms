<?php

class Controller_Api_Stats extends Controller_Api_Base
{
	/**
	 * 入力された成績にエラーが無いかをチェックするAPI
	 * @get integer game_id
	 */
	public function get_check()
	{
		$game_id = Input::get('game_id', null);

		// game_id validation
		if (is_null($game_id) or ! Model_Game::find($game_id))
		{
			$message = 'game_idが正しく指定されていません。';
			Log::error($message);
			return $this->error(400, $message);
		}

		// check logic
		// TODO:

		return $this->success();
	}
}
