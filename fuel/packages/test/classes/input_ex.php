<?php

namespace Test;

use Fuel\Core\Input;

class InputEX extends Input {
    /**
     * @var null
     */
    private $input;

    public static function reset(){
        self::$input = null;
//    parent::$input = null;
  }
}
