<?php

/**
 * Tests for Model_Stats_Pitching
 *
 * @group App
 * @group Model
 * @group Model_Stats_Pitching
 */
class Test_Model_Stats_Pitching extends \Test_Model_Base
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
