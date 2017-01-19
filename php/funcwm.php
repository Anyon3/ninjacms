<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
date_default_timezone_set('Europe/Paris');
//Fixing PHP session_start bug -> Illegal chars or too long
$ok = @session_start();
if(!$ok){
session_regenerate_id(true); // replace the Session ID
session_start();
}

//mysql connection settings
require('db.php');

//Include sphinxapi
require('sphinxapi.php');

//Include purifier html (avoid xss or bug display by incorrect bbcode)
require_once 'library/HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();

//blacklist URL
$config->set('URI.HostBlacklist', array('shareimg.co','zone-telechargement.com','world-lolo.com','mkvcorporation.com','noelshack.com'));
$purifier = new HTMLPurifier($config);

//If cookie nojs set
$nojs = (isset($_COOKIE['nojs']) && $_COOKIE['nojs'] === 'nojs') ? true : false;

//If cookie available and $_SESSION['logwm'] unset, try to setup it
if(!isset($_SESSION['logwm']) && isset($_COOKIE['login']))
	restore_sess($mysqli);

//Set true (connected) / false (disconnected) on $_SESSION['logwm']
$is_connected = (isset($_SESSION['logwm'])) ? true : false;

//Set the information of the connected user to a varible (as number of posts, pts...)
$uinfos = ($is_connected) ? get_user_info($_SESSION['logwm'], 'us_id') : false;

//Check every 10 min the PM box
if($is_connected && (int)$uinfos['us_show_badge'] !== 28 && $uinfos['us_show_badge'] !== 36) {

    if(!file_exists(__DIR__.'/../cache/pm/'.$uinfos['us_id'].'.html') || filemtime(__DIR__.'/../cache/pm/'.$uinfos['us_id'].'.html') < time() - 600) {

        //Get the number of MP
        $stmt = $mysqli->prepare('SELECT id FROM mp WHERE receiver = ?');
        $stmt->bind_param("i", $uinfos['us_id']);
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->num_rows;
        $stmt->close();

        file_put_contents(__DIR__.'/../cache/pm/'.$uinfos['us_id'].'.html', $result);

    }

}

//Check if the user did not get ban, if yes unset cookie + session
if((int)$uinfos['us_show_badge'] === 36) {
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
}

//Is staff members
function staff($badge, $mysqli) {

	if((int)$badge === 1 || (int)$badge === 29)
	    return true;
	else
	   return false;
}

//Login
function login($username, $password, $mysqli) {

	$sph_connect = get_user_info($username, 'username');

	$userid = $sph_connect['us_id'];
	$userdb = $sph_connect['username'];
	$hash   = $sph_connect['us_password'];
	$badges = $sph_connect['us_show_badge'];

	//Not result
	if(empty($hash)) return '3';

	//Target user ban
	if((int)$badges === 36) return '7';

	//sha1 or md5 hash
	if(!preg_match('/^([\$])/', $hash)) {

		//Rewrite the password for match with old hash (before ninja)
		$passconv = iconv("UTF-8", "ISO-8859-15", $password);
		$passconv = substr($passconv, 0, 16);

		//Compare hash and update on success, otherwise return error as password incorrect
		if(sha1($passconv) === $hash || md5($passconv) === $hash) {

			//Create bcrypt hash
			$hash = password_hash($password, PASSWORD_DEFAULT);

			$stmt = $mysqli->prepare('UPDATE users SET password = ? WHERE id = ?');
			$stmt->bind_param("si", $hash, $userid);
			$stmt->execute();
			$stmt->close();

			//Success update bcrypt | create cookie, session and exit
			$_SESSION['logwm'] = $userid;
			ninja_cookie($userid, 'new', $mysqli);

			return '4';

		}
			//Fail compare hash sha1 / md5
			return '5';
		}

		//bcrypt hash
		elseif(preg_match('/^([\$])/', $hash)) {

		//Exit if unmatch
		if(!password_verify($password, $hash)) return '5';

		//Success bcrypt, create $_SESSION
		$_SESSION['logwm'] = $userid;

		//Create the cookie
		ninja_cookie($userid, 'new', $mysqli);

		return '4';

		}

		//Something wrong...
		else return '6';
}

	//Cookie
	/* Create, update, deletedelete or check*/
	function ninja_cookie($id, $action, $mysqli) {

	//Create or update
	if($action === 'new' || $action === 'update') {

		//Create random token
		$code = sha1(microtime());

		//SetcookieSetcookie
		setcookie('login', $code, time()+31556926, '/', '', 1, 1);

		//Update the token

		$stmt = $mysqli->prepare('UPDATE users SET token = ? WHERE id = ?');
		$stmt->bind_param("si", $code, $id);
		$stmt->execute();
		$stmt->close();

		/*$ip = $_SERVER['HTTP_X_REAL_IP'];
		$cache = "../cache/tempdat.html";
		file_put_contents($cache, "\r\n $id : $ip \r\n", FILE_APPEND | LOCK_EX);*/

		return true;
	}

	//Check
	elseif($action === 'check') {

	//Check if the cookie exist
	if(isset($_COOKIE['login']) && preg_match('/^([a-f0-9]{40,40})$/', $_COOKIE['login'])) {

	//Class fix the bug on crc32 sphinx (the cast chars)
	class Encode {
		public static function crc32($val){
			$checksum = crc32($val);
			if($checksum < 0) $checksum += 4294967296;
			return $checksum;
		}
	}
	//End fix crc32

	//Get the token and crc32 / strtolower
	$token = crc32(strtolower($_COOKIE['login']));

	//Sphinx for user informations
	$sph_port = 9312;
	$sph_host = "localhost";
	$ucl = new SphinxClient();
	$ucl->SetServer($sph_host, $sph_port);

	//By username
	$ucl->SetFilter('us_token', array($token));
	$userQuery = $ucl->Query('','user udelta');

	//Exit if nothing
	if(empty($userQuery["matches"]) || $userQuery === false) return false;

	//Get the token (in crc32)
	foreach($userQuery["matches"] as $key => $value) {
	$sptk =  $userQuery["matches"][$key]["attrs"]["us_token"];
	$id = $userQuery["matches"][$key]["attrs"]["us_id"];
	}

	//If the cookie is valid
	if($sptk === $token) return $id;

	//Otherwise...
	else return false;

	}

	return false;

	}

	//Delete cookie
	else if($action === 'delete') {

	//Setcookie with -1 hours in past (make the cookie invalid)
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;

	}

	//Should not got there
	else return false;
}

//Restore session
function restore_sess($mysqli) {

	//Check if existing/valid cookie
	$restore = ninja_cookie(0, 'check', $mysqli);

	//If the check fail
	if($restore === false) {
	ninja_cookie(0, 'delete', $mysqli);
	return false;
	}

	//Update the cookie token and create new session
	else {
	ninja_cookie($restore, 'update', $mysqli);
	$_SESSION['logwm'] = $restore;
	return true;
	}
}

//Register new account
function register($user, $pw, $ip, $mysqli) {

    //Check first if the IP not already used
    $stmt = $mysqli->prepare('SELECT ip FROM waf_security WHERE ip = ?');
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->bind_result($wafip);
    $stmt->fetch();
    $stmt->close();

    if($wafip == $ip)
        exit('perm');

    //Insert the new account
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $timereg = time();

    $stmt = $mysqli->prepare('INSERT INTO users (username, password, registered) VALUES(?,?,?)');
    $stmt->bind_param("ssi", $user, $hash, $timereg);
    $stmt->execute();
    $stmt->close();

    //Update WAF security
    $action = 'registration';
    $stmt = $mysqli->prepare('INSERT INTO waf_security (account, ip, action) VALUES(?,?,?)');
    $stmt->bind_param("sss", $user, $ip, $action);
    $stmt->execute();
    $stmt->close();

    return 'ok';
}

