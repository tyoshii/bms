<?php

/**
 * Tests for Model_Game
 *
 * @group App
 * @group Model
 * @group Model_Game
 */
class Test_Model_Game extends \Test_Model_Base
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
