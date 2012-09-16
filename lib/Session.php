<?php

/**
 Use the static method getInstance to get the object.
 */

class Session extends Singleton
{

	// The state of the session
	private $session_state = FALSE;
	 
	// THE only instance of the class
	private static $instance;
	 
	 
	protected function __construct()
	{
		 
	}

	/**
	 * Instance a singleton instance.
	 *
	 * The session is automatically initialized if it wasn't.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return Session singleton instance
	 */
	public static function getInstance()
	{
		self::$instance = parent::getInstance(__CLASS__);
		self::$instance->startSession();
		return self::$instance;
	}

	/**
	 *    (Re)starts the session.
	 *
	 *    @return    bool    TRUE if the session has been initialized, else FALSE.
	 **/
	 
	public function startSession()
	{
		if(@session_start() === FALSE)
		{
			@session_destroy(); // Destroy if exist for some reason
			@session_start();
			$this->session_state == TRUE;
		}
		 
		return $this->session_state;
	}
	 
	 
	/**
	 *    Stores datas in the session.
	 *    Example: $instance->foo = 'bar';
	 *
	 *    @param    name    Name of the datas.
	 *    @param    value    Your datas.
	 *    @return    void
	 **/
	 
	public function __set($name , $value)
	{
		$_SESSION[$name] = $value;
	}
	 
	 
	/**
	 *    Gets datas from the session.
	 *    Example: echo $instance->foo;
	 *
	 *    @param    name    Name of the datas to get.
	 *    @return    mixed    Datas stored in session.
	 **/
	 
	public function __get($name)
	{
		if ( isset($_SESSION[$name]))
		{
			return $_SESSION[$name];
		}
	}
	 
	 
	public function __isset($name)
	{
		return isset($_SESSION[$name]);
	}
	 
	 
	public function __unset($name)
	{
		unset($_SESSION[$name]);
	}

	public function __destroy()
	{
		$this->destroy();
	}
	 
	 
	/**
	 *    Destroys the current session.
	 *
	 *    @return    bool    TRUE is session has been deleted, else FALSE.
	 **/
	 
	public function destroy()
	{
		if ($this->session_state === TRUE)
		{
			$this->session_state = !session_destroy();
			unset($_SESSION);
			 
			return !$this->session_state;
		}
		 
		return FALSE;
	}
}