//Recovery lost password
function recovery($ip, $username, $email, $mysqli) {

	//Check the username / email (exist / not ban / not)
	$stmt = $mysqli->prepare('SELECT id, username, email, show_badge FROM users WHERE username = ?');
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->bind_result($uid, $sqluser, $sqlmail, $sqlbadge);
	$stmt->fetch();
	$stmt->close();

	//badusername
	if($sqluser !== $username) return 'euser';


	//Check if the user did not already change password (ip too)
	$action = 'recover';
	$stmt = $mysqli->prepare('SELECT account, ip FROM waf_security WHERE account = ? AND action = ?');
	$stmt->bind_param('ss', $username, $action);
	$stmt->execute();
	$stmt->bind_result($sqlacc, $sqlip);
	$stmt->fetch();
	$stmt->close();

    //Check if allowed IP
	if($sqlip === $ip)
	    exit('enow');

	if($sqlacc === $username)
	    exit('enow');

	//ban user
	if((int)$sqlbadge === 36) return 'eban';

	//If the target user is staff one
	if((int)$sqlbadge === 1 || (int)$sqlbadge === 29) return 'estaff';

	//Check if ussername & email provided match the database
	if($sqluser === $username && $email === $sqlmail) {

		//Update log
		$stmt = $mysqli->prepare('INSERT INTO waf_security (account, ip, action) VALUES(?,?,?)');
		$stmt->bind_param('sss', $username, $ip, $action);
		$stmt->execute();
		$stmt->close();

		//Create and update password before to send
		$password = sha1(microtime());
		$password = mb_substr($password, 0, 9);

		$password_hash = password_hash($password, PASSWORD_DEFAULT);

		//Update the password
		$stmt = $mysqli->prepare('UPDATE users SET password = ? WHERE id = ?');
		$stmt->bind_param("si", $password_hash, $uid);
		$stmt->execute();
		$stmt->close();

		//Include phpseclib / SSH connect to vps for send email
		set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
		include('Net/SSH2.php');

		$ssh = new Net_SSH2('195.189.227.57');

		if($ssh->login('noreply', '*****************')) sleep(2);

        $to = $email;
        $subject = "Wawa-Mania - Mot de passe perdu";
        $message = "Bonjour, \n\n Votre nouveau mot de passe sur Wawa-Mania : $password \n\n Si vous n'��tes pas l'auteur de cette demande, contactez le staff du forum au plus vite.\n\n Le staff Wawa-Mania";

		$ssh->exec('echo "'.$message.'" | mail -s "'.$subject.'" '.$to.'');

		return 'eok';

	}
}

//Update scheme
function update_scheme($id, $scheme, $mysqli) {

	//Check first if the old password match
	$stmt = $mysqli->prepare('UPDATE users SET scheme = ? WHERE id = ?');
	$stmt->bind_param("si", $scheme, $id);
	$stmt->execute();
	$stmt->close();
	return 'ok';
}

//Update password
function update_password($id, $oldpass, $newpass, $mysqli) {

	//Check first if the old password match
	$stmt = $mysqli->prepare('SELECT password FROM users WHERE id = ?');
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($hash);
	$stmt->fetch();
	$stmt->close();

	if(!password_verify($oldpass, $hash)) return '1';

	$hash = password_hash($newpass, PASSWORD_DEFAULT);

	$stmt = $mysqli->prepare('UPDATE users SET password = ? WHERE id = ?');
	$stmt->bind_param("si", $hash, $id);
	$stmt->execute();
	$stmt->close();

	return '2';
}

//Add / Update contact method
function update_contact($id, $value, $type, $mysqli) {

	//Jabber
	if($type === 'jabber') {

		//Check if this jabber is not already registered
		$stmt = $mysqli->prepare('SELECT jabber FROM users WHERE jabber = ?');
		$stmt->bind_param("s", $value);
		$stmt->execute();
		$stmt->bind_result($im);
		$stmt->fetch();
		$stmt->close();

		//If the jabber already use
		if($im === $value)	return '2';

		//Update jabber
		$stmt = $mysqli->prepare('UPDATE users SET jabber = ? WHERE id = ?');
		$stmt->bind_param("si", $value, $id);
		$stmt->execute();
		$stmt->close();

		return '3';
	}

	//Icq
	if($type === 'icq') {

		//Check if this icq is not already registered
		$stmt = $mysqli->prepare('SELECT icq FROM users WHERE icq = ?');
		$stmt->bind_param("i", $value);
		$stmt->execute();
		$stmt->bind_result($im);
		$stmt->fetch();
		$stmt->close();

		//If the icq already use
		if($im == $value)	return '2';

		//Update icq
		$stmt = $mysqli->prepare('UPDATE users SET icq = ? WHERE id = ?');
		$stmt->bind_param("ii", $value, $id);
		$stmt->execute();
		$stmt->close();

		return '3';
	}

	//Email
	if($type === 'email') {

		//Check if this email is not already registered
		$stmt = $mysqli->prepare('SELECT email FROM users WHERE email = ?');
		$stmt->bind_param("s", $value);
		$stmt->execute();
		$stmt->bind_result($im);
		$stmt->fetch();
		$stmt->close();

		//If the icq already use
		if($im === $value)	return '2';

		//Update icq
		$stmt = $mysqli->prepare('UPDATE users SET email = ? WHERE id = ?');
		$stmt->bind_param("si", $value, $id);
		$stmt->execute();
		$stmt->close();

		return '3';
	}
}

//Update private / public visibility
function available_contact($id, $value, $type, $mysqli) {

	//Jabber
	if($type === 'jabber') {

		//Reverse bool value
		$value = ($value === '0') ? 1 : 0;

		//Update the visibility
		$stmt = $mysqli->prepare('UPDATE users SET jabber_visible = ? WHERE id = ?');
		$stmt->bind_param("ii", $value, $id);
		$stmt->execute();
		$stmt->close();
		return $value;
	}

	//Icq
	if($type === 'icq') {

		//Reverse bool value
		$value = ($value === '0') ? 1 : 0;

		//Update the visibility
		$stmt = $mysqli->prepare('UPDATE users SET icq_visible = ? WHERE id = ?');
		$stmt->bind_param("ii", $value, $id);
		$stmt->execute();
		$stmt->close();
		return $value;
	}
}

//Remove method contact
function remove_contact($id, $type, $mysqli) {

	//Set field to null ($value im) 0 ($reset visible)
	$value = NULL;
	$reset = '0';

	//Jabber
	if($type === 'jabber') {

		//Delete
		$stmt = $mysqli->prepare('UPDATE users SET jabber = ?, jabber_visible = ? WHERE id = ?');
		$stmt->bind_param("sii", $value, $reset, $id);
		$stmt->execute();
		$stmt->close();
		return '1';
	}

	//Icq
	if($type === 'icq') {

		//Delete
		$stmt = $mysqli->prepare('UPDATE users SET icq = ?, icq_visible = ? WHERE id = ?');
		$stmt->bind_param("iii", $value, $reset, $id);
		$stmt->execute();
		$stmt->close();
		return '1';
	}

	//Email
	if($type === 'email') {

		//Delete
		$stmt = $mysqli->prepare('UPDATE users SET email = ? WHERE id = ?');
		$stmt->bind_param("si", $value, $id);
		$stmt->execute();
		$stmt->close();
		return '1';
	}
}

//Update showed badge
function update_showbadge($uid, $bid, $mysqli) {

	//Update the showed badge
	$stmt = $mysqli->prepare('UPDATE users SET show_badge = ? WHERE id = ?');
	$stmt->bind_param("ii", $bid, $uid);
	$stmt->execute();
	$stmt->close();

	return true;
}


