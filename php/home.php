<?php

if(!isset($nojs) || !$nojs)
    require('funcwm.php');

	//Load from the cache or create it
	if(file_exists(__DIR__.'/../cache/maincache/main.html')) {
	$parse = file_get_contents(__DIR__.'/../cache/maincache/main.html');
	echo $parse;
	}

	//Generate the home page forum and cache it
	else {

	$sql = $mysqli->query('SELECT s.id, s.id sid, s.name sname, s.description, s.last_post_id, s.num_topics, s.num_posts,
						   s.disp_position, s.last_poster_id, s.cat_id,
						   c.id, c.id cid, c.name cname, c.disp_position,
						   p.message,
						   t.id, t.id tid, t.subject, t.last_poster_id lastt, t.last_post_id, t.last_post_id AS lpid, t.last_post_ts, t.num_replies
						   FROM sections s
						   JOIN categories c ON s.cat_id = c.id
						   JOIN posts p ON s.last_post_id = p.id
						   JOIN topics t ON s.last_post_id = t.last_post_id
						   WHERE s.id>=1 AND s.id<=100
						   AND s.id NOT IN (61,62,78,24,54)
						   ORDER BY c.disp_position, s.disp_position ASC');

	if(!$sql) exit;
	$checkCat = array();
	$bool = false;

	//Categorie array for the block color
	$catcolor = [16 => 'hp_talkzone',
	             14 => 'hp_it',
	             6  => 'hp_movie',
	             7  => 'hp_app',
	             8  => 'hp_game',
	             9  => 'hp_music',
	             12 => 'hp_misc',
	             1  =>  ''];

	//Start caching
	ob_start();

	while($arr = $sql->fetch_array(MYSQLI_ASSOC)) {
		$infos[] = $arr;
	}

	foreach($infos as $display) {

	//Get user info
	$user = get_user_info($display['lastt'], 'u_id');

	//Check if cat_name was not already displayed
	if(!in_array($display['cid'], $checkCat)) {

	//Close the tag section on new section, first inherit do not spawn this end tag -> bool = false
	if($bool === true)
	   echo '</section>';

	echo '
	<section class="seHome" role="main" itemscope itemtype="http://schema.org/ItemList">

		<div class="labelHome '.$catcolor[$display['cid']].'">
			<label itemprop="name">'.$display['cname'].'</label>
			<span>Discussions</span>
			<span>Messages</span>
			<span>Dernier message</span>
		</div>';

	//Add the cat id to the array
	$checkCat[] = $display['cid'];

	//First inherit passed, next loop will close this.
	$bool = true;
	}

	//If the subsection catID == c.id
	if($display['cat_id'] == $display['cid'])

	$lastpo = $display['num_replies'] + 1;

	echo '

		<div class="containerHome" itemprop="itemListElement">
			<div id="'.$display['sid'].'" class="lefthome" itemscope itemtype="https://schema.org/Thing">
				<i class="'.$icons[$display['sid']].' fa-2x"></i>
				<h2 itemprop="name"><a href="https://forum.wawa-mania.ec/sub-'.$display['sid'].'" itemprop="url" title="'.$display['sname'].'">'.$display['sname'].'</a></h2>
				<p itemprop="description">'.$display['description'].'</p>
			</div>
			<div class="midHome">
				<p itemprop="numberOfItems">'.$display['num_topics'].'</p>
			</div>
			<div class="midHome">
				<p itemprop="numberOfItems">'.$display['num_posts'].'</p>
			</div>
			<div class="righthome">
				<i id="last-'.$display['tid'].'" class="preview fa fa-eye fa-lg"></i>
				<a id='.$display['lpid'].' href="pid-'.$display['lpid'].'" class=pidPost>'.$purifier->purify($display['subject']).'</a>
				<p class="pTime">Posté '.sem_time('sec', $display['last_post_ts'], true).'</p>
				<p class="nickcolor-'.$user['us_show_badge'].' lookTuser" data-title="Message(s) : '.$user['us_num_posts'].' <br> Inscrit le : '.sem_time('day', $user['us_registered'], false).' <br /> Avertissement(s) : '.$user['us_avertissement'].'"> '.$user['username'].'</p>
			</div>
		</div>';

		} //Foreach end

		//after last inherit, close last section
		echo '</section>';

		//End caching, write into main.html, and clean
		$end_cache = ob_get_clean();

		file_put_contents(__DIR__.'/../cache/maincache/main.html', $end_cache);

		echo $end_cache;

	  }
?>