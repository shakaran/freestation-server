<?php
/**
 * Singleton class file.
 *
 * @copyright 2011, (c) Ángel Guzmán Maeso
 * @author  	 Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @package 	 Core
 */
abstract class Singleton
{
	const UNDEFINED_CLASS = 1;

	protected static $__instances = array();

	protected function __construct()
	{
	}

	protected function __clone()
	{
	}

	protected static function getInstance($class)
	{
		if(!self::checkExists($class))
		{
			if(class_exists($class))
			    self::$__instances[$class] = new $class();
			else
			    throw new Exception('Exception(Singleton): Cannot instantiate undefined class \'' . $class . '\'', self::UNDEFINED_CLASS);
		}

		return self::$__instances[$class];
	}

	public static function checkExists($class)
	{
		return isset(self::$__instances[$class]);
	}
}