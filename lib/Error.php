<?php
/**
 * ErrorManager file.
 *
 * Enable a manager for all error types.
 *
 *
 * @copyright 	2011, (c) Ángel Guzmán Maeso
 * @author  	Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @package 	Lib
 */
class ErrorManager extends Singleton
{
	public static function getInstance()
	{
		return parent::getInstance(__CLASS__);
	}

	/**
	 * Handling fatal error.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	public function processFatalError()
	{
		$error = error_get_last();

		if(!is_null($error))
		{
			/** @see http://www.php.net/manual/en/errorfunc.constants.php */
			$critical_error = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);
			$suggestion_error = array(E_STRICT); //E_DEPRECATED
			$minor_error = array(E_NOTICE);
			 
			if(in_array($error['type'], $critical_error))
			{
				echo 'Critical error<br />';
				$this->renderPopup();
			}
			elseif(in_array($error['type'], $suggestion_error))
			{
				//if(Config::getInstance()->getDebugLevel() >= Config::DEBUG_EXTREME)
				//{
				//echo 'Suggestion error';
					//$this->renderPopup();
					//}
				}
				elseif(in_array($error['type'], $minor_error))
				{
					echo 'Minor error';
					echo var_dump($error);
					$this->renderPopup();
				}
				else
				{
					echo 'Unknow type PHP error on ErrorManager:'.'<br/>';
					echo var_dump($error);
					$this->renderPopup();
				}
	    
				# Checking if last error is a fatal error
				if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR))
				{
					echo 'Sorry, a serious error has occured in ' . $error['file'];
					echo var_dump($error);
					$this->renderPopup();
				}
	    
		}
		else
		{

		}
	}

	/**
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 */
	# Handler for user errors as sql or others
	public function processGenericError($errno, $errstr, $errfile, $errline)
	{
		global $game_forum, $server;
		switch ($errno)
		{
			case E_USER_ERROR: # 256
				if($errstr=='SQL')
				{
					switch(SQL_ERRNO)
					{
						case '1045':
							echo "
	                        <h1>Error establishing a database connection</h1>";
							break;
						case '1040': #Too many connections
							echo 'Too many connections. Retry in a few seconds.<br>';
							break;
						case '1203': # more than 'max_user_connections' active connections
							echo 'Too many active connections. Retry in a few seconds.<br>';
							break;
						case '2002': # CR_CONNECTION_ERROR
							# Can't connect to local MySQL server
							echo 'Error: El servidor local de MySQL no puede iniciarse.<br>';
							# SQL Error[2002] Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (2)
							#Query:Database to host connection for ao
							break;
						default:
							echo "<b>SQL Error</b>[".SQL_ERRNO."] ".SQL_ERROR."<br />\n";
						echo "Query:".SQL_QUERY."<br />\n";
						echo "On line ".SQL_ERROR_LINE." in file ".SQL_ERROR_FILE." ";
						echo ", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
						echo "Aborting...<br />\n";
						break;
					}

				}
				else
				{
					echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
					echo "  Fatal error on line $errline in file $errfile";
					echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
					echo "Aborting...<br />\n";
				}
				exit(1);
				break;
			case E_USER_WARNING:
				echo "Error User Warning<br>\n";
				break;
			case E_USER_NOTICE:
				echo "Error User Notice<br>\n";
				break;
			case E_PARSE:
				echo "Error PHP parse<br>\n";
				break;
			case E_STRICT:
				echo "Error PHP strict<br>\n";
			case E_ALL:
				echo "Error PHP all<br>\n";
				break;
			case E_ERROR:
				echo "Error PHP error<br>\n";
				break;
			case E_WARNING:
				echo "Error PHP warning:<br> $errstr<br>\n";
				echo "Warning error on line $errline in file $errfile";
				echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
				break;
			default:
				echo "Error PHP Unknown [$errno]:<br> $errstr<br>\n";
			echo "  Fatal error on line $errline in file $errfile";
			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "Aborting...<br />\n";
			exit();
			break;
		}
		return true; # Don't execute PHP internal error handler
	}

	/**
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 */
	# Handler for sql errors
	public function processSqlError($query = '', $php_file, $line)
	{
		define("SQL_QUERY", $query);
		define("SQL_ERRNO", mysql_errno());
		define("SQL_ERROR", mysql_error());
		define("SQL_ERROR_LINE", $line);
		define("SQL_ERROR_FILE", $php_file);
		trigger_error("SQL", E_USER_ERROR);
	}

	/**
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 */
	public function processExceptionError($exception)
	{
		#echo 'EXCEPTION HANDLER<br/>';

		if(FALSE)
	{
		$trace = $exception->getTrace();

		$result = array();
		foreach ($trace as $key => $stackPoint)
		{
			// I'm converting arguments to their type
			// (prevents passwords from ever getting logged as anything other than 'string')
			/*$trace[$key]['args'] = array(
			 array_map('gettype', $trace[$key]['args']),
			$trace[$key]['args']
			);*/
			$stack_args = '';

			$stackPointArgsParsed = array();
			foreach($trace[$key]['args'] as $args)
			{
				echo var_dump($args);
				$stackPointArgsParsed[] = implode(' ',
				array(
			    				    '<span style="color:#000">' . gettype($args) .'</span>',
			    				    '<span style="color:#cc0000">' . $args . '</span>'
				));
			}
		  
			if(!isset($trace[$key]['file']) || empty($trace[$key]['file']))
			$trace[$key]['file'] = 'Unknown file';
		  
			if(!isset($trace[$key]['line']) || empty($trace[$key]['line']))
			$trace[$key]['line'] = 'Unknown line';

			//echo var_dump($stack_args);
			$result[] =
		            '<span style="color:red"># ' . $key . '</span> ' . 
			$trace[$key]['file'] . '(' . $trace[$key]['line'] .
		            '): ' . 
		            '<span style="color:#000;font-weight:bold">' .$trace[$key]['class'] . '</span>' . $trace[$key]['type'] .
		            '<span style="color:#4e9a06">' .$trace[$key]['function'] . '</span>' .
		            '(' . implode(', ', $stackPointArgsParsed) . ')
		            <br />';
		}

		// trace always ends with {main}
		$result[] = '    <span style="color:green"># ' . ++$key . '</span> {main}';

		$msg = "
		    <div style='border:1px solid #DDDDDD;font-size: 12px;background-color:#EEEEEE;color:#3465a4;padding:0.75em 1.5em;'>
		    	<h1>ExceptionHander:</h1> 
		    	Class: " . get_class($exception) . "<br /> 
		    	<div style='padding-left:10px;'>
			    	Message: " . $exception->getMessage() . "<br /> 
				    File: " . $exception->getFile() . " <br />
				    Line: " . $exception->getLine() . " <br />
				    Code: " . $exception->getCode() . " <br />
			    </div>
			    <br />
			    <span style='text-decoration:underline;font-weight:bold'>
			    	Stack trace
			    </span>
			    <br />
			    <div style='padding-left:10px;'>
			    	" . implode('', $result) . "
			    </div>
			    <br />
		    </div> ";

		echo $msg;
	}
	 
	$this->renderPopup();
	}

	private function renderPopup()
	{
		echo "
		
	    <style>#blanket{background-color:#111;opacity: 0.65;position:absolute;z-index: 9001;top:0px;left:0px;}#popup_close{border:1px solid black;width:15px;color:red;background-color:#FEEFB3;height:15px;float:right;text-decoration:none;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;-ms-border-radius:3px;}#popUpDiv{background:#FFFFCC url(img/messages/error.png) 6px 6px no-repeat;color:#D8000C;background-color:#FFBABA;position:absolute;padding-top:15px;border:2px solid #D8000C;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;-ms-border-radius:8px;padding:4px;width:400px;height:36px;z-index:9002;text-align:center;font-size:12px;top:200px;}</style>
		<!--[if gte IE 5]> <style> #blanket {filter:alpha(opacity=65);}</style><![endif]-->
	    <script>
		var blanket = document.createElement('div');
		blanket.style.display = 'none'
		blanket.id = 'blanket'
		document.body.appendChild(blanket);
		
		var popup_close = document.createElement('a');
		popup_close.id = 'popup_close'
		popup_close.innerHTML = 'X'
		popup_close.href = '#'
		popup_close.onClick = 'popup()'
		popup_close.setAttribute('onclick','popup()') // for FF
    	popup_close.onclick = function() { popup()} // for IE
		
		var innerPopup = document.createElement('div');
		innerPopup.style.paddingLeft = '10px'
		innerPopup.style.paddingTop = '10px'
		innerPopup.innerHTML = 'Opps! Un error ha occurido. Perdona las molestias.'
    	
		var popUpDiv = document.createElement('div');
		popUpDiv.style.display = 'none'
		popUpDiv.id = 'popUpDiv'
		popUpDiv.appendChild(popup_close);
		popUpDiv.appendChild(innerPopup);
		
		
		
		document.body.appendChild(popUpDiv);
		
	    function toggle(div_id) 
	    {
			var el = document.getElementById(div_id)
			el.style.display = ( el.style.display == 'none' ) ? 'block' : 'none'
		}
		
		function popup() 
		{
			var blanket = document.getElementById('blanket');
			blanket.style.height =  window.innerHeight + document.body.offsetHeight + 'px';
			blanket.style.width =  document.width  + document.body.offsetWidth + 'px'
			
			var popUpDiv = document.getElementById('popUpDiv')
			popUpDiv.style.left = document.width / 2 - 150 + 'px'
			
			toggle('blanket')
			toggle('popUpDiv')	
		}
		
		popup()
	    </script>
	    
	    
	    ";
	}
}