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
* Clients file.
*
* Main access to the clients application.
*
* @copyright 	2011, (c) Ángel Guzmán Maeso
* @license 		AGPLv3 http://www.gnu.org/licenses/agpl-3.0.en.html
* @author  		Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 		1.0
*/

require_once 'lib/Loader.php';
require_once 'lib/session.php';

$CMS = new CMS();
$CMS->openPage('Clients');

if(!isset($_SESSION['user_id']))
{
	header('Location: /login/');
	exit();
}

$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : NULL;
$option = (isset($_REQUEST['option'])) ? $_REQUEST['option'] : NULL;

$mysql_driver = new MySqlDriver();

switch($action)
{
	case 'add':
		echo '<h3>Add a client</h3>';
		
		if(!isset($_REQUEST['submit']))
		{
			echo '
			<form method="post" action="/clients/add/">
				<p>
					<div style="width:200px;text-weight:bold">IP</div> <input autofocus name="ip" type="text" value="" />
				</p>
				<p>
					<div style="width:200px;text-weight:bold">Hostname</div> <input name="hostname" type="text" value="" />
				</p>
				<p>
					<div style="width:200px;text-weight:bold">Status </div>
					<select name="status">
						<option value="1" selected="selected">Enabled</option>
						<option value="0">Disabled</option>
					</select>
				</p>
				<p>
					<input name="submit" type="submit" value="Add" /> 
					<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back</a>
				</p>
			</form>
			';
		}
		else 
		{
			$ip = (isset($_REQUEST['ip'])) ? $_REQUEST['ip'] : NULL;
			$hostname = (isset($_REQUEST['hostname'])) ? htmlentities(addslashes($_REQUEST['hostname'])) : NULL;
			$status = (isset($_REQUEST['status'])) ? intval($_REQUEST['status']) : NULL;
			
			$ip_validation = ip2long($ip);
			if($ip_validation == -1 || $ip_validation === FALSE)
			{
				echo 'Invalid IP<br /><br />';
			}
			else 
			{
				$mysql_driver->query('INSERT INTO clients VALUES ("", "' . $ip . '", "' . $hostname . '", "' . $status . '", "0", "0")');
				
				echo 'Client ' . $ip . ' added successfully. <br /><br />';
			}
			
			echo '<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back</a>';
		}
	break;
	case 'edit':
		echo '<h3>Edit client ' . $option . '</h3>';
		

		if(!isset($_REQUEST['submit']))
		{
			$mysql_driver->query("SELECT ip, hostname, status FROM clients WHERE id='" . intval($option) . "' LIMIT 1");
			
			if($mysql_driver->getRows() === 1)
			{
				$row = $mysql_driver->fetch();
				$data = $row[0];
				
				echo '
									<form method="post" action="/clients/edit/">
										<p>
											<div style="width:200px;text-weight:bold">IP</div> <input autofocus name="ip" type="text" value="' . $data['ip'] . '" />
										</p>
										<p>
											<div style="width:200px;text-weight:bold">Hostname</div> <input name="hostname" type="text" value="' . $data['hostname'] . '" />
										</p>
										<p>
											<div style="width:200px;text-weight:bold">Status </div>
											<select name="status">
												<option value="1"' . (($data['status'] == 1) ? ' selected="selected"' : '') .'>Enabled</option>
												<option value="0"' . (($data['status'] == 0) ? ' selected="selected"' : '') .'>Disabled</option>
											</select>
										</p>
										<p>
										<input name="id" type="hidden" value="' . $option .'" />
											<input name="submit" type="submit" value="Save" /> 
											<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back</a>
										</p>
									</form> 
									';
			}
		}
		else
		{
			$id = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : NULL;
			$ip = (isset($_REQUEST['ip'])) ? $_REQUEST['ip'] : NULL;
			$hostname = (isset($_REQUEST['hostname'])) ? htmlentities(addslashes($_REQUEST['hostname'])) : NULL;
			$status = (isset($_REQUEST['status'])) ? intval($_REQUEST['status']) : NULL;
				
			$ip_validation = ip2long($ip);
			if($ip_validation == -1 || $ip_validation === FALSE)
			{
				echo 'Invalid IP<br /><br />';
			}
			else
			{
				$mysql_driver->query("UPDATE clients SET ip='" . $ip . "', hostname='" . $hostname . "', status='" . $status . "' WHERE id='" . intval($id) . "'");
		
				echo 'Client ' . $ip . ' edited successfully. <br /><br />';
			}
				
			echo '<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back</a>';
		}
	break;
	case 'delete':
	    $client_id = $option;
	    
		echo '<h3>Delete client ' . $client_id . '</h3>';
		
		try 
		{
		    // First delete widgets associated
		    ClientCore::deleteWidgetsAssociated($client_id);
		    // Second: delete the client (don't change order)
		    ClientCore::delete($client_id);
		    
		    echo 'Client ' . $client_id . ' deleted successfully. <br /><br />';
		}
		catch (ClientNoExist $e)
		{
		    echo 'Client ' . $client_id . ' does not exist. <br /><br />';
		}
		
		echo '<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back</a>';
	break;
	case 'status':
	    $client_id = $option;
	    
	    try
	    {
	        ClientCore::changeStatus($client_id);
	        header('Location: /clients/');
	        exit();
	    }
	    catch (ClientNoExist $e)
	    {
	        echo 'Client ' . $client_id . ' does not exist. <br /><br />';
	    }
	break;
	case 'associate':
	    if(!isset($_REQUEST['send']))
	    {
    	    $client = intval($option);
    	    
    	    $mysql_driver->query("SELECT id, ip, hostname, status, last_connection, requests FROM clients WHERE id='" . $client . "'");
    	    
    	    if($mysql_driver->getRows() === 1)
    	    {
    	        $row = $mysql_driver->fetch();
    	        $data = $row[0];
    	        
    	        echo '<h3>Associate a widget to client '. $data['hostname']  .'</h3>' .
    	        '<br />
    	        <form method="post" action="/clients/associate/">
    	        	<input type="hidden" name="client_id" value="' . $data['id'] . '" />';
    	        
    	        $data = WidgetCore::getList();
    	         
    	        if(!empty($data))
    	        {
    	            echo 'Widget: <select name="widget_id" />';
    	            
    	        	foreach($data as $widget)
    	        	{
    	        	    echo '<option value="' . $widget['id'] . '">' . $widget['name'] . '</option>';
    	        	}
    	        	
    	            echo '</select> <br />';
    	        }
    	        else
    	        {
    	            echo 'No widgets configured. Please add some <a href="/widgets/">widgets</a><br />';
    	        }
    	        
    	        echo '<input type="submit" name="send" value="Associate" />
    	        </form> <br />';
    	    }
    	    else 
    	    {
    	        echo 'The client requested does not exist <br />';
    	    }
	    }
	    else 
	    {
	        $client_id = (isset($_REQUEST['client_id'])) ? intval($_REQUEST['client_id']) : NULL;
	        $widget_id = (isset($_REQUEST['widget_id'])) ? intval($_REQUEST['widget_id']) : NULL;

	        if(!WidgetCore::exists($widget_id))
	        {
	            echo 'Error: Widget identifier does not exist.<br />';
	        }
	        elseif(!ClientCore::exists($client_id))
	        {
	            echo 'Error: Client identifier does not exist.<br />';
	        }
	        elseif(ClientCore::isWidgetsAssociated($client_id, $widget_id))
	        {
	            echo 'Error: Widget already associated to client.<br />';
	        }
	        else
	        {
	            $mysql_driver = new MySqlDriver();
	            $mysql_driver->query('INSERT INTO client_widgets VALUES(' . $client_id .', ' . $widget_id .', \'\')');
	            
	            echo 'Widget associated successfully <br />';
	        }
	    }
	    
	    echo '<br />' .
	         '<a class="medium yellow awesome" style="margin:3px" href="widgets/client/' . $client_id .'/">Back to client widgets</a>' .
	         '<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back to clients</a>' .
	    	 '<a class="medium yellow awesome" style="margin:3px" href="/widgets/">Back to widgets</a>';
	break;
	default:

	echo '
	<a class="medium red awesome" style="margin:3px" href="/clients/add/">Add client</a>
	
	<h3>List of FreeStation clients</h3>
	
	';

	$result = $mysql_driver->query("SELECT id, ip, hostname, status, last_connection, requests FROM clients");
	
	//check that at least one row was returned
	if($mysql_driver->getRows() > 0)
	{
		$freestations = $mysql_driver->fetch();
		
		echo '<link type="text/css" href="/css/tabpane.css" rel="stylesheet" media="all" />
		
		<table id="rounded-corner" summary="List of FreeStation servers and clients">
			<thead>
			<tr>
				<th scope="col" class="rounded-header-left">Hostname</th>
				<th scope="col">IP address</th>
				<th scope="col">Last connection</th>
				<th scope="col">Requests</th>
				<th scope="col">Status</th>
				<th scope="col" class="rounded-header-right">Actions</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
			<td colspan="5" class="rounded-foot-left"><em>List of FreeStation clients</em></td>
			<td class="rounded-foot-right">&nbsp;</td>
			</tr>
			</tfoot>
			<tbody>
			';
			
			foreach($freestations as $freestation)
			{
				$last_connection_time = $freestation['last_connection'];
				if ($last_connection_time == '0')
				{
					$last_connection_time = 'Never';
				}
				else 
				{
					$last_connection_time = date('H:i:s Y-m-d', $last_connection_time);
				}
				echo '<tr>
				        <td>' . $freestation['hostname'] . '</td>
				        <td>' . $freestation['ip'] . '</td>
				        <td>' . $last_connection_time . '</td>
				        <td>' . $freestation['requests'] . '</td>
				        <td><a href="/clients/status/'. $freestation['id'] .'/"><img title="Status" alt="Status" style="border:none;height:20px" 
				        src="/img/' . (($freestation['status'] == '1') ? 'enabled' : 'disabled') . '.png" /></a></td>
				     	<td><a href="/clients/edit/'. $freestation['id'] .'/" title="Edit client '. $freestation['id'] .'"><img 
				     	src="/img/edit.png" style="border:none;height:16px" title="Edit client '. $freestation['id'] .'" alt="Edit client '. $freestation['id'] .'" /></a> 
				     	<a href="/widgets/client/'. $freestation['id'] .'/" 
				     	title="Widget client '. $freestation['id'] .'"><img 
				     	src="/img/widgets.png" style="border:none" 
				     	title="Widget client '. $freestation['id'] .'" 
				     	alt="Widget client '. $freestation['id'] .'" /></a>
				     	<a href="/clients/delete/'. $freestation['id'] .'/" title="Delete client '. $freestation['id'] .'"><img 
				     	src="/img/remove.png" style="border:none;height:16px" title="Delete client '. $freestation['id'] .'" alt="Delete client '. $freestation['id'] .'" /></a> 
				     	</td>
				   </tr>';
			}
			
			echo '
			</tbody>
			</table>';
	}
	else 
	{
		echo 'No clients still configured.';
	}
	
	break;
}

$CMS->closePage();