<?php

require __DIR__.'/../funcwm.php';

//Update sections (last post / poster / ts
$stmt = $mysqli->prepare('SELECT id FROM users WHERE show_badge != 27 AND show_badge != 28 AND show_badge != 36 ');
$stmt->execute();
$stmt->bind_result($idup);
while($stmt->fetch()) {
$idu[] = $idup;
}
$stmt->close();

foreach($idu as $id) {
    $stmt = $mysqli->prepare('UPDATE topics SET moderate = 1 WHERE first_poster_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}


?>

