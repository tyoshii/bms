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
        29 => '031_create_score_meta',
        30 => '032_create_stats_hittings',
        31 => '033_create_stats_fieldings',
        32 => '034_create_stats_pitchings',
        33 => '035_rename_table_score_meta_to_stats_meta',
        34 => '036_create_stats_hittingdetails',
        35 => '037_add_team_id_to_stats_meta',
        36 => '038_add_disp_order_to_stats_meta',
        37 => '039_add_ip_frac_to_stats_pitchings',
        38 => '040_add_team_id_to_stats_pitchings',
        39 => '041_add_team_id_to_stats_fieldings',
        40 => '042_add_team_id_to_stats_hittings',
        41 => '043_add_team_id_to_stats_hittingdetails',
        42 => '044_delete_players_from_games',
        43 => '045_delete_pitchers_from_games',
        44 => '046_delete_batters_from_games',
        45 => '047_rename_table_stats_meta_to_stats_players',
        46 => '048_add_team_top_name_to_games',
        47 => '049_add_team_bottom_name_to_games',
        48 => '050_rename_field_number_to_number_in_players',
        49 => '051_add_statsu_to_teams',
        50 => '052_rename_field_statsu_to_status_in_teams',
        51 => '053_add_status_to_players',
        52 => '054_add_top_status_to_games',
        53 => '055_add_bottom_status_to_games',
        54 => '056_add_status_to_stats_hittings',
        55 => '057_add_status_to_stats_pitchings',
        56 => '058_add_updated_to_users',
        57 => '059_delete_updated_at_from_users',
        58 => '060_rename_field_updated_to_updated_at_in_users',
        59 => '061_rename_field_nullfalse_to_nullok_in_games_runningscores',
        60 => '062_create_stats_awards',
        61 => '063_add_stadium_to_games',
        62 => '064_add_memo_to_games',
        63 => '065_add_game_id_to_games_runningscores',
        64 => '066_create_games_teams',
        65 => '071_add_url_path_to_teams',
        66 => '072_add_opponent_team_name_to_games_teams',
        67 => '073_rename_field_team_to_team_id_in_players',
        68 => '074_add_role_to_players',
        69 => '075_drop_games_stats',
        70 => '076_add_order_to_stats_pitchings',
        71 => '077_add_last_inning_to_games_runningscores',
        72 => '078_add_regulation_at_bats_to_teams',
        73 => '079_add_input_status_to_stats_hittings',
        74 => '080_add_input_status_to_stats_pitchings',
        75 => '081_create_conventions',
        76 => '082_create_conventions_admins',
        77 => '083_create_conventions_teams',
        78 => '084_delete_status_from_stats_hittings',
        79 => '085_delete_status_from_stats_pitchings',
        80 => '086_delete_team_top_to_games',
        81 => '087_add_start_time_to_games',
        82 => '088_create_conventions_games',
      ),
    ),
    'module' => 
    array(
    ),
    'package' => 
    array(
    ),
  ),
  'folder' => 'migrations/',
  'table' => 'migration',
);
