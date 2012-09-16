<?php
/**
 * server file.
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
$CMS->openPage('Servers');

if(!isset($_SESSION['user_id']))
{
	header('Location: /login/');
	exit();
}

$widget_server = new WidgetServer();
$widget_server->render();

$CMS->closePage();