<?
/**
* Logout file.
*
* LICENSE: .
*
* @copyright 2011, (c) Ángel Guzmán Maeso.
* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 1.0
*/

require_once('lib/session.php');
if(session_is_registered('user_id'))
{
    $_SESSION = array();
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
}
#the session variable isn't registered, the user shouldn't even be on this page
else header('Location: /');