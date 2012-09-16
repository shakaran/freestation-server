<?php
/**
 * Loader class file.
 *
 * @copyright 2011, (c) Ángel Guzmán Maeso
 * @author  	 Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @package 	 Lib
 */
ini_set('zlib.output_compression_level', 9);
if(!ob_start('ob_gzhandler')) ob_start();

if(!defined('ROOT_PATH')) define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');

ini_set('include_path', ini_get('include_path') . ':'. ROOT_PATH);
ini_set('safe_mode', 'off');
ini_set('open_base_dir', ROOT_PATH);
ini_set('display_errors', 'on');
ini_set('scream.enabled', 'on');
error_reporting(E_ACTUALLY_ALL); // E_ALL still is no enough, sadly :(

require_once ROOT_PATH . '/lib/core/Singleton.php';
require_once ROOT_PATH . '/lib/Error.php';
require_once ROOT_PATH . '/lib/ClassHash.php';

/**
 * Autoload the class on a PHP context
 *
 * @author	Angel Guzmán Maeso <angel.guzman@alu.uclm.es>
 */
class Loader
{
	/**
	 * Loads the classes needed for the app.
	 *
	 * Use spl_autoload_register instead of __autoload that only can
	 * be define once.
	 * @author Angel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	public function __construct()
	{
		set_include_path($_SERVER['DOCUMENT_ROOT'] . 'classes');

		// Register a autoloader
		spl_autoload_register(array($this, 'loader'));
	}


	/**
	 * Register the fatal error, generic error and exception handlers.
	 *
	 * @author Angel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	private function registerHandlers()
	{
		register_shutdown_function(array('Loader', 'shutdown'));
		set_exception_handler(array('Loader', 'errorExceptionHandler'));
		set_error_handler(array('Loader', 'errorHandler'), E_ALL);
	}

	/**
	 * Autoload the class on a PHP context.
	 *
	 * @author	Angel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @param string $class_name the name of the class
	 * @return void
	 * @throws ClassNotExist if the class not exist on the class hash
	 */
	private function loader($class_name)
	{
		$this->registerHandlers();

		//echo 'Loader: Trying to load ' . $class_name . PHP_EOL;

		if(!class_exists($class_name, false))
		{
			/** @var ClassHash $class_hash */
			$class_hash = ClassHash::getInstance();
			$class_path = $class_hash->get($class_name);
			require_once ROOT_PATH . $class_path;
		}
	}

	public static function shutdown()
	{
		ErrorManager::getInstance()->processFatalError();

		// Parse,Compile, Core, etc... Errors
		//ini_set('html_errors',false);
		//ini_set('error_prepend_string','<html><head><META http-equiv="refresh" content="0;URL=/error.php?msg=');
		//ini_set('error_append_string','"></head></html>');
	}

	public static function errorHandler()
	{
		ErrorManager::getInstance()->processFatalError();
	}

	public static function errorExceptionHandler($exception)
	{
		ErrorManager::getInstance()->processExceptionError($exception);
	}
}

$autoloader = new Loader();