<?php

if(!isset($_COOKIE['nojs'])) {
    setcookie('nojs', 'nojs', time()+31556926, '/', '', 1, 1);
    $_COOKIE['nojs'] = 'nojs';
}

require(__DIR__.'/php/funcwm.php');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<?php
if ($_SERVER['REQUEST_URI'] === '/faq')
    echo '<title>Foire aux questions</title>';

elseif ($_SERVER['REQUEST_URI'] === '/donation')
    echo '<title>Donation à Wawa-Mania</title>';

elseif ($_SERVER['REQUEST_URI'] === '/login')
    echo '<title>Connexion au forum</title>';

elseif ($_SERVER['REQUEST_URI'] === '/')
    echo '<title>Wawa-Mania - Téléchargement direct</title>';

elseif ($_SERVER['REQUEST_URI'] === '/search')
    echo '<title>Recherche - Films, Séries, Jeux-vidéo</title>';

elseif ($_SERVER['REQUEST_URI'] === '/lost')
    echo '<title>Access - Mot de passe perdu</title>';

elseif ($_SERVER['REQUEST_URI'] === '/trade')
    echo '<title>Bureau des échanges</title>';

elseif ($_SERVER['REQUEST_URI'] === '/recent')
    echo '<title>Topics les plus récents</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-1')
    echo '<title>Règles & Informations</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-3')
    echo '<title>Avis / Problèmes / bugs</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-4')
    echo '<title>Espace détente</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-5')
    echo '<title>Films DVDrip</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-6')
    echo '<title>Séries Tv</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-7')
    echo '<title>Album Musique</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-8')
    echo '<title>Appz Windows</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-16')
    echo '<title>Jeux PC</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-20')
    echo '<title>Divers</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-22')
    echo '<title>Demande de badges</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-27')
    echo '<title>E-book en vrac</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-29')
    echo '<title>Tutoriels informatique</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-33')
    echo '<title>Graphisme</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-35')
    echo '<title>Films Screener</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-36')
    echo '<title>Appz Linux / Mac</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-38')
    echo '<title>Jeux consoles</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-40')
    echo '<title>Single musique</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-41')
    echo '<title>Clip / Concert</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-42')
    echo '<title>Films Vo et Vost</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-44')
    echo '<title>Anti-Virus</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-45')
    echo '<title>Films exclue</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-46')
    echo '<title>Films DVD / HD</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-51')
    echo '<title>Discographie</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-56')
    echo '<title>Docs / Spéctacles</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-57')
    echo '<title>Informatique générale</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-58')
    echo '<title>Dessins animés / Mangas</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-59')
    echo '<title>Linux / Mac\'os</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-60')
    echo '<title>Jeux vidéo PC, Gamer</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-68')
    echo '<title>Programmation / Coding</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-70')
    echo '<title>Android / Iphone</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-79')
    echo '<title>Section MAO</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-81')
    echo '<title>Sourds et malentendants</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-84')
    echo '<title>Panthéon</title>';

elseif ($_SERVER['REQUEST_URI'] === '/sub-85')
    echo '<title>Sport</title>';

else
    echo '<title>Wawa-Mania - Téléchargement direct</title>';
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if ($_SERVER['REQUEST_URI'] === '/faq')
    echo '<meta name="description" content="Aide à l\'utilisation de Wawa-Mania, de son fonctionnement et de ses règles.">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/faq">';

elseif ($_SERVER['REQUEST_URI'] === '/donation')
    echo '<meta name="description" content="Afin de nous aider à maintenir le forum en ligne et d\'assurer un développement régulier, nous vous remercions d\'avance pour votre geste">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/donation">';

elseif ($_SERVER['REQUEST_URI'] === '/')
    echo '<meta name="description" content="Forum de téléchargement direct, communauté de partage libre, à l\'abri de HADOPI et respectant votre vie privée.">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/">';

elseif ($_SERVER['REQUEST_URI'] === '/login')
    echo '<meta name="description" content="Connectez-vous à votre compte Wawa-Mania">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/login">';

elseif ($_SERVER['REQUEST_URI'] === '/search')
    echo '<meta name="description" content="Recherchez un fichier ou une information rapidement et facilement">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/search">';

