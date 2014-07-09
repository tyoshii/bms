<?php

class Common
{
  public static function db_clean( $table, $where )
  {
    $query = DB::delete($table);

    if ( $where )
      $query->where($where);

    $query->execute(); 
  }

  public static function debug($v)
  {
    echo "<pre>";
    print_r($v);
    exit;
  }

  public static function redirect($uri)
  {
    $redirect_to = Session::get('redirect_to');
    if ( ! $redirect_to )
      $redirect_to = $uri ? $uri : Uri::current();

    Response::redirect(Uri::create($redirect));
  }

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

  public static function get_usericon_url()
  {
    $email = md5(Auth::get_email());

    $gravatar_url = "http://www.gravatar.com/avatar/{$email}.jpg";
    $bms_url = Uri::base(false).'image/usericon/default.jpg';

    return sprintf('%s?d=%s', $gravatar_url, $bms_url);
  }
}
