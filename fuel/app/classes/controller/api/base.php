<?php

class Controller_Api_Base extends Controller_Rest
{
	/**
	 * 成功時のテンプレート
	 */
	public function success($content)
	{
		$content = $content ?: array(
			'status'  => 200,
			'message' => 'OK',
		);

		return $this->response($content);
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
