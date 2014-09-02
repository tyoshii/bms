<?php

/**
 * Tests for View_Top
 *
 * @group App
 * @group View
 * @group View_Top
 */
class Test_View_Top extends Test_View_Base
{
  protected function setUp()
  {
    $this->setBrowserUrl('http://localhost:8888/');
  }

  public function test_ログインできる()
  {
    $this->waitUntil(function() {
      $this->url('/');
      $this->byName('username')->value('admin');
      $this->byName('password')->value('adminadmin');
      $this->byName('login')->submit();

      return true;
    }, 60000);

    // TODO:どうやっててすと？
  }
}
