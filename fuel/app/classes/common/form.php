<?php

class Common_Form
{
  public $form;

  public function __construct($name = 'default', $config = array())
  {
    if ( count($config) === 0 )
    {
      $config = array(
        'form-attributes' => array('class' => 'form')
      );
    }

    $this->form = Fieldset::forge($name, $config);
    return $this;
  }
  public static function forge($name = 'default', $config = array())
  {
    return new Common_Form($name, $config);
  }
  
  public function username($value = '')
  {
    $this->form->add('username', 'ユーザー名', array(
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $value,
      'description' => '半角英数 / 50字以内',
    ), array())
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('valid_string', array('alpha', 'numeric'))
      ->add_rule('max_length', 50);

    return $this;
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
    $this->form->add('name', '名前', array(
      'type' => 'text',
      'class' => 'form form-control',
      'value' => $value,
      'description' => '60字以内',
    ), array())
      ->add_rule('required')
      ->add_rule('max_length', 60);

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
      ->add_rule('valid_email');

    return $this;
  }

  public function group($value = '')
  {
    // roles取得
    $groups = Config::get("simpleauth.groups");
    $roles = array();
    foreach ($groups as $k => $v) {
        if ($k > 0) {
            $roles[$k] = $v["name"];
        }
    }
    $role_ops = $roles;

    // form add
    $this->form->add('group', '権限グループ', array(
      'class'       => 'form-control',
      'type'        => 'select',
      'options'     => $role_ops,
      'value'       => $value,
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
