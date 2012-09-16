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
* WidgetMenuVertical class file.
* 
* Create the vertical menu box.
* 
* @copyright 	2011, (c) Ángel Guzmán Maeso
* @license 		AGPLv3 http://www.gnu.org/licenses/agpl-3.0.en.html
* @author  		Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 		1.0
* @package 		Lib/Widgets
*/
class WidgetVerticalMenu
{
	private $items = array();

	/**
	 * Build a WidgetVerticalMenu object.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	public function __construct()
	{
		$this->items[] = array('Home', '/', '/img/home.png');
		$this->items[] = array('Server', '/server/', '/img/server.png');
		$this->items[] = array('Clients', '/clients/', '/img/client.png');
		$this->items[] = array('Widgets', '/widgets/', '/img/widgets.png');
	}

	/**
	 * Create a item for the vertical menu.
	 * 
	 * @param array[string][string $item The name and the link as array 
	 * @param boolean $first By reference, set if it is the first menu
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return string The item as string
	 */
	private function createItem($item, &$first, $sub_item = NULL)
	{
	    list($name, $link, $image) = $item;
	    
	    $result = '<li>' . PHP_EOL .
	        	   '    <a href="' . $link . '"'.($first ? ' class="menu_selected"' : '').'>' . PHP_EOL;
	    
	    if(!empty($image))
	    {
	    	$result .= '<img style="height:16px;" src="' . $image . '" title="' . $name . '" alt="' . $name . '"/>';
	    }
	    
	    $result .= '        ' . $name . PHP_EOL .
	        	   '    </a>' . PHP_EOL . $sub_item .
	        	   '</li>' . PHP_EOL; 
	    
	    $first = FALSE;
	    
	    return $result;
	}
	
	/**
	* Create a subitem for the vertical menu.
	*
	* @param array[string][string $item The name and the link as array
	* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	* @return string The item as string
	*/
	private function createSubItem($item)
	{
		list($name, $link) = $item;
		 
		$result = '<li>' . PHP_EOL .
		        	   '    <a href="' . $link . '">' . PHP_EOL .
		        	   '        ' . $name . PHP_EOL .
		        	   '    </a>' . PHP_EOL .
		        	   '</li>' . PHP_EOL;
		 
		return $result;
	}
	
	/**
	 * Create the items for the vertical menu.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return string The items data created as string
	 */
	private function createMenu()
	{
		$result = NULL;
		$first = TRUE;

		foreach($this->items as $item)
		{
		    if(!is_array($item[0]))
		    {
		        $result .= $this->createItem($item, $first);
		    }
		    else 
		    {
		        list($subitems, $main_item) = $item;
		        
		        $subitem_text = '<ul class="sub-level">';
		        
		        foreach($subitems as $subitem)
		        {
		            $subitem_text .= $this->createSubItem($subitem);
		        }
		        
		        $subitem_text .= '</ul>';
		        
		        $result .= $this->createItem($main_item, $first, $subitem_text);
		    }
		}

		return $result;
	}
	
	/**
	 * Render the widget.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return string The widget data rendered as string
	 */
	public function render()
	{
		echo '<div id="menu">' . PHP_EOL .
			 '    <div class="menu_content">' . PHP_EOL .
			 '	      <div class="sub_menu">' . PHP_EOL .
			 '		      <ul>' . PHP_EOL .
			 '			      ' . $this->createMenu() .
			 '		      </ul>' . PHP_EOL .
			 '        </div>' . PHP_EOL .
			 '    </div>' . PHP_EOL .
		     '</div>';
	}
}