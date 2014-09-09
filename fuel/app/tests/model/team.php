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
    // パラメーターが足りない場合はfalse
    $this->assertFalse(Model_Team::regist());
    $this->assertFalse(Model_Team::regist(array('name'     => 'name'    )));
    $this->assertFalse(Model_Team::regist(array('url_path' => 'url_path')));

    // 新規登録
		$props = array(
    	'name' 		 => rand(),
			'url_path' => rand(),
		);
    $id = Model_Team::regist($props);

    // 登録したチームのモデル
    $team = Model_Team::find($id);

    $this->assertSame($id,   $team->id);
    $this->assertSame($name, $team->name);

		// clean up
		$this->assertTrue($team->delete);
  }
}
