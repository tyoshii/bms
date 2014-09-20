<?php

namespace Fuel\Migrations;

class Add_disp_order_to_stats_meta
{
  public function up()
  {
    \DBUtil::add_fields('stats_meta', array(
        'disp_order' => array('type' => 'tinyint', 'unsigned' => true),

    ));
  }

  public function down()
  {
    \DBUtil::drop_fields('stats_meta', array(
        'disp_order'

    ));
  }
}