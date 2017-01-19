<?php

require __DIR__.'/../funcwm.php';

//Rebuild the exact stats of topics / posts for each section
foreach($subName as $key => $value) {

    $f = 0;
    $cn_tid = 0;
    $cn_pid = 0;

    $stmt = $mysqli->prepare('SELECT id, num_replies FROM topics WHERE section = ?');
    $stmt->bind_param("i", $key);
    $stmt->execute();
    $stmt->bind_result($topicid, $postid);

    while($stmt->fetch()) {

        $cn_tid++;

        if($f === 0)
            $cn_pid = $postid;
        else
            $cn_pid = (int)$cn_pid + (int)$postid + 1;

        $f = 1;
    }

    $stmt->close();

    $stmt = $mysqli->prepare('UPDATE sections SET num_topics = ?, num_posts = ? WHERE id = ?');
    $stmt->bind_param("iii", $cn_tid, $cn_pid, $key);
    $stmt->execute();
    $stmt->close();

}