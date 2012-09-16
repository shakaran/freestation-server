<?php 
/**
* Header file.
*
* LICENSE: .
*
* @copyright 2011, (c) Ángel Guzmán Maeso.
* @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
* @version 1.0
*/
?>
<body>
	<div id="header">
		<div style="float: left; height: 70px">
			<img src="/img/fs-logo.png" style="padding:10px;float:left;border:none;width:50px;height:50px">
			<div
				style="float:left;font-size: 80px;">FreeStation</div>
		</div>
		<?php 
		if(isset($_SESSION['user_id']))
		{
			echo '<div style="float:right;margin:15px">Welcome, ' . $_SESSION['user_name'] . ' <a href="/logout/" title="Salir">Sign out</a></div>';
		}
		?>
		<br style="clear:both" />
	</div>