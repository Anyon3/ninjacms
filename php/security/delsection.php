<?php

require '/var/www/html/warez/orig/php/funcwm.php';
$sid = 9;
	$stmt = $mysqli->prepare('SELECT id FROM topics WHERE section = ?');
    $stmt->bind_param("i", $sid);
    $stmt->execute();
    $stmt->bind_result($id);
   	 while($stmt->fetch()) {
		$data[] = $id;
	}
    $stmt->close();	

	foreach($data as $tid) {
	$stmt = $mysqli->prepare('DELETE FROM posts WHERE topic_id = ?');
	$stmt->bind_param("i", $tid);
	$stmt->execute();
	 $stmt->close();

	$stmt = $mysqli->prepare('DELETE FROM topics WHERE id = ?');
	 $stmt->bind_param("i", $tid);
        $stmt->execute();
         $stmt->close();

	}

?>
