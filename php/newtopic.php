<?php

    if(!$is_connected)
        exit('<div id=notcon>Vous devez être connecté pour accéder au contenu de cette page</div>');

$fid = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_NUMBER_INT);

$fid = substr($fid, 1);

if((int)$uinfos['us_show_badge'] === 27 || (int)$uinfos['us_show_badge'] === 28) {
    echo '<div id=torerr>L\'utilisation de cette fonction via noscript (ou tor browser) nécessite un rang de level 4</div>';
exit;
}

$staff = staff($uinfos['us_badges'], $mysqli);

//New topic
if($is_connected && isset($_POST['sendt']) && mb_strlen($_POST['sendt']) > 4 && isset($_POST['title_tojs']) && mb_strlen($_POST['title_tojs']) > 10 && mb_strlen($_POST['title_tojs']) < 80 && isset($_POST['sectionid']) && is_numeric($_POST['sectionid']) && $_POST['sectionid'] < 100) {

    $tormess = false;
    $cache = __DIR__."/../cache/flood";

         if(file_exists($cache.'/'.$uinfos['us_id'].'.lock'))
        $tormess = '<div id=torerr>Vous devez attendre jusqu\'à 2 minutes pour pouvoir poster.</div>';

        elseif(file_exists($cache.'3/'.$uinfos['us_id'].'.lock'))
        $tormess = '<div id=torerr>Vous devez attendre jusqu\'à 3 minutes pour pouvoir poster.</div>';

        elseif(file_exists($cache.'/4/'.$uinfos['us_id'].'.lock'))
        $tormess = '<div id=torerr>Vous devez attendre jusqu\'à 5 minutes pour pouvoir poster.</div>';

        //Exit on existing lock (flood protection, reset every minute, bypass staff)
        if(!$staff && !$tormess) {

            //Check if the user have permission to reply
            include __DIR__.'/check.php';
            $check = check_topic($uinfos['us_id'], $_POST['sectionid'], $mysqli);

            //Exit and return message error if check fail
            if($check === 'p1')
		    $tormess = '<div id=torrr>Vous n\'avez pas l \'autorisation de poster</div>';

            //Write flood lock depends on level
            if($check > '2')
                file_put_contents(__DIR__.'/../cache/flood'.$check.'/'.$uinfos['us_id'].'.lock', '0', LOCK_EX);
            else
                file_put_contents(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock', '0', LOCK_EX);

        } //End staff bypass


        //(write lock protection flood) - STAFF
        if($staff)
            file_put_contents(__DIR__.'cache/flood/'.$uinfos['us_id'].'.lock', '0', LOCK_EX);

        if(!$tormess) {
            //Sanitize variable
            $title = $purifier->purify($_POST['title_tojs']);
            $sectionid = (int)$_POST['sectionid'];

            $result = send_topic($uinfos['us_id'], $sectionid, $title, $_POST['sendt'], strtotime("now"), $mysqli);

            $tormess = '<div id=torok>Votre topic s\'est crée avec succès ! <a href="https://forum.wawa-mania.ec/topic-'.$result.'">https://forum.wawa-mania.ec/topic-'.$result.'</a> sera disponible dans 2 minutes.</div>';
    }
        }

if($tormess !== false)
    echo $tormess;
?>
<div id=backtor>
	<a href="https://forum.wawa-mania.ec/sub-<?php echo $fid; ?>"><i class="<?php echo $icons[$fid]; ?>"></i> <?php echo $subName[$fid]; ?></a>
</div>

<div id=container_tojs>
<form action="newtopic-<?php echo $fid; ?>" method="post">
					 <div id=bar_bbcode>
					 <span id=bb_bold class="fa-stack fa-lg" data-title="Gras"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-bold fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_italic class="fa-stack fa-lg" data-title="Italic"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-italic fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_large class="fa-stack fa-lg" data-title="Grand"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-text-height fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_small class="fa-stack fa-lg" data-title="Petit"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-text-width fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_underline class="fa-stack fa-lg" data-title="Souligné"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-underline fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_strikethrough class="fa-stack fa-lg" data-title="Barré"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-strikethrough fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_code class="fa-stack fa-lg" data-title="Code"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-code fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_quote class="fa-stack fa-lg" data-title="Quote"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-quote-left fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_left class="fa-stack fa-lg" data-title="Gauche"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-left fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_center class="fa-stack fa-lg" data-title="Centre"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-center fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_right class="fa-stack fa-lg" data-title="Droite"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-right fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_justify class="fa-stack fa-lg" data-title="Pre"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-justify fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_paragraph class="fa-stack fa-lg" data-title="Paragraph"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-paragraph fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_image class="fa-stack fa-lg" data-title="Image"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-picture-o fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_url class="fa-stack fa-lg" data-title="Lien"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-link fa-stack-1x fa-inverse"></i></span>
					 <span id=bb_color class="spect fa-stack fa-lg" data-title="Couleur"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-eyedropper fa-stack-1x fa-inverse"></i></span>
					 <span id=close_to class="fa-stack fa-lg" data-title="Annuler"><i class="red fa fa-square fa-stack-2x fa-inverse"></i><i class="white fa fa-times fa-stack-1x fa-inverse"></i></span>
					 </div>
					<input id="title_tojs" type="text" name="title_tojs" placeholder="Titre de votre topic...">
					<textarea id="message_to" name="sendt" placeholder="Avant de poster, lisez les règles du forum et de la section dans laquelle vous vous apprétez à poster. Un post qui ne respect pas les règles peut conduire au banissement de votre compte définitivement."></textarea>
					<input type="hidden" name="sectionid" value="<?php echo $fid; ?>">
					<div id="bar_to"><input type="submit" id=send_to value="Envoyer" style="border:0"></div>
</form>
	</div>
