<?php

class Model_User extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'email' => array(
            'data_type' => 'varchar',
            'label' => 'メールアドレス',
            'validation' => array(
                'required',
                'valid_email',
            ),
            'form' => array(
                'type' => 'email',
                'class' => 'form-control',
            ),
        ),
        'password' => array(
            'data_type' => 'varchar',
            'label' => 'パスワード',
            'validation' => array(
                'required',
                'min_length' => array(8),
                'valid_string' => array('alpha', 'numeric', 'punctuation'),
            ),
            'form' => array(
                'type' => 'password',
                'class' => 'form-control',
            ),
        ),
        'username' => array('form' => array('type' => false)),
        'group' => array('form' => array('type' => false)),
        'last_login' => array('form' => array('type' => false)),
        'login_hash' => array('form' => array('type' => false)),
        'profile_fields' => array('form' => array('type' => false)),
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
    protected static $_table_name = 'users';

    public static function get_username_list()
    {
        $users = DB::select()
            ->from(self::$_table_name)
            ->execute()->as_array();

        $return = array();
        foreach ($users as $user) {
            $return[$user['username']] = $user['username'];
        }

        return $return;
    }

    /**
     * new user registe.
     *
     * @param string fullname
     * @param string email
     * @param string password (default null)
     *
     * @return mix user_id / false
     */
    public static function regist($fullname, $email, $password = null)
    {
        $regist_by_openid = false;

        // generate username,password
        $username = Common::random_string();
        if (is_null($password)) {
            $regist_by_openid = true;
            $password = Common::random_string();
        }

        try {
            // user create
            $user_id = Auth::create_user(
                $username,
                $password,
                $email,
                1,
                array(
                    'fullname' => $fullname,
                    'regist_by_openid' => $regist_by_openid ? 1 : 0,
                )
            );

            if ($user_id === false) {
                throw new Exception('Internal Error');
            }

            return $user_id;
        } catch (SimpleUserUpdateException $e) {
            Session::set_flash('error', 'アカウントの作成に失敗しました：'.$e->getMessage());

            return false;
        } catch (Exception $e) {
            Session::set_flash('error', 'アカウントの作成に失敗しました：'.$e->getMessage());

            return false;
        }
    }

    private static function _update($username, $values)
    {
        // グループ更新
        try {
            Auth::update_user($values, $username);
        } catch (Exception $e) {
            Session::set_flash('error', $e->getMessage());

            return false;
        }

        return true;
    }

    public static function update_group($username, $group)
    {
        return self::_update($username, array('group' => $group));
    }

    public static function disable($username)
    {
        if ($username === Auth::get('username')) {
            Session::set_flash('error', '自分自身のアカウントは無効にできません');

            return false;
        }

        // 無効化（グループで操作）
        return self::update_group($username, -1);
    }

    public static function get_users_only_my_team()
    {
        $team_id = Model_Player::get_my_team_id();
        if (!$team_id) {
            return array();
        }

        $players = Model_Player::query()
            ->where('team_id', $team_id)
            ->where('username', '!=', '')
            ->get();

        $usernames = array();
        foreach ($players as $player) {
            $usernames[] = $player->username;
        }

        return DB::select()->from(self::$_table_name)
            ->where('username', 'in', $usernames)
            ->execute()->as_array();
    }
}
