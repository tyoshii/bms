select
    gt.game_id,
    gt.opponent_team_name,
    gt.order,
    gr.tsum,
    gr.bsum,
    case when 1 <  (case when gt.order = "top" then (gr.tsum+1) / (gr.bsum+1) else (gr.bsum+1) /  (gr.tsum+1) END) then "win" else "lose" end
        as result

from games_teams as gt

left join games as g
    on g.id = gt.game_id
    
left join games_runningscores as gr 
    on 
        gr.game_id = gt.game_id

where
    gt.team_id = 1 and
    g.game_status = 2 
;
