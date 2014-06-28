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

  public static function sendmail($to, $subject, $body)
  {
    $email = Email::forge();

    $email->from('no-reply@bm-s.info');
    $email->to($to);

    $email->subject(self::$_subject_header.$subject);

    $email->body(self::$_body_header.$body.self::$_body_footer);

    $email->send();
  }

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

}
