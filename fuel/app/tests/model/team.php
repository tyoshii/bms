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

  /**
   *
   */
  public function test_スキーマチェック()
  {
    $this->assertSchema();
  }

  /**
   *
   */
  public function test_新規チーム登録()
  {
    // チーム名を与えない場合はfalse
    $this->assertFalse(Model_Team::regist());

    // 新規登録
    $name = rand();
    $id = Model_Team::regist($name);

    // 登録したチームのモデル
    $team = Model_Team::find($id);

    $this->assertSame($id,   $team->id);
    $this->assertSame($name, $team->name);
  }
}
