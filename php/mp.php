<?php

//If javascript enabled
if(!isset($nojs) || !$nojs)
    require('funcwm.php');

//Must be connected
if(!$is_connected)
    exit ('<div id=notcon>Vous devez être connecté pour accéder au contenu de cette page</div>');

//Check if the user is a no verified account
if((int)$uinfos['us_show_badge'] === 28 || (int)$uinfos['us_show_badge'] === 36)
    exit ('<div id=notcon>Le status de votre compte ne vous permet pas d\'avoir une boite privée</div>');

    //Send MP (TOR)
    if($is_connected  && isset($_POST['mp_twho']) && mb_strlen($_POST['mp_twho']) >= 3 && isset($_POST['mp_title']) && mb_strlen($_POST['mp_title']) >= 3 && mb_strlen($_POST['mp_title']) <= 100 && isset($_POST['mp_message']) && mb_strlen($_POST['mp_message']) >= 3 && mb_strlen($_POST['mp_message']) < 1000) {

        //Set $_POST
        $receipt = $_POST['mp_twho'];
        $title = $_POST['mp_title'];
        $message = $_POST['mp_message'];

        //Get the id account of the target
        $targetid = get_user_info($receipt, 'username');

        if(!$staff) {

        //If the user is a ghost or is ban
        if((int)$uinfos['us_show_badge'] === 28 || (int)$uinfos['us_show_badge'] === 36)
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
        if(empty($targetid['us_id'] ))
            exit('Ce membre n\'existe pas');

            //Check if the user can send MP (limit anti flood)
            include(__DIR__.'check.php');
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

$gurl = ($nojs) ? $_SERVER['REQUEST_URI'] : $_SERVER['HTTP_REFERER'];

    //Check URI (read or display MP's)
    if(strstr($gurl, '-')) {

        //Check if the target is not ban
        if((int)$uinfos['us_show_badge'] === 36)
                exit;

        //Read target MP, get the ID
        preg_match('/mp\-([0-9a-z]+)/', $gurl, $mid);

        if(is_numeric($mid[1])) {

            $message = read_mp($uinfos['us_id'], $mid[1], $mysqli);

            if(empty($message[0]))
                echo '<div id=mp_msg>Le message est inexistant ou n\'avez pas les autorisations nécessaire pour y accéder</div>';
            else
                echo '<div id=mp_msg><pre>'.$purifier->purify(htmlspecialchars($message[1])).'</pre><a id=mp_wrn href="/mp-new-'.$message[2].'" class="donot mp_answer"> Répondre</a></div> ';

         }

        else if($mid[1] === 'new') {

                preg_match('/mp\-([new]{3})\-?(?=([0-9]+)*)/', $gurl, $twh) ;

                $url = (is_numeric($twh[2]) ? get_user_info($twh[2],'us_id') : false);

                echo '<div id=mp_msg>
                        <form id=mp_new name=mp_new method="post" action="/mp">
                        <input type="text" id=mp_twho name="mp_twho" placeholder="Destinataire" data-holder="Destinataire" '.((!$url) ? '' : 'value="'.$purifier->purify($url['username']).'"').'>
                        <input type="text" id=mp_title name="mp_title" placeholder="Titre du message" data-holder="Titre du message">
                        <textarea id=mp_message name=mp_message placeholder="Votre message..." data-holder="Votre message..."></textarea>
                        <input id=mp_snewt type="submit"  class="donot mp_answer" value="Envoyer">
                        </form>
                      </div>';
         }

         else
             echo '<div id=mp_msg>
                            <p>Bienvenue dans votre boite MP <i>(message privé)</i> </p>
                            <p><i class="fa fa-exclamation-triangle"></i> Une fois ouvert, un message privé reçu est automatiquement supprimé.</p>
                             '.(($uinfos['us_show_badge'] === 27 ) ? ' <p><i class="fa fa-exclamation-triangle"></i>Le status de votre compte vous permet d\'envoyer des messages privés qu\'aux membres du staff</p>' : '').'
                             <a id=mp_wrn href="/mp-new"  class="donot mp_answer"> Envoyer un message</a>
                  </div>';
    }

    //Get every MP
    $mp = get_mp($uinfos['us_id'], $mysqli);

    if($nojs && !(int)$uinfos['us_show_badge'] !== 36)
        echo '<div id=mp_msg> <p>Bienvenue dans votre boite MP <i>(message privé)</i> </p><p><i class="fa fa-exclamation-triangle"></i> Une fois ouvert, un message privé reçu est automatiquement supprimé.</p>'.(($uinfos['us_show_badge'] === 27 ) ? ' <p><i class="fa fa-exclamation-triangle"></i>Le status de votre compte vous permet d\'envoyer des messages privés qu\'aux membres du staff</p>' : '').'<a id=mp_wrn href="/mp-new"  class="donot mp_answer"> Envoyer un message</a></div>';

        echo '<table id=tb_mp><tr class=tb_first><td><i class="fa fa-clock-o"></i> Date</td><td><i class="fa fa-user"></i> De</td><td><i class="fa fa-file-text-o"></i> Titre</td></tr>';

    if(empty($mp[0]))
        echo '<tr><td>Vous n\'avez aucun nouveau message</tr>';

    else if(!(int)$uinfos['us_show_badge'] === 36)
        exit;

    else
        foreach($mp as $message) {

        $sender = get_user_info($message[1], 'us_id');
        $time = date('d-n-Y à h:m', strtotime($message[3]));

        echo'<tr><td>'.$time.'</td><td>'.$sender['username'].'</td><td><a id=mp_wrn href="/mp-'.$purifier->purify($message[0]).'" class=donot>'.$purifier->purify(htmlspecialchars($message[2])).'</a></td></tr>';

        }

    echo '</table>';

?>