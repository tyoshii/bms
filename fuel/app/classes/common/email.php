<?php

class Common_Email
{
	private static $_subject_header = '[bms]';
	private static $_body_header = <<<__HEADER__
※このメールはシステムから自動送信されています。
※身に覚えのない場合、お手数ですが本メールは破棄してください。

__HEADER__;
	private static $_body_footer = <<<__FOOTER__


=====================================
BMS - Baseball Management System
http://bm-s.info
__FOOTER__;

	/**
	 * メールを送信する
	 */
	public static function sendmail($to, $subject, $body)
	{
		$email = Email::forge();

		$email->from('no-reply@bm-s.info');
		$email->to($to);

		$email->subject(self::$_subject_header.$subject);

		$email->body(self::$_body_header.$body.self::$_body_footer);

		$email->send();
	}

	/**
	 * パスワードのリセット
	 */
	public static function reset_password($username, $email, $time, $crypt)
	{
		$url = Uri::base(false);

		$subject = 'パスワードリセットのお知らせ';
		$body = <<<__BODY__
$username 様

システムからパスワードのリセット依頼が発行されました。

以下のURLからパスワードのリセットを行い、
ログイン後に新しいパスワードを設定してください。

{$url}reset_password/?u={$username}&t={$time}&c={$crypt}
__BODY__;

		self::sendmail($email, $subject, $body);
	}

	/**
	 * スタメン登録されたことの通知
	 */
	public static function regist_starter($game_id, $team_id, $player_id)
	{
		// to
		$to = Model_Player::get_player_email($player_id);
		if ( ! $to)
			return false;

		// subject
		$subject = '出場選手登録されました';

		// body
		$game = Model_Game::find($game_id);
		$date = $game->date;
		$opponent_team_name = $game->games_teams->opponent_team_name;

		$body = <<<__BODY__

以下の試合に出場選手登録されました。
$date vs $opponent_team_name

成績入力をしましょう。

__BODY__;

		$url_path = Model_Team::find($team_id)->url_path;
		$uri      = Uri::base(false);

		$body .= '野手成績：'.$uri.'team/'.$url_path.'/game/'.$game->id.'/edit/batter';
		$body .= '投手成績：'.$uri.'team/'.$url_path.'/game/'.$game->id.'/edit/pitcher';

		// sendmail
		self::sendmail($to, $subject, $body);
	}

	/**
	 * 成績入力のリマインド
	 */
	public static function remind_game_stats($player_id, $paths)
	{
		$name = Model_Player::find($player_id)->name;
		$email = Model_Player::get_player_email($player_id);
		if ( ! $email)
			return false;

		$subject = '成績入力のお願い';
		$body = <<<__BODY__

{$name} さん

成績入力の完了していない試合があります。
成績を入力し、完了してください。

__BODY__;

		$url = Uri::base(false);
		foreach ($paths as $path)
		{
			$body .= $url.$path."\n";
		}

		self::sendmail($email, $subject, $body);
	}
}
