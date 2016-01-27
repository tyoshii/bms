<?php

class Model_Team extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'name' => array(
            'date_type' => 'varchar',
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
            'label' => 'チーム名',
            'validation' => array(
                'required',
                'max_length' => array(64),
            ),
        ),
        'url_path' => array(
            'date_type' => 'varchar',
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
                'description' => '半角英数字と-_が使えます',
            ),
            'label' => '英語名',
            'validation' => array(
                'required',
                'max_length' => array(64),
                'valid_string' => array(array('alpha', 'numeric', 'dashes')),
            ),
        ),
        'regulation_at_bats' => array(
            'date_type' => 'varchar',
            'default' => '2.0',
            'label' => '規定打席数',
            'form' => array(
                'class' => 'form-control',
                'type' => 'select',
            ),
        ),
        'status' => array(
            'default' => 0,
            'form' => array('type' => false),
        ),
        'created_at' => array('form' => array('type' => false)),
        'updated_at' => array('form' => array('type' => false)),
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
    protected static $_table_name = 'teams';

    protected static $_has_many = array(
        'players' => array(
            'model_to' => 'Model_Player',
            'key_from' => 'id',
            'key_to' => 'team_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_belongs_to = array(
        'conventions_team' => array(
            'model_to' => 'Model_Conventions_Team',
            'key_from' => 'id',
            'key_to' => 'team_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * 新規チーム登録.
     *
     * @param array properties
     *              - name     : チーム名
     *              - url_path :
     *
     * @return bool|mixed
     */
    public static function regist($props = array())
    {
        extract($props);

        // validation
        if (!isset($name) or !isset($url_path)) {
            Log::error('name/url_pathが指定されていません');

            return false;
        }

        // duplicate check
        if (self::find_by_url_path($url_path)) {
            Log::error('そのURLは既に使われています。');

            return false;
        }

        // チーム登録
        $team = self::forge($props);
        $team->save();

        // チーム登録したユーザーをadminとして選手登録
        $props = array(
            'team_id' => $team->id,
            'name' => Auth::get_profile_fields('fullname'),
            'number' => 0,
            'username' => Auth::get('username'),
            'role' => 'admin',
        );
        Model_Player::regist($props);

        return $team->id;
    }

    /**
     * 自分の所属するチームを返す.
     *
     * @return mix object Model_Team / null
     */
    public static function get_belong_team()
    {
        $teams = static::get_belong_teams();

        return is_array($teams) ? reset($teams) : null;
    }
    /**
     * 自分の所属するチームを返す（複数チーム所属している場合.
     *
     * @return array Model_Team
     */
    public static function get_belong_teams()
    {
        return self::query()->related('players', array(
            'where' => array(
                array('username', Auth::get_screen_name()),
            ),
        ))->get();
    }

    public static function get_teams()
    {
        return DB::select()
            ->from(self::$_table_name)
            ->where('status', '!=', '-1')
            ->execute()->as_array('id');
    }

    public static function get_teams_key_value()
    {
        $teams = self::get_teams();

        $kv = array();
        foreach ($teams as $id => $team) {
            $kv[$id] = $team['name'];
        }

        return $kv;
    }

    /**
     * regist new team form.
     *
     * @return object Fieldset
     */
    public static function get_regist_form()
    {
        $form = Fieldset::forge('regist_team')->add_model(static::forge());

        // 規定打席は登録時は除く
        $form->disable('regulation_at_bats');

        // submit
        $form->add('regist', '', array(
            'type' => 'submit',
            'value' => '新規チーム登録',
            'class' => 'btn btn-success',
        ));

        return $form;
    }
}
