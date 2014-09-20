<?php

/**
 * Tests for Controler_Admin
 *
 * @group App
 * @group Controller
 * @group Controller_Admin
 */
class Test_Controller_Admin extends \TestCase
{
  protected function setUp()
  {
    parent::setUp();

    // login
    $id = Model_User::find_by_username('admin')->id;
    Auth::force_login($id);
  }

  protected function tearDown()
  {
    parent::tearDown();

    // logout
    Auth::logout();
  }

  /**
   *
   */
  public function test_未ログイン状態でアクセスするとトップページへ()
  {
    // logout
    Auth::logout();

    $res = Request::forge('admin')->execute()->response();

    // TODO:どうやってassertする？
    // beforeでredirectしているのでresponseとれない
  }

  /**
   *
   */
  public function test_トップページ()
  {
    $res = Request::forge('admin')->execute()->response();

    $this->_assert_admin_header($res->body());
  }

  /**
   *
   */
  public function test_admin_userページにGETアクセス()
  {
    $res = Request::forge('admin/user')->execute()->response();

    $this->assertTrue(is_array($res->body->users));
    $this->assertTrue(is_string($res->body->form));
  }

  private function _assert_admin_header($html)
  {
    $matcher = array(
        'tag'        => 'div',
        'attributes' => array('class' => 'page-header'),
    );
    $this->assertTag($matcher, $html);

    $matcher = array(
        'tag'        => 'ul',
        'attributes' => array('class' => 'nav nav-pills'),
        'children'   => array(
            'count' => 3,
            'only'  => array('tag' => 'li'),
        ),
    );
    $this->assertTag($matcher, $html);

    $matcher = array(
        'tag'   => 'li',
        'child' => array(
            'tag'        => 'a',
            'attributes' => array('href' => '/admin/user'),
        ),
    );
    $this->asserttag($matcher, $html);

    $matcher = array(
        'tag'   => 'li',
        'child' => array(
            'tag'        => 'a',
            'attributes' => array('href' => '/admin/player'),
        ),
    );
    $this->asserttag($matcher, $html);

    $matcher = array(
        'tag'   => 'li',
        'child' => array(
            'tag'        => 'a',
            'attributes' => array('href' => '/admin/team'),
        ),
    );
    $this->asserttag($matcher, $html);
  }
}
