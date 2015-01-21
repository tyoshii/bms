<?php

class Model_Convention extends \Orm\Model
{
	protected static $_properties = array(
		'id' => array('form' => array('type' => false)),
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
				'in_array' => array(
					'league',
					'tournament',
				),
			),
			'form' => array(
				'type' => 'select',
				'options' => array(
					'league',
					'tournament',
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
	 * @param
	 * @return FieldsetObject
	 */
	public static function get_form()
	{
		$form = Fieldset::forge('convention_add')->add_model(static::forge());

		$form->add('add', '', array(
			'type' => 'submit',
			'value' => '追加',
			'class' => 'form-control btn btn-success',
		));

		return $form;
	}
}
