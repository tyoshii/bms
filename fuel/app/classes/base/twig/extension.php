<?php

class Base_Twig_Extension extends Twig_Extension
{
  public function getName()
  {
  }

  public function getGlobals()
  {
    return array(
      'login' => Auth::check(),
      'screen_name' => Auth::get_screen_name(),
    );
  }

  public function getFunctions()
  {
    return array(
      new Twig_SimpleFunction('has_access', array($this, 'hasAccess')),
    );
  }

  public function hasAccess($v)
  {
    return Auth::has_access($v); 
  }
}
