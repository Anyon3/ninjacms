<?php
//Preview first and last posts of topic
//Use for the forum and search function
require('funcwm.php');

//Exit if user not connected
if(!$is_connected) exit;

//Sphinx API
$sph_port = 9312;
$sph_host = "localhost";
$cl = new SphinxClient();
$cl->SetServer($sph_host, $sph_port);
$cl->SetLimits(0,1,1,0);

    //Block preview first post / last post (icons eyes)
    if(!isset($_POST['lnkpa']) && isset($_POST['to_id'])) {

        //For tell to the next block to user preview settings
        $r1 = true;

    	//Set variable
    	$to_id = $_POST['to_id'];

    	$to_id = explode('-', $to_id);

    	//Check if the string match with
    	if($to_id[0] != 'first' && $to_id[0] != 'last')
    	    exit;

    	if(!is_numeric($to_id[1]))
    	    exit;

    	//Set variable
    	$wh = $to_id[0];
    	$tid = (int)$to_id[1];

    	$cl->SetFilter("to_id", array($tid));
    }

    //Block for the toolclean
    elseif(isset($_POST['lnkpa']) && !isset($_POST['to_id'])) {

        $r2 = true;
        
        $lnkpa = $_POST['lnkpa'];

        if($lnkpa === 'cleantool')
            $lnkpa = (int)$uinfos['us_pant'];
        elseif(is_numeric($lnkpa))
            $lnkpa = (int)$lnkpa;
        else 
            return false;

        $cl->SetFilter('to_id',array($lnkpa));
    }

    //Should not be here...
    else
        return false;

    //Query the sphinx engine
    $result = $cl->Query('','main mdelta');

	//Index running
	if(!$result)
		exit('<div id="noresult"><i class="fa fa-times"></i>Indexage en cours... Réesayez dans quelques minutes</div>');

    if(empty($result["matches"])) 
        return false;

		    if($r1) {
		        
    		  foreach ($result["matches"] as $display => $info) {
    			$infos[] = $result["matches"][$display]["attrs"];
    			}

    			if($wh == 'first')
    			    echo $purifier->purify(bbcode_to_html(nl2br(htmlspecialchars($infos[0]['po_message']))));

    			else
    			    echo $purifier->purify(bbcode_to_html(nl2br(htmlspecialchars($infos[0]['pl_message']))));
             }

            elseif($r2) {

        	    //Get information on tid
                $pth[] = $result["matches"][$lnkpa]["attrs"];
        
                //If the topic isn't located in phan section
                if((int)$pth[0]['to_section'] !== 84)
                    exit('badsection');
        
                //Get the block color / href (cleantool)
                preg_match('/\[color\=\#ea7a7a\]([a-z0-9\:\/\.\-\n\s]+)\[\/color\]/', $pth[0]['po_message'], $blockclean);
                 
                
                //Get the body message, cut into an array (must respect format, one link post)
                $linkex = explode("\n", $blockclean[1]);
        
                    foreach($linkex as $linkend) {
            
                        if(empty($linkend))
                            continue;
                        
                        //Get back of the actual link
                        preg_match("/topic-([0-9]*)/", $linkend, $idext);
            
                        //If the offset isn't numeric, go to the next iteration
                        if(!is_numeric($idext[1])) {
                            $build = '01';
                            $linkblc[] = $build."\n";
                            continue;
                        }

                        //Get information by tid
                        $resultext = get_topic_info($idext[1]);
            
                        //Sticky aren't allowed
                        if($resultext['to_sticky'] === 1)
                                     $build = '02';
            
                        //May link only on specified section
                        elseif(!empty($resultext['subject']) && (int)$resultext['to_section'] === 45 || (int)$resultext['to_section'] === 5 || (int)$resultext['to_section'] === 35 || (int)$resultext['to_section'] === 42 || (int)$resultext['to_section'] === 46 || (int)$resultext['to_section'] === 6 || (int)$resultext['to_section'] === 81 || (int)$resultext['to_section'] === 56 || (int)$resultext['to_section'] === 58 ||
                               (int)$resultext['to_section'] === 7 || (int)$resultext['to_section'] === 40 || (int)$resultext['to_section'] === 51 || (int)$resultext['to_section'] === 41 || (int)$resultext['to_section'] === 71 || (int)$resultext['to_section'] === 79 || (int)$resultext['to_section'] === 66 ||
                               (int)$resultext['to_section'] === 9 || (int)$resultext['to_section'] === 47 || (int)$resultext['to_section'] === 48 || (int)$resultext['to_section'] === 49 || (int)$resultext['to_section'] === 8 || (int)$resultext['to_section'] === 36 || (int)$resultext['to_section'] === 44 || (int)$resultext['to_section'] === 20 || (int)$resultext['to_section'] === 70 || (int)$resultext['to_section'] === 27)
            
                               $build = '<a id='.$resultext["to_id"].'  class=rmlnk href="'.$linkend.'"> Topic '.$resultext['to_id'].'</a><span> '.$subName[$resultext["to_section"]].'</span><span style="color:green"> Valide</span>';
            
                        //Topic ins't exist or deleted
                        elseif(!empty($resultext['subject']))
                            $build = '03';
            
                        else
                            $build = '04';
            
                        //Add the value to final array for future display
                        $linkblc[] = $build."\n";
                        
                        }

                
                echo json_encode($linkblc);
        
                return;

	           }

		  else
            return false;
		
?>