<?php

require __DIR__.'/../funcwm.php';

//Get the count of topic read
$file = file_get_contents(__DIR__.'/../../cache/avtk.html');

$atkdt = explode("\n", $file);

	foreach($atkdt as $flood) {

		//value[2] count | value[3] pseudo
		preg_match("/^(([\d]*)\s?(.*))$/", $flood, $value);

		//40 in minute = ban (except of level 4 <=
		if((int)$value[2] > 3) {
			$sph_flood = get_user_info($value[3], 'username');
			$reason = 'botav';
			$uid = (int)$sph_flood['us_id'];

				//Update sections (last post / poster / ts
				$stmt = $mysqli->prepare('UPDATE users SET email = "bans", pts = 0, badges = 36, show_badge = 36, reason = ? WHERE id = ?');
				$stmt->bind_param("si", $reason, $uid);
				$stmt->execute();
				$stmt->close();
				file_put_contents(__DIR__.'/../../cache/banpot.html', "\r banav -> $value[3]", FILE_APPEND | LOCK_EX);


		}
	}
?>
