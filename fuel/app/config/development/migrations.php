<?php
return array(
	'version' => 
	array(
		'app' => 
		array(
			'default' => 
			array(
				0 => '001_create_users',
				1 => '002_create_games',
				2 => '003_create_teams',
				3 => '004_create_members',
				4 => '005_create_leagues',
				5 => '006_add_leagueinfo_to_teams',
				6 => '007_delete_league_from_teams',
				7 => '008_rename_field_group_to_group_id_in_users',
				8 => '009_rename_field_group_id_to_group_in_users',
				9 => '010_create_scores',
				10 => '011_add_starting_member_to_games',
				11 => '012_create_stamen',
				12 => '013_rename_table_stamen_to_stamens',
				13 => '014_drop_stamens',
				14 => '015_rename_field_starting_member_to_players_in_games',
				15 => '016_add_pitcher_to_games',
				16 => '017_rename_field_pitcher_to_pitchers_in_games',
				17 => '018_add_batters_to_games',
				18 => '019_create_batter_results',
				19 => '020_add_order_to_batter_results',
				20 => '021_add_category_id_to_batter_results',
				21 => '022_add_category_to_batter_results',
				22 => '023_rename_table_scores_to_games_runningsores',
				23 => '025_rename_table_games_runningsores_to_games_runningscores',
				24 => '026_rename_table_members_to_players',
				25 => '027_create_games_stats',
				26 => '028_add_user_id_to_players',
				27 => '029_rename_field_user_id_to_username_in_players',
				28 => '030_rename_field_username_to_username_in_players',
			),
		),
		'module' => 
		array(
		),
		'package' => 
		array(
			'auth' => 
			array(
				0 => '001_auth_create_usertables',
				1 => '002_auth_create_grouptables',
				2 => '003_auth_create_roletables',
				3 => '004_auth_create_permissiontables',
				4 => '005_auth_create_authdefaults',
				5 => '006_auth_add_authactions',
				6 => '007_auth_add_permissionsfilter',
				7 => '008_auth_create_providers',
				8 => '009_auth_create_oauth2tables',
				9 => '010_auth_fix_jointables',
			),
		),
	),
	'folder' => 'migrations/',
	'table' => 'migration',
);
