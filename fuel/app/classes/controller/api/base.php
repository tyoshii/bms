<?php

class Controller_Api_Base extends Controller_Rest
{
	/**
	 * 成功時のテンプレート
	 */
	public function success()
	{
		return $this->response(array(
			'status'  => 200,
			'message' => 'OK',
		));
	}

	/**
	 * 失敗時のテンプレート
	 */
	public function error($status = 500, $message = 'Internal Error')
	{
		return $this->response(array(
			'status'  => $status,
			'message' => $message,
		));
	}
}