elseif ($_SERVER['REQUEST_URI'] === '/lost')
    echo '<meta name="description" content="Récupérez votre mot de passe simplement et rapidement">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/lost">';

elseif ($_SERVER['REQUEST_URI'] === '/trade')
    echo '<meta name="description" content="Le bureau des échanges vous permet d\'échanger vos points contre différent services ou privilège">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/trade">';

elseif ($_SERVER['REQUEST_URI'] === '/recent')
    echo '<meta name="description" content="Derniers sujets de Wawa-Mania, posté par les membres">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/recent">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-3')
    echo '<meta name="description" content="Concernant les problèmes de navigation, de compatabilité ou pour soumettre une idée pour le forum">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-3">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-4')
    echo '<meta name="description" content="Discussions générales, de l\'actualité politique à l\'informatique aux demandes d\'aides...">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-4">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-5')
    echo '<meta name="description" content="Film gratuit en téléchargement direct">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-5">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-6')
    echo '<meta name="description" content="Tous les episodes / saisons des séries TV">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-6">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-7')
    echo '<meta name="description" content="Album musique en téléchargement direct">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-7">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-8')
    echo '<meta name="description" content="Logiciels et applications pour Windows">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-8">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-16')
    echo '<meta name="description" content="Discussions autour des jeux vidéo sur PC">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-16">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-20')
    echo '<meta name="description" content="Tout ce qui ne rentre pas dans les autres sections">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-20">';

 elseif ($_SERVER['REQUEST_URI'] === '/sub-22')
    echo '<meta name="description" content="Demande de badge communautaire">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-22">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-27')
    echo '<meta name="description" content="Documents, livres, journaux en format PDF compatible tablettes et smartphones">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-27">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-29')
    echo '<meta name="description" content="Tutoriels relatif à l\'informatique, en image et expliqué de façon simple">
 <link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-29">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-33')
    echo '<meta name="description" content="Section autour du graphisme, web, avatar...">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-33">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-35')
    echo '<meta name="description" content="Film de qualité screener et équivalent">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-35">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-36')
    echo '<meta name="description" content="Discussions autour des systèmes Mac et Linux">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-36">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-38')
    echo '<meta name="description" content="Jeux pour console de salon ou portable">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-38">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-41')
    echo '<meta name="description" content="Clips, vidéos et concert musicaux">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-41">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-42')
    echo '<meta name="description" content="Films en vo, vost en qualité DVD">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-42">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-44')
    echo '<meta name="description" content="Logiciels anti-virus pour protection de votre système">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-44">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-45')
    echo '<meta name="description" content="Les films du moment, en français ou VOSTFR">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-45">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-46')
    echo '<meta name="description" content="Format full DVD, 4K, 8K">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-46">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-51')
    echo '<meta name="description" content="Discographie des artistes">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-51">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-56')
    echo '<meta name="description" content="Documentaires et spectacles en téléchargement http">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-56">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-57')
    echo '<meta name="description" content="Problème informatique en tout genre, venez y demander aide ou bien venez aider en répondant aux problèmes des membres">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-57">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-58')
    echo '<meta name="description" content="Dessins animés pour enfant, Mangas">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-58">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-59')
    echo '<meta name="description" content="App / Software propriétaire sur Mac et Linux">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-59">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-60')
    echo '<meta name="description" content="Jeux vidéo sur PC format ISO">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-60">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-68')
    echo '<meta name="description" content="Discussion autour des languages de programmation">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-68">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-79')
    echo '<meta name="description" content="Section MAO, logiciels et tutoriels">
 <link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-79">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-81')
    echo '<meta name="description" content="Média pour les personnes souffrant d\'un handicap auditif">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-81">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-84')
    echo '<meta name="description" content="Affichez ce que vous désirez à propos de vous">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-84">';

elseif ($_SERVER['REQUEST_URI'] === '/sub-85')
    echo '<meta name="description" content="Vidéos sportives">
<link rel="alternate" hreflang="fr" href="https://forum.wawa-mania.ec/sub-85">';

