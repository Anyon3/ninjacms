<?php

function check_vote($id, $pid, $act, $com, $mysqli) {

	//Make sure the user still have vote / badge or authorized to vote
	$stmt = $mysqli->prepare('SELECT pts, vote_left, badges FROM users WHERE id = ?');
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($pts, $vote, $badges);
	$stmt->fetch();
	$stmt->close();


	//Cut badge string
	$bcut = explode('-', $badges);

	//Get badges infos
	$cache = __DIR__."/../cache/badges.php";

	//Get level badges
	$contents = file_get_contents($cache);
	$level = json_decode($contents, true);

	//Check badges
	foreach($bcut as $value) {
	if($level[$value]["level"] === 0) $p0 = true;
	elseif($level[$value]["level"] === 5) $p5 = true;
	elseif($level[$value]["level"] === 4) $p4 = true;
	elseif($level[$value]["level"] === 3) $p3 = true;
	elseif($level[$value]["level"] === 2) $p2 = true;
	elseif($level[$value]["level"] === 1) $p1 = true;
	}

	//if the user got the level 1 / 2 / 3 / 4, minimum respected
	if(isset($p1))
		$bool = true;

	elseif(isset($p2))
		$bool = true;

	elseif(isset($p3))
		$bool = true;

	elseif(isset($p4))
		$bool = true;

	elseif(isset($p5))
        return 'f6';

    else
        return false;

	//Exit, not authorized
	if(!isset($bool)) return 'f1';

	//If no more vote left
	if($vote === 0) return 'f2';

	//Check if voter did not already vote for this post
	$stmt = $mysqli->prepare('SELECT voter_id FROM posts_pts WHERE post_id = ? AND voter_id = ?');
	$stmt->bind_param("si", $pid, $id);
	$stmt->execute();
	$stmt->bind_result($checkal);
	$stmt->fetch();
	$stmt->close();

	//No more vote left
	if($checkal === $id) return 'f3';

	//Check if the voter do not own this post
	//Check if the post has at least 1 pts
	//Get extra infos for shorter following SQL (pid / tid)

	$stmt = $mysqli->prepare('SELECT poster_id, topic_id, pts FROM posts WHERE id = ?');
	$stmt->bind_param("i", $pid);
	$stmt->execute();
	$stmt->bind_result($uid, $tid, $po_pts);
	$stmt->fetch();
	$stmt->close();

	//Own post
	if($uid === $id) return 'f4';

	//Post has not vote and negative action
	if($po_pts === 0 && $act === 0) return 'f5';

	//Update post and topic on first pid
	if($act === '1')
		$stmt = $mysqli->prepare('UPDATE posts SET pts = pts + 1 WHERE id = ?');
	else
		$stmt = $mysqli->prepare('UPDATE posts SET pts = pts - 1 WHERE id = ?');
	$stmt->bind_param('i', $pid);
	$stmt->execute();
	$stmt->close();

	//Update topic pts (only if pid was first post)
	if($act === '1')
		$stmt = $mysqli->prepare('UPDATE topics SET pts = pts + 1 WHERE first_post_id = ?');
	else
		$stmt = $mysqli->prepare('UPDATE topics SET pts = pts - 1 WHERE first_post_id = ?');
	$stmt->bind_param('i', $pid);
	$stmt->execute();
	$stmt->close();

	//Update poster id pts
	if($act === '1')
		$stmt = $mysqli->prepare('UPDATE users SET pts = pts + 1 WHERE id = ?');
	else
		$stmt = $mysqli->prepare('UPDATE users SET pts = pts - 1 WHERE id = ?');
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->close();

	//Insert final infos
	$stmt = $mysqli->prepare('INSERT INTO posts_pts (topic_id, post_id, poster_id, voter_id, action, comment) VALUES(?,?,?,?,?,?)');
	$stmt->bind_param("iiiiis", $tid, $pid, $uid, $id, $act, $com);
	$stmt->execute();
	$stmt->close();

	//Remove 1 vote to the voter
	$stmt = $mysqli->prepare('UPDATE users SET vote_left = vote_left - 1 WHERE id = ?');
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->close();

	return 'fok';
} //End check_vote

