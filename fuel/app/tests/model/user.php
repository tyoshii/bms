<?php

/**
 * Tests for Model_User
 *
 * @group App
 * @group Model
 * @group Model_User
 */
class Test_Model_User extends \Test_Model_Base
{
  public function setUp()
  {
  }

  public function tearDown()
  {
  }

  public function test_スキーマチェック()
  {
    $this->assertSchema();
  }
}
