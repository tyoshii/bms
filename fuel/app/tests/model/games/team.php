<?php

/**
 * Tests for Model_Games_Team
 *
 * @group App
 * @group Model
 * @group Model_Games_Team
 */
class Test_Model_Games_Team extends \Test_Model_Base
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
