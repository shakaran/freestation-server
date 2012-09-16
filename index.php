<?php
/**
 * Index file.
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
$CMS->openPage('Home');

echo 'Welcome! This a FreeStation server. You can manage the server, add clients, and configure widgets for the clients.';

$CMS->closePage();
