<?php

class Common
{
  public static function add_team_select($form, $default)
  {
    $teams = Model_Team::getTeams();

    $form->add('team', '所属チーム', array(
      'type' => 'select',
      'options' => array(''=>'') + $teams,
      'value' => $default,
      'class' => 'form-control chosen-select',
      'data-placeholder' => 'Select Team',
    ))
      ->add_rule('in_array', array_keys($teams));
    
    return $form;
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
}
