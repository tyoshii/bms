<?php

class Model_Player extends \Orm\Model
{
    protected static $_properties = array(
        'id' => array('form' => array('type' => false)),
        'team_id' => array('form' => array('type' => false)),
        'name' => array(
            'data_type' => 'varchar',
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
            'label' => '選手名',
            'validation' => array(
                'required',
                'max_length' => array(60),
            ),
        ),
        'number' => array(
            'data_type' => 'varchar',
            'form' => array(
                'class' => 'form-control',
                'type' => 'text',
            ),
            'label' => '背番号',
            'validation' => array(
                'required',
                'max_length' => array(4),
                'valid_string' => array('numeric'),
            ),
        ),
        'username' => array(
            'form' => array('type' => false),
            'default' => '',
        ),
        'status' => array(
            'default' => 1,
            'form' => array('type' => false),
        ),
        'role' => array(
            'default' => 'user',
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
    protected static $_table_name = 'players';

    protected static $_has_one = array(
        'team' => array(
            'model_to' => 'Model_Team',
            'key_from' => 'team_id',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_belongs_to = array(
        'teams' => array(
            'model_to' => 'Model_Team',
            'key_from' => 'team_id',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * get player name by username.
     *
     * @param string username
     *
     * @return string/null player name
     */
    public static function get_name_by_username($username = null)
    {
        if (!$username) {
            return;
        }

        if ($player = self::find_by_username($username)) {
            return $player->name;
        }

        return;
    }

    /**
     * get login user player id.
     *
     * @return string/null player id
     */
    public static function get_my_player_id()
    {
        if ($res = self::find_by_username(Auth::get_screen_name())) {
            return $res->id;
        }

        return;
    }

    /**
     * get login user team name.
     *
     * @return string/null team name
     */
    public static function get_my_team_name()
    {
        if ($team_id = self::get_my_team_id()) {
            return Model_Team::find($team_id)->name;
        }

        return;
    }

    /**
     * get login user team id.
     *
     * TODO: 複数チームに所属している場合に対応しきれない（１つしか返せない）
     */
    public static function get_my_team_id()
    {
        if ($res = self::find_by_username(Auth::get_screen_name())) {
            return $res->team_id;
        }

        return;
    }

    /**
     * get players.
     *
     * @param string team_id
     *
     * @return array
     */
    public static function get_players($team_id = null)
    {
        $query = DB::select('p.*', array('teams.name', 'teamname'))
            ->from(array(self::$_table_name, 'p'))
            ->join('teams', 'LEFT')->on('p.team_id', '=', 'teams.id')
            ->where('p.status', '!=', -1)
            ->order_by(DB::expr('CAST(p.number as SIGNED)'));

        if ($team_id) {
            $query->where('p.team_id', $team_id);
        }

        return $query->execute()->as_array();
    }

    /**
     * 選手登録.
     *
     * @param $props
     * @param array properties
     *              - team_id
     *              - name
     *              - number
     *              - username
     *              - role
     *
     * @return player object
     */
    public static function regist($props, $id = null)
    {
        try {
            $player = $id ? self::find($id) : self::forge();

            // 既に登録されたusernameかチェック
            if (array_key_exists('username', $props) and $props['username'] !== $player->username) {
                $already = self::query()->where(array(
                    array('username', $props['username']),
                    array('team_id', $props['team_id']),
                ))->get();

                if ($already) {
                    throw new Exception('そのユーザーは既に他の選手に紐づいています');
                }
            }

            // 登録/更新
            $player->set($props);
            $player->save();

            return $player;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public static function disable($id)
    {
        try {
            $player = self::find($id);

            $player->number = '';
            $player->username = '';
            $player->status = -1;

            $player->save();

            return true;
        } catch (Exception $e) {
            Session::set_flash('error', $e->getMessage());

            return false;
        }
    }

    /**
     * get player email address.
     *
     * @param string player_id
     *
     * @return string email(or empty)
     */
    public static function get_player_email($player_id)
    {
        $user = DB::select()
            ->from(array(self::$_table_name, 'p'))
            ->join(array('users', 'u'))->on('p.username', '=', 'u.username')
            ->where('p.id', $player_id)
            ->limit(1)
            ->execute()->as_array();

        if (count($user) === 0) {
            return '';
        }

        $user = $user[0];
        if ($user['username'] === '') {
            return '';
        }

        return $user['email'];
    }

    /**
     * 指定されたチームにユーザーが所属しているかどうか.
     *
     * @param string team_id
     * @param string username
     *
     * @return player object / false
     */
    public static function is_belong($team_id = null, $username = null)
    {
        if (is_null($team_id)) {
            return false;
        }

        if (is_null($username)) {
            $username = Auth::get('username');
        }

        $player = self::query()
            ->where('team_id', $team_id)
            ->where('username', $username)
            ->get_one();

        return $player ?: false;
    }

    /**
     * チームの管理者権限をもっているかどうか.
     *
     * @param string team_id
     *
     * @return bool
     */
    public static function has_team_admin($team_id)
    {
        $res = self::query()->where(array(
            array('username', Auth::get('username')),
            array('team_id', $team_id),
        ))->get_one();

        return $res and $res->role === 'admin';
    }

    /**
     * player.roleを更新.
     *
     * @param string team_id
     * @param string player_id
     * @param string role
     *
     * @return bool
     */
    public static function update_role($team_id, $player_id, $role)
    {
        $player = self::find($player_id, array(
            'where' => array(array('team_id', $team_id)),
        ));

        if (!$player) {
            Log::error('選手が存在しません');

            return false;
        }

        if (!in_array($role, array('user', 'admin'))) {
            Log::error('存在しないroleです');

            return false;
        }

        // update
        $player->role = $role;
        $player->save();

        return true;
    }

    /**
     * フォーム取得.
     *
     * @param array field_name => value
     *
     * @return Fieldset object
     */
    public static function get_form($values = array())
    {
        $form = Fieldset::forge('profile', array(
            'form_attributes' => array('class' => 'form'),
        ));

        $form->add_model(self::forge());

        // submit
        $form->add('submit', '', array(
            'type' => 'submit',
            'value' => '更新',
            'class' => 'btn btn-success',
        ));

        // default value
        foreach ($values as $name => $value) {
            $form->field($name)->set_value($value);
        }

        return $form;
    }
}
