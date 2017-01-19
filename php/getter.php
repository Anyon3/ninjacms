<?php
require('funcwm.php');

//Setup $staff on connected
if($is_connected)
    $staff = staff($uinfos['us_show_badge'], $mysqli);

//Check if the cookie JS is set to enabled
if(isset($_POST['js'])) {

    if(!isset($_COOKIE['nojs']) || $_COOKIE['nojs'] !== 'js') {
        setcookie('nojs', 'js', time()+31556926, '/', '', 1, 1);
        $_COOKIE['nojs'] = 'js';
    }
}

//Upload avatar
if($is_connected && isset($_FILES['file']['error']) && $_FILES['file']['error'] === 0) {

    //not for level 5
    if((int)$uinfos['us_show_badge'] === 27 || (int)$uinfos['us_show_badge'] === 28)
        exit('lvl');

     //Set $_FILES value into variable
     $name = $_FILES['file']['name'];
     $type = $_FILES['file']['type'];
     $tmpn = $_FILES['file']['tmp_name'];
     $size = $_FILES['file']['size'];

     $chtype = ['image/png','image/jpeg','image/jpg','image/gif'];

     //Check if the size and mime type
     if($size > 419200 || !in_array($type, $chtype))
         exit('err');

    //Set extention
     if($type === 'image/png')
         $ext = "png";

    elseif($type === 'image/jpeg')
        $ext = 'jpeg';

    elseif($type === 'image/jpg')
        $ext = 'jpg';

    elseif($type === 'image/gif')
        $ext = 'gif';

    else
        exit('err');

    //Set a random filename
    $newn = mb_substr(md5(microtime()), 0, 25);

    $result = avatar($tmpn, $uinfos['us_id'], "$newn.$ext", $mysqli);

    echo $result;

}

//Login
if(isset($_POST['login']) && isset($_POST['username']) && strlen($_POST['username']) < 26 && strlen($_POST['username']) > 2 &&
    isset($_POST['password']) && strlen($_POST['password']) > 4 && isset($_SESSION['checkcap']) && isset($_POST['security']) && strlen($_POST['security']) < 20) {

		//Exit on not allowed chars
		if(preg_match("/([\/\\\'\"%])/", $_POST['username'])) exit('2');

		$security = strtolower($_POST['security']);

		//Check security captcha
		$answer = $_SESSION['checkcap']; //Get the answer previously setup inside a $_SESSION

		//Bad anwser
		if($answer != $security) {
		  //Destroy the answer session / disable captcha
		  unset($_SESSION['checkcap']);
		  exit('badcap');

	    }

		$result = login($_POST['username'], $_POST['password'], $mysqli);
		echo $result;
}

//Disconnect
elseif($is_connected && isset($_POST['disconnect'])) {

	unset($_SESSION['logwm']);
	setcookie('login', '', 1, '/', '', 1, 1);
	header('Location: https://forum.wawa-mania.ec');
	exit;
}

//Get connected username infos
elseif(isset($_POST['my']) && $is_connected) {
	$result = $uinfos['username'];
	echo $result;
}

//Password recovery
elseif(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['recovery'])) {

	//Check email if valid
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) exit('email');

	//Check length username
	if(strlen($_POST['username']) > 25 || strlen($_POST['username']) < 3 || preg_match("/([\/\\\'\"\%])/", $_POST['username'])) exit('euser');

	//Set temporary the ip (avoid flood) and check validity
	$ip = $_SERVER['HTTP_X_REAL_IP'];

	if(!filter_var($ip, FILTER_VALIDATE_IP)) exit('enow');

	$result = recovery($ip, $_POST['username'], $_POST['email'], $mysqli);

	echo $result;
}

//Registration new account
elseif(isset($_POST['username'])  && strlen($_POST['username']) < 25 &&
isset($_POST['password']) && strlen($_POST['password']) > 5 && isset($_SESSION['checkcap']) && isset($_POST['security']) &&
strlen($_POST['security']) < 20) {


    //Assign $_POST to $var
    $username = strtolower($_POST['username']);
    $password = $_POST['password'];
    $security = strtolower($_POST['security']);

    //Check username
    if(!preg_match('/^([[:alnum:]]?[[:alnum:]]+)$/', $username))
        exit('perm');

    //Check security captcha
    $answer = $_SESSION['checkcap']; //Get the answer previously setup inside a $_SESSION

    //Bad anwser
    if($answer != $security) {
        //Destroy the answer session / disable captcha
        unset($_SESSION['checkcap']);
        exit('badcap');
    }

    //Check if the pseudo is not taken
    $check = get_user_info($username, 'username');

    if($check !== false)
       exit('utaken');

    //Set IP for new registration, keep in log 24 H MAX
    $ip = $_SERVER['HTTP_X_REAL_IP'];

    //Exec register function
    $result = register($username, $password, $ip, $mysqli);

    echo $result;
}


