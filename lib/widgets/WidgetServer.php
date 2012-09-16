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
* WidgetServer class file.
* 
* Create the server management box.
* 
* @copyright 	2011, (c) Ángel Guzmán Maeso
* @license 		AGPLv3 http://www.gnu.org/licenses/agpl-3.0.en.html
* @author  		Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 		1.0
* @package 		Lib/Widgets
*/
class WidgetServer
{
	private $action = NULL;
	private $option = NULL;

	/**
	 * Build a WidgetServer object.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	public function __construct()
	{
		$this->action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : NULL;
		$this->option = (isset($_REQUEST['option'])) ? $_REQUEST['option'] : NULL;
	}
	
	/**
	* Prints the data on standard output box
	*
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return void
	*/
	private function standardOutputBox()
	{
		if(file_exists('backend/ice_output.log'))
		{
		    $data = file_get_contents('backend/ice_output.log');
		    
			echo '<div class="ice_output_boxed">' .
				 '    <a style="float:right;width:50px;" href="/server/empty/standard_output/#tab=2" title="Empty standard output">' .
				 '        <img src="/img/empty.png" title="Empty log" alt="Empty log" /></a>' .
				 '    <pre style="margin-top:0px">' . $data .'</pre>' .
			     '</div>';
		}
	}
	
	/**
	* Prints the data on standard error output box
	*
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return void
	*/
	private function standardErrorOutputBox($data = NULL)
	{
		echo '<div class="standard_error_output_boxed">' .
			 '    <a style="float:right;width:50px;" href="/server/empty/standard_error/#tab=3" title="Empty standard error output">' .
			 '        <img src="/img/empty.png" title="Empty log" alt="Empty log" /></a>' .
			 '    <pre style="margin-top:0px">' . $data .'</pre>' . 
			 '</div>';
	}

	/**
	* Prints the data on warning output box
	*
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return void
	*/
	private function warningOutputBox($data = NULL)
	{
		echo '<div class="warning_output_boxed">' .
			 '    <a style="float:right;width:50px;" href="/server/empty/warning/#tab=4" title="Empty warning output">' .
			 '		<img src="/img/empty.png" title="Empty log" alt="Empty log" /></a>' .
			 '    <pre style="margin-top:0px">' . $data .'</pre>' .
			 '</div>';
	}

	/**
	* Prints the data on trace output box
	*
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return void
	*/
	private function traceOutputBox($data = NULL)
	{
		echo '<div class="trace_output_boxed">' .
			 '		<a style="float:right;width:50px;" href="/server/empty/trace/#tab=5" title="Empty trace output">' .
			 '		<img src="/img/empty.png" title="Empty log" alt="Empty log" /></a>' .
			 '		<pre style="margin-top:0px">' . $data .'</pre>' .
			 '</div>';
	}

	/**
	* Prints the data on other output box
	*
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return void
	*/
	private function otherOutputBox($data = NULL)
	{
		echo '<div class="other_output_boxed">' .
			 '    <a style="float:right;width:50px;" href="/server/empty/other/#tab=6" title="Empty other output">' .
			 '        <img src="/img/empty.png" title="Empty log" alt="Empty log" /></a>' .
			 '    <pre style="margin-top:0px">' . $data .'</pre>' .
			 '</div>';
	}

	/**
	* Prints the data on running output box
	*
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return void
	*/
	private function runningOutputBox()
	{
		if(file_exists('backend/running.log'))
		{
		    $data = file_get_contents('backend/running.log');
		    
			echo '<div class="running_log">' .
				 '    <a style="float:right;width:50px;" href="/server/empty/running/#tab=1" title="Empty running output">' .
				 '        <img src="/img/empty.png" title="Empty log" alt="Empty log" /></a>' .
				 '    <pre style="float:left;margin-top:0px">' . $data .'</pre>' .
				 '</div>';
		}
	}

	private function printServerStatus($server_pid)
	{
	    if(!empty($server_pid))
	    {
	        echo '<img title="Enabled PID ' . $server_pid . '" alt="Enabled PID ' . $server_pid . '" style="border:none;height:20px" src="/img/enabled.png" />';
	    }
	    else
	    {
	        echo '<img title="Disabled" alt="Disabled" style="border:none;height:20px" src="/img/disabled.png" />';
	    }
	}
	
	private function adminBox()
	{
		$server_pid = ServerCore::getServerPid();

		echo '
		<div id="admin_box">
			<div id="info_box">
			Status: ';
		
        $this->printServerStatus($server_pid);

		echo '<br />Server IP: ' . $_SERVER['SERVER_ADDR'] . '
			</div>
			<div id="action_box" style="float:left">';

		if(!empty($server_pid))
		{
			echo '<a class="medium red awesome" style="margin:3px" href="/server/stop/"><img src="/img/stop.png" title="Stop server" alt="Stop server" /> Stop server</a>';
		}
		else
		{
			echo '<a class="medium red awesome" style="margin:3px" href="/server/start/"><img src="/img/start.png" title="Start server" alt="Start server" /> Start server</a>';
		}

		echo '
				<a class="medium red awesome" style="margin:3px" href="/server/restart/"><img src="/img/restart.png" title="Restart server" alt="Restart server" /> Restart server</a>
			</div>
			<br style="clear:both" />
		</div>';
	}

