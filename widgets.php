<?php
/**
 * widgets file.
 *
 * LICENSE: .
 *
 * @copyright 2011, (c) Ángel Guzmán Maeso.
 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @version 1.0
 */

require_once 'lib/Loader.php';
require_once 'lib/session.php';

$CMS = new CMS();
$CMS->openPage('Inicio');

if(!isset($_SESSION['user_id']))
{
	header('Location: /login/');
	exit();
}

$mysql_driver = new MySqlDriver();

$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : NULL;
$widget = (isset($_REQUEST['widget'])) ? $_REQUEST['widget'] : NULL;

$client_widgets = array(
	array('name' => 'logoarea',        'description' => 'Render a image as logo'),
	array('name' => 'titledisplay',    'description' => 'Print a text as title'),
);

switch($action)
{
	case 'remove':
		echo '<h3>Remove widget</h3>';
		
		$widget = (isset($_REQUEST['widget'])) ? $_REQUEST['widget'] : NULL;
		
		if($widget === 'client')
		{
		    $widget_name = (isset($_REQUEST['op'])) ? $_REQUEST['op'] : NULL;
		    $client_id = (isset($_REQUEST['sop'])) ? $_REQUEST['sop'] : NULL;
		    
		    $widget_core = new WidgetCore();
		    $widget_id = $widget_core::getFromName($widget_name);
		    
		    if(empty($widget_id))
		    {
		        echo 'Error: no widget name found.';
		    }
		    elseif(!ClientCore::exists($client_id))
	        {
	            echo 'Error: Client identifier does not exist.<br />';
	        }
	        else
	        {
	            $mysql_driver = new MySqlDriver();
	            $mysql_driver->query("DELETE FROM client_widgets WHERE client_id='" . $client_id . "' AND widget_id='" . $widget_id . "'");
	            
	            echo 'Removing widget ' . $widget_name. ' from client ' . $client_id . '<br />';
	        }
		}
		
		echo '<a class="medium yellow awesome" style="margin:3px" href="/widgets/client/' . $client_id .'/">Back widgets client</a>';
		echo '<a class="medium yellow awesome" style="margin:3px" href="/widgets/">Back</a>';
	break;
	case 'add':
		echo '<h3>Add widget</h3>';
		
		echo 'Adding widget' . $widget. '<br />';
	break;
	case 'save':
	    if(isset($_REQUEST['submit']))
	    {
	        $data = (isset($_REQUEST['data'])) ? $_REQUEST['data'] : NULL;
	        $width = (isset($_REQUEST['width'])) ? $_REQUEST['width'] : NULL;
	        $height = (isset($_REQUEST['height'])) ? $_REQUEST['height'] : NULL;
	        $client_id = (isset($_REQUEST['client_id'])) ? $_REQUEST['client_id'] : NULL;
	        $widget_id = (isset($_REQUEST['widget_id'])) ? $_REQUEST['widget_id'] : NULL;
	        $widget_name = (isset($_REQUEST['widget_name'])) ? $_REQUEST['widget_name'] : NULL;
	        
	        switch ($widget_name)
	        {
	            case 'title_display':
	                if(!WidgetCore::exists($widget_id))
	                {
	                    echo 'Error: Widget identifier does not exist.<br />';
	                }
	                elseif(!ClientCore::exists($client_id))
	                {
	                    echo 'Error: Client identifier does not exist.<br />';
	                }
	                elseif(!ClientCore::isWidgetsAssociated($client_id, $widget_id))
	                {
	                    echo 'Error: Widget no associated to client.<br />';
	                }
	                else
	                {
	                    
	                    $widget_data = array('properties' => array(
	                                                                'spacing' => 1,
	                                                                'homogeneus' => 5,
	                    											'width' => $width,
	                    											'height' => $height,
	                    											'data' => $data,
	                    										 )
	                    					);
	                    
	                    $mysql_driver = new MySqlDriver();
        	            $mysql_driver->query("UPDATE client_widgets SET widget_data='" . serialize($widget_data) . "' WHERE client_id='" . $client_id . "' AND widget_id='" . $widget_id ."'");
        	            
        	            echo 'Widget ' . $widget_name . ' saved successfully on client ' . $client_id .'<br />';
	                }
	                
	                echo '<a class="medium yellow awesome" style="margin:3px" href="/widgets/client/' . $client_id .'/">Back widgets client</a>';
	                echo '<a class="medium yellow awesome" style="margin:3px" href="/widgets/">Back</a>';
	            break;
	        }
	        // data] => UCLM - FreeStation [width] => 300 [height] => 200 [client_id] => 7 [widget_id] => 3 [widget_name] => title_display
	    }
	break;
	case 'configure':
		echo '<h3>Configure ' . ucfirst($widget) . ' widget</h3>';
		
		switch($widget)
		{
			case 'browser':
				echo '
				<form>
					Title: 
					<input type="text" size="50" value="">
					<br />
					Web: <input type="text" size="50" value="http://">
					<br /><br />
					<a class="medium red awesome" style="margin:3px" href="/widgets/save/' . $widget . '">Save</a>
					<a class="medium yellow awesome" style="margin:3px" href="/widgets/">Back</a>
				</form>';
				
			break;
			case 'logo_area':
			    echo '
							<form>
								Source image: 
								<input type="text" size="50" value="">
								<br />
								Width: <input type="text" size="5" value="300">
								<br />
								Height: <input type="text" size="5" value="200">
								<br /><br />
								<a class="medium red awesome" style="margin:3px" href="/widgets/save/' . $widget . '">Save</a>
								<a class="medium yellow awesome" style="margin:3px" href="/widgets/">Back</a>
							</form>';
			
			break;
			case 'title_display':
			    $client_id = (isset($_REQUEST['op'])) ? $_REQUEST['op'] : NULL;
			    
			    $widget_name = 'title_display';
			    $widget_core = new WidgetCore();
			    $widget_id = $widget_core::getFromName($widget_name);
			    
			    if(empty($widget_id))
			    {
			        echo 'Error: no widget name found.';
			    }
			    elseif(!ClientCore::exists($client_id))
			    {
			        echo 'Error: Client identifier does not exist.<br />';
			    }
			    else 
			    {
			        echo '
						<form method="post" action="/widgets/save/">
						Title: 
											<input type="text" name="data" size="50" value="">
											<br />
											Width: <input type="text" name="width" size="5" value="300">
											<br />
											Height: <input type="text" name="height" size="5" value="200">
											<br /><br />
											<input type="hidden" name="client_id" value="' . $client_id. '" />
											<input type="hidden" name="widget_id" value="' . $widget_id. '" />
											<input type="hidden" name="widget_name" value="' . $widget_name. '" />
								<p>
                					<input name="submit" type="submit" value="Save" /> 
                					<a class="medium red awesome" style="margin:3px" href="/clients/">Back</a>
                				</p>
						</form>';
			    }
			break;
			default:
				echo 'Error: widget "' . $widget . ' could not found<br />';
			break;
		}
	break;
	case 'client':
		
		$mysql_driver->query("SELECT id, ip, hostname, status, last_connection, requests FROM clients WHERE id='" . intval($widget) . "'");
		
		if($mysql_driver->getRows() === 1)
		{
			$row = $mysql_driver->fetch();
			$data = $row[0];
			
			$last_connection_time = $data['last_connection'];
			if ($last_connection_time == '0')
			{
				$last_connection_time = 'Never';
			}
			else
			{
				$last_connection_time = date('H:i:s Y-m-d', $last_connection_time);
			}
			
			$client_id = $data['id'];
			echo '
				  
				<h3>Widget list deployment for client '. $data['hostname']  .' (' . $data['ip'] . ')</h3>
				
				<a class="medium red awesome" style="margin:3px" href="/clients/associate/' . $data['id'] . '/">Associate widget</a>
				
				<br />
				<br />
				Status: <img title="Status" alt="Status" style="border:none;height:20px" 
				        src="/img/' . (($data['status'] == '1') ? 'enabled' : 'disabled') . '.png" /><br />
				Last connection time: ' . $last_connection_time .'<br />
				Requests: ' . $data['requests'] . '<br />
				Base widget path for client : /clients/' . $client_id . '/widgets/<br />
				<br />
							
				';
			
			$client_widgets = ClientCore::getWidgetsAssociated($client_id);
			
			$total_widgets = count($client_widgets);
				
			echo 'Total widgets loaded: ' . $total_widgets . '<br />';
			
			foreach($client_widgets as $client_widget)
			{
			    $data = WidgetCore::get($client_widget['widget_id']);
			    
			    $widget = $data[0];
			    
			    if(!empty($widget))
			    {
				    WidgetCore::render($widget['name'], $widget['description'], $client_id);
			    }
			    else 
			    {
			        echo 'Error: widget ' . $client_widget['widget_id'] . ' not found.';
			    }
			}
		}
		else
		{
			echo 'Client ' . $option . ' does not exist. <br /><br />';
			
			echo '<a class="medium yellow awesome" style="margin:3px" href="/clients/">Back</a>';
		}
	break;
	default:
	
		// Pick & build<br/>Configure<br/>Add new
		echo '
				<h3>Widget management</h3>
				
				<a class="medium red awesome" style="margin:3px" href="/widgets/add/">Add widget</a>
				<br /><br />
				Available widgets created on ' . $_SERVER['SERVER_ADDR'] . '<br />
				Loaded widgets from path: /widgets/<br />
				<br />
				<br />
				';
	
	    $data = WidgetCore::getList();
    	
    	if(!empty($data))
    	{
    	    $total_widgets = count($data);
    	    
    	    echo 'Total widgets loaded: ' . $total_widgets . '<br />';
    	    
    	    foreach ($data as $widget)
    	    {
    	        WidgetCore::render($widget['name'], $widget['description']);
    	    }
    	}
    	else
    	{
    	    echo 'No widgets configured.<br />';
    	}
    	
	break;
}

$CMS->closePage();