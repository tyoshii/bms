<?php

namespace Fuel\Migrations;

class Add_order_to_batter_results
{
  public function up()
  {
    \DBUtil::add_fields('batter_results', array(
        'order' => array('constraint' => 11, 'type' => 'int'),

    ));
  }

  public function down()
  {
    \DBUtil::drop_fields('batter_results', array(
        'order'

    ));
  }
}