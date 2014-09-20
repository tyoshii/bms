<?php

abstract class Test_Base extends \TestCase
{
  protected function setUp()
  {
    parent::setUp();
    Auth::logout();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  public function get_property($class_name, $prop_name)
  {
    $class = new ReflectionClass($class_name);
    $prop = $class->getProperty($prop_name);
    $prop->setAccessible(true);

    $orig = new $class_name;

    return $prop->getValue($orig);
  }

	public function assertRedirect($res, $location, $code = 302)
	{
		$this->assertSame($code, $res->status);
		$this->assertSame($location, $res->headers['Location']);
	}

	public function assertException($func)
	{
		try {
			$func();
			$this->assertTrue(false);
		} catch ( Exception $e ) {
			$this->assertTrue(true);
		}
	}
}
