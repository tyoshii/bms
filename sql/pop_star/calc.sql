select
    p.name,
    sh.player_id,
    count(sh.player_id) as pop,
    (select 
        sum(TPA)
    from
        stats_hittings as s
    where
        s.player_id = sh.player_id and
        s.game_id in (select id from games as g where g.game_status = 2)
    ) as TPA
from
    stats_hittingdetails as sh

left join
    players as p
on
    p.id = sh.player_id

where
    sh.team_id = 1 and
    direction in ( 1,2,3,4,5,6 ) and
    kind = 3 and
    result_id = 11

group by
    player_id

order by
    player_id
