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
      new Twig_SimpleFunction('get_flash', array($this, 'getFlash')),
    );
  }

  public function hasAccess($v)
  {
    return Auth::has_access($v); 
  }

  public function getFlash($v)
  {
    return Session::get_flash($v);
  }
}
