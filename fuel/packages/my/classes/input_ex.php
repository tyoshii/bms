<?php

namespace My;

class InputEX extends \Fuel\Core\Input {
  public static function reset(){
    parent::$input = null;
  }
}
