<?php
return array(
  '_root_' => 'top/index',
  'login'  => 'top/login',
  'logout' => 'top/logout',

  'user/info'     => 'user/info',
  'user/password' => 'user/password',

  'admin'         => 'admin/index',
  'admin/user'            => 'admin/user',
  'admin/user/(:segment)' => 'admin/user_detail/$1',
  'admin/player'  => 'admin/player',
  'admin/player/(:segment)'  => 'admin/playerinfo/$1',
  'admin/team'    => 'admin/team',
  'admin/league'  => 'admin/league',

  // game
  'game'     => 'game/list',
  'game/add' => 'game/add',

  // game stats
  'game/(:game_id)/(:kind)/(:team_id)' => 'game/edit',
  'game/(:game_id)/(:kind)'            => 'game/edit',
  'game/(:game_id)'                    => 'game/summary',
  
  'api/game/(:segment)'  => 'api/game/$1',
  'api/deploy'           => 'api/deploy/index',
  'api/mail/remind'      => 'api/mail/remind',

  'score'               => 'score/record_team',
  'score/record_self'   => 'score/record_self',
#  'score/record_league' => 'score/record_league',

  'register' => 'register/index',
  'forget_password' => 'register/forget_password',
  'reset_password'  => 'register/reset_password',

  'error/(:status_code)' => 'base/error',
  '_404_'   => 'top/404',
);
