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
* Login file.
* 
* It handles the form to login.
* 
* @copyright 	2011, (c) Ángel Guzmán Maeso
* @license 		AGPLv3 http://www.gnu.org/licenses/agpl-3.0.en.html
* @author  		Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 		1.0
* @category		Frontend
* @package 		Login
* @subpackage	-
* @link         http://freestation.quijost.com
*/
require_once 'lib/Loader.php';

$CMS = new CMS();
$CMS->openPage('Login');

?>
<p>

<h3>Login</h3>

<?
$e=(isset($_REQUEST['e']))?$_REQUEST['e']:NULL;
if(!is_numeric($e)) $e=0;
switch($e)
{
	case '4':
		echo ERR('El usuario no existe.');
		break;
	case '3':
		echo ERR('Contraseña incorrecta.');
		break;
	case '2':
		echo ERR('El usuario o contraseña no ha sido especificada.');
		break;
	case '1':
		echo ERR('La sesión ha expirado');
		break;
}
?>
                <form id="frm_login" method="post" action="check_login.php">
                    <p>
                        <div class="fieldname">Usuario</div>
                        <input type="text" name="username" tabindex="1" size="20" maxlength="25" class="input_user" />
                    </p>
                    <p>

                        <div class="fieldname">Contraseña</div>
                        <input type="password" name="password" tabindex="2" size="20" maxlength="25" class="input_password" />
                    </p>
                    <p>
                        <input class="medium red awesome" type="submit" value="Entrar" tabindex="3" name="login" />
                    </p>
                </form>
            <p/>
<?php 
$CMS->closePage();