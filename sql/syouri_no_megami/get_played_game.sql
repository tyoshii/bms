select
    p.name,
    sp.player_id,
    count(sp.id)
from
    stats_players as sp

left join players as p
on
    p.id = sp.player_id

where
    sp.game_id in (select id from games as g where g.game_status = 2) and
    sp.team_id = 1

group by
    sp.player_id

order by
    sp.player_id
