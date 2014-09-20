<?php
return array(
	'_root_'                                        => 'top/index',
	'login'                                         => 'top/login',
	'logout'                                        => 'top/logout',

	'user/info'                                     => 'user/info',
	'user/password'                                 => 'user/password',

	'admin'                                         => 'admin/index',
	'admin/user'                                    => 'admin/user',
	'admin/user/(:segment)'                         => 'admin/user_detail/$1',
	'admin/player'                                  => 'admin/player',
	'admin/player/(:segment)'                       => 'admin/playerinfo/$1',
	'admin/team'                                    => 'admin/team',
	'admin/league'                                  => 'admin/league',

	// team
	'team'                                          => 'team/search',
	'team/regist'                                   => 'team/regist',

	// team/game
	'team/(:url_path)/game'                         => 'team/game/index',
	'team/(:url_path)/game/add'                     => 'team/game/add',
	'team/(:url_path)/game/(:game_id)/edit/(:kind)' => 'team/game/edit',
	'team/(:url_path)/game/(:game_id)'              => 'team/game/detail',

	// team/stats team/player
	'team/(:url_path)/player/(:player_id)'          => 'team/player',
	'team/(:url_path)/player'                       => 'team/player',
	'team/(:url_path)/stats'                        => 'team/stats',

	// team/config
	'team/(:url_path)/config/(:kind)'               => 'team/config/index',

	// team/offer
	'team/(:url_path)/offer'                        => 'team/offer',

	// team/index
	'team/(:url_path)'                              => 'team/index',

	// game
	'game'                                          => 'game/list',

	// game stats
	'game/(:game_id)/(:kind)/(:team_id)'            => 'game/edit',
	'game/(:game_id)/(:kind)'                       => 'game/edit',
	'game/(:game_id)'                               => 'game/summary',

	'api/game/(:segment)'                           => 'api/game/$1',
	'api/deploy'                                    => 'api/deploy/index',
	'api/mail/remind'                               => 'api/mail/remind',

	'register'                                      => 'register/index',
	'forget_password'                               => 'register/forget_password',
	'reset_password'                                => 'register/reset_password',

	'error/(:status_code)'                          => 'error/index',
	'_404_'                                         => 'error/error404',
);
