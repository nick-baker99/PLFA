<?php 
    require_once("../config.php");
    //set the variables from the GET request
    if(isset($_REQUEST["q"])) {
        $q = $_REQUEST["q"];
    }
    $num = $_REQUEST["num"];
    $pos = $_REQUEST["pos"];
    //select all the players that play in the position the user is serching for
    $sql = "SELECT playerID, webname FROM players WHERE position='$pos'";
    $result = mysqli_query($conn, $sql);
    //if the user is seaching for a player by name
    if(isset($q)) {
        //if q not empty
        if($q !== "") {
            //change text to lowercase
            $q = strtolower($q);
            //find length of the string
            $len = strlen($q);
            $i = 0;
            //loop over all players to try and find the one the user searching for
            foreach($result as $player) {
                //if the input is a sub string of a player in the database
                if(stristr($q, substr($player["webname"], 0, $len))) {
                    //if first player
                    if($i == 0) {
                        //display a button with the players name
                        echo "
                        <div class='player' style='border: 1px solid lightgrey'>
                            <button class='select-button' onclick='selectPlayer(\"".$player["webname"]."\", $num)'>
                            ".$player["webname"]."</button>
                        </div>";
                    }else {
                        echo "
                        <div class='player' style='border: 1px solid lightgrey; border-top:0'>
                            <button class='select-button' onclick='selectPlayer(\"".$player["webname"]."\", $num)'>
                            ".$player["webname"]."</button>
                        </div>";
                    }
                    $i++;
                } 
            }
        }
    //if the user has not searched for a player display all players who play in that position
    } else {
        $i = 0;
        foreach($result as $player) {
            if($i == 0) {
                echo "
                <div class='player' style='border: 1px solid lightgrey'>
                    <button class='select-button' onclick='selectPlayer(\"".$player["webname"]."\", $num)'>
                    ".$player["webname"]."</button>
                </div>";
            }else {
                echo "
                <div class='player' style='border: 1px solid lightgrey; border-top:0'>
                <button class='select-button' onclick='selectPlayer(\"".$player["webname"]."\", $num)'>
                ".$player["webname"]."</button>
                </div>";
            }
            $i++;
        }
    }

?>