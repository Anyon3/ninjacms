<?php
if($is_connected) {
    header('location: https://forum.wawa-mania.ec/');
    exit;
}

//S
if($nojs) {

    $random = captcha($mysqli);

    $hm = count($random[0]) - 1;

    $rdmo = mt_rand(0, $hm);

    //[1] ID
    $_SESSION['checkcap'] = $random[0][$rdmo][5];
}

?>
<div class=loginForm>
	<h1>Connexion</h1>
	<h4>Si vous rencontrez un problème avec l'utilisation du site, pensez à <a href="http://fr.wikihow.com/effacer-le-cache-de-votre-navigateur" title="Supprimez votre cache navigateur" target=_blank>supprimer le cache de votre navigateur</a></h4>
	<form method="post" action="/login" id=formCon>
	<i class="inside fa fa-user"></i>
	<input type="text" class="loginu" placeholder="Votre nom d'utilisateur" name="username" data-holder="Votre nom d'utilisateur" maxlength="25" />
	<i class="inside fa fa-key"></i>
	<input type="password" class="loginp nob" placeholder="Votre mot de passe" name="password" data-holder="Votre mot de passe" />
	<?php
	   if($nojs) {

	       //Fail login
	       if($error !== false)
	           echo '<div id=errorCon><i class="fa fa-exclamation"></i> '.$error.'</div>';

	       //Display the question captcha
	       echo $random[0][$rdmo][3];
	   }
	?>

	<input id=answercap type="text" name="answercap" placeholder="Réponse à la question" data-title="Réponse" />
	<input id=clog type="submit" value="Connexion" />
	</form>
	<i class="iclog fa fa-envelope">
		<a class=lost href="https://forum.wawa-mania.ec/lost" title="Mot de passe perdu">Mot de passe perdu</a>
	</i>
	<i class="iclog fa fa-user-plus">
		<a class=register href="https://forum.wawa-mania.ec/register" title="S'inscrire">Inscription</a>
	</i>
</div>