//Get random captcha, for JS version
elseif(isset($_POST['randomcc']) && $_POST['randomcc'] === 'gen') {

    $random = captcha($mysqli);

    $hm = count($random[0]) - 1;

    $rdmo = mt_rand(0, $hm);

    //[1] ID
    $_SESSION['checkcap'] = $random[0][$rdmo][5];

    //[3] Question
    echo $random[0][$rdmo][3];
}


//Get select / option (search / move topic...)
elseif(isset($_POST['selector'])) {

    if(!file_exists(__DIR__.'/../cache/select.html'))
	   $result = select_generator($mysqli);
    else
       $result = file_get_contents(__DIR__.'/../cache/select.html');

	echo $result;
}

//Get stats
elseif($is_connected && isset($_POST['stats']) && (int)$_POST['stats'] === 1) {
	$result = ninja_stats($mysqli);
	echo $result;
}

//Get top rank users
elseif($is_connected && isset($_POST['toprank']) && $_POST['toprank'] === '1') {
    $result = top_rank($mysqli);
    echo $result;
}

//Get target user info
elseif($is_connected && isset($_POST['look4user']) && strlen($_POST['look4user']) >= 3 && strlen($_POST['look4user']) <= 40) {
	$result = get_user_info($_POST['look4user'], 'username');
	echo json_encode($result);
}

//Get badge info (topic)
/*[0] Id [1] Name [2] Subtitle [3] Description [4] Level [5] Icon [6] Groupe [7] Power */
elseif($is_connected && isset($_POST['badges']) && is_numeric($_POST['badges'])) {
	$result = get_badges((int)$_POST['badges'], $mysqli);
	echo json_encode($result);
}

/* Profile */
//Get infos user
elseif($is_connected && isset($_POST['optinfos'])) {
	//$result = get_user_info($uinfos['us_id'], 'us_id');
	echo json_encode($uinfos);
}

//Get contacts informations
elseif($is_connected && isset($_POST['optcontact'])) {
	//Get result
	$result = array(1 => $uinfos['us_jabber_visible'], 2 => $uinfos['us_jabber'], 3 => $uinfos['us_icq_visible'], 4 => $uinfos['us_icq'], 5 => $uinfos['us_email']);
	echo json_encode($result);
}

//Update password
elseif($is_connected && isset($_POST['optpassword']) && strlen($_POST['op']) >= 5 && strlen($_POST['np']) >= 5 && strlen($_POST['co']) >= 5) {
	$result = update_password($uinfos['us_id'], $_POST['op'], $_POST['np'], $mysqli);
	echo $result;
}

//Update private/public

/* Jabber */

//Update permission
elseif($is_connected && isset($_POST['jc'])) {
	//If != from boolean exit
	if($_POST['jc'] !== '0' && $_POST['jc'] !== '1') exit('2');
	$result = available_contact($uinfos['us_id'], $_POST['jc'], 'jabber', $mysqli);
	echo $result;
}

//Update IM
elseif($is_connected && isset($_POST['jabberfield'])) {
	//Format jabber id failed
	if(!filter_var($_POST['jabberfield'], FILTER_VALIDATE_EMAIL)) exit('1');
	$result = update_contact($uinfos['us_id'], $_POST['jabberfield'], 'jabber', $mysqli);
	echo $result;
}

//Delete
elseif($is_connected && isset($_POST['rem_jabber'])) {
	$result = remove_contact($uinfos['us_id'], 'jabber', $mysqli);
	echo $result;
}

/* ICQ */

//Update permission
elseif($is_connected && isset($_POST['iq'])) {
	//If != from boolean exit
	if($_POST['iq'] !== '0' && $_POST['iq'] !== '1') exit('2');
	$result = available_contact($uinfos['us_id'], $_POST['iq'], 'icq', $mysqli);
	echo $result;
}

