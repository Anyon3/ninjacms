<?php
if(!isset($nojs))
    require('funcwm.php');

if($is_connected) {
header('location:https://forum.wawa-mania.ec/');
exit;
}

?>

<div class=loginForm>
	<h1>Nouveau compte</h1>
	<form method="post" action="/register" id=formReg>
	<i class="inside fa fa-user"></i>
	<input id=accreg type="text" class="loginu" placeholder="Pseudo désiré" name="username" data-title="Votre nom d'utilisateur" maxlength="25" />
	<i class="inside fa fa-key"></i>
	<input id=pwreg type="password" class="loginp nob" placeholder="Mot de passe" name="Mot de passe" data-title="Mot de passe" />
	<i class="answericn fa fa-lock"></i>
	<input id=answercap type="text" name="answercap" placeholder="Réponse à la question" data-title="Réponse" />
	<input id=creg type="submit" value="Envoyer" />
	</form>
	<br>
</div>