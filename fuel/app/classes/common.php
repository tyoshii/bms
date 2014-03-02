<?php

class Common
{
  public static function get_dispname()
  {
    $info = Auth::get_profile_fields();
    $name = isset($info['dispname']) ? $info['dispname']
                                     : Auth::get_screen_name();

    return $name;
  }

  public static function update_user($props)
  {
    $info = Auth::get_profile_fields();

    foreach ( $props as $key => $val )
      $info[$key] = $val;

    Auth::update_user($info, Auth::get_screen_name());
  }
}
