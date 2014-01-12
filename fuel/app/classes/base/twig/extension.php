<?php

class Base_Twig_Extension extends Twig_Extension
{
  public function getGlobals()
  {
    return array(
      'login' => Auth::check(),
      'screen_name' => Auth::get_screen_name(),
    );
  }

  public function getName()
  {
  }
}
