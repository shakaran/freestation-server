<?php
/**
*    Freestation, plataform for software distribution
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
* ClassHash class file.
*
* Load dinamically invoked classes predefined.
*
* @copyright 	2011, (c) Ángel Guzmán Maeso
* @license 		AGPLv3 http://www.gnu.org/licenses/agpl-3.0.en.html
* @author  		Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 		1.0
* @package 		Lib
*/
class ClassHash extends Singleton
{
	/**
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @var array $class_hash Hash with all the instances
	 * @access private
	 */
	private $class_hash = array();

	/**
	 * Instance a singleton instance.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return ClassHash singleton instance
	 */
	public static function getInstance()
	{
		return parent::getInstance(__CLASS__);
	}

	/**
	 * Creates a hash with all the classes indexed.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	public function __construct()
	{
		$this->class_hash['Session']                 = 'lib/Session.php';
		$this->class_hash['DB']                      = 'inc/connection.php';
		$this->class_hash['CMS']                     = 'lib/CMS.php';
		$this->class_hash['WidgetVerticalMenu']      = 'lib/widgets/WidgetVerticalMenu.php';
		$this->class_hash['WidgetServer']            = 'lib/widgets/WidgetServer.php';
		$this->class_hash['Config']                  = 'lib/Config.php';
		$this->class_hash['MySqlDriver']             = 'lib/MySqlDriver.php';
		$this->class_hash['Sanitizer']               = 'lib/Sanitizer.php';
		$this->class_hash['ServerCore']              = 'lib/ServerCore.php';
		$this->class_hash['WidgetCore']              = 'lib/WidgetCore.php';
		$this->class_hash['ClientCore']              = 'lib/ClientCore.php';
	}

	/**
	 * Get a vale from the class hash index.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @param string $class_name
	 * @return string the class path for a class name
	 * @throws ClassNotExist if the class not exist on the class hash
	 */
	public function get($class_name)
	{
		if(!isset($this->class_hash[$class_name]))
		throw new ClassNotExist('The class '. $class_name . ' could not be found on the class hash.');

		return $this->class_hash[$class_name];
	}
}

/**
 * ClassNotExist
 *
 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 */
class ClassNotExist extends Exception {}