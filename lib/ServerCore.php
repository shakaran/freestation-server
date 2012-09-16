<?php
/**
*    Freestation, plataform for software distribution.
*
*    Copyright (C) 2012	Ángel Guzmán Maeso
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
* 
* ServerCore class file.
* 
* It handles the driver for start/stop and other common functions.
* 
* @copyright 	2011, (c) Ángel Guzmán Maeso
* @license 		AGPLv3 http://www.gnu.org/licenses/agpl-3.0.en.html
* @author  		Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 		1.0
* @category		Core
* @package 		Lib
* @subpackage	-
* @link         http://freestation.quijost.com
*/
class ServerCore
{
	/** @const PYTHON_BINARY Set the python version binary to run */
	const PYTHON_BINARY = 'python2.7';
	/** @const SERVER_BINARY Freestation server python binary name */
	const SERVER_BINARY = 'fs_server.py';
	
	/** @const RUNNING_LOG Log for running output */
	const RUNNING_LOG         = 'running.log';
	/** @const STANDARD_OUTPUT_LOG Log for standard output */
	const STANDARD_OUTPUT_LOG = 'ice_output.log';
	/** @const STANDARD_ERROR_LOG Log for standard error output */
	const STANDARD_ERROR_LOG  = 'ice_error.log';
	/** @const WARNING_LOG Log for warning output */
	const WARNING_LOG         = 'ice_error.log';
	/** @const TRACE_LOG Log for trace output */
	const TRACE_LOG           = 'ice_error.log';
	/** @const OTHER_LOG Log for other output */
	const OTHER_LOG           = 'ice_error.log';
	
	/**
	 * Build the ServerCore object.
	 * 
	 * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * 
	 * @return  void
	 */
	public function __construct()
	{

	}

	/**
	* Get the server PID of python freestation server process.
	*
	* @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	*
	* @return  string The PID as string
	*/
	public static function getServerPid()
	{
		// Systems shows the ouput of awk print, so using passtrhu instead
		ob_start();
		passthru('ps u -C ' . self::PYTHON_BINARY . ' | grep ' . self::SERVER_BINARY . " | awk {'print \$2'}");
		$server_pid = ob_get_contents();
		ob_end_clean();

		//echo var_dump($server_pid);

		return $server_pid;
	}

	/**
	* Reset the server logs (standard output, error and running)
	*
	* @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	*
	* @return  void
	*/
	private static function resetLogs()
	{
		echo 'Emptying logs. <br />';
		self::emptyLog('standard_output');
		self::emptyLog('standard_error');
		self::emptyLog('running');
	}
	
	/**
	* Start the freestation server process.
	* 
	* It empty the logs previously to start.
	*
	* @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	*
	* @return  void
	*/
	public static function start()
	{
		self::resetLogs();

		echo 'Starting new process: ';
		
		$mysql = new MySqlDriver();
		$mysql->close();
		
		// Forcing with python2.7, but python2.4 works too.
		$binary_path = '/usr/local/bin/' . self::PYTHON_BINARY;
		chdir('backend');
		$result = system('nohup ' . $binary_path .' ' . self::SERVER_BINARY . ' > ' . self::RUNNING_LOG . ' 2>&1 &', $status);
		// Another invoke: /usr/bin/python FreeStationServer.py &> running.log

		// Wait 0,5 sec
		time_nanosleep(0, 500000000);
		// Show as successfull if empty. But it could get no start when the server crash or throw exception
		// Consider this function as "launcher" but it doesn't have guarantee of a nice start.
		// For that check that the logs are properly and you get a "server started".
		if(empty($result))
		{
			// Ensure that the process is really running after launch requesting the PID
			$server_pid = self::getServerPid();

			if(!empty($server_pid) && $status === 0)
			{
				echo 'Successful<br/><br/>';
			}
			elseif(empty($server_pid))
			{
				echo '<br />Error: failed to start. No PID found. Please check error logs.<br/><br/>';
			}
			else
			{
				echo '<br />Error: started but failed with errors (status ' . $status .' with result ' . $result .').<br />
				     Please check error logs.<br/><br/>';
			}
		}
		else
		{
			echo 'Error: failed to start. Status ' . $status .' with result ' . $result .'.<br />
			Please check error logs.<br/><br/>';
		}
	}

	/**
	* Stop the freestation server process.
	*
	* It uses SIGTERM signall to kill the process.
	*
	* @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	*
	* @return  void
	*/
	function stop()
	{
		$server_pid = self::getServerPid();

		if(!empty($server_pid))
		{
			echo 'Trying to stop process PID ' . $server_pid . ': ';
			$result = system('kill -9 ' . $server_pid, $status);

			// Kill return 0 status or empty if successful
			if(empty($result) && $status === 0)
			{
				echo 'Successful<br/><br/>';
			}
			else
			{
				echo var_dump($status);
				echo 'Error: ' . $result . ' with status ' . $status . '<br/><br/>';
			}
		}
		else
		{
			echo 'Error: could not find any process PID. Aborting stop server.<br />';
		}
	}

	/**
	* Empty the log given.
	*
	* Some error logs are shared from the same file,
	* so requesting empty if blow up all the others.
	*
	* @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	*
	* @param   string $log The log name. By default NULL
	* @return  integer The tab index of the log
	*/
	public static function emptyLog($log = NULL)
	{
		switch($log)
		{
			case 'running':
				$result = system('cat /dev/null > backend/' . self::RUNNING_LOG, $status);
				return 0;
				break;

			case 'standard_output':
				$result = system('cat /dev/null > backend/' . self::STANDARD_OUTPUT_LOG, $status);
				return 1;
				break;
					
			case 'standard_error':
				$result = system('cat /dev/null > backend/' . self::STANDARD_ERROR_LOG, $status);
				return 2;
				break;

			case 'warning':
				$result = system('cat /dev/null > backend/' . self::WARNING_LOG, $status);
				return 3;
				break;

			case 'trace':
				$result = system('cat /dev/null > backend/' . self::TRACE_LOG, $status);
				return 4;
				break;

			case 'other':
				$result = system('cat /dev/null > backend/' . self::OTHER_LOG, $status);
				return 5;
				break;
		}
	}
}