//Check new topic function
function check_topic($id, $sectionid, $mysqli) {

	//Make sure the user have the minimim requierement
	$stmt = $mysqli->prepare('SELECT show_badge FROM users WHERE id = ?');
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($badges);
	$stmt->fetch();
	$stmt->close();

	//Get level badges
	$contents = file_get_contents(__DIR__."/../cache/badges.php");
	$level = json_decode($contents, true);

	//Level targeted
    if((int)$level[$badges]["level"] === 5)
    	 $p5 = true;

    elseif((int)$level[$badges]["level"] === 4)
    	   $p4 = true;

    elseif((int)$level[$badges]["level"] === 3)
    	   $p3 = true;

    elseif((int)$level[$badges]["level"] === 2)
    	   $p2 = true;

    elseif((int)$level[$badges]["level"] === 1)
    	   $p1 = true;
    else
       return 'p1';

	//Make sure the session id exist
	$stmt = $mysqli->prepare('SELECT cat_id FROM sections WHERE id = ?');
	$stmt->bind_param("i", $sectionid);
	$stmt->execute();
	$stmt->bind_result($cid);
	$stmt->fetch();
	$stmt->close();

	//Section exclue
	//Must have the badges of group 5 (uploader groupe)
	if((int)$sectionid === 45) {

        if((int)$level[$badges]["groupe"] === 5)
            $g5 = true;
        else
            return 'p1';
	}

	//Section café
	//Must have the badges level 4 minimum
	if((int)$sectionid === 4) {

	    if(!isset($p4) && !isset($p3) && !isset($p2) && !isset($p1))
	        return 'p1';
	}

	//Must have the badges level 4 minimum
	if($cid === 7 || $cid === 8)
	    if(!isset($p4) && !isset($p3) && !isset($p2) && !isset($p1)) return 'p1';

	//Return level
	if(isset($p2))
	    return '2';

	elseif(isset($p3))
	   return '3';

	elseif(isset($p4))
        return '4';

	elseif(isset($p5))
	   return '5';

	else
        return false;
}
//End new topic check

//Check reply function
function check_reply($id, $tid, $mysqli) {

	//Make sure the user have the minimim requierement
	$stmt = $mysqli->prepare('SELECT show_badge FROM users WHERE id = ?');
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($badges);
	$stmt->fetch();
	$stmt->close();

	//Get level badges
	$contents = file_get_contents(__DIR__."/../cache/badges.php");
	$level = json_decode($contents, true);

    //Level targeted
    if((int)$level[$badges]["level"] === 5)
    	 $p5 = true;

    elseif((int)$level[$badges]["level"] === 4)
    	   $p4 = true;

    elseif((int)$level[$badges]["level"] === 3)
    	   $p3 = true;

    elseif((int)$level[$badges]["level"] === 2)
    	   $p2 = true;

    elseif((int)$level[$badges]["level"] === 1)
    	   $p1 = true;
    else
       return 'p1';

	//Get section id of the topic
	$stmt = $mysqli->prepare('SELECT section, closed FROM topics WHERE id = ?');
	$stmt->bind_param("i", $tid);
	$stmt->execute();
	$stmt->bind_result($sectionid, $closed);
	$stmt->fetch();
	$stmt->close();

	//No right to post on this section (rules and informations)
	if((int)$sectionid === 1)
	    return 'p1';

	//Section café
	//Must have the badges level 4 minimum
	if((int)$sectionid === 4) {

		if(!isset($p4) && !isset($p3) && !isset($p2) && !isset($p1) && (int)$uinfos['us_show_badge'] !== 11)
		    return 'p1';
	}

	//topic is closed
	if($closed === 1)
	    return 'p1';

	//Return level
	if(isset($p2))
	    return '2';

	elseif(isset($p3))
	   return '3';

	elseif(isset($p4))
	   return '4';

	elseif(isset($p5))
	   return '5';
}

function check_edit($id, $pid, $title, $mysqli) {

	//Make sure the user have the minimim requierement
	$stmt = $mysqli->prepare('SELECT p.id, p.poster_id, p.topic_id, t.id, t.first_post_id, t.closed FROM posts p JOIN topics t ON p.topic_id = t.id WHERE p.id = ?');
	$stmt->bind_param("i", $pid);
	$stmt->execute();
	$stmt->bind_result($pidsql, $posterid, $ptid, $tid, $fpid, $close);
	$stmt->fetch();
	$stmt->close();

	//Check if the poster own this topic & not close
	if((int)$id !== (int)$posterid || (int)$close === 1) return 'p1';

	//If title is set for update, check the perm
	if($title !== false) {
		if((int)$fpid !== (int)$pid) return 'p1';
	}
}

