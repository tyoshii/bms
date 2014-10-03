<?php

/**
 * Tests for Model_Stats_Common
 *
 * @group App
 * @group Model
 * @group Model_Stats_Common
 */
class Test_Model_Stats_Common extends \Test_Model_Base
{
	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	public function test_関数get_stats_alerts()
	{
		// 引数が足らない
		$this->assertexception(function(){
			model_stats_common::get_stats_alerts();
		});
		$this->assertexception(function(){
			model_stats_common::get_stats_alerts('team_id');
		});

		// 正常系
		$alerts = Model_Stats_Common::get_stats_alerts(self::$sample['team']->id, self::$sample['player']->id);

		$this->assertTrue(is_array($alerts));
	}
}
