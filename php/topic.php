<?php

if(!isset($nojs) || !$nojs)
    require('funcwm.php');

    if(!$is_connected)
        exit('<div id=notcon>Vous devez être connecté pour accéder au contenu de cette page</div>');

//Write the post param for NOjs
if($nojs) {

	//Get the URI
	$dataUri = str_replace('/', '', $_SERVER['REQUEST_URI']);

	//[1] section | [2] (optional) offset
	$data = explode('-', $dataUri);

	//If the URI is not valid
	if(!is_numeric($data[1]))
	    header('Location:https://forum.wawa-mania.ec');

	$key = ($data[0] === 'topic') ? 'tid' : 'pid';

	//Settings $_POST
	if($key === 'tid')
		$_POST['tid'] = (int)$data[1];

	else
		$_POST['pid'] = (int)$data[1];

	$_POST['offset'] = (is_numeric($data[2])) ? $data[2] : '0';

}
//End Nojs

$tid = (!isset($_POST['tid']) || !is_numeric($_POST['tid']) ? false : $_POST['tid']); //Topic id
$pid = (!isset($_POST['pid']) || !is_numeric($_POST['pid']) ? false : $_POST['pid']); //pid

$offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && (int)$_POST['offset'] > 0) ? (int)$_POST['offset'] : 0; //Offset of sub section

//if no tid and pid valid
if($tid === false && $pid === false) return false;

//If pid retrieve information needed
if($pid !== false) {

	$mysqli->query('SET @a = 0');
	$stmt = $mysqli->prepare('SELECT id, topic_id, @a:=@a+1 AS rnumber FROM posts WHERE topic_id = (SELECT p.topic_id FROM posts p WHERE p.id = ?) GROUP BY id ASC');
	$stmt->bind_param("i", $pid);
	$stmt->execute();
	$stmt->bind_result($id, $toid, $num);
	while($stmt->fetch()) {
		$data[$id] = ['num' => $num,
					  'tid' => $toid];
	}

	$stmt->close();

	if(empty($data[$pid]['num'])) return false;

	$offset = $data[$pid]['num'];
	$tid = $data[$pid]['tid'];
}

//Make sure the modulus JS was not overwrite
//$offset = ($offset === '30') ? 29 : $offset;
//$roff = $offset % 30;
//$offset = $offset - $roff;

