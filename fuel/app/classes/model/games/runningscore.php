<?php

class Model_Games_Runningscore extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'game_id',
        'last_inning' => array('default' => 0),
        't1' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't2' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't3' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't4' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't5' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't6' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't7' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't8' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't9' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't10' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't11' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't12' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't13' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't14' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't15' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't16' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't17' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        't18' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'tsum' => array(
            'data_type' => 'int',
            'default' => 0,
            'validation' => array(
                'required',
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b1' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b2' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b3' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b4' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b5' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b6' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b7' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b8' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b9' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b10' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b11' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b12' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b13' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b14' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b15' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b16' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b17' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'b18' => array(
            'data_type' => 'int',
            'default' => null,
            'validation' => array(
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'bsum' => array(
            'data_type' => 'int',
            'default' => 0,
            'validation' => array(
                'required',
                'valid_string' => array('numeric'),
            ),
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
        ),
        'created_at',
        'updated_at',
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );
    protected static $_table_name = 'games_runningscores';

    protected static $_belongs_to = array(
        'games' => array(
            'model_to' => 'Model_Game',
            'key_from' => 'game_id',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'conventions_game' => array(
            'model_to' => 'Model_Conventions_Game',
            'key_from' => 'game_id',
            'key_to' => 'game_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    public static function regist($game_id = null, $stats = array())
    {
        if (!$game_id) {
            return false;
        }

        // last_inning
        $li = (count($stats) - 2) / 2;
        if ($li < 0) {
            $li = 0;
        }

        $stats['last_inning'] = $li;

        // default
        $stats += array(
            't1' => 0,
            'b1' => 0,
        );

        // 空の値をnullにする
        foreach ($stats as $key => $val) {
            if ($val === '') {
                $stats[$key] = null;
            }
        }

        // 既に登録されているものであれば、一度削除
        if ($score = self::find_by_game_id($game_id)) {
            $score->delete();
        }

        $score = self::forge(array('game_id' => $game_id) + $stats);
        $score->save();

        return $score;
    }
}
