<?php

/**
 * Tests for Model_Stats_Player
 *
 * @group App
 * @group Model
 * @group Model_Stats_Player
 */
class Test_Model_Stats_Player extends \Test_Model_Base
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
