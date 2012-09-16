<?
/**
* session file.
*
* LICENSE: .
*
* @copyright 2011, (c) Ángel Guzmán Maeso.
* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 1.0
*/
ini_set('session_save_path', '/home/freestat/tmp');
session_name('freestation');
if(@session_start() == False){session_destroy();session_start();}