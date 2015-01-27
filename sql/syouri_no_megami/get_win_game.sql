select
    p.name,
    sp.player_id as player_id,
    count(sp.player_id) as win_game
from
    stats_players as sp
left join
    players as p
on
    p.id = sp.player_id

where

    sp.game_id in (
4,
25,
27,
28,
29,
32,
38,
42,
43,
45,
46,
47,
57
)

group by
    sp.player_id

order by
    sp.player_id