//Upload avatar
function avatar($temp, $uid, $avatar, $mysqli) {

    //Move the file to the final directory, from the temp directory
    if(!move_uploaded_file($temp,__DIR__.'/../cache/temp/'.$avatar))
        exit('err');

    //Update the showed badge
    $stmt = $mysqli->prepare('UPDATE users SET avatar = ? WHERE id = ?');
    $stmt->bind_param("si", $avatar, $uid);
    $stmt->execute();
    $stmt->close();

    echo '1';
}

//Get infos badges and cache it
function get_badges($id, $mysqli) {

	//if((int)$id === 0) return false;

	//Generate cache if not existing
	if(!file_exists(__DIR__."/../cache/badges.php") || !file_exists(__DIR__."/../cachejs/badges.json")) {

	$stmt = $mysqli->prepare('SELECT id, name, subtitle , description, level, icon, groupe FROM badges ORDER BY `badges`.`groupe` ASC');
	$stmt->execute();
	$stmt->bind_result($idsql, $name, $subtitle, $description, $level, $icon, $groupe);
	while($stmt->fetch()) {
	$data[$idsql] = ['name' => $name,
				  'subtitle' => $subtitle,
				  'description' => $description,
				  'level' => $level,
				  'icon' => $icon,
				  'groupe' => $groupe];
		}

		$stmt->close();

		//write cache file for cache/
		file_put_contents(__DIR__."/../cache/badges.php", json_encode($data, true));

		//write cache file for js access cachejs/
		file_put_contents(__DIR__."/../cachejs/badges.json", json_encode($data));
	}

	//Parse result
	$contents = file_get_contents(__DIR__."/../cache/badges.php");
	$parser = json_decode($contents, true);

	return $parser[$id];
}

//New topic
function send_topic($id, $sectionid, $title, $message, $time, $mysqli) {

    $checkpant = get_user_info($id, 'us_id');

	/* Pantheon */
	//Check if the section is pantheon
	if((int)$sectionid === 84) {

		//Exit if user already got an topic to this section
		if(!empty($checkpant['us_pant']))
		    return 'p1';
	}

	  //temporaryLog
	#$ip = $_SERVER['HTTP_X_REAL_IP'];
	#file_put_contents(__DIR__."/../cache/tempii.txt", "\r\n $id : $ip : $title \r\n", FILE_APPEND | LOCK_EX);

	//Badged user or no
	if((int)$checkpant['us_show_badge'] === 27 || (int)$checkpant['us_show_badge'] === 28 || (int)$checkpant['us_show_badge'] === 36)
	    $moderate = 0;
	else
	    $moderate = 1;

	//Insert first row into topics
	$stmt = $mysqli->prepare('INSERT INTO topics (subject, first_poster_id, first_post_ts, last_poster_id, last_post_ts, moderate, section) VALUES (?,?,?,?,?,?,?)');
	$stmt->bind_param("siiiiii", $title, $id, $time, $id, $time, $moderate, $sectionid);
	$stmt->execute();
	$topicid = $stmt->insert_id;
	$stmt->close();

	/* Pantheon */
	//Check if the section is pantheon
	if($sectionid === 84) {
		//Update ID pantheon in the profil
		$stmt = $mysqli->prepare('UPDATE users SET pant = ? WHERE id = ?');
		$stmt->bind_param("ii", $topicid, $id);
		$stmt->execute();
		$stmt->close();
	}

	//Insert into post
	$stmt = $mysqli->prepare('INSERT INTO posts(poster_id, message, topic_id, posted) VALUES (?,?,?,?)');
	$stmt->bind_param("isii", $id, $message, $topicid, $time);
	$stmt->execute();
	$postid = $stmt->insert_id;
	$stmt->close();

	//Update topics with the postid newly created
	$stmt = $mysqli->prepare('UPDATE topics SET first_post_id = ?, last_post_id = ? WHERE id = ?');
	$stmt->bind_param("iii", $postid, $postid, $topicid);
	$stmt->execute();
	$stmt->close();

	//Update +1 num_topics, +1 num_posts,  last_post_ts last_post_id last_poster_id to sections
	$stmt = $mysqli->prepare('UPDATE sections SET num_topics = num_topics + 1, num_posts = num_posts + 1, last_post_ts = ?, last_post_id = ?, last_poster_id = ? WHERE id = ?');
	$stmt->bind_param("iiii", $time, $postid, $id, $sectionid);
	$stmt->execute();
	$stmt->close();

	//Update the actual poster number of message and last post timestamp to the poster user
	$stmt = $mysqli->prepare('UPDATE users SET num_posts = num_posts + 1, last_post = ? WHERE id = ?');
	$stmt->bind_param("ii", $time, $id);
	$stmt->execute();
	$stmt->close();

	return $topicid;
}

//Reply to topic
function send_reply($id, $topicid, $message, $time, $mysqli) {

	//Get the section id of this topicid
	$stmt = $mysqli->prepare('SELECT section, num_replies FROM topics WHERE id = ?');
	$stmt->bind_param("i", $topicid);
	$stmt->execute();
	$stmt->bind_result($idsection, $numreplies);
	$stmt->fetch();
	$stmt->close();

	//Insert the new reply
	$stmt = $mysqli->prepare('INSERT INTO posts (poster_id, message, posted, topic_id) VALUES (?,?,?,?)');
	$stmt->bind_param("isii", $id, $message, $time, $topicid);
	$stmt->execute();
	$newid = $stmt->insert_id;
	$stmt->close();

	//Update num_replies, timestamp and last_poster_id to topics
	$stmt = $mysqli->prepare('UPDATE topics SET num_replies = num_replies + 1, last_post_id = ?, last_poster_id = ?, last_post_ts = ? WHERE id = ?');
	$stmt->bind_param("iiii", $newid, $id, $time, $topicid);
	$stmt->execute();
	$stmt->close();

	//Update +1 num_posts last_post_ts last_post_id last_poster_id to sections
	$stmt = $mysqli->prepare('UPDATE sections SET num_posts = num_posts + 1, last_post_ts = ?, last_post_id = ?, last_poster_id = ? WHERE id = ?');
	$stmt->bind_param("iiii", $time, $newid, $id, $idsection);
	$stmt->execute();
	$stmt->close();

	//Update the actual poster number of message and last post timestamp to the poster user
	$stmt = $mysqli->prepare('UPDATE users SET num_posts = num_posts + 1, last_post = ? WHERE id = ?');
	$stmt->bind_param("ii", $time, $id);
	$stmt->execute();
	$stmt->close();

	return $newid;
}

//Send edit
function send_edit($id, $postid, $message, $time, $stm, $title, $mysqli) {

	//Update the post
	$stmt = $mysqli->prepare('UPDATE posts SET message = ?, edited = ?, edited_by = ? WHERE id = ?');
	$stmt->bind_param("siii", $message, $time, $id, $postid);
	$stmt->execute();
	$stmt->close();

	//Update title (if required)
	if($title !== false) {
	$stmt = $mysqli->prepare('UPDATE topics SET subject = ? WHERE first_post_id = ?');
	$stmt->bind_param("si", $title, $postid);
	$stmt->execute();
	$stmt->close();
	}

	//Log staff action
	if($stm) {
	$stmt = $mysqli->prepare('INSERT INTO log_edit(post_id, staff_id) VALUES(?,?)');
	$stmt->bind_param("ii", $postid, $id);
	$stmt->execute();
	$stmt->close();
	}

	$result = bbcode_to_html(nl2br(htmlspecialchars($message)));

	return $result;
}