else
    echo '<meta name="description" content="Communauté libre et indépendante de partage de fichiers et d\'informations.">';

?>

<link rel="icon" href="favicon.ico">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/gfonts.css" type="text/css">
<link rel="stylesheet" href="css/jquery-ui.min.css" type="text/css">
<link rel="stylesheet" href="css/style.css?v=1.163" type="text/css">
<?php
// Should load the dark scheme ?
if ($is_connected && $uinfos['us_scheme'] === 'dark')
    echo '<link rel="stylesheet" href="css/dark.css?v=1.150" type="text/css">';
?>
</head>
<body>
<?php

// Login for NO js browser
$error = false;

if(!$is_connected && $nojs && isset($_POST['username']) && strlen($_POST['username']) < 26 && strlen($_POST['username']) > 2
    && isset($_POST['password']) && strlen($_POST['password']) > 4 && isset($_SESSION['checkcap']) && isset($_POST['answercap']) && strlen($_POST['answercap']) < 20) {

     //Get answer from session
     $security = strtolower($_POST['answercap']);

     //Get answer captcha from user
     $answer = $_SESSION['checkcap']; //Get the answer previously setup inside a $_SESSION

    //Exit on not allowed chars
    if(preg_match("/([\/\\\'\"\%])/", $_POST['username']))
        $error = 'Caractère(s) invalide dans votre pseudo';

    //Bad anwser
    if($answer != $security && !$error)
        $error = '<div id=nojslog class=red>Captcha incorrect</div>';

    if(!$error) {

        $result = login($_POST['username'], $_POST['password'], $mysqli);

        if ((int)$result === 4)
            echo '<div id=nojslog class=green>Vous êtes identifié avec succès, pour accéder à votre compte, rechargez la page !</div>';

        elseif($result === 3 || $result === 5 || $result === 6)
            echo '<div id=nojslog class=red>Ce compte n\'existe pas ou le mot de passe est incorrect</div>';

        else
            $error = false;
    }
}
// End Login no JS

// Recovery lost password for NO js browser
if(isset($_POST['username']) && isset($_POST['email'])) {

    // Check email if valid
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        $error = 'Email incorrect';

        // Check length username
    if(strlen($_POST['username']) > 25 || strlen($_POST['username']) < 3 || preg_match("/([\/\\\'\"\%])/", $_POST['username']))
        $error = 'Utilisateur incorrect';

    // Set temporary the ip (avoid flood) and check validity
    $ip = $_SERVER['HTTP_X_REAL_IP'];

    if(!filter_var($ip, FILTER_VALIDATE_IP) && ! $error)
        $error = 'Autorisation refusée';

    if(!$error) {

        $result = recovery($ip, $_POST['username'], $_POST['email'], $mysqli);

        if ($result === 'email')
            $error = 'Utilisateur ou email incorrect';

        elseif ($result === 'euser')
            $error = 'Utilisateur ou email incorrect';

        elseif ($result === 'enow')
            $error = 'Autorisation refusée';

        elseif ($result === 'eok')
            $error = 'Nouveau mot de passe envoyé';

        else
            $error = false;
    }
}
// End recovery lost password for NO js browser
?>
    <!-- Wrapper -->
	<div id=wrapper>
		<!-- Header -->
		<header>
			<!-- Container menu -->
			<div id=ctn-menu>
<?php

if ($is_connected && $uinfos['us_scheme'] === 'dark')
    // Ninja dark if connected and selected as scheme
    echo '           <!-- Logo svg -->
                             <img src="svg/ninja-dark.svg" alt="Ninja" class=logoNinja>';

else
    // Ninja blue if not connected, default or selected as scheme
    echo '            <!-- Logo svg -->
            <img src="svg/ninja.svg" alt="Ninja" class=logoNinja>';
