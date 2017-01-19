<?php
require('funcwm.php');

//Sphinx API
$limit = 20;
$sph_port = 9312;
$sph_host = "localhost";
$cl = new SphinxClient();
$cl->SetServer($sph_host, $sph_port);

//Offset must be lower to 200 (maximum result)
$offset = (isset($_POST['startfrom']) && is_numeric($_POST['startfrom']) && $_POST['startfrom'] < 200) ? $_POST['startfrom'] : 0;
//Words
$k = (isset($_POST['search'])) ? $_POST['search'] : '';
//Filter - Author / Subject
$filter = (isset($_POST['filter'])) ? $_POST['filter'] : 'subject';
//ASC - DESC
$sort = (isset($_POST['sort'])) ? $_POST['sort'] : 'desc';
//target sb
$sub = (isset($_POST['sub'])) ? $_POST['sub'] : 'all';
//By topicid or last post id
$by = (isset($_POST['by'])) ? $_POST['by'] : 'to_id';

$cl->SetLimits((int)$offset,(int)$limit,200,0);

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

	else
		$sub = 'all'; // <- any

	if($sort == 'desc') $cl->SetSortMode(SPH_SORT_ATTR_DESC, $by); //New to old

	else $cl->SetSortMode(SPH_SORT_ATTR_ASC, $by); //Old to new

	//Escape string consider as special operator
	$k = $cl->EscapeString($k);

	//Execute sphinx query
	if($filter == 'poster')
		$result = $cl->Query('','main mdelta'); // <- By author topic

	else
		//$cl->SetRankingMode(SPH_RANK_MATCHANY);
		$result = $cl->Query($k,'main mdelta'); // <- By keywords (associated filter / sort)

	//Index running
	if(!$result)
        exit ('<div id="noresult"><i class="fa fa-times"></i>Indexage en cours... Réesayez dans quelques minutes</div>');

	elseif(!empty($result["matches"]))

	    foreach ($result["matches"] as $display => $info) {
	       $arr[] = $result["matches"][$display]['attrs'];
       }

	//Empty
	else
        exit('<div id="noresult"><i class="fa fa-times"></i>Aucun résultat ne correspond à votre recherche. Veuillez réessayer avec d\'autres termes</div>');


	if(!isset($_POST['resu'])) {

        $result['total_found'] = ($result['total_found'] > 200) ? 200 : $result['total_found'];

        echo '<i id="nbrow" class="fa fa-file-o '.$sub.'" role="complementary"><p>'.$result['total_found'].' résultat trouvé</p></i><span id="getmore" style="display:none;">'.$k.'</span>';
	}

	foreach($arr as $search_arr) {

		//Set the offset last_post
		$res = $search_arr['to_num_replies'] / 30;
		$res = floor($res);

		$a = ($res >= 30) ? 0 : $res*30;

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
				<a id="go-'.$search_arr["to_id"].'" href="topic-'.$search_arr["to_id"].'" class="shref viewtopic" target=_blank> Accéder au topic</a>
			</span>' : '').'
			<span class="timestamp">Crée '.$timestamp.'</span>
			<a href="'.(($is_connected) ? 'topic-'.$search_arr['to_id'].'-'.$search_arr['to_num_replies'].' ' : '/login').'" class="shref"> Dernier message à '.$timelast.'</a>
		</div>
		<div class="leftInfo">
			<span class="title_sort"><i class="'.$icons[$search_arr["to_section"]].'"></i> '.$search_arr["subject"].'</span>
			<span class="categories_sort">'.$subName[$search_arr["to_section"]].'</span>
			<span class="nickcolor-'.$user['us_show_badge'].' byWho">Par '.$user['username'].'</span>
		</div>
		<div style="clear:both"></div>
	</section>

	'.(($is_connected) ? ' <article class="container-body" style="display:none;">'.$purifier->purify(bbcode_to_html(nl2br($search_arr["po_message"]))).'</article> ' :
	                      '<article class="container-body" style="display:none;">Vous devez être connecté pour afficher le contenu de ce message</article>').'
	';
	}
?>