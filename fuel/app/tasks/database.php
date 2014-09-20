<?php

namespace Fuel\Tasks;

class Database
{

  /**
   * This method gets ran when a valid method name is not used in the command.
   *
   * Usage (from command line):
   *
   * php oil r database
   *
   * @param null $args
   *
   * @return string
   */
  public function run($args = NULL)
  {
    echo "\n===========================================";
    echo "\nRunning DEFAULT task [Database:Run]";
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
   * php oil r database:test "arguments"
   *
   * @param null $args
   *
   * @return string
   */
  public function test($args = NULL)
  {
    echo "\n===========================================";
    echo "\nRunning task [Database:Test]";
    echo "\n-------------------------------------------\n\n";

    /***************************
     * Put in TASK DETAILS HERE
     **************************/

    \DB::query('SHOW TABLES')->execute();

    echo "Database Connection OK";
  }

}
/* End of file tasks/database.php */
