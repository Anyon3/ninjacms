<?php

require __DIR__.'/../funcwm.php';


	//Get each user, except badges 28 (ghost), 36 (ban)
	$bfirst  = 28;
	$bsecond = 36;

	$stmt = $mysqli->prepare('SELECT id, show_badge FROM users WHERE show_badge != ? AND show_badge != ?');
	$stmt->bind_param("ii", $bfirst, $bsecond);
	$stmt->execute();
	$stmt->bind_result($uid, $badge);
	while($stmt->fetch()) {
		$data[$uid] = $badge;
	}
	$stmt->close();

	//Assign to each user
	foreach($data as $key => $value) {

		$inf = get_badges($value, $mysqli);
		$level = $inf['level'];

		//Detect level
		if((int)$level === 5) $lup = 2;
		elseif((int)$level === 4) $lup = 5;
		elseif((int)$level === 3) $lup = 10;
		elseif((int)$level === 2) $lup = 20;
		else $lup = 20;

			//Update
			$stmt = $mysqli->prepare('UPDATE users SET vote_left = ? WHERE id = ?');
			$stmt->bind_param("ii", $lup, $key);
			$stmt->execute();
			$stmt->close();
	}

?>