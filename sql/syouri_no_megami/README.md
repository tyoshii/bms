# 勝利の女神算出

## 勝利ゲームのIDを取得

```
mysql -u root -pmysqlroot bms < get_win_game_id.sql  | grep win | awk '{print $1}' | sed -e "s/$/,/"
```

## IDを`get_win_game.sql`の中へ記述

## 女神数を取得

```
mysql -u root -pmysqlroot bms < get_win_game_id.sql
```

## 全体の参加数を取得

```
mysql -u root -pmysqlroot bms < get_played_game.sql
```