?>

			    <!-- Title  -->
				<h1>Wawa-Mania</h1>
				<!-- Menu -->
				<div id=menu itemscope itemtype="http://www.schema.org/SiteNavigationElement">
				<?php if($is_connected) { ?>
                <!-- Home -->
					<a itemprop="url" id=home href="https://forum.wawa-mania.ec"><i class="fa fa-home fa-2x ui-corner-all" title="Accueil" data-title="Accueil"></i>
					<meta itemprop="name" content="Accueil">
					</a>
					<!-- Member area -->
					<i id="<?php echo 'us'.$uinfos['us_id']; ?>" class="aream fa fa-user fa-2x ui-corner-all" data-jslvl="<?php echo $uinfos['us_show_badge']; ?>" title="<?php echo $uinfos['username']; ?>" data-title="<?php echo $uinfos['username']; ?>"></i>
					<!-- Search -->
					<a itemprop="url" id=search href="https://forum.wawa-mania.ec/search"><i class="fa fa-search fa-2x ui-corner-all" title="Recherche" data-title="Recherche"></i>
					<meta itemprop="name" content="Recherche">
					</a>
					<!-- Faq -->
					<a itemprop="url" class=faq href="https://forum.wawa-mania.ec/faq" title="FAQ" data-title="FAQ"><i class="fa fa-question-circle fa-2x ui-corner-all"></i>
					<meta itemprop="name" content="Faq">
					</a>
					<!-- Private box -->
					<a itemprop="url" class="mp  donot" href="https://forum.wawa-mania.ec/mp"><?php

        if ((int)$uinfos['us_badge'] !== 28 && file_exists(__DIR__.'/cache/pm/'.$uinfos['us_id'].'.html')) {

            $cntf = file_get_contents(__DIR__.'/cache/pm/'.$uinfos['us_id'].'.html');

            if ($cntf > 0)
                echo '<i class="imp fa fa-envelope fa-2x ui-corner-all nvi" data-title="'.$cntf.' nouveau(x) message(s)"></i>';

            else
                echo '<i class="fa fa-envelope-o fa-2x ui-corner-all nvi" data-title="Pas de nouveau message"></i>';
        }

        else
            echo '<i class="imn fa fa-envelope fa-2x ui-corner-all nvi" data-title="Boite inactive"></i>';

        ?></a>
					<!-- Trade pts -->
					<i id=trade class="fa fa-exchange fa-2x ui-corner-all trade" title="Bureau des échanges" data-title="Bureau des échanges"></i>
					<!-- Donate -->
					<a itemprop="url" href="https://forum.wawa-mania.ec/donation" class=donation><i id=donate class="fa fa-heart fa-2x" title="Donation" data-title="Donation"></i>
					<meta itemprop="name" content="Donation">
					</a>
				<?php

}
    if(!$is_connected) {
        ?>
					<!-- Home -->
					<a itemprop="url" id=home href="https://forum.wawa-mania.ec"><i class="fa fa-home fa-2x ui-corner-all" data-title="Accueil"></i>
					<meta itemprop="name" content="Accueil">
					</a>
					<!-- Login -->
					<a itemprop="url" id=login href="https://forum.wawa-mania.ec/login"><i class="fa fa-sign-in fa-2x ui-corner-all" data-title="Se connecter"></i>
					<meta itemprop="name" content="Connexion">
					</a>
					<!-- Faq -->
					<a itemprop="url" class=faq href="https://forum.wawa-mania.ec/faq" data-title="Foire aux questions"><i class="fa fa-question-circle fa-2x ui-corner-all" data-title="Faq"></i>
					<meta itemprop="name" content="Faq">
					</a>
					<!-- Search -->
					<a itemprop="url" id=search href="https://forum.wawa-mania.ec/search"><i class="searchCl fa fa-search fa-2x ui-corner-all" data-title="Recherche"></i>
					<meta itemprop="name" content="Recherche">
					</a>
					<!-- Donate -->
					<a itemprop="url" href="https://forum.wawa-mania.ec/donation" class=donation><i id=donate class="fa fa-heart fa-2x" data-title="Donation"></i>
					<meta itemprop="name" content="Donation">
					</a>
<?php } ?>
<!-- End menu -->
				</div>
				<!-- End container menu -->
			</div>
<?php
// Search
if (strstr($_SERVER['REQUEST_URI'], 'search') && $nojs)
    include (__DIR__.'/html/searchnjs.html');
