<?php

namespace Test;

class InputEX extends \Fuel\Core\Input {
  public static function reset(){
    parent::$input = null;
  }
}
