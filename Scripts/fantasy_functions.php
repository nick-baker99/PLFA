<?php 
    require_once("config.php");

    $ch = curl_init();
    //call api
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);

    $fplData = json_decode($response, true);
    //this function takes in a fantasy team and returns an array of the player's scores
    function findPlayerScores($conn, $fplData, $fTeamID) {
        $sql = "SELECT * FROM fantasy_teams WHERE FteamID = '$fTeamID'";
        $result = mysqli_fetch_array(mysqli_query($conn, $sql));
        
        $playerScores = [];
        //loop over each player and find out how many fantasy points they have
        for($i = 1; $i <= 11; $i++) {
            foreach($fplData["elements"] as $element) {
                if($result["p".$i] == $element["id"]) {
                    array_push($playerScores, $element["event_points"]);
                }
            }
        }
        //return array
        return $playerScores;
    }
?>