select
    p.id,
    p.name,
    (select count(*) from stats_awards as sa where sa.mvp_player_id = p.id and 
        sa.game_id in (select id from games as g where g.game_status = 2)
    ) * 2 as 2point,
    (select count(*) from stats_awards as sa where sa.second_mvp_player_id = p.id and 
        sa.game_id in (select id from games as g where g.game_status = 2)
    ) as 1point

from
    players as p

where
    p.team_id = 1

group by
    p.id 

order by
    p.id