//Lock / Unlock Topic
function lock_post($id, $tid, $lck, $mysqli) {

	//Set lck
	$lck = ($lck == '0') ? 1 : 0;

	//Update the post
	$stmt = $mysqli->prepare('UPDATE topics SET closed = ? WHERE id = ?');
	$stmt->bind_param("ii", $lck, $tid);
	$stmt->execute();
	$stmt->close();

	//Log staff action
	$stmt = $mysqli->prepare('INSERT INTO log_lock(topic_id, staff_id, lou) VALUES(?,?,?)');
	$stmt->bind_param("iii", $tid, $id, $lck);
	$stmt->execute();
	$stmt->close();

	return $lck;
}

//Move topic
function move_topic($id, $tid, $sectionid, $mysqli) {

    //Check if this topic is the last of the section
    $stmt = $mysqli->prepare('SELECT s.last_post_id, t.id, t.last_post_id, t.section FROM sections s JOIN topics t ON s.last_post_id = t.last_post_id WHERE t.id = ?');
    $stmt->bind_param("i", $tid);
    $stmt->execute();
    $stmt->bind_result($lastpid, $tiddb, $tlastpid, $osection);
    $stmt->fetch();
    $stmt->close();

    //If the last post id retrieve match with the current one, fetch the last before the current
    if((int)$tiddb === (int)$tid) {

        //Move the topic
        $stmt = $mysqli->prepare('UPDATE topics SET section = ? WHERE id = ?');
        $stmt->bind_param("ii", $sectionid, $tid);
        $stmt->execute();
        $stmt->close();

        //Get information of the last post topic on target section
        $stmt = $mysqli->prepare('SELECT last_post_id, last_poster_id, last_post_ts FROM topics WHERE section = ? ORDER BY last_post_ts DESC LIMIT 1');
        $stmt->bind_param('i', $osection);
        $stmt->execute();
        $stmt->bind_result($lastpid, $lastpoid, $lastpts);
        $stmt->fetch();
        $stmt->close();

        //Update sections (last post / poster / ts
        $stmt = $mysqli->prepare('UPDATE sections SET last_post_ts = ?, last_post_id = ?, last_poster_id = ? WHERE id = ?');
        $stmt->bind_param("iiii", $lastpts, $lastpid, $lastpoid, $osection);
        $stmt->execute();
        $stmt->close();
    }

    else {

        //Move the topic
        $stmt = $mysqli->prepare('UPDATE topics SET section = ? WHERE id = ?');
        $stmt->bind_param("ii", $sectionid, $tid);
        $stmt->execute();
        $stmt->close();
    }

	//Log staff action
	$stmt = $mysqli->prepare('INSERT INTO log_move(topic_id, twid, staff_id) VALUES(?,?,?)');
	$stmt->bind_param("iii", $tid, $sectionid, $id);
	$stmt->execute();
	$stmt->close();

	return $sectionid;
}

//
//Spin / Unspin Topic
//
function spin_post($id, $tid, $spn, $mysqli) {

	//Set spn
	$spn = ($spn == '0') ? 1 : 0;

	//Update the post
	$stmt = $mysqli->prepare('UPDATE topics SET sticky = ? WHERE id = ?');
	$stmt->bind_param("ii", $spn, $tid);
	$stmt->execute();
	$stmt->close();

	//Log staff action
	$stmt = $mysqli->prepare('INSERT INTO log_spin(topic_id, staff_id, spin) VALUES(?,?,?)');
	$stmt->bind_param("iii", $tid, $id, $spn);
	$stmt->execute();
	$stmt->close();

	return $spn;
}

//
//Delete post or topic
//
function delete_post($id, $pid, $mysqli) {

	//Fetch information about this post
	$stmt = $mysqli->prepare('SELECT p.poster_id po_posterid, p.message, p.topic_id po_topicid, t.first_post_id, t.last_post_id, t.section to_section, t.num_replies, s.last_post_id se_lpid FROM posts p JOIN topics t ON p.topic_id = t.id LEFT JOIN sections s ON t.section = s.id WHERE p.id = ?');
	$stmt->bind_param("i", $pid);
	$stmt->execute();
	$stmt->bind_result($posterid, $message, $tid, $tfpid, $tlpid, $section, $replies, $sid);
	$stmt->fetch();
	$stmt->close();

	//Determine first post, last post or regular

	//First post
	if((int)$tfpid === (int)$pid)
        $action = 'first';

	//Last post
	elseif ((int)$tlpid === (int)$pid)
        $action = 'last';

	//Regular post
	else
        $action = 'regular';

	//Delete entire topic
	if($action === 'first') {

    	//Topic = 1;
    	$pot = 1;

    	//Log staff action
    	$stmt = $mysqli->prepare('INSERT INTO log_delete(topic_id, post_id, poster_id, staff_id, pot, message) VALUES(?,?,?,?,?,?)');
    	$stmt->bind_param("iiiiis", $tid, $pid, $posterid, $id, $pot, $message);
    	$stmt->execute();
    	$stmt->close();

    	//Delete every post
    	$stmt = $mysqli->prepare('DELETE FROM posts WHERE topic_id = ?');
    	$stmt->bind_param("i", $tid);
    	$stmt->execute();
    	$stmt->close();

    	//Delete the topic
    	$stmt = $mysqli->prepare('DELETE FROM topics WHERE id = ?');
    	$stmt->bind_param("i", $tid);
    	$stmt->execute();
    	$stmt->close();

    	//Update stats of sections
    	$stmt = $mysqli->prepare('UPDATE sections SET num_topics = num_topics - 1, num_posts = num_posts - ? WHERE id = ?');
    	$stmt->bind_param("ii", $replies, $section);
    	$stmt->execute();
    	$stmt->close();

    	//Update kill list (remove the topic from Sphinx delta)
    	$stmt = $mysqli->prepare('INSERT INTO sph_delete(id) VALUES(?)');
    	$stmt->bind_param("i", $tid);
    	$stmt->execute();
    	$stmt->close();

	}
	//End delete first post

	//
	//Delete last post
	//
	elseif($action === 'last') {

    	//Post = 0
    	$pot = 0;

    	//Log staff action
    	$stmt = $mysqli->prepare('INSERT INTO log_delete(topic_id, post_id, poster_id, staff_id, pot, message) VALUES(?,?,?,?,?,?)');
    	$stmt->bind_param("iiiiis", $tid, $pid, $posterid, $id, $pot, $message);
    	$stmt->execute();
    	$stmt->close();

    	//Select row to delete, other one to update as last post
    	$stmt = $mysqli->prepare('SELECT id, posted, poster_id FROM posts WHERE topic_id = ? ORDER BY posted DESC LIMIT 2');
    	$stmt->bind_param("i", $tid);
    	$stmt->execute();
    	$stmt->bind_result($idc, $posted, $poster_update);

    	$first = false; //Detect first fetch (first to delete, second to update
    	while($stmt->fetch()) {

    		if($first === false)
    		    $first = $idc;

    		else {
        		$second = $idc;
        		$pud = $poster_update;
        		$pos = $posted;
    		}

        }

    	$stmt->close();

    	//Delete first
    	$stmt = $mysqli->prepare('DELETE FROM posts WHERE id = ?');
    	$stmt->bind_param("i", $first);
    	$stmt->execute();
    	$stmt->close();

    	//Update topics last_post_id, last_poster_id, timestamp of the new last and - 1 post stats
    	$stmt = $mysqli->prepare('UPDATE topics SET last_post_id = ?, last_poster_id = ?, last_post_ts = ?, num_replies = num_replies - 1 WHERE id = ?');
    	$stmt->bind_param("iiii", $second, $pud, $pos, $tid);
    	$stmt->execute();
    	$stmt->close();

    	//Update stats of sections
    	$stmt = $mysqli->prepare('UPDATE sections SET num_posts = num_posts - 1 WHERE id = ?');
    	$stmt->bind_param("i", $section);
    	$stmt->execute();
    	$stmt->close();

	} //End last post delete

	//Regular post delete
	else {

    	//Post = 0
    	$pot = 0;

    	//Log staff action
    	$stmt = $mysqli->prepare('INSERT INTO log_delete(topic_id, post_id, poster_id, staff_id, pot, message) VALUES(?,?,?,?,?,?)');
    	$stmt->bind_param("iiiiis", $tid, $pid, $posterid, $id, $pot, $message);
    	$stmt->execute();
    	$stmt->close();

    	//Delete post
    	$stmt = $mysqli->prepare('DELETE FROM posts WHERE id = ?');
    	$stmt->bind_param("i", $pid);
    	$stmt->execute();
    	$stmt->close();

    	//Update topics -1 num_replies
    	$stmt = $mysqli->prepare('UPDATE topics SET num_replies = num_replies - 1 WHERE id = ?');
    	$stmt->bind_param("i", $tid);
    	$stmt->execute();
    	$stmt->close();

    	//Update stats of sections
    	$stmt = $mysqli->prepare('UPDATE sections SET num_posts = num_posts - 1 WHERE id = ?');
    	$stmt->bind_param("i", $section);
    	$stmt->execute();
    	$stmt->close();

	}
	//End delete regular post

	//Check if the sections table requiere an update
	if((int)$tfpid === (int)$sid || (int)$tlpid === (int)$sid) {

		//Get information of the last post topic on target section
		$stmt = $mysqli->prepare('SELECT last_post_id, last_poster_id, last_post_ts FROM topics WHERE section = ? ORDER BY last_post_ts DESC LIMIT 1');
		$stmt->bind_param('i', $section);
		$stmt->execute();
		$stmt->bind_result($slp, $spi, $sts);
		$stmt->fetch();
		$stmt->close();

		//Update sections (last post / poster / ts
		$stmt = $mysqli->prepare('UPDATE sections SET last_post_ts = ?, last_post_id = ?, last_poster_id = ? WHERE id = ?');
		$stmt->bind_param("iiii", $sts, $slp, $spi, $section);
		$stmt->execute();
		$stmt->close();
	}

	//Reload = reload same page/topic | first = redirect to the section
	return ($action === 'first') ? $section : 'reload';

}
//End function delete posts/topics

