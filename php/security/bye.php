<?php

require '/var/www/html/warez/php/funcwm.php';

//Insert here the username(s) of the account to be erase from the database
$data = array('trolleur','nazisansbite','Idiotdufn','papynosaurevieuxconquivoteaufn','marineneserajamaispresidente');

foreach($data as $user) {
    
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($idm);
    $stmt->fetch();
    $stmt->close();

     $id = (int)$idm;

    $stmt = $mysqli->prepare('SELECT t.id FROM topics t JOIN posts p ON t.id = p.topic_id WHERE t.first_poster_id = ?');
	$stmt->bind_param("i", $id);
    $stmt->execute();
   $stmt->bind_result($tid);
     while($stmt->fetch()) {
		$to[] = $tid;
	 }
     $stmt->close();

    foreach($to as $tid) {

		$stmt = $mysqli->prepare('DELETE FROM topics WHERE id = ?');
		$stmt->bind_param("i", $tid);
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare('DELETE FROM posts WHERE topic_id = ?');
		$stmt->bind_param("i", $tid);
		$stmt->execute();
		$stmt->close();


	}

  
    $stmt = $mysqli->prepare('DELETE FROM posts WHERE poster_id = ?');
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

   $stmt = $mysqli->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

}

?>
