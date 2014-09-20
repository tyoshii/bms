<?php

namespace Fuel\Tasks;

class Sendmail
{

  /**
   * This method gets ran when a valid method name is not used in the command.
   *
   * Usage (from command line):
   *
   * php oil r sendmail
   *
   * @param null $args
   *
   * @return string
   */
  public function run($args = NULL)
  {
    echo "\n===========================================";
    echo "\nRunning DEFAULT task [Sendmail:Run]";
    echo "\n-------------------------------------------\n\n";

    /***************************
     * Put in TASK DETAILS HERE
     **************************/
  }

  public function test($to = false)
  {
    echo "\n===========================================";
    echo "\nRunning task [Sendmail:test]";
    echo "\n-------------------------------------------\n\n";

    /***************************
     * Put in TASK DETAILS HERE
     **************************/
    if ( ! $to)
      die('第一引数にテストメール送信先を指定してください');

    $email = \Email::forge();
    $email->from('no-reply@bm-s.info');
    $email->to($to);

    $email->subject('bm-s.infoからのテストメール');
    $body = <<<__BODY__
※このメールはシステムから自動的に送信されています。

BMS - Baseball Management System
http://bm-s.info

======================================================
野球の成績管理ができるシステムです。

今までの成績管理では、
チームの代表/担当の人がスコア入力を全て担当していたと思いますが、
成績の入力はどうしても面倒・・

このシステムでは所属選手一人ひとりが自分のアカウントを持ち、
自分の成績を入力することが出来ます。
======================================================


チームの成績管理ならBMSへ！
__BODY__;
    $email->body($body);

    try
    {
      $email->send();
    } catch (\EmailValidationFailedException $e)
    {
      echo "hoge";
      echo $e->getMessage();
    } catch (\EmailSendingFailedException $e)
    {

      echo "hoge";
      echo $e->getMessage();
    }

    echo "メール送信テスト完了";
  }

}
/* End of file tasks/sendmail.php */
