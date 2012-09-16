<?php
/**
 * MySqlDriver class file.
 *
 * @copyright 2011, (c) Ángel Guzmán Maeso
 * @author  	 Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @package 	 Lib
 */
class MySqlDriver {
	private $link     = NULL;
	private $database = NULL;
	private $result   = NULL;
	private $numRows  = NULL;
	private $rows     = NULL;

	public function __construct() {
		$this->link =  mysql_connect(Config::MYSQL_SERVER, Config::MYSQL_USER, Config::MYSQL_PASS)
		or die('Could not open connection to server');
	}

	public function setDatabase($database = Config::DEFAULT_DATABASE) {
		$this->database = $database;
		mysql_select_db($database, $this->link)
		or die('Could not select database '. $database);
	}

	public function query($query) {
		if($this->database === NULL) {
			$this->setDatabase();
		}

		$this->result = mysql_query($query, $this->link)
		or die('Error: ' . mysql_error() . '<br/>'. $query);
        
		//mysql_query('COMMIT', $this->link) or die('Error: autocommit ' . mysql_error() . '<br/>'. $query);
		
		return $this->result;
	}

	public function getLink() {
		return $this->link;
	}

	public function getRows() {
		$this->numRows = mysql_num_rows($this->result);
		return $this->numRows;
	}

	public function fetch() {
		if(!isset($this->numRows)) {
			$this->getRows();
		}

		if ($this->numRows === 0) {
			$this->rows = NULL;
		} else if ($this->numRows === 1) {
			$this->rows[] = mysql_fetch_array($this->result, MYSQL_ASSOC);
		} else {
			while($row = mysql_fetch_array($this->result, MYSQL_ASSOC)) {
				$this->rows[] = $row;
			}
		}

		return $this->rows;
	}
	
	public function close()
	{
	    if(isset($this->link))
	    {
	        mysql_close($this->link);
	    }
	}
}