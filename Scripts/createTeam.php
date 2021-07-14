<?php 
    require_once("../config.php");
    // Get the values passed to the http request
    if(isset($_REQUEST["q"])) {
        $playerNames = explode(',', $_REQUEST["q"]);
        $uName = $_REQUEST["uName"];
        $tName = $_REQUEST["tName"];
    } 
    // Array to store player IDs
    $players = [];
    if(isset($playerNames)) {
        // Loop over each player in the team
        foreach($playerNames as $player) {
            // SQL statement to find the ID of the player using their name
            $selectSql = "SELECT playerID FROM players
            WHERE webname='$player'";
            // execute the SQL query
            $result = mysqli_query($conn, $selectSql);
            $r = mysqli_fetch_array($result);
            // Add the players ID to the ID array
            array_push($players, $r["playerID"]);
        }
        $date = date("Y-m-d H:i:s");
        // SQL statement to insert the fantasy team into the fantasy team table in DB
        $insertSql = "INSERT INTO fantasy_teams (p1, p2, p3, p4, 
        p5, p6, p7, p8, p9, p10, p11, FTeamName, creationDate) 
        VALUES ($players[0], $players[1], $players[2], $players[3],
        $players[4], $players[5], $players[6], $players[7],
        $players[8], $players[9], $players[10], '$tName', '$date')";
        // Execute the query
        mysqli_query($conn, $insertSql)
            // If it fails print the error message
            or die(mysqli_error($conn));

        // SQL to find the fantasy team IDs
        $findIdSql = "SELECT fTeamID FROM fantasy_teams ORDER BY FteamID DESC";
        $result = mysqli_query($conn, $findIdSql);
        $r = mysqli_fetch_array($result);
        //Find the FTeamID
        $fTeamID = $r["fTeamID"];
        // Upate the FTeamID for the current user
        $sql = "UPDATE users
        SET fTeamID = $fTeamID WHERE username = '$uName'";
        // Execute
        mysqli_query($conn, $sql)
            or die(mysqli_error($conn));
        
        sleep(1);
    }
    
?>
