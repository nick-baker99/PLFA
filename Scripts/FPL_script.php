<?php 
    require_once("../config.php");
    require_once("FPL_functions.php");
    
    while(true) {
        updatePlayers($conn, $fplData);
        updateFantasyPoints($conn);

        echo "Fantasy Football Updated!";

        sleep(86400);
    }
    
?>