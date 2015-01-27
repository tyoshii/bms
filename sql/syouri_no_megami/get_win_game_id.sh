mysql -u root -pmysqlroot bms < get_win_game_id.sql  | grep win | awk '{print $1}' | sed -e "s/$/,/"