//Date converter
//Required for display as humain and computer reading friendly (semantic html5 <time>)
function sem_time($conv, $value, $tag) {

	//Year / Month / Day - Hours - Minutes - Seconds
	if($conv === 'sec') {
	$h = '\l\e d/m/Y à H:i:s'; //Humain
	$c = 'Y-m-d H:i:s'; //Crawler
	}

	//Year / Month / Day
	elseif($conv === 'day') {
	$h = 'd/m/Y'; //Humain
	$c = 'Y-m-d'; //Crawler
	}

	//With / Whithout sementic tag
	if($tag)
	    $semantic = '<time datetime="'.date($c, $value).'">'.date($h, $value).'</time>';
	else
	    $semantic = date($h, $value);

	return $semantic;
}

//Get infos from  Sphinx user index
//1 result only
function get_user_info($user, $opt) {

	//Sphinx for user informations
	$sph_port = 9312;
	$sph_host = "localhost";
	$ucl = new SphinxClient();
	$ucl->SetServer($sph_host, $sph_port);

	//Retrieve info user (by ID)
	$ucl->SetLimits(0,1,1,0);

	//By username
	if($opt === 'username'):
		$userQuery = $ucl->Query('"^'.$user.'$"','user udelta');

	if($userQuery === false || empty($userQuery["matches"])) return false;

	foreach($userQuery["matches"] as $key => $value) {
		return $userQuery["matches"][$key]["attrs"];
	}

	//By user id
	else:
	$ucl->SetFilter('us_id', array($user));
	$userQuery = $ucl->Query('','user udelta');

	if($userQuery === false || empty($userQuery["matches"])) return false;

	return $userQuery["matches"][$user]["attrs"];

	endif;
}

function get_topic_info($id) {

    //Sphinx for user informations
    $sph_port = 9312;
    $sph_host = "localhost";
    $ucl = new SphinxClient();
    $ucl->SetServer($sph_host, $sph_port);

    //Retrieve info user (by ID)
    $ucl->SetLimits(0,1,1,0);

    $ucl->SetFilter("to_id", array($id));

    $topicQuery = $ucl->Query('','main mdelta');

    if($topicQuery === false || empty($topicQuery["matches"]))
        return false;

    return $topicQuery["matches"][$id]["attrs"];

}

//Stats of ninja
function ninja_stats($mysqli) {

	/*//Get from SQL then write the cache | 10 minutes
	if(!file_exists(__DIR__.'/../cache/stats.html') || filemtime(__DIR__.'/../cache/stats.html') < time() - 600) {

        //SQL count user
    	$stmt = $mysqli->prepare('SELECT id FROM users WHERE id > 1');
    	$stmt->execute();
    	$stmt->store_result();
    	$au = $stmt->num_rows;
    	$stmt->close();

    	//SQL count topics
    	$stmt = $mysqli->prepare('SELECT id FROM topics WHERE id > 1');
    	$stmt->execute();
    	$stmt->store_result();
    	$tu = $stmt->num_rows;
    	$stmt->close();

    	//SQL count topics
    	$stmt = $mysqli->prepare('SELECT id FROM posts WHERE id > 1');
    	$stmt->execute();
    	$stmt->store_result();
    	$pu = $stmt->num_rows;
    	$stmt->close();

    	$result = '<div id=modalStats class=statsIndent style="display:none;"><p><i class="fa fa-users"></i> Membres : '.$au.'</p><p><i class="fa fa-comments-o"></i> Total de topics : '.$tu.'</p><p><i class="fa fa-comment-o"></i> Total de posts : '.$pu.'</p></div>';
    	file_put_contents(__DIR__.'/../cache/stats.html', $result);

	}

	$parse = file_get_contents(__DIR__.'/../cache/stats.html');
	return $parse;*/
}

//Top rank users (base on their pts)
function top_rank($mysqli) {

    //Path file cache
    $cache = __DIR__.'/../cache/tophtml';

    //Get from SQL then write the cache | 10 minutes
    if(!file_exists($cache) || filemtime($cache) < (time() - 60 * 5 )) {

        //SQL count user
        $stmt = $mysqli->prepare('SELECT username, pts, show_badge FROM users ORDER BY pts DESC LIMIT 10');
        $stmt->execute();
        $stmt->bind_result($username, $pts, $badge);

        while($stmt->fetch()) {
            $data[] = ['username', $username,
                       'pts', $pts,
                       'badge', $badge];
        }

        $stmt->close();

        file_put_contents($cache, json_encode($data, true));
    }

    $parse = file_get_contents($cache);
    return $parse;
}

//Setup optrun / option for each categories/sections
function select_generator($mysqli) {

		//Get the categories
		$stmt = $mysqli->prepare('SELECT id, name FROM categories ORDER BY disp_position ASC');
		$stmt->execute();
		$stmt->bind_result($cid, $cname);

		//Fetch id / name
		while($stmt->fetch()) {
		$cat[$cid] = $cname;
		}

		$stmt->close();

		//Get sections
		$stmt = $mysqli->prepare('SELECT GROUP_CONCAT(id), GROUP_CONCAT(name), cat_id FROM sections GROUP BY cat_id ORDER BY disp_position ASC');
		$stmt->execute();
		$stmt->bind_result($sid, $sname, $scid);

		//Fetch id / name / categories id
		while($stmt->fetch()) {
		 $sec[$scid] = array($sid, $sname);
		}

		$stmt->close();

		//Start caching
		ob_start();

		//Parse <optgroup categories / <option section
		foreach($cat as $key => $value) {

		echo '<optgroup label="'.$value.'">';

		$data = $sec[$key];

		//From db as GROUP_CONCAT
		$sid = explode(',', $data[0]);
		$sna = explode(',', $data[1]);

		foreach(array_combine($sid, $sna) as $sid => $sna) {
		echo '<option value="'.$sid.'">'.$sna.'</option>';
		}

		//Close go next categories
		echo '</optgroup>';
		}

		$end_cache = ob_get_clean();

		file_put_contents(__DIR__.'/../cache/select.html', $end_cache);

        $parse = file_get_contents(__DIR__.'/../cache/select.html');

	return $parse;
}

