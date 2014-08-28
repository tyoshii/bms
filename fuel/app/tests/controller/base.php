<?php

/**
 * Tests for Controler_Base
 *
 * @group App
 * @group Controller
 * @group Controller_Base
 */
class Test_Controller_Base extends \TestCase
{
  protected function setUp()
  {
    parent::setUp();
  }
  protected function tearDown()
  {
    parent::tearDown();
  }

  /**
   *
   */
  public function test_最初アクセスすると未ログイン状態()
  {
    $res = Request::forge('/')->execute()->response();

    $this->assertFalse(Auth::check());
  } 

  /**
   *
   */
  public function test_ログイン状態でアクセスするとログイン状態に()
  {
    $id = Model_User::find('first')->id;
    Auth::force_login($id);
    
    $res = Request::forge('/')->execute()->response();

    $this->assertTrue(Auth::check());

    Auth::logout();
  }

  /**
   *
   */
  public function test_未ログイン状態でPOSTリクエストを送ると、ログインを検証()
  {
    $_POST['username'] = '';
    $_POST['password'] = '';

    $res = Request::forge('/')->set_method('POST')->execute()->response();

    $this->assertFalse(Auth::check());
    $this->assertSame('ログインに失敗しました', Session::get_flash('error'));
  }

  /**
   *
   */
  public function test_ログインに成功したらトップページへリダイレクト()
  {
    InputEx::reset();

    // create user for test
    $rand = rand();
    $username = 'test_'.$rand;
    $password = $rand;

    Auth::create_user($username, $password, 'test@yahoo.co.jp');

    // login
    $_POST['username'] = $username;
    $_POST['password'] = $password;

    $res = Request::forge('/')->set_method('POST')->execute()->response();
    
    $this->assertTrue(Auth::check());

    // delete user
    Auth::delete_user($username);
  }
}
