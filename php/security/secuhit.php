<?php

require __DIR__.'/../funcwm.php';

$fileorig = file_get_contents(__DIR__.'/../../cache/waf-botreply.html');

//Get the count of topic read
$file = file_get_contents(__DIR__.'/../../cache/wafcn-hit.html');

$atkdt = explode("\n", $file);

	foreach($atkdt as $flood) {

		//value[2] count | value[3] pseudo
		preg_match("/^(([\d]*)\s?(.*))$/", $flood, $value);

		//6 fail captcha / Reset every 6 hours
		if((int)$value[2] > 14 && strlen($value[3]) > 3 && $value[3] != 'New' && $value[3] != 'Reply') {

		$user = $value[3];
		$reason = 'bothit';

				//Update sections (last post / poster / ts
				$stmt = $mysqli->prepare('UPDATE users SET email = "bans", pts = 0, badges = 36, show_badge = 36, reason = ? WHERE username = ?');
				$stmt->bind_param("ss", $reason, $user);
				$stmt->execute();
				$stmt->close();
				file_put_contents(__DIR__.'/../../cache/banpot.html', "\r ban -> $value[3]", FILE_APPEND | LOCK_EX);
                $replace = str_replace($value[3],'', $fileorig);
                file_put_contents(__DIR__.'/../../cache/waf-botreply.html', $replace);

		}

	}
?>