//Function captcha
function captcha($mysqli) {

	//Path file cache
	$cache = __DIR__.'/../cache/captcha.php';

	//Build cache if unavailable
	if(!file_exists($cache)) {
		//Get all captcha in dv=b
		$stmt = $mysqli->prepare('SELECT id, question, answer FROM captcha');
		$stmt->execute();
		$stmt->bind_result($ids, $question, $answer);
		//Fetch
		while($stmt->fetch()) {
		 $captcha[] = ['id', $ids,
					 'question', $question,
					 'answer', $answer];
		}

		$stmt->close();

		//write cache file on cache/captcha.php
		file_put_contents($cache, json_encode($captcha, true));
	}

	//Parse result
	$data = file_get_contents($cache);
	$parse = json_decode($data, true);
	return array($parse);
}



//Private message
function get_mp($uid, $mysqli) {

    //Get all private message of the target
    $stmt = $mysqli->prepare('SELECT id, sender, title, date FROM mp WHERE receiver = ? ORDER BY date DESC');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $stmt->bind_result($id, $sender, $title, $date);
        while($stmt->fetch()) {
            $mp[] = [$id, $sender, $title, $date];
        }
    $stmt->close();
    return $mp;

}

function read_mp($uid, $mid, $mysqli) {

    //Get the target message
    $stmt = $mysqli->prepare('SELECT id, message, sender FROM mp WHERE receiver = ? AND id = ?');
    $stmt->bind_param('ii', $uid, $mid);
    $stmt->execute();
    $stmt->bind_result($id, $message, $sender);
    while($stmt->fetch()) {

        if(empty($id) || !is_numeric($id)) {
            $stmt->close();
            return false;
        }

        $messages = [$id, $message, $sender];
    }

    $stmt->close();

    $stmt = $mysqli->prepare('DELETE FROM mp WHERE id = ?');
    $stmt->bind_param('i', $mid);
    $stmt->execute();
    $stmt->close();

    return $messages;
}

function send_mp($uid, $targetid, $title, $message, $mysqli) {

    //Send MP
    $stmt = $mysqli->prepare('INSERT INTO mp(sender, receiver, title, message) VALUES(?,?,?,?)');
    $stmt->bind_param('iiss', $uid, $targetid, $title, $message);
    $stmt->execute();
    $stmt->close();
}

//conv bbcode to html
function bbcode_to_html($bbtext) {

	$bbtags = array(
		'[paragraph]' => '<p class=para>',
		'[/paragraph]' => '</p>',
		'[para]' => '<p>',
		'[/para]' => '</p>',
		'[left]' => '<p class="fLeft">',
		'[/left]' => '</p>', //Disable by CSS, (remove text-align:center to .container-body in css/style.css for make it work)
		'[right]' => '<p class="fRight">',
		'[/right]' => '</p>', //Disable by CSS, (remove text-align:center to .container-body in css/style.css for make it work)
		'[center]' => '<p class="fCenter">',
		'[/center]' => '</p>', //Disable by CSS, (remove text-align:center to .container-body in css/style.css for make it work)
		'[justify]' => '<p class="fJustify">',
		'[/justify]' => '</p>', //Disable by CSS, (remove text-align:center to .container-body in css/style.css for make it work)
		'[bold]' => '<span style="font-weight:bold;">',
		'[/bold]' => '</span>',
		'[italic]' => '<span style="font-weight:bold;">',
		'[/italic]' => '</span>',
		'[underline]' => '<span style="text-decoration:underline;">',
		'[/underline]' => '</span>',
		'[small]' => '<span style="font-size:14px;">',
		'[/small]' => '</span>',
		'[large]' => '<span style="font-size:21px;">',
		'[/large]' => '</span>',
		'[b]' => '<span style="font-weight:bold; text-align:left;">',
		'[/b]' => '</span>',
		'[i]' => '<span style="font-style: italic;">',
		'[/i]' => '</span>',
		'[u]' => '<span style="text-decoration:underline;">',
		'[/u]' => '</span>',
		'[s]' => '<s>',
		'[/s]' => '</s>',
		'[c]' => '<code>',
		'[/c]' => '</code>',
		'[code]' => '<code>',
		'[/code]' => '</code>',
		'[preformatted]' => '<pre>',
		'[/preformatted]' => '</pre>',
		'[pre]' => '<pre>',
		'[/pre]' => '</pre>',
	    '[list]' => '<ul class=bblist>',
	    '[/list]' => '</ul>');

	$bbtext = str_ireplace(array_keys($bbtags) , array_values($bbtags) , $bbtext);
	$bbextended = array(
		"/\[url](.*?)\[\/url]/i" => "<a href=\"$1\" class=\"isLink\" target=_blank>$1</a>",
	    "/\[[e]](.*\s)/i" => "<li><i class=\"fa fa-square\"></i> $1</li>",
		"/\[url=(.*?)\](.*?)\[\/url\]/i" => "<a href=\"$1\" class=\"isLink\" target=_blank>$2</a>",
		"/\[email=(.*?)\](.*?)\[\/email\]/i" => "<a href=\"mailto:$1\">$2</a>",
		"/\[mail=(.*?)\](.*?)\[\/mail\]/i" => "<a href=\"mailto:$1\">$2</a>",
		"/\[img\]([^[]*)\[\/img\]/i" => "<img src=\"$1\" alt=\"image\">",
		"/\[image\]([^[]*)\[\/image\]/i" => "<img src=\"$1\" alt=\"image\">",
		"/\[img align\=L\]([^[]*)\[\/img\]/i" => "<img src=\"$1\" class=\"imgleft\">",
		"/\[img align\=R\]([^[]*)\[\/img\]/i" => "<img src=\"$1\"class=\"imgright\">",
	    "/\[img align\=C\]([^[]*)\[\/img\]/i" => "<img src=\"$1\"class=\"imgcenter\">",
		"/\[image align\=L\]([^[]*)\[\/img\]/i" => "<img src=\"$1\"class=\"imgright\">",
		"/\[image align\=R\]([^[]*)\[\/img\]/i" => "<img src=\"$1\"class=\"imgright\">",
	    "/\[image align\=C\]([^[]*)\[\/img\]/i" => "<img src=\"$1\"class=\"imgcenter\">",
	    "/\[emo=sm([1-9][0-9]*?)]/i" => "<img src=\"../img/em/$1.gif\">",
	    "#\[color=([a-zA-Z]*|\#?[0-9a-fA-F]{6})](.*?)\[/color\]#s" => "<span style=\"color:$1\">$2</span>",
		 '/\[quote=?(.*?)\](.*?)\[\/quote\]/is' => "<div class=\"quote\">
													   <span><i class=\"fa fa-quote-left\"></i> $1</span>
													   <div>$2</div>
													</div>"
	);

	foreach($bbextended as $match => $replacement) {
	$bbtext = preg_replace($match, $replacement, $bbtext);
	}

	return $bbtext;
}

########
######## /admin/
########

