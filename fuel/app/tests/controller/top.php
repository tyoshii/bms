<?php

/**
 * Tests for Controler_Top
 *
 * @group App
 * @group Controller
 * @group Controller_Top
 */
class Test_Controller_Top extends \TestCase
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
  public function test_未ログイン状態でトップにアクセス()
  {
    $res = Request::forge('/')->execute()->response();

    // login_form
    $this->_assert_login_form($res->body->login_form);
  }
  
  /**
   *
   */
  public function test_ログイン状態でトップにアクセス()
  {
    // login
    $id = Model_User::find('first')->id;
    Auth::force_login($id);

    $res = Request::forge('/')->execute()->response();

    // login_form はない
    try {
      $res->body->login_form;
      $this->assertTrue(false);
    }
    catch ( Exception $e ) {
      $this->assertTrue(true);
    }

    // logout
    Auth::logout();
  }
  
  /**
   *
   */
  public function test_ログインページにログイン状態でアクセスするとトップページへ()
  {
    // login
    $id = Model_User::find('first')->id;
    Auth::force_login($id);

    $res = Request::forge('login')->execute()->response();

    $this->assertSame(302, $res->status);

    // logout
    Auth::logout();
  }
  
  /**
   *
   */
  public function test_未ログイン状態でアクセスするとログインフォームを表示()
  {
    // logout
    Auth::logout();

    $res = Request::forge('login')->execute()->response();

    $this->assertNull(Session::get('redirect_to'));
    $this->_assert_login_form($res->body->form);
  }

  /**
   *
   */
  public function test_urlパラメータを付与するとredirect_toセッションに保存()
  {
    // logout
    Auth::logout();

    // parameter
    $url  = 'http://bm-s.info/test';
    $_GET = array('url' => $url);
    
    $res = Request::forge('login')->execute()->response();

    $this->assertSame($url, Session::get('redirect_to'));
  }
  
  /**
   *
   */
  public function test_logout処理()
  {
    $res = Request::forge('logout')->execute()->response();

    $this->assertFalse(Auth::check());
    $this->assertSame(302, $res->status);
  }
  
  /**
   * 
   */
  public function test_404ページのテスト()
  {
    $res = Request::forge('_404_')->execute()->response();
    
    $this->assertSame(404, $res->status);
  }

  /**
   * login_formのテスト
   */
  private function _assert_login_form($html)
  {
    $matcher = array(
      'tag' => 'input',
      'id'  => 'form_username', 
      'attributes' => array('type' => 'text'),
    );
    $this->assertTag($matcher, $html);

    $matcher = array(
      'tag' => 'input',
      'id'  => 'form_password', 
      'attributes' => array('type' => 'password'),
    );
    $this->assertTag($matcher, $html);

    $matcher = array(
      'tag' => 'input',
      'id'  => 'form_login', 
      'attributes' => array('type' => 'submit'),
    );
    $this->assertTag($matcher, $html);
  } 
}
