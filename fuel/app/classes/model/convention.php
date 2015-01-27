<?php

class Model_Convention extends \Orm\Model
{
	protected static $_properties = array(
		'id' => array(),
		'name' => array(
			'date_type' => 'varchar',
			'label' => '大会名',
			'validation' => array(
				'required',
				'max_length' => array(64),
			),
			'form' => array(
				'type' => 'text',
				'class' => 'form-control',
			),
		),
		'kind' => array(
			'date_type' => 'varchar',
			'label' => '大会の種類',
			'validation' => array(
				'required',
				// TODO: validation
			),
			'form' => array(
				'type' => 'select',
				'options' => array(
					'league' => 'league',
					'tournament' => 'tournament',
				),
				'value' => 'league',
				'class' => 'form-control',
			),
		),
		'published' => array(
			'date_type' => 'varchar',
			'label' => '大会を非公開にする',
			'validation' => array(
				'match_value' => array('false'),
			),
			'form' => array(
				'type' => 'checkbox',
				'value' => 'false',
				'class' => 'form-control',
			),
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

	protected static $_table_name = 'conventions';

	/**
	 * convention add/update form
	 * @param Model_Convention
	 * @return FieldsetObject
	 */
	public static function get_form($model)
	{
		$form = Fieldset::forge('convention_add')->add_model(static::forge());

		// submit
		$form->add($model ? 'update' : 'add', '', array(
			'type' => 'submit',
			'value' => $model ? '更新' : '追加',
			'class' => 'form-control btn btn-success',
		));

		// when update ..
		if ($model)
		{
			// add id field
			$form->add('id', '', array(
				'type' => 'hidden',
				'class' => 'form-control',
			))->add_rule('require')
				->add_rule('match_value', $model->id);

			// default value
			$form->populate($model);

			// kind field readonly
			$form->field('kind')->add_rule('match_value', $model->kind);
			$form->field('kind')->set_attribute('readonly', 'readonly');
		}

		return $form;
	}

	/**
	 * convention regist
	 * @param array $val->validated()
	 * @return boolean
	 */
	public static function regist($prop)
	{
		if (array_key_exists('id', $prop))
		{
			// update
		}

		// new
		if ( ! $prop['published'])
		{
			$prop['published'] = 'true';
		}

		Mydb::begin();

		$conv = static::forge($prop);
		$conv->save();

		Model_Conventions_Admin::add($conv['id'], Auth::get_screen_name());

		Mydb::commit();

		return true;
	}

	/**
	 * get own convnetion list
	 *   published = true && admin convention
	 * @return array
	 */
	public static function get_own_list()
	{
		// TODO: Convention / Conventions_Admin をrelationに
		// 今のままだと Conventionテーブルの値しかとってこれてない

		$ids = Model_Conventions_Admin::query()
						->select('convention_id')
						->where('username', Auth::get_screen_name());

		$res = static::query()
			->where('published', 'true')
			->or_where('id', 'in', $ids->get_query(true))
			->get();

		return $res;
	}
}