//Check if members of staff
$staff = staff($uinfos['us_show_badge'], $mysqli);

	//Cache path
	$testFile = glob(__DIR__.'/../cache/topcache/'.$tid.'-*.html');

	//If cache found
	if(!empty($testFile) && !$staff) {

		//For each entry
		foreach($testFile as $path) {

			$exPath = explode('-',$path);

			//Get timestamp cache
			preg_match('/^([\d$]+)/', $exPath[1], $tsCache);
			$tsCache[1];

			//Check the offset
			preg_match('/^([\d$]+)/', $exPath[2], $offCache);
			$cache = ($offCache[1] == $offset) ? true : false;
			if($cache !== false):

			//Get the last timestamp and compare with cache
			$stmt = $mysqli->prepare('SELECT UNIX_TIMESTAMP(updated_ts) FROM topics WHERE id = ?');
			$stmt->bind_param("i", $tid);
			$stmt->execute();
			$stmt->bind_result($puts);
			$stmt->fetch();
			$stmt->close();

			//If timestamp still the same
			if($puts == $tsCache[1]):

        	        $parse = file_get_contents($path);
 	               echo $parse;

 	               $atkus = $uinfos['username'];

 	               if($atkus !== '' && !empty($atkus))
 	                   file_put_contents(__DIR__.'/../cache/atkpot.html', "\r $atkus", FILE_APPEND | LOCK_EX);

			exit;

			else:
			$delCache = glob('../cache/topcache/'.$tid.'-*.html');
			foreach($delCache as $delFile) { unlink($delFile); }
			$isdone = false;
			endif;
			endif;
		}

	} // End load by cache

	//Sphinx API
	$sph_port = 9312;
	$sph_host = "localhost";
	$cl = new SphinxClient();
	$cl->SetServer($sph_host, $sph_port);

	//Sphinx for user informations
	$ucl = new SphinxClient();
	$ucl->SetServer($sph_host, $sph_port);

	//remove viewforum ID - 61 62 78 24 54 2 (useless or private)
	$cl->SetFilter('to_section', array(61, 62, 78, 24, 54), true);

	//Target topic id
	$tid = (int)$tid;
	$cl->SetFilter('to_id', array($tid));
	$cl->SetSortMode(SPH_SORT_ATTR_DESC, 'to_id');
	$result = $cl->Query('','main mdelta');

	//Indexing running or sphinxsearch deamon got a problem
	if(!$result)
	exit('<div id="noresult"><i class="fa fa-times"></i> Les index du moteur de Wawa-Mania sont en reconstruction.
		  La procédure sera terminée dans moins de 10 minutes.</div>');

	//Empty result
	if(empty($result["matches"]))
	exit('<div id="noresult"><i class="fa fa-times"></i>Ce topic n\'existe pas ou a été supprimé.</div>');

	//Fetch sphinx result
	foreach ($result["matches"] as $display => $info) {
	$infos[] = $result["matches"][$display]["attrs"];
	}

    if($infos[0]['to_section'] === 48) {
        $disable = file_get_contents(__DIR__.'/../html/disable.html');
        echo $disable;
        exit;
    }

	//Start cache
	ob_start();

	//Echo subject for replace the actual <title>
	echo '
	<i class="titleTohtml" style="display:none;">'.$purifier->purify($infos[0]['subject']).'</i>
		 ';

	//Get the number of replies
	$res = $infos[0]['to_num_replies'] / 30;
	$res = floor($res);
	$a = 0;

	$rebis = $res;

	if(!isset($offset) || $offset === 0) $offset = 1;
	$o = (int)$offset / 30;
	$oo = ceil($o);
	$offset = $oo*30 - 30;

	$offbis = $offset;

	//Get or set the offset, post > 1
	if($offset > 0)
	   $nb = $offset / 30; //Each page number

	//Post = 1
	else $nb = 0;

	$nbis = $nb;

	echo '<div class="navTop">';

	//Back/Next page hands
	if($offset > 0 || $nb < $res) {

	echo'
		<div class="right">
		'.($offset > 0 ? '
			<a href="https://forum.wawa-mania.ec/topic-'.$tid.'-'.(($offset > 1) ? $offset - 29 : 1).'" id="'.$tid.'-'.(($offset > 1) ? $offset - 29 : 1).'" class="prevTop fa-stack fa-lg">
				<i class="fa fa-square-o fa-stack-2x"></i>
				<i class="fa fa-hand-o-left fa-stack-1x" title="Page précédente"></i>
			</a>' : '').'

		'.($nb < $res ? '
			<a href="https://forum.wawa-mania.ec/topic-'.$tid.'-'.($offset + 30 + 1).'" id="'.$tid.'-'.($offset + 30 + 1).'" class="nextTop fa-stack fa-lg">
				<i class="fa fa-square-o fa-stack-2x"></i>
				<i class="fa fa-hand-o-right fa-stack-1x" title="Page suivante"></i>
			</a>' : '').'
		</div>

		<div class="left">
			<span class="fa-stack smallarrow">
				<i class="fa fa-square-o fa-stack-2x"></i>
				<i class="'.$icons[$infos[0]['to_section']].' fa-stack-1x"></i>
			</span>
			<a href="https://forum.wawa-mania.ec/sub-'.$infos[0]['to_section'].'" id="'.$infos[0]['to_section'].'" class="backSub subjectinf" data-title="Retour '.$subName[$infos[0]['to_section']].'">'.$subName[$infos[0]['to_section']].'</a>
			<span class="subjectinf"> <i class="arsep fa fa-chevron-right"></i><i class="arsep fa fa-chevron-right"></i> <a id="lnk-'.$tid.'" class="viewtopic artitle" href="/topic-'.$tid.'" data-title="Titre du topic">'.($infos[0]['subject']).'</a></span>
		</div>
		';
	}

	//Select page (listing)
	if($res >= 1) {

		echo ' <div class="pageTop">
			  <p id="'.((!$staff && $infos[0]['to_closed'] == '1') ? 'closed' : 'reply').'" class="'.$tid.' wi"><a href="https://forum.wawa-mania.ec/newreply-'.$tid.'" class="donot white"><i class="fa fa-commenting-o fa-lg"></i> Répondre</a></p>
			  <label>Aller à la page</label>
			  <select id="'.$tid.'" class="pageTop" name="top">';

			  while($a <= $res) {


				//Select by page - prev/next class or clic on topic
				if($nb == $a)
					echo '<option selected="selected" class="actPage" value="'.($a * 30 + 1).'">'.($a + 1).'</option>';
				else echo
					'<option value="'.($a * 30 + 1).'">'.($a + 1).'</option>';
				$a++;
			 }

		 echo ' </select>
			   </div>';
	}

		//If not action on previous res, echo reply topic
		echo ($res < 1) ?
		'<div class="left">
			<span class="fa-stack smallarrow">
				<i class="fa fa-square-o fa-stack-2x"></i>
				<i class="'.$icons[$infos[0]['to_section']].' fa-stack-1x"></i>
			</span>
			<a href="https://forum.wawa-mania.ec/sub-'.$infos[0]['to_section'].'" id="'.$infos[0]['to_section'].'" class="backSub subjectinf" data-title="Retour '.$subName[$infos[0]['to_section']].'">'.$subName[$infos[0]['to_section']].'</a>
			<span class="subjectinf"> <i class="arsep fa fa-chevron-right"></i><i class="arsep fa fa-chevron-right"></i> <a id="lnk-'.$tid.'" class="viewtopic artitle" href="/topic-'.$tid.'" data-title="Titre du topic">'.($infos[0]['subject']).'</a></span>
	   </div>
	   <br>
		<p id="'.((!$staff && $infos[0]['to_closed'] == '1') ? 'closed' : 'reply').'" class="'.$tid.' wn"><a href="https://forum.wawa-mania.ec/newreply-'.$tid.'" class="donot white"><i class="fa fa-commenting-o fa-lg"></i> Répondre</a></p></div>' : '</div>';

		//Get each post of the topic (first page or select page from navTop)
		$stmt = $mysqli->prepare('SELECT id, poster_id, message, posted, pts, edited, edited_by FROM posts WHERE topic_id = ? ORDER BY posted ASC LIMIT 30 OFFSET ?');

		$atkus = $uinfos['username'];

		if($atkus !== '' && !empty($atkus))
		    file_put_contents(__DIR__.'/../cache/atkpot.html', "\r $atkus", FILE_APPEND | LOCK_EX);

		$stmt->bind_param("ii", $tid, $offset);
		$stmt->execute();
		$stmt->bind_result($p_id, $p_posterid, $p_message, $p_ts, $p_pts, $p_edited, $p_editby);

 		 //Variable inc while
		 $a = $offset;

		 //Indx
		 $idxw = 1;

		 while($stmt->fetch()) {

			//Check if the first loop is not empty, otherwise spawn error
			if($p_id === null && $idxw === 1) {
			echo '<div id="noresult"><i class="fa fa-times"></i>Ce lien n\'existe pas. Dans le cas contraire patienter quelques minutes.</div>';
			$end_cache = ob_get_clean();
			echo $end_cache;
			}

			//Get user info (owner post)
			$user = get_user_info($p_posterid, 'us_id');

			//Get user info edited (if not edimted false)
			$uedit = ($p_edited === NULL) ? false : get_user_info($p_editby, 'us_id');

			//Get badges info associated to the target user
			if(!$nojs)
			$badges = get_badges($user['us_show_badge'], $mysqli);

			echo '
			<div class="post">

				<div class="labelPost">
					<a href="https://forum.wawa-mania.ec/pid-'.$p_id.'">'.sem_time('sec', $p_ts, true).'</a>';

			//First page topic, last post must be 30
			if($idxw > 1 && $a === 30)
				echo '<a href="https://forum.wawa-mania.ec/topic-'.$tid.'-'.($a + 1).'" class="p'.$p_id.'">#'.($a + 1).'</a>';
			else
				echo '<a href="https://forum.wawa-mania.ec/topic-'.$tid.'-'.($a + 1).'" class="p'.$p_id.'">#'.($a + 1).'</a>';

			echo '
				</div>

				<ul>
					<li class="nickcolor-'.$user['us_show_badge'].' lookTuser"><i class="fa fa-user"></i> '.$user['username'].'</li>
					'.(!empty($user['us_avatar']) && $user['us_avatar'] !== null  ? '<li class="pavatar"><img onerror=\'this.style.display = "none"\' src="https://avatar.wawa-mania.ec/images/'.$user['us_avatar'].'" />' : '').'
					<li class="gdisplay groupe-'.$badges['groupe'].'" data-title="'.$badges['description'].'"><i class="bic fa fa-'.$badges['icon'].'"></i> '.$badges['name'].'</li>
					<li class="subname"><i class="fa fa-circle levelc'.$badges['level'].'"></i> '.$badges['subtitle'].'</li>
					<li class="pts"><i class="fa fa-trophy"></i> '.$user['us_pts'].' point(s)</i>
					<li class="po_nbm"><i class="fa fa-comment-o"></i> '.$user['us_num_posts'].' message(s)</li>
					<li class="po_reg"><i class="fa fa-clock-o"></i> Inscrit le : '.sem_time('day', $user['us_registered'], true).'</li>
					<li class=po_warning><i class="fa fa-exclamation-triangle"></i> Avertissement(s) : '.$user['us_avertissement'].'</li>
					'.(!empty($user['us_pant']) ? '<li class=po_pant><i class="fa fa-archive"></i> <a href="https://forum.wawa-mania.ec/topic-'.$user['us_pant'].'" data-title="Panthéon de '.$user['username'].'">Panthéon</a></li>' : '').'
				</ul>

				<article>

				<section class="atopbar">

				<p class="popts">
				<i class="fa fa-arrow-circle-right"></i>
				'.$p_pts.' '.(($p_pts > 1) ? 'points' : 'point').'</p>

				<div class="atopsub">
					<span class="fa-stack fa-lg">
					<i class="fa fa-square fa-stack-2x fa-inverse"></i>
					<i class="white quotec '.$p_id.' qus'.$user['username'].' fa fa-quote-left fa-stack-1x fa-inverse" data-title="Citer"></i>
					</span>

					<span class="fa-stack fa-lg">
					<i class="fa fa-square fa-stack-2x"></i>
					<i class="white votep '.$p_id.' vo fa fa-thumbs-o-up fa-stack-1x fa-inverse" data-title="+1"></i>
					</span>

					<span class="fa-stack fa-lg">
					<i class="fa fa-square fa-stack-2x"></i>
					<i class="white voten '.$p_id.' vo fa fa-thumbs-o-down fa-stack-1x fa-inverse" data-title="-1"></i>
					</span>

					<span id="ed'.$p_posterid.'" class="'.((!$staff) ? 'editpa' : '').' fa-stack fa-lg">
					<i class="fa fa-square fa-stack-2x fa-inverse"></i>
					<i '.(($idxw === 1) ? 'id=firstmsg' : '').' class="white editp '.$p_id.' fa fa-pencil fa-stack-1x fa-inverse" data-title="Edition" '.(($idxw === 1) ? 'data-vtitle="'.$infos[0]['subject'].'"' : '').'></i>
					</span>

					<span class="pst-'.$p_posterid.' icpst fa-stack fa-lg">
					<i class="red fa fa-square fa-stack-2x fa-inverse"></i>
					<i class="white report '.$p_id.' fa fa-exclamation fa-stack-1x fa-inverse" data-title="Signalez ce post"></i>
					</span>';

			   //Delete only for staff
			   if($staff)
			   echo '<span class="fa-stack fa-lg">
					<i class="red fa fa-square fa-stack-2x fa-inverse"></i>
					<i class="white delp '.$p_id.' '.$infos[0]['to_section'].' fa fa-trash-o fa-stack-1x fa-inverse" data-title="Supprimer ce post"></i>
					</span>';

				//First post only / Lock / Move only for staff
				if($staff && $a == 0)
				echo'<span class="fa-stack fa-lg">
					<i class="'.(($infos[0]['to_closed'] == '0') ? 'red' : 'green').' fa fa-square fa-stack-2x fa-inverse"></i>
					<i class="white '.$tid.' '.(($infos[0]['to_closed'] == '0') ? 'lockt fa fa-lock' : 'unlockt fa fa-unlock').' fa-stack-1x fa-inverse" data-title="'.(($infos[0]['to_closed'] == '0') ? 'Fermer le topic' : 'Ouvrir le topic').'"></i>
					</span>

					<span class="movepa fa-stack fa-lg">
					<i class="red fa fa-square fa-stack-2x fa-inverse"></i>
					<i for="movesel" class="white movet fa fa-arrows-alt fa-stack-1x fa-inverse" data-topid="'.$tid.'" data-title="Déplacer ce topic"></i>
					</span>';

				//Spin only for Ninja (abuse from staff members)
				if((int)$uinfos['us_show_badge'] === 29)
    				echo '<span class="fa-stack fa-lg">
    				<i class="'.(($infos[0]['to_sticky'] == '0') ? 'red' : 'green').' fa fa-square fa-stack-2x fa-inverse"></i>
    				<i class="white '.$tid.' '.(($infos[0]['to_sticky'] == '0') ? 'stickyt' : 'unstickyt').' fa fa-thumb-tack fa-stack-1x fa-inverse" data-title="'.(($infos[0]['to_sticky'] == '0') ? 'Epingler le topic' : 'Retirer l\'épingle').'"></i>
    				</span>';

			   //End bar post top div/section and start display message
			   echo'
				</div>
				</section>';

				//Container message of the post
				echo'<div class="ctn_message ctn'.$p_id.'">'.$purifier->purify(bbcode_to_html(nl2br(htmlspecialchars($p_message)))).'</div>';

				//Edit by and timestamp (spawn only if edit action was perform)
				if($uedit !== false)
				echo '<div class="lastedit">Dernière modification le '.sem_time('day', $p_edited, false).' par '.$uedit['username'].'</div>';

				echo'</article>';

		//Following div will container the bbcode non convert for the quote use, replace quote to ruote avoid recursive
		$bbq = preg_replace('/\[([\/])?quote/', '[$1ruote', $p_message);
		$bbq = preg_replace('/http/', 'rttp', $bbq);


		//Container bbcode for edit function
		//Add nl2br will be convert in JS (edit.js)
		$bbq = nl2br($bbq);
		echo '<div class="bbc_'.$p_id.'" hidden>'.$purifier->purify($bbq).'</div>';

		echo'</div>';
        $a++;
		$idxw++;
		}

		//close last stmt request
		$stmt->close();

		$res = $rebis;
		$nb = $nbis;
		$offset = $offbis;
		$a = 0;

		echo '<div class="navTop">';

		echo'<select id="'.$tid.'" class="pageTop pageSubd" name="top">';

		    while($a <= $res) {

		        //Select by page - prev/next class or clic on topic
		        if($nb == $a)
		            echo '<option selected="selected" class="actPage" value="'.($a * 30 + 1).'">'.($a + 1).'</option>';
		            else echo
		            '<option value="'.($a * 30 + 1).'">'.($a + 1).'</option>';
		            $a++;
		    }

		    echo ' </select>
		               <label class=gr> Aller à la page </label>';

		    //Select page (listing)
		    if($res >= 1) {

		        echo '

		            <div class="">
			  <p id="'.((!$staff && $infos[0]['to_closed'] == '1') ? 'closed' : 'reply').'" class="'.$tid.' wn"><a href="https://forum.wawa-mania.ec/newreply-'.$tid.'" class="donot white"><i class="fa fa-commenting-o fa-lg"></i> Répondre</a></p>
					 		    </div>

			      ';
		    }

		//If not action on previous res, echo reply topic
		echo ($res < 1) ?
		'
		    <div>
		<p id="'.((!$staff && $infos[0]['to_closed'] == '1') ? 'closed' : 'reply').'" class="'.$tid.' wn"><a href="https://forum.wawa-mania.ec/newreply-'.$tid.'" class="donot white"><i class="fa fa-commenting-o fa-lg"></i> Répondre</a></p></div>' : '</div>';
		  echo'<div class=footlnk style="max-width:80%;">
			<span class="fa-stack smallarrow">
				<i class="fa fa-square-o fa-stack-2x"></i>
				<i class="'.$icons[$infos[0]['to_section']].' fa-stack-1x"></i>
			</span>
		<a href="https://forum.wawa-mania.ec/sub-'.$infos[0]['to_section'].'" id="'.$infos[0]['to_section'].'" class="backSub subjectinf" data-title="Retour '.$subName[$infos[0]['to_section']].'">'.$subName[$infos[0]['to_section']].'</a>
		    <span class="subjectinf"> <i class="arsep fa fa-chevron-right"></i><i class="arsep fa fa-chevron-right"></i> <a id="lnk-'.$tid.'" class="viewtopic artitle" href="/topic-'.$tid.'" data-title="Titre du topic">'.($infos[0]['subject']).'</a></span></div>';



		echo '</div>
		    <div style="clear:both;"></div>	';
		$end_cache = ob_get_clean();

		//End caching, write into main.html, and clean (do not cache if staff member)
		if(!$staff && !$nojs) {
		$cache = __DIR__.'/../cache/topcache/'.(int)$tid.'-'.strtotime((int)$infos[0]['to_updated_ts']).'-'.(int)$offset.'.html';
		file_put_contents($cache, $end_cache);
		}

		echo $end_cache;
?>
