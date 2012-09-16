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
* ClientCore class file.
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
class ClientCore
{
    /**
    * Build the ClientCore object.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @return  void
    */
    public function __construct()
    {
    
    }
    
    /**
    * Get a client from a given client identifier on server.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @param integer The client identifier
    * @return array List of client data
    */
    public static function get($client_id)
    {
        $data = array();
    
        $mysql_driver = new MySqlDriver();
        $mysql_driver->query("SELECT id, ip, hostname, status, last_connection, requests FROM clients WHERE id='" . $client_id . "'");
    
        if($mysql_driver->getRows() === 1)
        {
            $data = $mysql_driver->fetch();
        }
    
        return $data;
    }
    
    /**
     * Check if a client identifier exists on server.
     *
     * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
     *
     * @param integer The client identifier
     * @return boolean TRUE if the client exist
     */
    public static function exists($client_id)
    {
        $client = self::get($client_id);
    
        return !empty($client);
    }
    
    /**
     * Get the list of widgets availables for a given client.
     * 
     * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
     * 
     * @return array List of widgets
     */
    public static function getWidgetsAssociated($client_id = NULL)
    {
        $widget_list = array();
        
        $mysql_driver = new MySqlDriver();
        $mysql_driver->query("SELECT client_id, widget_id, widget_data FROM client_widgets WHERE client_id='" . $client_id . "'");
         
        if($mysql_driver->getRows() > 0)
        {
            $widget_list = $mysql_driver->fetch();
        }

        return $widget_list;
    }
    
    /**
    * Check if a widgets is associated for a given client.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @return boolean TRUE if the widget is associated
    */
    public static function isWidgetsAssociated($client_id = NULL, $widget_id = NULL)
    {
        $widget_list = array();
    
        $mysql_driver = new MySqlDriver();
        $mysql_driver->query("SELECT client_id, widget_id, widget_data FROM client_widgets WHERE client_id='" . $client_id . "' AND widget_id='" . $widget_id . "'");
         
        return $mysql_driver->getRows() > 0;
    }
    
    /**
    * Delete the list of widgets availables for a given client.
    *
    * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
    *
    * @return void
    */
    public static function deleteWidgetsAssociated($client_id = NULL)
    {
        $mysql_driver = new MySqlDriver();
        
        $mysql_driver->query("SELECT id FROM clients WHERE id='" . intval($client_id) . "'");
        
        if($mysql_driver->getRows() === 1)
        {
            $mysql_driver->query("DELETE FROM client_widgets WHERE client_id='" . intval($client_id) . "'");
        }
        else
        {
            throw ClientNoExist();
        }
    }
    
    /**
     * Delete a given client identifier from client pool
     * 
     * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
     * 
     * @param integer $client_id The client identifier.
     * 
     * @throws ClientNoExist if the client to delete does not exist.
     */
    public static function delete($client_id = NULL)
    {
        $mysql_driver = new MySqlDriver();
        $mysql_driver->query("SELECT id FROM clients WHERE id='" . intval($client_id) . "'");
        
        if($mysql_driver->getRows() === 1)
        {
            $mysql_driver->query("DELETE FROM clients WHERE id='" . intval($client_id) . "'");
            
            echo 'Client ' . $option . ' deleted successfully. <br /><br />';
        }
        else
        {
            throw ClientNoExist();
        }
    }
    
    /**
     * Change the status for a given client identifier from client pool
     * 
     * @author  Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
     * 
     * @param integer $client_id The client identifier.
     * 
     * @throws ClientNoExist if the client to change does not exist.
     */
    public static function changeStatus($client_id = NULL)
    {
        /*
        Used on a future, when there are more possible status?
        $mysql_driver->query("SELECT status FROM clients WHERE id='" . intval($client_id) . "'");
        
        if($mysql_driver->getRows() === 1)
        {
        $data = $mysql_driver->fetch();
        echo var_dump($data);
        }*/
        
        // Fast way to change bit status
        $mysql_driver = new MySqlDriver();
        
        $mysql_driver->query("SELECT id FROM clients WHERE id='" . intval($client_id) . "'");
        
        if($mysql_driver->getRows() === 1)
        {
            $mysql_driver->query("UPDATE clients SET status = !status WHERE id='" . intval($client_id) . "'");
        }
        else
        {
            throw ClientNoExist();
        }
    }
}

class ClientNoExist extends Exception { }