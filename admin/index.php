<?php
require(__DIR__.'/../php/funcwm.php');

//Setup $staff on connected
$staff = staff($uinfos['us_show_badge'], $mysqli);

//Exit if staff check fail
if(!$staff) {
    file_put_contents(__DIR__.'/../cache/admten.txt', $uinfos['us_id'], FILE_APPEND | LOCK_EX);
	setcookie('login', '', 1, '/', '', 1, 1);
	header('LOCATION: https://forum.wawa-mania.ec');
	exit;
}

//head
echo '<link rel="stylesheet" href="style.css" type="text/css">
	 <link rel="stylesheet" href="../css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="../css/font-awesome.min.css">
	 <script type="text/javascript" src="../js/jquery-2.2.1.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.min.js"></script>';

//Body
echo '<body>
		<div id=admctn>

		<ul id=admmenu>
			<li id=admrpt><i class="fa fa-exclamation-circle"></i> Signalements</li>
			<li id=showact><i class="fa fa-list-alt"></i> Action admins</li>
			<li id=srcus><i class="fa fa-search"></i> Recherche utilisateur</li>
            <li id=srctool><i class="fa fa-search"></i> Cleantools</li>
		</ul>

		<p id=admback>
		Nouveau mdp pour le chan du staff : coucouhellohi321
		</p>

		</div>
		<script type="text/javascript" src="admin.js"></script>
		</body>
	 ';
?>