//Check report function
function check_report($id, $pid, $sig, $mysqli) {

	//Check if this post is not already reported by someone
	$stmt = $mysqli->prepare('SELECT post_id FROM report WHERE post_id = ? AND status = 0');
	$stmt->bind_param("i", $pid);
	$stmt->execute();
	$stmt->bind_result($check);
	$stmt->fetch();
	$stmt->close();

	//If already reported, exit
	if(!empty($check))
	    return 'f1';

	//Get badges of the logued user
	$stmt = $mysqli->prepare('SELECT show_badge FROM users WHERE id = ?');
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($badges);
	$stmt->fetch();
	$stmt->close();

	//Get level badges
	$contents = file_get_contents(__DIR__."/../cache/badges.php");
	$level = json_decode($contents, true);

    //Level targeted
    if((int)$level[$badges]["level"] === 5)
    	 $p5 = true;

    elseif((int)$level[$badges]["level"] === 4)
    	   $p4 = true;

    elseif((int)$level[$badges]["level"] === 3)
    	   $p3 = true;

    elseif((int)$level[$badges]["level"] === 2)
    	   $p2 = true;

    elseif((int)$level[$badges]["level"] === 1)
    	   $p1 = true;
    else
       return 'p1';

	//if the user got the level 1 / 2 / 3 add without flood check
	if(isset($p1))
		if($p1) $flood = true;

	if(isset($p2))
		if($p2) $flood = true;

	if(isset($p3))
		if($p3) $flood = true;

	if(isset($p4))
		if($p4) $flood = false;

	if(isset($p5))
		if($p5) $flood = false;

	//If flood false check and limit the number of report
	if(!$flood) {

		//Max report send that has not yet being check
		$limit = ($p5) ? 5 : 2;

		//Prepare SQL
		$stmt = $mysqli->prepare('SELECT id FROM report WHERE poster_id = ? AND status = 0');
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->store_result();

		//If >= number of rows, exit
		if($limit <= $stmt->num_rows)
		    return 'f2';

		$stmt->close();
	}

	//Get $tid, $uid
	$stmt = $mysqli->prepare('SELECT p.id, p.topic_id, p.poster_id, t.subject, t.section FROM posts p JOIN topics t ON t.id = p.topic_id WHERE p.id = ?');
	$stmt->bind_param("i", $pid);
	$stmt->execute();
	$stmt->bind_result($npid, $tid, $uid, $subj, $sect);
	$stmt->fetch();
	$stmt->close();

	//Insert the new report
	$stmt = $mysqli->prepare('INSERT INTO report(subj, topic_id, section, post_id, poster_id, report_id, reason) VALUES(?,?,?,?,?,?,?)');
	$stmt->bind_param("siiiiis", $subj, $tid, $sect, $npid, $uid, $id, $sig);
	$stmt->execute();
	$stmt->close();

	return 'fok';
}

//Get information about the target
//Function use for check if the target exist
//Use in the admin on the displayed information block
function admc_uid($uid, $mysqli) {

	//Get badges target user
	$stmt = $mysqli->prepare('SELECT badges, email FROM users WHERE id = ?');
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($badges, $chban);
	$stmt->fetch();
	$stmt->close();

	//Cut badge string
	$bcut = explode('-', $badges);

	//Get badges infos
	$cache = __DIR__.'/../cache/badges.php';

	//Get level badges
	$contents = file_get_contents($cache);
	$level = json_decode($contents, true);

	//Check badges
	foreach($bcut as $value) {
	if($level[$value]["level"] === 0) $p0 = true;
	elseif($level[$value]["level"] === 5) $p5 = true;
	elseif($level[$value]["level"] === 4) $p4 = true;
	elseif($level[$value]["level"] === 3) $p3 = true;
	elseif($level[$value]["level"] === 2) $p2 = true;
	elseif($level[$value]["level"] === 1) $p1 = true;
	}

	//Not allowing the edit of staff members
	if(isset($p1)) return false;

	//Not allowing the dit of ban members
	if($chban === 'bans') return false;

	return true;
}

//Check trade verified pass
function check_trck($godfather, $username, $mysqli) {

    //Check in SQL if the user got enought pts
    $stmt = $mysqli->prepare('SELECT pts FROM users WHERE id = ?');
    $stmt->bind_param("i", $godfather);
    $stmt->execute();
    $stmt->bind_result($pts);
    $stmt->fetch();
    $stmt->close();

    if((int)$pts < 5)
      return 'ptsless';

    //Check in SQL if the user got enought pts
    $stmt = $mysqli->prepare('SELECT id, show_badge FROM users WHERE username = ?');
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nid, $badge);
    $stmt->fetch();
    $stmt->close();


    if((int)$badge !== 28)
      return 'no';
    else
        return $nid;
}

function check_trcy($uid, $mysqli) {

    //Check in SQL if the user got enought pts
    $stmt = $mysqli->prepare('SELECT pts, badges FROM users WHERE id = ?');
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($pts, $badges);
    $stmt->fetch();
    $stmt->close();

    if((int)$pts < 10)
        return 'ptsless';

    //Cut badge string
    $bcut = explode('-', $badges);

    foreach($bcut as $bd) {
        if((int)$bd === 11 || (int)$bd === 12 || (int)$bd === 13 || (int)$bd === 14)
           return 'bad';
    }

    $badges = "$badges-11";

    return $badges;
}

//Send MP
function check_smp($username, $mysqli) {

        //Check the target
        $stmt = $mysqli->prepare('SELECT account, ip FROM waf_security WHERE account = ? AND action = "mps"');
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($account, $nmp);
        $stmt->fetch();
        $stmt->close();

        //Add daily limit of mps to the WAF
        if(empty($account)) {
            $stmt = $mysqli->prepare('INSERT INTO waf_security(account, ip, action) VALUES(?,"1","mps")');
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();

            return 'ok';
        }

        //Check if limit is not reach and update
        elseif((int)$nmp <= 50) {
            $stmt = $mysqli->prepare('UPDATE waf_security SET ip = ip + 1  WHERE account = ? AND action = "mps"');
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();

            return 'ok';
        }

        elseif((int)$nmp >= 50)
            return 'limit';

        else
            return 'error';
}

?>