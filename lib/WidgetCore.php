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
* WidgetCore class file.
* 
* It handles the widget common functions.
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
class WidgetCore
{
    /**
    * Build the WidgetCore object.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @return  void
    */
    public function __construct()
    {
    
    }
    
    /**
     * Get the list of widgets availables on server.
     * 
     * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
     * 
     * @return array List of widgets
     */
    public static function getList()
    {
        $widget_list = array();
        
        $mysql_driver = new MySqlDriver();
        $mysql_driver->query("SELECT id, name, description, image FROM widgets");
         
        if($mysql_driver->getRows() > 1)
        {
            $widget_list = $mysql_driver->fetch();
        }

        return $widget_list;
    }
    
    /**
    * Get a widget from a given widget identifier on server.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @param integer The widget identifier
    * @return array A widget with the data associated
    */
    public static function get($widget_id = NULL)
    {
        $data = array();
    
        $mysql_driver = new MySqlDriver();
        
        if(!empty($widget_id))
        {
            $mysql_driver->query("SELECT id, name, description, image FROM widgets WHERE id='" . intval($widget_id) . "'");
    
            if($mysql_driver->getRows() === 1)
            {
                $data = $mysql_driver->fetch();
            }
        }
    
        return $data;
    }
    
    /**
    * Get a widget identfier from a given widget name on server.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @param integer The widget name
    * @return integer A widget identifier
    */
    public static function getFromName($widget_name = NULL)
    {
        $data = NULL;
    
        $mysql_driver = new MySqlDriver();
    
        if(!empty($widget_name))
        {
            $mysql_driver->query("SELECT id FROM widgets WHERE name='" . $widget_name . "'");
    
            if($mysql_driver->getRows() === 1)
            {
                $data = $mysql_driver->fetch();
                $data = $data[0]['id'];
            }
        }
    
        return $data;
    }
    
    /**
    * Check if a widget identifier exists on server.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @param integer The widget identifier
    * @return boolean TRUE if the widget exists
    */
    public static function exists($widget_id)
    {
        $widget = self::get($widget_id);
        
        return !empty($widget);
    }
    
    /**
     * Render a simple widget icon with name and description
     * 
     * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
     * 
     * @param string $widget The name of widget.
     * @param string $description The description of widget.
     * 
     * @return  void
     */
    public static function render($widget, $description, $client_id = NULL)
    {
        echo '<div id="widget_' . $widget . '" class="widget_container">
    			<img border="0" src="/img/widgets/' . $widget . '.png" class="widget_thumb" />
    			<a href="/widgets/configure/' . $widget . '/" 
    			title="Configure ' . strtoupper($widget) . ' widget" 
    			class="widget_title">' . strtoupper(str_replace('_', ' ', $widget)) . '</a>
    			<br />
    			<div class="widget_description">
    				' . $description .'
    			</div>
    			<div class="widget_action" style="display:none;">
    		        Status: <img src="/img/enabled.png" title="Enabled" alt="Enabled" style="border:none;width:20px" />
    		        <br />
    		        <div class="small_title">
    		            Actions:
    		        </div>
    		        <div class="widget_holder_text">
    		            <a href="/widgets/configure/' . $widget . '/' . (!empty($client_id) ? $client_id . '/' : NULL) . '" title="Configure ' . strtoupper($widget) . ' widget">
    		            	<img src="/img/edit.png" style="border:none;height:16px" /> 
    		            </a>
    		            <a href="/widgets/configure/' . $widget . '/' . (!empty($client_id) ? $client_id . '/' : NULL) . '" title="Configure ' . strtoupper($widget) . ' widget">
    		            	Configure
    		            </a>
    		        </div>
    		        <div class="widget_holder_text">
    		            <a href="/widgets/remove/' . (!empty($client_id) ? 'client/' : NULL) . $widget . '/' . (!empty($client_id) ? $client_id . '/' : NULL) . '"
    		            	title="Remove ' . strtoupper($widget) . ' widget">
    		            	<img src="/img/remove.png" style="border:none;height:16px" /> 
    		            </a>
    		            <a href="/widgets/remove/' . (!empty($client_id) ? 'client/' : NULL) . $widget . '/' . (!empty($client_id) ? $client_id . '/' : NULL) . '" 
    		            	title="Remove ' . strtoupper($widget) . ' widget">
    		            	Remove
    		            </a>
    		        </div>
    			</div>
    		</div>';
    }
}