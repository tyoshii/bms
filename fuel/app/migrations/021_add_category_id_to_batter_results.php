<?php

namespace Fuel\Migrations;

class Add_category_id_to_batter_results
{
  public function up()
  {
    \DBUtil::add_fields('batter_results', array(
        'category_id' => array('constraint' => 11, 'type' => 'int'),

    ));
  }

  public function down()
  {
    \DBUtil::drop_fields('batter_results', array(
        'category_id'

    ));
  }
}