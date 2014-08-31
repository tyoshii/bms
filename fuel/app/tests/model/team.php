<?php

/**
 * Tests for Model_Team
 *
 * @group App
 * @group Model
 * @group Model_Team
 */
class Test_Model_Team extends \Test_Model_Base
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