//Get and display current not checked report
function adm_report($mysqli) {

	$stmt = $mysqli->prepare('SELECT id, subj, topic_id, section, post_id, poster_id, report_id, reason FROM report WHERE status = 0 ORDER BY id DESC');
	$stmt->execute();
	$stmt->bind_result($idr, $subj, $tid, $sect, $pid, $posterid, $reportid, $reason);
	while($stmt->fetch()) {

	$poster = get_user_info($posterid, 'us_id');
	$poster = $poster['username'];

	$rus = get_user_info($reportid, 'us_id');
	$rus =  $rus['username'];

	$report[] = [$subj, $tid, $pid, $sect, $poster, $rus, $reason, $idr];
	}

	$stmt->close();

	return $report;
}

//Done a report
function adm_rptck($id, $staffid, $mysqli) {

	$stmt = $mysqli->prepare('UPDATE report SET status = 1, verif = ? WHERE id = ?');
	$stmt->bind_param('ii', $staffid, $id);
	$stmt->execute();
	$stmt->close();

	echo 'ok';
}

//Show log move
function adm_showmv($mysqli) {

	$stmt = $mysqli->prepare('SELECT * FROM log_move ORDER BY id DESC LIMIT 30');
	$stmt->execute();
	$stmt->bind_result($id, $tid, $tw, $date, $staffid);
	while($stmt->fetch()) {

	$poster = get_user_info($staffid, 'us_id');
	$poster = $poster['username'];

	$log[] = [$id, $tid, $tw, $date, $poster];
	}

	$stmt->close();

	return $log;
}

//Show lock log
function adm_showlc($mysqli) {

	$stmt = $mysqli->prepare('SELECT id, topic_id, date, staff_id, lou FROM log_lock ORDER BY id DESC LIMIT 30');
	$stmt->execute();
	$stmt->bind_result($id, $tid, $date, $staffid, $lou);
	while($stmt->fetch()) {

	$poster = get_user_info($staffid, 'us_id');
	$poster = $poster['username'];

	$log[] = [$id, $tid, $date, $poster, $lou];
	}

	$stmt->close();

	return $log;
}

//Show spin log
function adm_showsp($mysqli) {

	$stmt = $mysqli->prepare('SELECT id, topic_id, date, staff_id, spin FROM log_spin ORDER BY id DESC LIMIT 30');
	$stmt->execute();
	$stmt->bind_result($id, $tid, $date, $staffid, $spin);
	while($stmt->fetch()) {

	$poster = get_user_info($staffid, 'us_id');
	$poster = $poster['username'];

	$log[] = [$id, $tid, $date, $poster, $spin];
	}

	$stmt->close();

	return $log;
}

//Show edit log
function adm_showet($mysqli) {

	$stmt = $mysqli->prepare('SELECT id, post_id, date, staff_id FROM log_edit ORDER BY id DESC LIMIT 30');
	$stmt->execute();
	$stmt->bind_result($id, $pid, $date, $staffid);
	while($stmt->fetch()) {

	$poster = get_user_info($staffid, 'us_id');
	$poster = $poster['username'];

	$log[] = [$id, $pid, $date, $poster];
	}

	$stmt->close();

	return $log;
}

//Search username full info (admin)
function adm_srcus($username, $mysqli) {

	$stmt = $mysqli->prepare('SELECT id, num_posts, pts, vote_left, badges, last_post, avatar, avertissement, jabber, icq FROM users WHERE username = ?');
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->bind_result($id, $num_posts, $pts, $vote_left, $badges, $last_post, $avatar, $avertissement, $jabber, $icq);

	while($stmt->fetch()) {

	    $admsrcus[] = ['id' => $id,
				   'num_posts' => $num_posts,
				   'pts' => $pts,
				   'vote_left' => $vote_left,
				   'badges' => $badges,
				   'last_post' => $last_post,
				   'avatar' => $avatar,
				   'avertissement' => $avertissement,
				   'jabber' => $jabber,
				   'icq' => $icq];

	}

	//Keep the ID separate var for look into posts_pts
	$idm = (int)$id;

	$stmt->close();

	$inc = 1;

	$stmt = $mysqli->prepare('SELECT topic_id, post_id, poster_id, action, comment FROM posts_pts WHERE voter_id = ?');
	$stmt->bind_param('i', $idm);
	$stmt->execute();
	$stmt->bind_result($idto, $postid, $posterid, $action, $comment);
	while($stmt->fetch()) {

	$uf = get_user_info($posterid, 'us_id');

	$admsrcus[] =  ['idto', $inc,
				   'topic_id', $idto,
				   'post_id', $postid,
				   'poster_id', $uf['username'],
				   'action', $action,
				   'comment', $comment];

	++$inc;

	}

	$inc = 1;

	$stmt = $mysqli->prepare('SELECT post_id, voter_id, action, comment FROM posts_pts WHERE poster_id = ?');
	$stmt->bind_param('i', $idm);
	$stmt->execute();
	$stmt->bind_result($postid, $voterid, $action, $comment);
	while($stmt->fetch()) {

	$uss = get_user_info($voterid, 'us_id');

	$admsrcus[] =  ['idvo', $inc,
				   'post_id', $postid,
				   'voter_id', $uss['username'],
				   'action', $action,
				   'comment', $comment];
	}

	return $admsrcus;
}

