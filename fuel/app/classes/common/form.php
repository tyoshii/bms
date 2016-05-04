<?php

class Common_Form
{
    public $form;

    public function __construct($name = 'default', $config = array())
    {
        if (count($config) === 0) {
            $config = array(
                'form-attributes' => array('class' => 'form'),
            );
        }

        $this->form = Fieldset::forge($name, $config);

        return $this;
    }

    public static function forge($name = 'default', $config = array())
    {
        return new self($name, $config);
    }

    public function id($value)
    {
        $this->form->add('id', 'ID', array(
            'type' => 'hidden',
            'value' => $value,
        ))
            ->add_rule('required')
            ->add_rule('match_value', $value, true);

        return $this;
    }

    public function username($value = '')
    {
        self::add_username($this->form, $value);

        return $this;
    }

    public static function add_username($form, $value = '')
    {
        if ($form->field('username')) {
            $form->delete('username');
        }

        $form->add('username', 'ユーザー名', array(
            'type' => 'text',
            'class' => 'form-control',
            'value' => $value,
            'description' => '半角英数と「 - 」「 _ 」が使用可能 / 50字以内',
        ), array())
            ->add_rule('required')
            ->add_rule('trim')
            ->add_rule('valid_string', array('alpha', 'numeric', 'dashes'))
            ->add_rule('max_length', 50);
    }

    public function password()
    {
        $this->form->add('password', 'パスワード', array(
            'type' => 'password',
            'class' => 'form-control',
            'description' => '半角英数と「. , ! ? : ;」が使用可能 / 8字以上 / 250字以内',
        ), array())
            ->add_rule('required')
            ->add_rule('min_length', 8)
            ->add_rule('max_length', 250)
            ->add_rule('valid_string', array('alpha', 'numeric', 'punctuation'))
            ->add_rule('match_field', 'confirm');

        return $this;
    }

    public function confirm()
    {
        $this->form->add('confirm', '確認用パスワード', array(
            'type' => 'password',
            'class' => 'form-control',
        ), array())
            ->add_rule('required');

        return $this;
    }

    public function name($value = '')
    {
        $this->form->add('name', '選手名', array(
            'type' => 'text',
            'class' => 'form-control',
            'value' => $value,
            'description' => '60字以内',
        ), array())
            ->add_rule('required')
            ->add_rule('trim')
            ->add_rule('max_length', 60);

        return $this;
    }

    public function number($value = '')
    {
        $this->form->add('number', '背番号', array(
            'type' => 'text',
            'class' => 'form-control',
            'value' => $value,
            'description' => '数字のみ / 3桁まで',
            'min' => 0,
        ))
            ->add_rule('required')
            ->add_rule('trim')
            ->add_rule('valid_string', array('numeric'))
            ->add_rule('max_length', 3);

        return $this;
    }

    public function team_id($value = '')
    {
        $teams = Model_Team::get_teams_key_value();

        $this->form->add('team_id', '所属チーム', array(
            'type' => 'select',
            'options' => array('' => '') + $teams,
            'value' => $value,
            'class' => 'select2',
        ))
            ->add_rule('required')
            ->add_rule('in_array', array_keys($teams));

        return $this;
    }

    public function email($value = '')
    {
        $this->form->add('email', 'メールアドレス', array(
            'type' => 'email',
            'class' => 'form-control',
            'value' => $value,
        ), array())
            ->add_rule('required')
            ->add_rule('max_length', 250)
            ->add_rule('valid_email');

        return $this;
    }

    public function group($value = '')
    {
        $groups = Auth::get_groups();
        $my_group = $groups[0][1];

        // roles取得
        $groups = Config::get('simpleauth.groups');
        $roles = array();
        foreach ($groups as $k => $v) {
            if ($k > 0 and $k <= $my_group) {
                $roles[$k] = $v['name'];
            }
        }

        $role_ops = $roles;

        // form add
        $this->form->add('group', '権限グループ', array(
            'class' => 'form-control',
            'type' => 'select',
            'options' => $role_ops,
            'value' => $value,
        ))
            ->add_rule('required');

        return $this;
    }

    public function submit($value = '')
    {
        $this->form->add('submit', '', array(
            'type' => 'submit',
            'class' => 'btn btn-success',
            'value' => $value,
        ), array());

        return $this;
    }

    public function get_object()
    {
        return $this->form;
    }
}
