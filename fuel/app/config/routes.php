<?php
return array(
  'test'   => 'top/test',

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

  'game'                                  => 'game/list',
  'game/(:segment)'                       => 'game/summary/$1',
  'game/(:segment)/(:segment)'            => 'game/edit/$1/$2',
  'game/(:segment)/(:segment)/(:segment)' => 'game/edit/$1/$2/$3',
  
  'api/game/(:segment)'  => 'api/game/$1',

  'score'               => 'score/record_team',
  'score/record_self'   => 'score/record_self',
#  'score/record_league' => 'score/record_league',

  'register' => 'register/index',

//  '_root_'  => 'welcome/index',  // The default route
  '_404_'   => 'welcome/404',    // The main 404 route

  'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);
