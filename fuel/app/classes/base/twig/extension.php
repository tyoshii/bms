<?php

class Base_Twig_Extension extends Twig_Extension
{
  public function getName()
  {
  }

  public function getGlobals()
  {
    return array(
        'screen_name' => Common::get_dispname(),
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
