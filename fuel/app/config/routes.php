<?php
return array(
  '_root_' => 'top/index',
  'login'  => 'top/login',
  'logout' => 'top/logout',

  'user/info'     => 'user/info',
  'user/password' => 'user/password',

  'admin'         => 'admin/index',
  'admin/user'    => 'admin/user',
  'admin/member'  => 'admin/member',
  'admin/member/(:segment)'  => 'admin/memberinfo/$1',
  'admin/team'    => 'admin/team',
  'admin/league'  => 'admin/league',

  'game/create' => 'game/create',
  'game/score'  => 'game/score',
  'game/list'   => 'game/list',
  'game/status' => 'game/status',

  'game/edit'                                  => 'game/edit',
  'game/edit/(:segment)'                       => 'game/edit/$1',
  'game/edit/(:segment)/(:segment)'            => 'game/edit/$1/$2',
  'game/edit/(:segment)/(:segment)/(:segment)' => 'game/edit/$1/$2/$3',

  'game/score/player'   => 'game/player',
  'game/score/pitcher'  => 'game/pitcher',
  'game/score/batter'   => 'game/batter',

  'score'               => 'score/record_team',
  'score/record_self'   => 'score/record_self',
#  'score/record_league' => 'score/record_league',

  'register' => 'register/index',

//  '_root_'  => 'welcome/index',  // The default route
  '_404_'   => 'welcome/404',    // The main 404 route

  'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);
