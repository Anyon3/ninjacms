<?php

require __DIR__.'/../funcwm.php';

    $stmt = $mysqli->prepare('UPDATE topics SET pts = 0 WHERE section = 45 OR section = 5 OR section = 35 OR section = 42 OR section = 46 OR section = 6
        OR section = 81 OR section = 56 OR section = 58 OR section = 8 OR section = 36 OR section = 44 OR section = 16 OR section = 38 OR
        section = 7 OR section = 40 OR
        section = 51 OR section = 41 OR section = 66 OR section = 79 OR section = 20 OR section = 70 OR section = 27
         AND pts > 0');
    $stmt->execute();
    $stmt->close();
?>