?>          <!-- End header -->
		</header>
		<!-- Container principal -->
		<div id=central role="main">
			<!-- Container result from DB -->
			<div class=resultDb>
<?php

//Homepage
if($nojs && $_SERVER['REQUEST_URI'] === '/')
    include(__DIR__.'/php/home.php');

elseif($nojs && $_SERVER['REQUEST_URI'] === '/login')
    include(__DIR__.'/php/login.php');

//Faq
elseif($nojs && $_SERVER['REQUEST_URI'] === '/faq')
    include(__DIR__.'/html/faq.html');

// Donation
elseif($nojs && $_SERVER['REQUEST_URI'] === '/donation')
    include(__DIR__.'/php/donation.php');

    // Recover
elseif($nojs && !$is_connected && strstr($_SERVER['REQUEST_URI'], 'lost'))
    include(__DIR__.'/php/lost.php');

    // Section
elseif($nojs && strstr($_SERVER['REQUEST_URI'], 'sub'))
    include (__DIR__.'/php/sub.php');

elseif($nojs && $is_connected && strstr($_SERVER['REQUEST_URI'], 'pid'))
    include (__DIR__.'/php/topic.php');

    // Create topic
elseif($nojs && strstr($_SERVER['REQUEST_URI'], 'newtopic'))
    include (__DIR__.'/php/newtopic.php');

// Reply topic
elseif($nojs && strstr($_SERVER['REQUEST_URI'], 'newreply'))
    include (__DIR__.'/php/newreply.php');
// Topic
 elseif($nojs && $is_connected && strstr($_SERVER['REQUEST_URI'], 'topic'))
    include (__DIR__.'/php/topic.php');
    // MP
elseif($nojs && $is_connected && $nojs && strstr($_SERVER['REQUEST_URI'], 'mp'))
    include (__DIR__.'/php/mp.php');
else
   echo '';
?>
            <!-- End result from DB -->
			</div>
		<!-- Shortlink -->
		<?php if($is_connected) { ?>
<div id=shortLink>
				<p class=shortIcon>
					<i class="backUp fa fa-arrow-up fa-lg" data-title="Retourner en haut"></i>
					<i class="wmStats fa fa-bar-chart fa-lg" data-title="Stats du forum"></i>
					<i class="toprank fa fa-trophy fa-lg" data-title="Top membres"></i>
				</p>
				<a class=recent href="https://forum.wawa-mania.ec/recent" title="Sujet récent">Afficher les derniers messages</a>
				<p class=look4user>Rechercher un membre</p>
		<!-- End shortlink -->
		</div><?php } ?>

	    <!-- End container principal -->
		</div>
<!-- End wrapper -->
</div>
<!-- Footer -->
<footer>
	<p><a href="http://www.sphinxsearch.com/" title="SphinxSearch"><img src="img/sphinx_blog.png" width="80" height="15" alt="powered by Sphinx"></a> <a href="https://forum.wawa-mania.ec" title="Ninja"><img src="img/ninjacmsrsb.png" width="80" height="15" alt="NinjaCms"></a> <a href="https://gentoo.org" title="Gentoo Linux" data-title="Gentoo Linux, alternative à microchiotte windaube"><img src="img/gentoo.png" width="80" height="15" alt="Gentoo"></a> </p>
	<p>Wawa-Mania 2006 - 2016<p>
</footer>
<!-- End footer -->
<?php
if ($is_connected)
    echo '<input id="trigPop" class="default" name="default" style="display:none;" data-isc="y" data-nrow="0" data-sub="all" />';
else
    echo '<input id="trigPop" class="default" name="default" style="display:none;" data-isc="n" data-nrow="0" data-sub="all" />';
?>

<!-- Javascript files -->
	<script type="text/javascript" src="js/jquery-2.2.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/spectrum.js"></script>
	<script type="text/javascript" src="js/function.js?v=1.117"></script>
	<script type="text/javascript" src="js/aream.js?v=1.117"></script>
	<script type="text/javascript" src="js/general.js?v=1.117"></script>
<?php
if(!$is_connected)
    echo '<script type="text/javascript" src="js/loginoff.js?v=1.117"></script>';
?>
</body>
</html>