	private function outputBox()
	{
		echo '
		<script src="/js/TabPane.js" type="text/javascript"></script>
		<script src="/js/tabpane-functions.js" type="text/javascript"></script>
		<link type="text/css" href="/css/tabpane.css" rel="stylesheet" media="all" />
		
		<div id="sidebar"></div>
		
	    <h2><img src="/img/output.png" title="Output logs" alt="Output logs" /> Output logs 
		<a href="javascript:document.location.reload()" title="Refresh output logs" alt="Refresh output logs"><img 
		id="refresh-icon" src="/img/refresh.png" title="Refresh output logs" /></a></h2>
		';

		$data = NULL;
		if(file_exists('backend/ice_error.log'))
		{
			$data = file_get_contents('backend/ice_error.log');

			$matches = NULL;
			preg_match_all("~!! (.*)\n~i", $data, $matches);

			$error_counter = 0;
			$error_data = NULL;
			if(isset($matches[0]))
			{
				$error_counter = count($matches[0]);

				foreach($matches[0] as $key => $value)
				{
					$error_data .= substr($value, 3, strlen($value));
				}
			}

			$matches = NULL;
			preg_match_all("~-! (.*)\n~i", $data, $matches);

			$warning_counter = 0;
			$warning_data = NULL;
			if(isset($matches[0]))
			{
				$warning_counter = count($matches[0]);

				foreach($matches[0] as $key => $value)
				{
					$warning_data .= substr($value, 3, strlen($value));
				}
			}

			$matches = NULL;
			preg_match_all("~-- (.*)\n~i", $data, $matches);

			$trace_counter = 0;
			$trace_data = NULL;
			if(isset($matches[0]))
			{
				$trace_counter = count($matches[0]);

				foreach($matches[0] as $key => $value)
				{
					$trace_data .= substr($value, 3, strlen($value));
				}
			}

			$matches = NULL;
			preg_match_all('@(.*)\n@', $data, $matches);

			$other_counter = 0;
			$other_data = NULL;
			if(isset($matches[0]))
			{
				foreach($matches[0] as $key => $value)
				{
					if($value[0] == '!' && $value[1] == '!'){
					}
					elseif($value[0] == '-' && $value[1] == '!'){
					}
					elseif($value[0] == '-' && $value[1] == '-'){
					}
					else
					{
						$other_data .= $value ;
						$other_counter += 1;
					}
				}
			}

		}
		else
		{
			$error_counter   = 0;
			$warning_counter = 0;
			$trace_counter   = 0;
			$other_counter   = 0;
			$data = 'empty.';
		}

		echo '
		<div id="output_panel">
		    <ul class="tabs">
		        <li id="running_tab" class="tab running">Running <span class="hide">&times;</span></li>
		        <li id="standard_output_tab" class="tab standard_output">Standard <span class="hide">&times;</span></li>
		        <li id="standard_error_tab" class="tab standard_error">Error (<span style="font-weight:bold">' . $error_counter . '</span>)<span class="hide">&times;</span></li>
		        <li id="warning_tab" class="tab warning">Warning (<span style="font-weight:bold">' . $warning_counter . '</span>)<span class="hide">&times;</span></li>
		        <li id="trace_tab" class="tab trace">Trace (<span style="font-weight:bold">' . $trace_counter . '</span>)<span class="hide">&times;</span></li>
		        <li id="other_tab" class="tab other">Other (<span style="font-weight:bold">' . $other_counter . '</span>)<span class="hide">&times;</span></li>
		    </ul>
	    
		    <div class="content running_box">
		    	';
		$this->runningOutputBox();
		echo '
		    </div>
	    	    <div class="content standard_output_box">
		    	';
		$this->standardOutputBox();
		echo '
		    </div>
	    	<div class="content standard_error_box">
		    	';
		$this->standardErrorOutputBox($error_data);
		echo '
		    </div>	    
		    <div class="content warning_box">
		    	';
		$this->warningOutputBox($warning_data);
		echo '
		    </div>
		    <div class="content trace_box">
		    	';
		$this->traceOutputBox($trace_data);
		echo '
		    </div>
		    <div class="content other_box">
		    	';

		$this->otherOutputBox($other_data);
		echo '
		    </div>
		</div>
	';
	}

	/**
	 * Render the widget.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return string The widget data rendered as string
	 */
	public function render()
	{
		echo ' <h1>Server management</h1>';

		switch($this->action)
		{
			case 'start':
				echo '<h3>Start server</h3>';

				$server_pid = ServerCore::getServerPid();
				if(empty($server_pid))
				{
					ServerCore::start();
				}
				else
				{
					echo 'There are another instance currently running.<br />
					Do you want? <br /><br />
					<a class="medium red awesome" style="margin:3px" href="/server/stop/">Stop server</a>
					<a class="medium red awesome" style="margin:3px" href="/server/restart/">Restart server</a>
					';
				}

				echo '<a class="medium yellow awesome" style="margin:3px" href="/server/">Back</a>';
				break;

			case 'stop':
				echo '<h3>Stop server</h3>';
				ServerCore::stop();
				echo '<a class="medium yellow awesome" style="margin:3px" href="/server/">Back</a>';
				break;

			case 'restart':
				echo '<h3>Restart server</h3>';

				$server_pid = ServerCore::getServerPid();

				if(empty($server_pid))
				{
					echo 'No stopping beacuse any server was previously found started.<br/><br/>';

					ServerCore::start();
				}
				else
				{
					ServerCore::stop();
					ServerCore::start();
				}

				echo '<a class="medium yellow awesome" style="margin:3px" href="/server/">Back</a>';
				break;

			case 'empty':
				$tab_number = ServerCore::emptyLog($this->option);

				header('Location: /server/#tab=' . $tab_number);
				exit();
				break;

			default:
				$this->adminBox();
				$this->outputBox();
			break;
		}
	}
}