select
    sp.id,
    sp.player_id,
    (select name from players where id = sp.player_id) as name,
    sp.game_id,
    sp.order,
    (select d.AB  from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as AB,
    (select d.H   from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as H,
    (select d.2B  from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as 2B,
    (select d.3B  from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as 3B,
    (select d.HR  from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as HR,
    (select d.BB  from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as BB,
    (select d.HBP from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as HBP,
    (select d.SF  from stats_hittings as d where d.game_id = sp.game_id and d.player_id = sp.player_id) as SF


from
    stats_players as sp

where
    sp.team_id = 1 and
    sp.game_id in (select id from games where game_status = 2)

order by
    sp.game_id, sp.order
