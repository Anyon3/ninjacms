<?php

//Write the post param for NOjs
if($nojs) {

    //Get the URI
    $dataUri = str_replace('/', '', $_SERVER['REQUEST_URI']);

    //[1] section | [2] (optional) offset
    $data = explode('-', $dataUri);

    //If the URI is not valid
    if(!is_numeric($data[1])) header('Location:https://forum.wawa-mania.ec');

    //Settings $_POST
    $_POST['fid'] = $data[1];
    $_POST['offset'] = (is_numeric($data[2])) ? $data[2] : '0';
}
//End Nojs

//If javascript enabled
else
    require(__DIR__.'/funcwm.php');

$fid = (!isset($_POST['fid']) || !is_numeric($_POST['fid']) ? exit : (int)$_POST['fid']);  // sub id / forum id...
$offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] !== '0') ? $_POST['offset'] : '0'; //Offset

//Make sure the modulus JS was not overwrite
if($offset !== '0') {
$roff = $offset % 50;
$offset = $offset - $roff;
}

	//Check if fid exist
	if(!array_key_exists($fid, $subName))
	    exit;

	$cache = __DIR__.'/../cache/section/sub-'.$fid.'-'.$offset.'.html';

	//Load from the cache or create it
	if(file_exists($cache)) {
		$parse = file_get_contents($cache);
		echo $parse;
	}

	else {

	//Sphinx API
	$sph_port = 9312;
	$sph_host = "localhost";
	$cl = new SphinxClient();
	$cl->SetServer($sph_host, $sph_port);


	//remove viewforum ID - 61 62 78 24 54 2 (useless or private)
	$cl->SetFilter('to_section', array(61, 62, 78, 24, 54), true);

	//Target categorie
	$cl->SetFilter('to_section', array($fid));

	$downsec = [16,45,5,35,42,46,6,81,56,58,7,40,51,41,71,79,9,47,48,8,49,36,44,20,70,27];

	//First page section : sticky + vote in Download section Or sticky only not Download section
	if ($offset < 49 ) {

		$cl->SetLimits((int)$offset,55,150000,0);

		//If section DVD, then : sticky, higher vote, last_ts
		if(in_array($fid, $downsec))
		  $cl->SetSortMode(SPH_SORT_EXTENDED,'to_sticky DESC, to_moderate DESC, to_pts DESC, to_last_post_id DESC');

		//Omit vote
		else

		  $cl->SetSortMode(SPH_SORT_EXTENDED,'to_sticky DESC, to_last_post_id DESC');
	}

	//Otherwise omit sticky
	else {

	    $cl->SetLimits((int)$offset,50,150000,0);
	    $cl->SetFilter('to_sticky', array(1), true);

	    //If section DVD, then : sticky, higher vote, last_ts
	 		//If section DVD, then : sticky, higher vote, last_ts
	    //If section DVD, then : sticky, higher vote, last_ts
	   	if(in_array($fid, $downsec))

	        $cl->SetSortMode(SPH_SORT_EXTENDED,'to_moderate DESC, to_last_post_id DESC');

	    else
	        $cl->SetSortMode(SPH_SORT_EXTENDED,'to_last_post_id DESC');
    }

	$result = $cl->Query('','main mdelta');

	//Index running
	if($result === false) {
		echo '<div id="noresult"><i class="fa fa-times"></i>Indexage en cours... Réesayez dans quelques minutes</div>';
		return false;
	}

	else {

		if(!empty($result["matches"]))
		foreach ($result["matches"] as $display => $info) {
			$infos[] = $result["matches"][$display]["attrs"];
		}

		//Empty
		else {
			echo '<div id="noresult"><i class="fa fa-times"></i>Aucun résultat ne correspond à votre recherche. Veuillez réessayer avec d\'autres termes</div>';
			return false;
		}
	}

	$res = $result['total_found'] / 50;
	$res = floor($res);
	$resbis  = $res;

	$a = 0;

	$nb = (isset($offset) ? $offset / 50 : 0);
	$nb = floor($nb);
	$nbis  = $nb;

	$brLoop = 1;

	//Start cache
	ob_start();

	echo'
		<div class="navSub">
			<p class="right">
				'.($offset > 49 ? '
				<a href="https://forum.wawa-mania.ec/sub-'.$fid.'-'.($offset - 50).'" data-idn="'.$fid.'-'.($offset - 50).'" class="prevSub fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i>
					<i class="fa fa-hand-o-left fa-stack-1x" title="Page précédente"></i>
				</a>' : '').'

			   '.($nb < $res ? '
				<a href="https://forum.wawa-mania.ec/sub-'.$fid.'-'.($offset + 50).'" data-idn="'.$fid.'-'.($offset + 50).'" class="nextSub fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i>
					<i class="fa fa-hand-o-right fa-stack-1x" title="Page suivante"></i>
				</a>' : '').'
			</p>

			<p class="left">
				<a href="https://forum.wawa-mania.ec" class="backHome fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i>
					<i class="fa fa-home fa-stack-1x" title="Retour sur l\'index"></i>
				</a>
			</p>

		      '.(($fid === 84) ?
		     '<p class="left">
    		      <span id=cltool class="fa-stack fa-lg" data-title="CleanTool">
    		      <i class="fa fa-square-o fa-stack-2x"></i>
    		      <i class="fa fa-list fa-stack-1x"></i>
		      </span>
		      </p>' : '').'
			<div class="pageSub">
			<p class=wi><a id=button_newto href="newtopic-'.$fid.'" data-bnt="'.$fid.'" class=wi><i class="fa fa-commenting-o fa-lg"></i> Nouveau topic</a></p>

			<label>Aller à la page</label>
			<select class="pageSub" name="sub" data-idt="'.$fid.'">';

				while($a <= $res) {
					if($nb == $a)
						echo '<option selected="selected" class=actPage value="'.($a * 50).'">'.($a + 1).'</option>';
					else
						echo '<option value="'.($a * 50).'">'.($a + 1).'</option>';
					$a++;
				}
			echo'
			</select>
			</div>
		</div>

		<section class="seSub" role="main" itemscope itemtype="http://schema.org/ItemList">

			<div class=labelsub>
				<h2 itemprop="name">'.$subName[$infos[0]['to_section']].'</h2>
				<span>Réponses</span>
				<span>Dernier message</span>
			</div>
		';
	foreach($infos as $display) {

		//First poster
		$user 	 = get_user_info($display['to_first_poster_id'],'us_id');

		//Last poster
		$userbis = get_user_info($display['to_last_poster_id'],'us_id');

		 //$roft = $display['to_num_replies'] % 30;
		 //$offt = $display['to_num_replies'] - $roft;
		 $lastpo = $display['to_num_replies'] + 1;

		 //Number of page and default for the loop
		 $cal = (int)$display['to_num_replies'] / 30;
		 $nbpage = ceil($cal);
		 $dfpage = 0;

  echo '
		<section class="containerSub" itemprop="itemListElement">
			<div class="leftsub"  itemscope itemtype="https://schema.org/Thing">
				<i id="first-'.$display['to_id'].'" class="preview fa fa-eye fa-lg" data-title="Prévisualisation du premier post"></i>
				<a href="https://forum.wawa-mania.ec/topic-'.$display['to_id'].'" id="go-'.$display['to_id'].'" class="viewtopic" itemprop="url">
					<i class="'.(($display['to_sticky'] === 1) ? "stickyPost" : "normalPost").' '.(($display['to_closed'] === 1 && $display['to_sticky'] !== 1) ? "closePost" : '').' fa fa-folder fa-2x"></i>
					<h3 itemprop="name">'.$purifier->purify($display['subject']).'</h3>
				</a>
				'.($display['to_pts'] > 0 ? '<span class="tovt">+ '.$display['to_pts'].'</span>' : '').'
				<p class="nickcolor-'.$user['us_show_badge'].' lookTuser" data-title="Message(s) : '.$user['us_num_posts'].' <br> Inscrit le : '.sem_time('day', $user['us_registered'], false).' <br /> Avertissement(s) : '.$user['us_avertissement'].'">'.$user['username'].'</p>
				';

            //Check how many page for the topic and display if needed (1 page only won't display anything
             if($nbpage > 1) {

                //First echo the container
                echo '<p class=pgtop ><i class="fa fa-list-ol" data-title="Page"></i>';

                while($dfpage < $nbpage) {

                   $dfpage++;
                   $result = $dfpage * 30 - 29;

                   if($dfpage > 10) {
                       $resfinal = $nbpage * 30 - 29;
                       echo' ... <a href="topic-'.$display['to_id'].'-'.$resfinal.'" id="go-'.$display['to_id'].'-'.$resfinal.'" class=viewtopic> '.$nbpage.'</a>';
                       break;
                   }

                   else
                        echo '<a href="topic-'.$display['to_id'].'-'.$result.'" id="go-'.$display['to_id'].'-'.$result.'" class=viewtopic> '.$dfpage.'</a>';

                }

                //Close the container
                echo'</p>';
            }
            echo '
	 		</div>
			<div class="midSub">
				<p itemprop="numberOfItems">'.$display['to_num_replies'].'</p>
			</div>
	 		<div class="rightsub">
				<i id="last-'.$display['to_id'].'" class="preview fa fa-eye fa-lg" data-title="Prévisualisation du dernier post"></i>
	 			<a id='.$display['to_last_post_id'].' href="pid-'.$display['to_last_post_id'].'" class=pidPost> Posté '.sem_time('sec', $display['to_last_post_ts'], true).'</a>
				<p class="nickcolor-'.$userbis['us_show_badge'].' lookTuser" data-title="Message(s) : '.$userbis['us_num_posts'].' <br> Inscrit le : '.sem_time('day', $userbis['us_registered'], false).' <br /> Avertissement(s) : '.$userbis['us_avertissement'].'">'.$userbis['username'].'</p>
			</div>
		</section>';
		//<a href="tid-'.$display['to_id'].'-'.$display['to_last_post_id'].'"> Posté à '.sem_time('sec', $display['to_last_post_ts'], true).'</a>
		//Parse 50 topics maximum for the first page of each sub (topic post-it + 50 last updated)
		if($display['to_sticky'] !== 1) {
			$brLoop++;
			if($brLoop > 50) break;
		}
	}

		echo'</section>'; //End seSub
		$res = $resbis;
		$a = 0;

		$nb = $nbis;

		$brLoop = 1;

		echo'
		<div class="navSub">

			<select class="pageSubd pageSub" name="sub" data-idt="'.$fid.'">';

		while($a <= $res) {
		    if($nb == $a)
		        echo '<option selected="selected" class=actPage value="'.($a * 50).'">'.($a + 1).'</option>';
		        else
		            echo '<option value="'.($a * 50).'">'.($a + 1).'</option>';
		            $a++;
		}
		echo'
			</select>
		    <label class=gr> Aller à la page </label>

			<p class="left">
				    <p class=wi><a id=button_newto_bis href="newtopic-'.$fid.'" data-bnt="'.$fid.'" class=wi><i class="fa fa-commenting-o fa-lg"></i> Nouveau topic</a>
			</p>

			<div class="pageSub">

			  <p class=wi>
			        <a href="https://forum.wawa-mania.ec" class="backHome fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i>
					<i class="fa fa-home fa-stack-1x" title="Retour sur l\'index"></i>
				</a>
			   </p>

			    <p class="right">
				'.($offset > 49 ? '
				<a href="https://forum.wawa-mania.ec/sub-'.$fid.'-'.($offset - 50).'" data-idn="'.$fid.'-'.($offset - 50).'" class="prevSub fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i>
					<i class="fa fa-hand-o-left fa-stack-1x" title="Page précédente"></i>
				</a>' : '').'

			   '.($nb < $res ? '
				<a href="https://forum.wawa-mania.ec/sub-'.$fid.'-'.($offset + 50).'" data-idn="'.$fid.'-'.($offset + 50).'" class="nextSub fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i>
					<i class="fa fa-hand-o-right fa-stack-1x" title="Page suivante"></i>
				</a>' : '').'
			</p>

			</div>

		</div>
<div style="clear:both;"></div>
				    ';

		//End caching, write into main.html, and clean
		$end_cache = ob_get_clean();

		file_put_contents($cache, $end_cache);

		echo $end_cache;
	}
?>