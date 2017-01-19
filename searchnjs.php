<?php
require('php/funcwm.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<title>Recherche de fichier</title>
<meta charset="UTF-8">
<meta name="description" content="Recherchez un fichier ou une information rapidement et facilement, films, séries tv">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="ico" href="favicon.ico">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/gfonts.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
<?php
//Should load the dark scheme ?
if($is_connected && $uinfos['us_scheme'] === 'dark')
echo '<link rel="stylesheet" href="css/dark.css" type="text/css">';
?>
</head>
<body>
<!-- Wrapper -->
<div id=wrapper>
<!-- Form search -->
<header>
<!-- Container menu -->
		<div id=ctn-menu>
<?php

    if($is_connected && $uinfos['us_scheme'] === 'dark')
        //Ninja dark if connected and selected as scheme
        echo'           <!-- Logo svg -->
                             <img src="svg/ninja-dark.svg" alt="Ninja" class=logoNinja>';

    else
        //Ninja blue if not connected, default or selected as scheme
        echo '            <!-- Logo svg -->
            <img src="svg/ninja.svg" alt="Ninja" class=logoNinja>';
?>

			<!-- Title  -->
			<h1>Wawa-Mania</h1>
			<!-- Menu -->
			<div id=menu itemscope itemtype="http://www.schema.org/SiteNavigationElement">
				<?php if($is_connected) { ?>
<!-- Home -->
				<a itemprop="url" id=home href="https://forum.wawa-mania.ec/home"><i class="fa fa-home fa-2x ui-corner-all" data-title="Accueil"></i></a>
				<!-- Member area -->
				<i id="<?php echo 'us'.$uinfos['us_id']; ?>" class="aream fa fa-user fa-2x ui-corner-all" data-jslvl="<?php echo $uinfos['us_show_badge']; ?>" data-title="<?php echo $uinfos['username']; ?>"></i>
				<!-- Search -->
				<a itemprop="url" id=search href="https://forum.wawa-mania.ec/search"><i class="fa fa-search fa-2x ui-corner-all" data-title="Recherche"></i></a>
				<!-- Faq -->
				<a itemprop="url" class=faq href="https://forum.wawa-mania.ec/faq" data-title="Foire aux questions"><i class="fa fa-question-circle fa-2x ui-corner-all"></i></a>
				<!-- Private box -->
				<a itemprop="url" class="mp  donot" href="https://forum.wawa-mania.ec/mp"><?php

    if((int)$uinfos['us_badge'] !== 28 && file_exists(__DIR__.'/cache/pm/'.$uinfos['us_id'].'.html')) {

        $cnt = __DIR__.'/cache/pm/'.$uinfos['us_id'].'.html';
        $cntf = file_get_contents($cnt);

         if($cntf > 0)
            echo '<i class="imp fa fa-envelope fa-2x ui-corner-all nvi" data-title="'.$cntf.' nouveau(x) message(s)"></i>';

         else
            echo '<i class="fa fa-envelope-o fa-2x ui-corner-all nvi" data-title="Pas de nouveau message"></i>';

    }

    else
        echo '<i class="imn fa fa-envelope fa-2x ui-corner-all nvi" data-title="Boite inactive"></i>';

?></a>
				<!-- XMPP -->
				<i id=chat class="fa fa-comments-o fa-2x ui-corner-all nvi" data-title="Chat privé"></i>
				<!-- Trade pts -->
				<i id=trade class="fa fa-exchange fa-2x ui-corner-all trade" data-title="Bureau des échanges"></i>
				<!-- Donate -->
				<a itemprop="url" href="https://forum.wawa-mania.ec/donation" class=donation><i id=donate class="fa fa-heart fa-2x" data-title="Donation"></i></a>
				<?php }
				 if(!$is_connected) { ?>
				 <!-- Home -->
				<a itemprop="url" id=home href="https://forum.wawa-mania.ec/home"><i class="fa fa-home fa-2x ui-corner-all" data-title="Accueil"></i></a>
				<!-- Login -->
				<a itemprop="url" id=login href="https://forum.wawa-mania.ec/login"><i class="fa fa-sign-in fa-2x ui-corner-all" data-title="Se connecter"></i></a>
				<!-- Faq -->
				<a itemprop="url" class=faq href="https://forum.wawa-mania.ec/faq" data-title="Foire aux questions"><i class="fa fa-question-circle fa-2x ui-corner-all" data-title="Faq"></i></a>
				<!-- Search -->
				<a itemprop="url" id=search href="https://forum.wawa-mania.ec/search"><i class="searchCl fa fa-search fa-2x ui-corner-all" data-title="Recherche"></i></a>
				<!-- Donate -->
				<a itemprop="url" href="https://forum.wawa-mania.ec/donation" class=donation><i id=donate class="fa fa-heart fa-2x" data-title="Donation"></i></a>
<?php } ?>
<!-- End menu -->
</div>
</div>
		<div id=hSearch class=hMain role=search>
		<form id="optContainer" action="searchnjs.php" method="get">
		<h5>Rechercher un post (100 résultats max)</h5>
		<input id=searchSel name=search type="text" placeholder="Rechercher par mots clés" /><br>
		<select name="sub" id="sub">
			<option value="all">Rechercher partout</option>
			<option disabled="disabled">--------</option>
				<optgroup label="La board">
					<option value="17">Vos demandes</option>
				</optgroup>
				<option disabled="disabled">--------</option>
				<optgroup label="La coin détente">
					<option value="4">Café</option>
				</optgroup>
				<optgroup label="Informatique">
					<option value="68">Programming / Coding</option>
					<option value="57">Informatique / Générale</option>
					<option value="29">Tutoriels</option>
					<option value="59">Linux MacOs FreeBSD</option>
					<option value="60">Gamer</option>
				</optgroup>
				<optgroup label="Films / Vidéo">
					<option value="45">Films (exclues)</option>
					<option value="5">Films (dvdrip)</option>
					<option value="35">Films (Screener et TS)</option>
					<option value="42">Films (Vo et VoSt)</option>
					<option value="46">Full DvD / HD</option>
					<option value="6">Séries télé</option>
					<option value="81">Sourds et malentendants</option>
					<option value="56">Docs, spectacles</option>
					<option value="58">Dessin animés / Animes / Mangas</option>
				</optgroup>
				<optgroup label="Appz">
					<option value="8">Appz Windows</option>
					<option value="36">Appz Linux/Mac/Freebsd</option>
					<option value="44">Anti-Virus / Anti-spyware / Anti-trojan...</option>
				</optgroup>
				<optgroup label="Gamez">
					<option value="16">Gamez PC</option>
					<option value="37">GameZ Consoles de salon</option>
					<option value="38">GameZ Consoles portables</option>
				</optgroup>
				<optgroup label="Musique">
					<option value="7">Album Musique</option>
					<option value="40">Single Musique</option>
					<option value="51">Discographie</option>
					<option value="41">Clip</option>
					<option value="71">Concerts, Spectacles musicaux</option>
					<option value="66">H-Q</option>
					<option value="79">Section M.A.O</option>
				</optgroup>
				<optgroup label="Divers warez">
					<option value="20">Divers</option>
					<option value="70">Mobile et Pocket PC</option>
					<option value="27">E-book</option>
				</optgroup>
				<optgroup label="Majeur XXX">
					<option value="9">[Majeur] Films</option>
					<option value="47">[Majeur] Vidéo</option>
					<option value="49">[Majeur] Divers</option>
				</optgroup>
		</select>
			<div id=optctn>
			<fieldset id="optSearch">
				<legend>Rechercher par</legend>
					<input type="radio" name="optSearch" value="subject">Titre du topic<br>
					<input type="radio" name="optSearch" value="poster">Auteur du topic
			</fieldset>
			<fieldset id="optBy" data-by="to_id">
				<legend>Date</legend>
					<input type="radio" name="optBy" value="to_last_post_id">Dernier message<br>
					<input type="radio" name="optBy" value="to_id">Création du topic
			</fieldset>
			<fieldset id="optSort" data-sort="desc">
				<legend>Par ordre</legend>
					<input type="radio" name="optSort" value="asc">Ancien au plus récent<br>
					<input type="radio" name="optSort" value="desc">Récent au plus ancien
			</fieldset>
			</div>
		<input type="submit" class="subSearch fa fa-search" value="Rechercher" />
		</form>
		</div>
		</header>

		<div id=central role="main">

		<!-- Container result from DB -->
		<div class=resultDb>
				<!-- End result from DB -->
<!-- End form search -->
<?php

//Sphinx API
$limit = 100;
$sph_port = 9312;
$sph_host = "localhost";
$cl = new SphinxClient();
$cl->SetServer($sph_host, $sph_port);

//Words
$k = (isset($_GET['search'])) ? $_GET['search'] : exit;
//Filter - Author / Subject
$filter = (isset($_GET['optSearch'])) ? $_GET['optSearch'] : 'subject';
//ASC - DESC
$sort = (isset($_GET['optSort'])) ? $_GET['optSort'] : 'desc';
//target sb
$sub = (isset($_GET['sub'])) ? $_GET['sub'] : 'all';
//By topicid or last post id
$by = (isset($_GET['optBy'])) ? $_GET['optBy'] : 'to_id';

$cl->SetLimits((int)$offset,(int)$limit,100,0);

	//Check length $str
	//if(strlen($k) > 30 || strlen($k) < 2) $k = '';
	 if(strlen($k) > 30 || strlen($k) < 2) $k = '';

	//Check $filter
	$sec_filter = array('poster','subject');
	if(!in_array($filter, $sec_filter)) exit;

	//Check $by
	$sec_by = array('to_last_post_id','to_id');
	if(!in_array($by, $sec_by)) exit;

	//Check $sort
	$sec_sort = array('desc','asc');
	if(!in_array($sort, $sec_sort)) exit;

	 //Filter by author
     if($filter == 'poster') {
		//Get id user (use username fullsearch)
		$idp = get_user_info($k, 'username');
		$user_id = $idp['us_id'];
		//Filter result by username ID who -> created topic
    	$cl->SetFilter('to_first_poster_id',array($user_id));
    }

	//Filter subcategorie
	if(isset($sub) && is_numeric($sub))
		$cl->SetFilter('to_section',array($sub)); // <- subcategorie target

	else {
		$sub = 'all'; // <- any
		//remove viewforum ID (useless or private)
		$cl->SetFilter('to_section', array(1, 3, 22, 84, 31, 33, 34, 48), true);
	}

	if($sort == 'desc') $cl->SetSortMode(SPH_SORT_ATTR_DESC, $by); //New to old

	else $cl->SetSortMode(SPH_SORT_ATTR_ASC, $by); //Old to new

	//Escape string consider as special operator
	$k = $cl->EscapeString($k);



	//Execute sphinx query
	if($filter == 'poster')
		$result = $cl->Query('','main mdelta'); // <- By author topic
	else {
		//$cl->SetRankingMode(SPH_RANK_MATCHANY);
		$result = $cl->Query($k,'main mdelta'); // <- By keywords (associated filter / sort)
	}
	//Index running
	if(!$result) {
		echo '<div id="noresult"><i class="fa fa-times"></i>Indexage en cours... Réesayez dans quelques minutes</div>';
		return false;
	}

	else {

		if(!empty($result["matches"]))
			foreach ($result["matches"] as $display => $info) {
			$arr[] = $result["matches"][$display]['attrs'];
		}

		//Empty
		else exit('<div id="noresult"><i class="fa fa-times"></i>Aucun résultat ne correspond à votre recherche. Veuillez réessayer avec d\'autres termes</div>');
	}

		if($result['total_found'] > 100) $result['total_found'] = 100;
		echo '<i id="nbrow" class="fa fa-file-o '.$sub.'" role="complementary"><p>'.$result['total_found'].' résultat trouvé</p></i><span id="getmore" style="display:none;">'.$k.'</span>';


	foreach($arr as $search_arr) {

		//Time unixtimestamp
		$timestamp = sem_time('sec', $search_arr["to_first_post_ts"], true); //Date topic created
		$timelast = sem_time('sec', $search_arr["to_last_post_ts"], true); //Date last post

		$user = get_user_info($search_arr['to_first_poster_id'],'u_id');

	echo '
	<section class="container-link">
		<div class="rightInfo">
			'.(($is_connected) ? '
			<span class="ninTopic">
				<i class="fa fa-arrow-circle-right"></i>
				<a id="'.(($is_connected) ? 'go-'.$search_arr["to_id"].'" href="topic-'.$search_arr["to_id"].'' : '/login').'" class="shref viewtopic"> Accéder au topic</a>
			</span>' : '').'
			<span class="timestamp">Crée à '.$timestamp.'</span>
			<a href="'.(($is_connected) ? 'topic-'.$search_arr['to_id'].'-'.$search_arr['to_num_replies'].'' : '/login').'" class="shref"> Dernier message à '.$timelast.'</a>
		</div>
		<div class="leftInfo">
			<span class="title_sort"><i class="'.$icons[$search_arr["to_section"]].'"></i> '.$search_arr["subject"].'</span>
			<span class="categories_sort">'.$subName[$search_arr["to_section"]].'</span>
			<span class="byWho">Par '.$user['username'].'</span>
		</div>
		<div style="clear:both"></div>
	</section>';
	}
?>
		</div>
		</div>	<!-- End container principal -->
<!-- End wrapper -->
</div>
<!-- Footer -->
<footer>
		<p>
			<a href="http://www.sphinxsearch.com/"><img src="img/sphinx_blog.png" width="80" height="15" alt="powered by Sphinx"></a>
			<a href="#" title="Ninja"><img src="img/ninjacmsrsb.png" width="80" height="15" alt="NinjaCms"></a>
			<a href="https://debian.org" title="Debian Linux" data-title="Debian Linux, l\'alternative à microsoft winbouze"><img src="img/button-mini.png" width="80" height="15" alt="Debian Linux"></a>
			<a href="https://www.mozilla.org/fr/firefox/new/" title="Privé, Rapide, code entièrement public..." data-title="Privé, Rapide, code entièrement public..."><img src="img/firefox.gif" width="80" height="15" alt="Privé, Rapide, code entièrement public..."></a>
		</p>
	</footer>
</body>
</html>