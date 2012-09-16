<?php
/**
 * Check login file.
 *
 * LICENSE: .
 *
 * @copyright 2011, (c) Ángel Guzmán Maeso>.
 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @version 1.0
 */

require_once 'lib/Loader.php';

$username = (isset($_REQUEST['username'])) ? $_REQUEST['username'] : NULL;
$password = (isset($_REQUEST['password'])) ? $_REQUEST['password'] : NULL;

if(empty($username) || empty($password))
{
    header('Location: clients/?e=2');
    exit;
}
else
{
    $username = htmlentities(addslashes($username));
    $password = sha1(htmlentities(addslashes($password)));

    $mysql_driver = new MySqlDriver();
    
    $result = $mysql_driver->query("SELECT id, user, password, email FROM users WHERE user='$username' LIMIT 1");

    //check that at least one row was returned
    if($mysql_driver->getRows() > 0)
    {
    	$data = $mysql_driver->fetch();
    	
    	$row = $data[0];
    	
    	if($row['password'] !== $password)
		{
			header('Location: clients/?e=3');
			exit;
		}
		else
		{
			//start the session and register a variable
			ini_set('session_save_path', '/home/freestat/tmp');
			session_name('freestation');

			if(@session_start() == False){session_destroy();session_start();}
			$_SESSION['user_id']       = $row['id'];
			$_SESSION['user_name']     = $row['user'];
			$_SESSION['user_password'] = $row['password'];
			
			//we will redirect the user to another page where we will make sure they're logged in
			$go='/server/';
			header('Location: '.$go);
			exit;
		}
    }
    else
    {
    	header('Location: clients/?e=4');
    	exit;
    }
}