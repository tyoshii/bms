select
    p.id,
    p.name,
    (select count(*) from stats_hittingdetails as sh where sh.direction in (3,4,9) and sh.player_id = p.id and
        sh.game_id in (select id from games as g where g.game_status = 2) ) as right_way,
    (select count(*) from stats_hittingdetails as sh where sh.direction in (5,6,7) and sh.player_id = p.id and
        sh.game_id in (select id from games as g where g.game_status = 2) ) as left_way

from
    players as p

where
    p.team_id = 1

group by
    p.id

order by
    p.id
