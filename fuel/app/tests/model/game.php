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
  public function test_get_infoで試合情報の一覧を取得します()
  {
    $games = Model_Game::get_info();

    foreach ( $games as $game_id => $game )
    {
      // returnの型
      $this->assertSame('Model_Game', get_class($game));

      // DBからの戻り値以外で付与した属性があるかどうか
      $this->assertTrue(isset($game->tsum));
      $this->assertTrue(isset($game->bsum));
      $this->assertTrue(isset($game->own));
      $this->assertTrue(isset($game->top_result));
      $this->assertTrue(isset($game->bottom_result));
      $this->assertTrue(isset($game->result));

      // relationのデータ
      $this->assertSame('Model_Games_Runningscore', get_class($game->games_runningscores[$game_id]));
      $this->assertTrue(isset($game->stats_players));
    }
  }

  /**
   *
   */
  public function test_get_info_by_teamで値指定しなければ空配列()
  {
    $games = Model_Game::get_info_by_team();

    $this->assertTrue(is_array($games));
    $this->assertSame(0, count($games));
  }

  /**
   *
   */
  public function test_get_info_by_teamで正しい値が返る()
  {
    $id = Model_Team::find('first')->id;
    $games = Model_Game::get_info_by_team($id);

    $this->assertTrue(is_array($games));

    // 先攻か後攻のどちらかに$idが含まれていることを確認
    foreach ( $games as $game )
    {
      $this->assertTrue($game['team_top'] === $id or $game['team_bottom'] === $id);
    }
  }
}
