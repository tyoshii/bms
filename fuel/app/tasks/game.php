<?php

namespace Fuel\Tasks;

class Game
{

  /**
   * This method gets ran when a valid method name is not used in the command.
   *
   * Usage (from command line):
   *
   * php oil r game
   *
   * @param null $args
   *
   * @return string
   */
  public function run($args = NULL)
  {
    echo "\n===========================================";
    echo "\nRunning DEFAULT task [Game:Run]";
    echo "\n-------------------------------------------\n\n";

    /***************************
     * Put in TASK DETAILS HERE
     **************************/
  }


  /**
   * This method gets ran when a valid method name is not used in the command.
   *
   * Usage (from command line):
   *
   * php oil r game:index "arguments"
   *
   * @return string
   */
  public function updateTeamName()
  {
    $games = \Model_Game::query()->get();

    foreach ($games as $game)
    {
      if ( ! $game->team_top_name)
        $game->team_top_name = \Model_Team::find($game->team_top)->name;

      if ( ! $game->team_bottom_name)
        $game->team_bottom_name = \Model_Team::find($game->team_bottom)->name;

      $game->save();
    }

    echo "DONE !!";
  }

}
/* End of file tasks/game.php */
