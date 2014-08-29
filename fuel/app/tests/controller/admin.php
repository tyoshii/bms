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
  }
  protected function tearDown()
  {
    parent::tearDown();
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

}