//Update email target user (admin)
function adm_uemail($id, $uid, $email, $mysqli) {

	//Update sections (last post / poster / ts
	$stmt = $mysqli->prepare('UPDATE users SET email = ? WHERE id = ?');
	$stmt->bind_param("si", $email, $uid);
	$stmt->execute();
	$stmt->close();


	//Log action
	$action = 'email';
	$stmt = $mysqli->prepare('INSERT INTO log_eyes(staff, action) VALUES(?,?)');
	$stmt->bind_param("is", $id, $action);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

function adm_uicq($id, $uid, $icq, $mysqli) {

	//Update sections (last post / poster / ts
	$stmt = $mysqli->prepare('UPDATE users SET icq = ? WHERE id = ?');
	$stmt->bind_param("ii", $icq, $uid);
	$stmt->execute();
	$stmt->close();

	//Log action
	$action = 'icq';
	$stmt = $mysqli->prepare('INSERT INTO log_eyes(staff, action) VALUES(?,?)');
	$stmt->bind_param("is", $id, $action);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

function adm_ujabber($id, $uid, $jabber, $mysqli) {

	//Update sections (last post / poster / ts
	$stmt = $mysqli->prepare('UPDATE users SET jabber = ? WHERE id = ?');
	$stmt->bind_param("si", $jabber, $uid);
	$stmt->execute();
	$stmt->close();

	//Log action
	$action = 'jabber';
	$stmt = $mysqli->prepare('INSERT INTO log_eyes(staff, action) VALUES(?,?)');
	$stmt->bind_param("is", $id, $action);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

function adm_udis($id, $uid, $pts, $mysqli) {

	//Not allow minus
	if($pts < 0) $pts = 0;

	//Update sections (last post / poster / ts
	$stmt = $mysqli->prepare('UPDATE users SET vote_left = ? WHERE id = ?');
	$stmt->bind_param("ii", $pts, $uid);
	$stmt->execute();
	$stmt->close();

	//Log action
	$action = 'voteleft';
	$stmt = $mysqli->prepare('INSERT INTO log_eyes(staff, action) VALUES(?,?)');
	$stmt->bind_param("is", $id, $action);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

function adm_upts($id, $uid, $pts, $mysqli) {

	//Not allow minus
	if($pts < 0) $pts = 0;

	//Update sections (last post / poster / ts
	$stmt = $mysqli->prepare('UPDATE users SET pts = ? WHERE id = ?');
	$stmt->bind_param("ii", $pts, $uid);
	$stmt->execute();
	$stmt->close();

	//Log action
	$action = 'pts';
	$stmt = $mysqli->prepare('INSERT INTO log_eyes(staff, action) VALUES(?,?)');
	$stmt->bind_param("is", $id, $action);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

//Add ban
function adm_ban($uid, $reason, $id, $mysqli) {

	//Update sections (last post / poster / ts
	$stmt = $mysqli->prepare('UPDATE users SET email = "bans", pts = 0, badges = 36, show_badge = 36, reason = ? WHERE id = ?');
	$stmt->bind_param("si", $reason, $uid);
	$stmt->execute();
	$stmt->close();

	//Log ban
	$stmt = $mysqli->prepare('INSERT INTO log_ban(staff, member) VALUES(?,?)');
	$stmt->bind_param("ii", $id, $uid);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

//Add warning
function adm_wa($reason, $post, $uid, $id, $mysqli) {

    //Increment field on user table (+1 avertissement)
    $stmt = $mysqli->prepare('UPDATE users SET avertissement = avertissement + 1 WHERE id = ?');
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->close();

    //Insert the avertissement (reason / post / admin...)
    $stmt = $mysqli->prepare('INSERT INTO avertissement (iduser, reason, staff, post) VALUES(?,?,?,?)');
    $stmt->bind_param("isii", $uid, $reason, $id, $post);
    $stmt->execute();
    $stmt->close();

    return 'ok';
}

//Get details warning on target ID
function adm_gwa($id, $mysqli) {

    //Get details
    $stmt = $mysqli->prepare('SELECT reason, staff, post FROM avertissement WHERE iduser = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($reason, $staff, $post);
    while($stmt->fetch()) {

        //Conv id to username
        $ustaff = get_user_info($staff, 'us_id');

        $details[] = ['reason', $reason,
                      'staff', $ustaff['username'],
                      'post', $post];
    }

    return $details;

}

//Change the password of the target user
/*function adm_pw($uid, $pass, $id, $mysqli) {

	$password = password_hash($pass, PASSWORD_DEFAULT);

	//Update password
	$stmt = $mysqli->prepare('UPDATE users SET password = ? WHERE id = ?');
	$stmt->bind_param("si", $password, $uid);
	$stmt->execute();
	$stmt->close();

	//Log action
	$action = 'pw';
	$stmt = $mysqli->prepare('INSERT INTO log_eyes(staff, action) VALUES(?,?)');
	$stmt->bind_param("is", $id, $action);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}*/

//Add/Remove badge to the target user
function adm_addbdg($badge, $newbdg, $uid, $id, $mysqli) {

	//Update badge
	$stmt = $mysqli->prepare('UPDATE users SET badges = ?, show_badge = ? WHERE id = ?');
	$stmt->bind_param("sii", $newbdg, $badge, $uid);
	$stmt->execute();
	$stmt->close();

	//Log action
	$stmt = $mysqli->prepare('INSERT INTO log_bdg(badge, user, staff) VALUES(?,?,?)');
	$stmt->bind_param("iii", $badge, $uid, $id);
	$stmt->execute();
	$stmt->close();

	return 'ok';
}

function donate($system, $amount, $who, $mysqli) {

    $url = 'https://www.payssion.com/api/v1/payment/create';

    $sig = 'aff23ac42349b96b|'.$system.'|'.$amount.'|EUR|'.$who.'|'.$who.'|5790635aded461dda5c6e5228315cfc5';

    $hashsig = md5($sig);

    $data = ["api_key" => "aff23ac42349b96b",
        "pm_id" => $system,
        "description" => "Donation WM",
        "amount" => $amount,
        "currency" => "EUR",
        "track_id" => $who,
        "sub_track_id" => $who,
        "api_sig" => $hashsig,
        "language", "fr"];


    // use key 'http' even if you send the request to https://...
        $options = ['http' => ['header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

  //var_dump($result);
    return $result;
}

//Trade verified pass
function trck($giver, $given, $mysqli) {

    //Log godfather
    $stmt = $mysqli->prepare('INSERT INTO log_godfather (giver, given) VALUES(?,?)');
    $stmt->bind_param("ii", $giver, $given);
    $stmt->execute();
    $stmt->close();

    //Update given
    $stmt = $mysqli->prepare('UPDATE users SET badges = 27, show_badge = 27 WHERE id = ?');
    $stmt->bind_param("i", $given);
    $stmt->execute();
    $stmt->close();

    //Update giver
    $stmt = $mysqli->prepare('UPDATE users SET pts = pts - 5 WHERE id = ?');
    $stmt->bind_param("i", $giver);
    $stmt->execute();
    $stmt->close();

}
//Trade Citizen
function trcy($uid, $badges, $mysqli) {

    //Update giver
    $stmt = $mysqli->prepare('UPDATE users SET pts = pts - 10, badges = ? WHERE id = ?');
    $stmt->bind_param("si", $badges, $uid);
    $stmt->execute();
    $stmt->close();

}
//Array categories name (avoid one JOIN SQL)
$subName = array(
'1' => 'Règles/Informations',
'2' => 'Présentation',
'3' => 'Avis',
'4' => 'Café',
'5' => 'Films (DvDrip)',
'6' => 'Séries télé',
'7' => 'Album Musique',
'8' => 'Appz Windows',
'9' => '[Majeur] Films',
'16' => 'GameZ PC',
'19' => 'Dead/Leech',
'20' => 'Divers',
'22' => 'Demande de badge',
'27' => 'E-Book',
'29' => 'Tutoriels',
'31' => 'Demandes graphiques',
'33' => 'Graphisme',
'35' => 'Films (Screener et TS)',
'36' => 'Appz Linux/Mac/Freebsd',
'38' => 'GameZ Consoles',
'41' => 'Clip / Concert',
'40' => 'Single Musique',
'42' => 'Films (Vo et VoSt)',
'44' => 'Anti-Virus / Anti-spyware / Anti-trojan...',
'45' => 'Films (Exclue)',
'46' => 'Full DvD / HD',
'51' => 'Discographie',
'56' => 'Docs, spectacles',
'57' => 'Informatique Générale',
'58' => 'Dessin animés / Animes / Mangas',
'59' => 'Linux, Mac\'OS, Freebsd',
'60' => 'Gamer',
'66' => 'H-Q',
'68' => 'Programmation/Coding',
'70' => 'Android / Iphone / Windows Phone',
'79' => 'Section M.A.O',
'81' => 'Sourds et malentendants',
'84' => 'Panthéon',
'85' => 'Sport');

//Array icons for categories
$icons = array(
1 => 'fa fa-dot-circle-o',
2 => 'fa fa-male',
3 => 'fa fa-comments-o',
17 => 'fa fa-search-plus',
84 => 'fa fa-archive',
19 => 'fa fa-crosshairs',
22 => 'fa fa-star',
4 => 'fa fa-coffee',
83 => 'fa fa-globe',
68 => 'fa fa-code',
57 => 'fa fa-desktop',
29 => 'fa fa-file',
59 => 'fa fa-linux',
60 => 'fa fa-gamepad',
31 => 'fa fa-search-plus',
33 => 'fa fa-file-image-o',
34 => 'fa fa-coffee',
45 => 'fa fa-film',
5 => 'fa fa-film',
35 => 'fa fa-film',
42 => 'fa fa-film',
46 => 'fa fa-film',
6 => 'fa fa-film',
81 => 'fa fa-deaf',
56 => 'fa fa-film',
58 => 'fa fa-film',
85 => 'fa fa-futbol-o',
8 => 'fa fa-rocket',
36 => 'fa fa-linux',
44 => 'fa fa-exclamation-circle',
16 => 'fa fa-gamepad',
37 => 'fa fa-gamepad',
38 => 'fa fa-gamepad',
7 => 'fa fa-music',
40 => 'fa fa-music',
51 => 'fa fa-music',
41 => 'fa fa-music',
71 => 'fa fa-music',
66 => 'fa fa-music',
79 => 'fa fa-music',
20 => 'fa fa-file',
70 => 'fa fa-mobile',
27 => 'fa fa-book');
?>
