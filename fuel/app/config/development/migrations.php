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
