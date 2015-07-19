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
		// developmentではログイン中のユーザーにしかメールを送信しない
		if (Auth::check() && Fuel::$env === 'development')
		{
			if ($to !== Auth::get_email())
			{
				Log::debug('ログイン時はログインユーザーのメールにしか送信しません。');
				Log::debug("$to へのメールはスキップされました。");
				return false;
			}	
		}

		// テスト用のメールアドレス以外は送信しない
		$emails = Config::get('bms.test_email', array());
		foreach ($emails as $email)
		{
			if (preg_match("/$email/", $to))
			{
				goto SEND_EMAIL;
			}
		}

		Log::debug('config/bms.phpに設定されているtest_emailにマッチしないアドレスには送信しません。');
		Log::debug("$to へのメールはスキップされました。");
		return false;

SEND_EMAIL:

		$email = Email::forge();

		$email->from('no-reply@bm-s.info');
		$email->to($to);

		$email->subject(self::$_subject_header.$subject);

		$email->body(self::$_body_header.$body.self::$_body_footer);

		$email->send();

		Log::warning("sendmail -> to:$to subject:$subject");
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

		static::sendmail($email, $subject, $body);
	}

	/**
	 * new user regist, confirm mail send
	 *
	 * @param string email
	 * @param string time
	 * @param string crypt
	 *
	 * @return boolean
	 */
	public static function regist_user()
	{
		// already check
		if (Model_User::find_by_email(Input::post('email')))
		{
			Session::set_flash('error', 'そのメールアドレスは既に登録済みです。');
			return false;
		}

		// create crypt
		$time     = time();
		$fullname = Input::post('furllname');
		$email    = Input::post('email');
		$password = Input::post('password', null);

		$crypt = Crypt::encode(implode("\t", array($time,$fullname,$email,$password)));

		$url = sprintf('%sregister/confirm/?t=%s&c=%s', Uri::base(false), $time ,$crypt);

		$subject = 'BMS本登録のご案内';
		$body = <<<__BODY__
本登録を完了するには、以下のリンクをクリックして下さい。

{$url}
__BODY__;

		static::sendmail($email, $subject, $body);

		return true;
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
		$opponent_team_name = $game->games_team->opponent_team_name;

		$body = <<<__BODY__

以下の試合に出場選手登録されました。
$date vs $opponent_team_name

成績入力をしましょう。

__BODY__;

		$url_path = Model_Team::find($team_id)->url_path;
		$uri      = Uri::base(false);

		$body .= '野手成績：'.$uri.'team/'.$url_path.'/game/'.$game->id.'/edit/batter';
		$body .= "\n";
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

	/**
	 * チーム内連絡
	 */
	public static function team_notice($team_id, $subject, $body)
	{
		foreach (Model_Player::query()->where('team_id', $team_id)->get() ?: array() as $team)
		{
			$email = Model_User::find_by_username($team->username);

			if ($email)
			{
				static::sendmail($email->email, $subject, $body);	
			}
		}
	}
}