//Update IM
elseif($is_connected && isset($_POST['icqfield'])) {
	//Format icq id failed
	if(!is_numeric($_POST['icqfield']) || strlen($_POST['icqfield']) < 5) exit('1');
	$result = update_contact($uinfos['us_id'], $_POST['icqfield'], 'icq', $mysqli);
	echo $result;
}

//Delete
elseif($is_connected && isset($_POST['rem_icq'])) {
	$result = remove_contact($uinfos['us_id'], 'icq', $mysqli);
	echo $result;
}

/* Email  */

//Update IM
elseif($is_connected && isset($_POST['emailfield'])) {
	//Format jabber id failed
	if(!filter_var($_POST['emailfield'], FILTER_VALIDATE_EMAIL)) exit('1');
	$result = update_contact($uinfos['us_id'], $_POST['emailfield'], 'email', $mysqli);
	echo $result;
}

//Delete
elseif($is_connected && isset($_POST['rem_email'])) {
	$result = remove_contact($uinfos['us_id'], 'email', $mysqli);
	echo $result;
}

//Get badge session user
elseif($is_connected && isset($_POST['optbadge'])) {
	//Get result
	$result = array(1 => $uinfos['us_badges'], 2 => $uinfos['us_show_badge']);
	echo json_encode($result);
}

//Update showed badge
elseif($is_connected && isset($_POST['showbadge']) && is_numeric($_POST['showbadge'])) {
	//Exit if the user do not get that badge
	$check_badge = explode('-', $uinfos['us_badges']);
	if(!in_array($_POST['showbadge'], $check_badge)) return false;

	$result = update_showbadge($uinfos['us_id'], $_POST['showbadge'], $mysqli);
	echo ($result) ? 1 : 2;
}

//Preview reply / create topic
elseif($is_connected && isset($_POST['textr']) && strlen($_POST['textr']) > 10 && !empty($_POST['textr'])) {

	$result = bbcode_to_html(nl2br(htmlspecialchars($_POST['textr'])));
	$result = $purifier->purify($result);
	echo $result;
}

