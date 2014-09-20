<?php

namespace Fuel\Migrations;

class Add_ip_frac_to_stats_pitchings
{
  public function up()
  {
    \DBUtil::add_fields('stats_pitchings', array(
        'IP_frac' => array('constraint' => 8, 'type' => 'varchar'),

    ));
  }

  public function down()
  {
    \DBUtil::drop_fields('stats_pitchings', array(
        'IP_frac'

    ));
  }
}