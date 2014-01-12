<?php
return array(
  '_root_' => 'top/index',
  'admin/signup' => 'admin/signup',

  'game/create' => 'game/create',
  'game/edit'   => 'game/edit',
  'game/delete' => 'game/delete',
  'game/list'   => 'game/list',


  'score' => 'score/index',

//  '_root_'  => 'welcome/index',  // The default route
  '_404_'   => 'welcome/404',    // The main 404 route

  'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);
