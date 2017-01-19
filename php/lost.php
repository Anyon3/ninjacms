<?php
if($is_connected) {
header('location:https://forum.wawa-mania.ec/');
exit;
}
?>

<div class=loginForm>
	<h1>Mot de passe oublié</h1>
	<h4>Problème de réception corrigé, enjoy.</h4>
	<form method="post" action="/lost" id=formLos>
	<i class="inside fa fa-user"></i>
	<input id=accrec type="text" class="loginu" placeholder="Votre nom d'utilisateur" name="username" data-holder="Votre nom d'utilisateur" maxlength="25" />
	<i class="inside fa fa-key"></i>
	<input id=mailrec type="text" class="loginp nob" placeholder="Votre email" name="email" data-holder="Votre email" />
	<?php if($error !== false && isset($error)) echo "<p id=errorCon><i class=\"fa fa-exclamation\"></i> $error</p>"; ?>
	<input id=crec type="submit" value="Envoyer" />
	</form>
</div>