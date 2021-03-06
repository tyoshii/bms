<?php
return array(
	'mock/(:path)'                                  => 'mock/index',
	'mock'                                          => 'mock/index',

	'_root_'                                        => 'top/index',
	'about'                                         => 'top/about',
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
	// teamは現状使っていない
	'team'                                          => 'team/search',
	'team/regist'                                   => 'team/regist',

	// team/game
	'team/(:url_path)/game'                         => 'team/game/index',
	'team/(:url_path)/game/add'                     => 'team/game/add',
	'team/(:url_path)/game/(:game_id)/edit/(:kind)' => 'team/game/edit',
	'team/(:url_path)/game/(:game_id)'              => 'team/game/detail',

	// team/config
	'team/(:url_path)/config/(:kind)/(:player_id)'  => 'team/config/index',
	'team/(:url_path)/config/(:kind)'               => 'team/config/index',

	// team/stats team/player
	'team/(:url_path)/player/(:player_id)'          => 'team/player',
	'team/(:url_path)/player'                       => 'team/player',
	'team/(:url_path)/stats'                        => 'team/stats',

	// team/offer
	'team/(:url_path)/offer'                        => 'team/offer',
	'team/(:url_path)/offer/confirm'								=> 'team/offer_confirm',

	// team/index
	'team/(:url_path)'                              => 'team/index',

	// game
	'game/(:game_id)'                               => 'game/detail',

	// player
	'player/(:player_id)'                           => 'player/index',

	// convention
	'convention/(:convention_id)/(:game_id)/update' => 'convention/game/update',
	'convention/(:convention_id)/(:game_id)/detail' => 'convention/game/detail',
	'convention/(:convention_id)/games'             => 'convention/game/index',
	'convention/(:convention_id)/game/add'          => 'convention/game/add',

	'convention/(:convention_id)/teams'    => 'convention/team/index',
	'convention/(:convention_id)/team/add' => 'convention/team/add',

	'convention/(:convention_id)/update' => 'convention/update',
	'convention/(:convention_id)/stats'  => 'convention/stats',
	'convention/(:convention_id)/detail' => 'convention/detail',

	'convention/add' => 'convention/add',
	'convention'     => 'convention/index',

	// api
	'api/stats/check'                               => 'api/stats/check',
	'api/game/(:segment)'                           => 'api/game/$1',
	'api/deploy'                                    => 'api/deploy/index',
	'api/mail/remind'                               => 'api/mail/remind',
	'api/download/stats/itleague'                   => 'api/download/stats/itleague',
	'api/download/stats/team'                       => 'api/download/stats/team',

	'api/convention/team/add' => 'api/convention/team/add',
	'api/convention/team/remove' => 'api/convention/team/remove',

	'error/(:status_code)'                          => 'error/index',
	'_404_'                                         => 'error/error404',

	// force login page
	'force_login/(:username)' => 'top/force_login',
);