//New topic
elseif($is_connected && isset($_POST['sendt']) && strlen($_POST['sendt']) > 4 && isset($_POST['title_to']) && strlen($_POST['title_to']) > 10 && mb_strlen($_POST['title_to']) <= 80 && isset($_POST['sectionid']) && is_numeric($_POST['sectionid']) && $_POST['sectionid'] < 100) {

	if(file_exists(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock'))
	    exit('flood');

	elseif(file_exists(__DIR__.'/../cache/flood3/'.$uinfos['us_id'].'.lock'))
	    exit('flood3');

	elseif(file_exists(__DIR__.'/../cache/flood4/'.$uinfos['us_id'].'.lock'))
	   exit('flood4');

	elseif(file_exists(__DIR__.'/../cache/flood5/'.$uinfos['us_id'].'.lock')) {

	    $tsp = date('h:i:s');
	    $userch = $uinfos["username"];

	    file_put_contents(__DIR__.'/../cache/waf-botreply.html', "$userch $tsp \r\n",  FILE_APPEND | LOCK_EX);
	    exit('flood5');
	}

	//Captcha rule for default members
	if((int)$uinfos['us_show_badge'] === 27) {

	    $answer = $_SESSION['checkcap'];
	    $security = strtolower($_POST['security']);

	    if($answer != $security) {

	        $tsp = date('h:i:s');
	        $userch = $uinfos["username"];

	        unset($_SESSION['checkcap']);
	        file_put_contents(__DIR__.'/../cache/waf-botcaptcha.html', " $userch $tsp \r\n",  FILE_APPEND | LOCK_EX);
	        exit('badcap');
	    }

	    else
	        unset($_SESSION['checkcap']);

	}

	//Exit on existing lock (flood protection, reset every minute, bypass staff)
	if(!$staff) {

		//Check if the user have permission to reply
		include __DIR__.'/check.php';
		$check = check_topic($uinfos['us_id'], $_POST['sectionid'], $mysqli);

		//Exit and return message error if check fail
		if($check === 'p1') exit('p1');

		//Write flood lock depends on level
		if($check > '2') file_put_contents(__DIR__.'/../cache/flood'.$check.'/'.$uinfos['us_id'].'.lock', '0',  LOCK_EX);
		else file_put_contents(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock', '0', LOCK_EX);

	} //End staff bypass

	//(write lock protection flood) - STAFF
	if($staff) file_put_contents(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock', '0',  LOCK_EX);

	//Sanitize variable
	$title = $purifier->purify($_POST['title_to']);
	$sectionid = (int)$_POST['sectionid'];

	$result = send_topic($uinfos['us_id'], $sectionid, $title, $_POST['sendt'], strtotime("now"), $mysqli);

	echo $result;
}

//Reply
elseif($is_connected && isset($_POST['sendr']) && strlen($_POST['sendr']) > 4 && isset($_POST['topicid']) && is_numeric($_POST['topicid'])) {

	//Exit on existing lock
	if(file_exists(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock'))
	    exit('flood');

	elseif(file_exists(__DIR__.'/../cache/flood3/'.$uinfos['us_id'].'.lock'))
	   exit('flood3');

	elseif(file_exists(__DIR__.'/../cache/flood4/'.$uinfos['us_id'].'.lock'))
	   exit('flood4');

	elseif(file_exists(__DIR__.'/../cache/flood5/'.$uinfos['us_id'].'.lock')) {

	    $tsp = date('h:i:s');
	    $userch = $uinfos["username"];

	    file_put_contents(__DIR__.'/../cache/waf-botreply.html', "$userch $tsp Reply \r\n",  FILE_APPEND | LOCK_EX);
	    sleep(2);
	   exit('flood5');
	}

	//Captcha rule for default members
	if((int)$uinfos['us_show_badge'] === 27) {

	    $answer = $_SESSION['checkcap'];
	    $security = strtolower($_POST['security']);

	   if($answer != $security) {

	        $tsp = date('h:i:s');
	        $userch = $uinfos["username"];

	        unset($_SESSION['checkcap']);
	        file_put_contents(__DIR__.'/../cache/waf-botcaptcha.html', " $userch $tsp \r\n",  FILE_APPEND | LOCK_EX);
	        sleep(2);
	        exit('badcap');
	    }

	    else
	        unset($_SESSION['checkcap']);

	}

	if(!$staff) {

		//Check if the user have permission to reply
		include __DIR__.'/check.php';
		$check = check_reply($uinfos['us_id'], $_POST['topicid'], $mysqli);

		//Exit and return message error if check fail
		if($check === 'p1') exit('p1');

		//Write flood lock depends on level
		if($check > '2')
		    file_put_contents(dirname (__FILE__) . '/../cache/flood'.$check.'/'.$uinfos['us_id'].'.lock', '0');
		else
		    file_put_contents(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock', '0');

	} //End staff bypass

	//Send the reply (write lock protection flood) - STAFF
	if($staff)
	    file_put_contents(__DIR__  . '/../cache/flood/'.$uinfos['us_id'].'.lock', '0');

	$result = send_reply($uinfos['us_id'], $_POST['topicid'], $_POST['sendr'], strtotime("now"), $mysqli);

	echo $result;
}

//Edit post
elseif($is_connected && isset($_POST['editr']) && strlen($_POST['editr']) > 10 && isset($_POST['postid']) && is_numeric($_POST['postid'])) {

	//Optional title
	if(isset($_POST['edtitle']))
	if(strlen($_POST['edtitle']) < 10 || strlen($_POST['edtitle']) > 80) return false;

	//Exit on existing lock (flood protection, reset every minute)
	if(file_exists(__DIR__.'/../cache/flood/'.$uinfos['us_id'].'.lock')) exit('flood');

	if(!$staff) {

		//Check if the user have permission to reply
		include __DIR__.'/check.php';
		$check = check_edit($uinfos['us_id'], $_POST['postid'], (isset($_POST['edtitle']) ? $_POST['edtitle'] : false), $mysqli);

		//Not authorized
		if($check === 'p1') exit('p1');
	}

	//Send the reply (write lock protection flood)
	file_put_contents(__DIR__  . '/../cache/flood/'.$uinfos['us_id'].'.lock', '0');

	$result = send_edit($uinfos['us_id'], $_POST['postid'], $_POST['editr'], strtotime("now"), $staff, (isset($_POST['edtitle']) ? $_POST['edtitle'] : false), $mysqli);

	echo $result;
}

//Vote
elseif($is_connected && isset($_POST['postid']) && is_numeric($_POST['postid']) && isset($_POST['act']) && isset($_POST['com']) && strlen($_POST['com']) > 8) {

	//if act not 0 or 1
	if($_POST['act'] != '0' && $_POST['act'] != '1') exit;

	//Clean msg
	$com = $purifier->purify($_POST['com']);

	//include
	include __DIR__.'/check.php';
	$result = check_vote($uinfos['us_id'], $_POST['postid'], $_POST['act'], $com, $mysqli);
	echo $result;
}

//Report
//&& isset($_POST['report']) && strlen($_POST['report']) >= '10' && strlen($_POST['report']) < '150'
elseif($is_connected && isset($_POST['postid']) && is_numeric($_POST['postid']) && isset($_POST['report'])) {

	//Clean msg
	$report = $purifier->purify($_POST['report']);
	//include
	include __DIR__.'/check.php';
	$result = check_report($uinfos['us_id'], $_POST['postid'], $report, $mysqli);
	echo $result;
}

//Lock (staff only)
elseif($is_connected && isset($_POST['lck']) && is_numeric($_POST['lck']) && isset($_POST['topicid']) && is_numeric($_POST['topicid'])) {

	//If != 0 OR 1
	if($_POST['lck'] != '0' && $_POST['lck'] != '1') return false;

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = lock_post($uinfos['us_id'], $_POST['topicid'], $_POST['lck'], $mysqli);

	echo $result;
}

//Spin (staff only)
elseif($is_connected === true && isset($_POST['spn']) && is_numeric($_POST['spn']) && isset($_POST['topicid']) && is_numeric($_POST['topicid'])) {

	//If != 0 OR 1
	if($_POST['spn'] != '0' && $_POST['spn'] != '1') return false;

	//If not delete his cookie / session
	if((int)$uinfos['us_show_badge'] !== 29) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = spin_post($uinfos['us_id'], $_POST['topicid'], $_POST['spn'], $mysqli);

	echo $result;
}

//Update scheme
if($is_connected && isset($_POST['scheme'])) {
	//Define scheme color
	$sc = ($_POST['scheme'] === 'dark') ? 'dark' : 'blue';
	$result = update_scheme($uinfos['us_id'], $sc, $mysqli);
	echo $result;
}

//Delete post or topic (staff only)
elseif($is_connected && isset($_POST['postid']) && is_numeric($_POST['postid']) &&  isset($_POST['sectionid']) && is_numeric($_POST['sectionid']) && isset($_POST['delp'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = delete_post($uinfos['us_id'], (int)$_POST['postid'], $mysqli);
	echo $result;
}

//Move the topic to another section
elseif($is_connected && isset($_POST['movet']) && is_numeric($_POST['sectionid']) && $_POST['sectionid'] < 100 && isset($_POST['topicid']) && is_numeric($_POST['topicid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = move_topic($uinfos['us_id'], $_POST['topicid'], $_POST['sectionid'], $mysqli);
	echo $result;
}

###########
########### /admin/
###########

//Display report
elseif($is_connected && isset($_POST['admrpt'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_report($mysqli);

	echo json_encode($result);
}

//Done a report
elseif($is_connected && isset($_POST['admck']) && is_numeric($_POST['admck'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_rptck($_POST['admck'], $uinfos['us_id'], $mysqli);
	echo ($result === '' || empty($result) || $result === null) ? 'done' : $result;
}

//Show log move
elseif($is_connected && isset($_POST['showmv'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_showmv($mysqli);
	echo json_encode($result);
}

//Show log lock
elseif($is_connected && isset($_POST['showlc'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_showlc($mysqli);
	echo json_encode($result);
}

//Show log spin
elseif($is_connected && isset($_POST['showsp'])) {

	//If not delete his cookie / session
	if((int)$uinfos['us_show_badge'] !== 29) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_showsp($mysqli);
	echo json_encode($result);
}

//Show log edit
elseif($is_connected && isset($_POST['showet'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_showet($mysqli);
	echo json_encode($result);
}

//Get full infos username (admin)
elseif($is_connected && isset($_POST['admsrcus']) && strlen($_POST['admsrcus']) <= 25) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	$result = adm_srcus($_POST['admsrcus'], $mysqli);
	echo json_encode($result);
}

elseif($is_connected && isset($_POST['admuemail']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	if(!filter_var($_POST['admuemail'], FILTER_VALIDATE_EMAIL)) exit('Incorrect');

	include __DIR__.'/check.php';
	$check = admc_uid($_POST['uid'], $mysqli);

	if(!$check) exit('Incorrect');

	$result = adm_uemail($uinfos['us_id'], $_POST['uid'], $_POST['admuemail'], $mysqli);
	echo $result;
}

elseif($is_connected && isset($_POST['admuicq']) && is_numeric($_POST['admuicq']) && strlen($_POST['admuicq']) >= 5 && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	//Check if uid is not staff members
	include  __DIR__  . '/check.php';
	$check = admc_uid($_POST['uid'], $mysqli);
	if(!$check) exit('Incorrect');

	$result = adm_uicq($uinfos['us_id'], $_POST['uid'], $_POST['admuicq'], $mysqli);
	echo $result;
}

elseif($is_connected && isset($_POST['admujabber']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	if(!filter_var($_POST['admujabber'], FILTER_VALIDATE_EMAIL)) exit('Incorrect');

	include __DIR__.'/check.php';
	$check = admc_uid($_POST['uid'], $mysqli);
	if(!$check) exit('Incorrect');

	$result = adm_ujabber($uinfos['us_id'], $_POST['uid'], $_POST['admujabber'], $mysqli);
	echo $result;
}

elseif($is_connected && isset($_POST['admudis']) && is_numeric($_POST['admudis']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	include __DIR__.'/check.php';
	$check = admc_uid($_POST['uid'], $mysqli);
	if(!$check) exit('Incorrect');

	$result = adm_udis($uinfos['us_id'], $_POST['uid'], $_POST['admudis'], $mysqli);
	echo $result;
}

elseif($is_connected && isset($_POST['admupts']) && is_numeric($_POST['admupts']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	unset($uinfos['us_id']);
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	include __DIR__.'/check.php';
	$check = admc_uid($_POST['uid'], $mysqli);
	if(!$check) exit('Incorrect');

	$result = adm_upts($uinfos['us_id'], $_POST['uid'], $_POST['admupts'], $mysqli);
	echo $result;
}

//Ban target user
elseif($is_connected && isset($_POST['admban']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//If not delete his cookie / session
	if(!$staff) {
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	include __DIR__.'/check.php';
	$check = admc_uid($_POST['uid'], $mysqli);
	if(!$check) exit('Incorrect');

	$reason = $purifier->purify($_POST['admban']);

	$result = adm_ban($_POST['uid'], $reason, $uinfos['us_id'], $mysqli);
	echo $result;
}

//Add warning
elseif($is_connected && isset($_POST['admaverto']) && isset($_POST['uid']) && is_numeric($_POST['uid']) && isset($_POST['post']) && is_numeric($_POST['post'])) {

    //check admin level
    if(!$staff) {
        setcookie('login', '', 1, '/', '', 1, 1);
        return false;
    }

    //Check if target user isn't an admin
    include __DIR__.'/check.php';
    $check = admc_uid($_POST['uid'], $mysqli);
    if(!$check) exit('Incorrect');

    $result = adm_wa($_POST['admaverto'], $_POST['post'], $_POST['uid'], $uinfos['us_id'], $mysqli);
    echo $result;
}

//Get warning details
//May be use for the user area too

elseif($is_connected && isset($_POST['target']) && is_numeric($_POST['target']) && isset($_POST['warning']) && $_POST['warning'] === '1') {

    //Check if target user isn't an admin
    include __DIR__.'/check.php';
    $check = admc_uid($_POST['uid'], $mysqli);
    if(!$check) exit('Incorrect');

    //Check if the user have the permission to get those informtions
    if((int)$_POST['target'] !== $uinfos['us_id'] && !$staff)
        exit('Incorrect');

    $result = adm_gwa($_POST['target'], $mysqli);
    echo json_encode($result);
}

//Add badge target user
elseif($is_connected && isset($_POST['addbadge']) && is_numeric($_POST['addbadge']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//target user & target badge
	$uid = (int)$_POST['uid'];
	$badge = (int)$_POST['addbadge'];

	//If not delete his cookie / session
	if(!$staff) {
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	//Check if the user is not a staff members
	include __DIR__.'/check.php';
	$check = admc_uid($uid, $mysqli);
	if(!$check) exit('Incorrect');

	//Check if the badge id is allowed
	$abdg = array(27, 28, 38, 2, 3,4, 8, 9, 11, 12, 23, 24, 15, 16, 18, 19, 20);

	//If the badge id isn't allowed or correct
	if(!in_array($badge, $abdg)) exit('Incorrect');

	//Check if the target user does not have already this badge
	$badges = get_user_info($uid, 'u_id');
	$badges = $badges['us_badges'];

	$bcut = explode('-', $badges);

	//If $badge found in $bcut exit
	if(in_array($badge, $bcut)) exit('Incorrect');

	//Rebuild the string badges, add the new badge
	$badges .= "-$badge";

	$result = adm_addbdg($badge, $badges, $uid, $uinfos['us_id'], $mysqli);
	echo 'ok';
}

//Remove badge target user
elseif($is_connected && isset($_POST['rmbadge']) && is_numeric($_POST['rmbadge']) && isset($_POST['uid']) && is_numeric($_POST['uid'])) {

	//target user & target badge
	$uid = (int)$_POST['uid'];
	$badge = (int)$_POST['rmbadge'];

	//If not delete his cookie / session
	if(!$staff) {
	setcookie('login', '', 1, '/', '', 1, 1);
	return false;
	}

	//Check if the user is not a staff members
	include __DIR__.'/check.php';
	$check = admc_uid($uid, $mysqli);
	if(!$check) exit('Incorrect');

	//Check if the badge id is allowed
	$abdg = array(27, 28, 38, 2, 3, 8, 9, 11, 12, 23, 24, 15, 16, 18, 19);

	//If the badge id isn't allowed or correct
	if(!in_array($badge, $abdg)) exit('Incorrect');

	//Check if the target user do have  this badge
	$badges = get_user_info($uid, 'u_id');
	$badges = $badges['us_badges'];

	$bcut = explode('-', $badges);

	//If only one badge, no remove
	if(count($bcut) <= 1) exit('only');

	//If $badge not found in $bcut exit
	if(!in_array($badge, $bcut)) exit('Incorrect');

	//Rebuild the string badges, add the new badge
	$a = 1;
	foreach($bcut as $value) {

		if($value != $badge) {

			if($a === 1) {
			    $badges = $value;
		      	$a++;
			}

			else
                $badges .= "-$value";
		}
	}

	//Select another badge to show
	foreach($bcut as $value) {

		if($value != $badge) {
		$badge = $value;
		break;
		}

	}

	$result = adm_addbdg($badge, $badges, $uid, $uinfos['us_id'], $mysqli);
	echo 'ok';
}

//Donate
elseif($is_connected && isset($_POST['system']) && isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] < 100) {

       $system = $_POST['system'];
       $syscheck = ['polipayment','bitcoin','yamoney','paysafecard','paybysms','paybycall','sofort','cutel','cashu','onecard','molpoints','cherrycredits','openbucks','qiwi','trustpay','bancodobrasil_br','hipercard_br','boleto_br','santander_br','nganluong_vn','bancochile_cl','redpagos_uy','dineromail_ar','enets_sg','webcash_my','bradesco_br','elo_br'];

       if(!in_array($system, $syscheck)) exit('error');

       $who = (isset($uinfos)) ? md5($uinfos['username']) : md5('noident');

       $who = mb_substr($who, 0, 5);

       $result = donate($system, $_POST['amount'], $who, $mysqli);
       echo $result;
}

//Delete each ID for the clean function
elseif($is_connected && isset($_POST['cltool']) && is_numeric($_POST['cltool'])) {

    //If not delete his cookie / session
    if(!$staff) {
        setcookie('login', '', 1, '/', '', 1, 1);
        return false;
    }

    //Set ID topic
    $idtpc = (int)$_POST['cltool'];

    //Check if the ID topic is correct
    $checkid = get_topic_info($idtpc);

    //If match one of those condition, exit
    if(empty($checkid['subject']))
        return false;

     if($checkid['to_sticky'] === 1)
        return false;

    if($checkid['to_section'] === 45 || $checkid['to_section'] === 5 || $checkid['to_section'] === 35 || $checkid['to_section'] === 42 || $checkid['to_section'] === 46 || $checkid['to_section'] === 6 || $checkid['to_section'] === 81 || $checkid['to_section'] === 56 || $checkid['to_section'] === 58 || $checkid['to_section'] === 85 ||
       $checkid['to_section'] === 8  || $checkid['to_section'] === 36 || $checkid['to_section'] === 44 ||
       $checkid['to_section'] === 16 || $checkid['to_section'] === 38 ||
       $checkid['to_section'] === 7 || $checkid['to_section'] === 40 || $checkid['to_section'] === 51 || $checkid['to_section'] === 41 || $checkid['to_section'] === 66 || $checkid['to_section'] === 79 ||
       $checkid['to_section'] === 20 || $checkid['to_section'] === 70 || $checkid['to_section'] === 27) {


        //Set the first_post_id
        $rmid = $checkid['to_first_post_id'];

        //Delete the target id
        $result = delete_post($uinfos['us_id'], $rmid, $mysqli);

        echo $idtpc;
        return;

    }

    return false;
}

//Trade verified
elseif($is_connected && isset($_POST['trck']) && isset($_POST['username']) && mb_strlen($_POST['username']) >= 3) {

    //Check - has at least 5 pts
    if((int)$uinfos['us_pts'] < 5)
        exit('ptsless');

    $username = $_POST['username'];

    //Check if the user have permission to reply
    include __DIR__.'/check.php';

    $check = check_trck($uinfos['us_id'], $username, $mysqli);

    if($check === 'ptsless' || $check === 'no')
        exit($check);

    $result = trck($uinfos['us_id'], (int)$check, $mysqli);

    echo 'ok';
}
//Trade citizen
elseif($is_connected && isset($_POST['trcy'])) {

    //Check - has at least 5 pts
    if((int)$uinfos['us_pts'] < 10)
        exit('ptsless');

    //Check if the user have permission to reply
    include __DIR__.'/check.php';

     $check = check_trcy($uinfos['us_id'], $mysqli);

     if($check === 'ptsless' || $check === 'bad')
        exit($check);

    $result = trcy($uinfos['us_id'], $check, $mysqli);

    echo 'ok';
}

//Send MP
elseif($is_connected && isset($_POST['sendmp']) && isset($_POST['receipt']) && mb_strlen($_POST['receipt']) >= 3 && isset($_POST['title']) && mb_strlen($_POST['title']) >= 3 && mb_strlen($_POST['title']) <= 100 && isset($_POST['message']) && mb_strlen($_POST['message']) >= 3 && mb_strlen($_POST['message']) < 1000) {

    //Set $_POST
    $receipt = $_POST['receipt'];
    $title = $_POST['title'];
    $message = $_POST['message'];

   //Get the id account of the target
    $targetid = get_user_info($receipt, 'username');

    if(!$staff) {

        //If the user is a ghost
        if((int)$uinfos['us_show_badge'] === 28)
            exit('Vous n\'avez pas l\'autorisation d\'envoyer un MP');

         //Check if the sender is not a level 5 (in this case, only to staff PM are allowed)
        if((int)$uinfos['us_show_badge'] === 27)
            if((int)$targetid['us_show_badge'] !== 1  && (int)$targetid['us_show_badge'] !== 29)
                exit('Vous n\'avez pas l\'autorisation d\'envoyer un MP, à l\'exception d\'un membre du staff');

             //Is the target can receive PM ?
        if((int)$targetid['us_show_badge']  === 27 || (int)$targetid['us_show_badge']  === 28 || (int)$targetid['us_show_badge']  === 36)
            exit('Ce membre ne peut pas recevoir de MP');

    }

     //The target doesn't exist
    if(empty($targetid['us_id']))
        exit('Ce membre n\'existe pas');

    //Check if the user can send MP (limit anti flood)
    include __DIR__.'/check.php';
    $check = check_smp($uinfos['username'], $mysqli);

    if($check === 'limit')
        exit('Votre limite d\'envois de MP est epuisée pour aujourd\'hui');

    elseif($check === 'error')
        exit('Erreur');

    elseif($check === 'ok')
        send_mp($uinfos['us_id'], $targetid['us_id'], $title, $message, $mysqli);

    else
        exit('Erreur');

        exit('MP envoyé avec succès');
}

else
    exit;
?>