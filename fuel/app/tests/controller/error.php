<?php

/**
 * Tests for Controler_Error
 *
 * @group App
 * @group Controller
 * @group Controller_Error
 */
class Test_Controller_Error extends Test_Base
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
  public function test_エラーページの表示
  {
    $res = Request::forge('/error/500')->execute()->response();

    $this->assertSame(500, $res->status);
  }
  
  /**
   *
   */
  public function test_404はデフォルトの_404_のためのactionを用意()
  {
    $res = Request::forge('_404_')->execute()->response();

    $this->assertSame(404, $res->status);
  }
  
  /**
   *
   */
  public function test_存在しないページへのアクセスは404へリダイレクトされる()
  {
    $res = Request::forge('/no_exist_page')->execute()->response();

    $this->assertSame(404, $res->status);
  }
}
