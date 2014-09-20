<?php

namespace Fuel\Migrations;

class Create_stamen
{
  public function up()
  {
    \DBUtil::create_table('stamen', array(
        'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
        'game_id'    => array('constraint' => 11, 'type' => 'int'),
        'data'       => array('type' => 'text'),
        'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
        'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

    ), array('id'));
  }

  public function down()
  {
    \DBUtil::drop_table('stamen');
  }
}