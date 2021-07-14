<?php 
    require_once("../config.php");

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);

    $fplData = json_decode($response, true);

    // UPDATE PLAYER INFO
    //updatePlayers($conn, $fplData);

    // UPDATE FANTASY TEAM POINTS
    //updateFantasyPoints($conn);

    function updatePlayers($conn, $fplData) {
        foreach($fplData["elements"] as $element) {
            $id = $element["id"];
            $FName = mysqli_real_escape_string($conn, $element["first_name"]);
            $LName = mysqli_real_escape_string($conn, $element["second_name"]);
            $points = $element["total_points"];
            $form = $element["form"];
            $team = $element["team"];
            $webname = mysqli_real_escape_string($conn, $element["web_name"]);

            $rowData = [$id, $FName, $LName, $points, $form, $team, $webname];

            
            fillDatabase($conn, $element, $rowData);
            updateDatabase($conn, $element, $rowData);
        }
    }

    function updateDatabase($conn, $element, $rowData) {
        $sql = "UPDATE players SET FPoints = '$rowData[3]', form = '$rowData[4]' WHERE playerID = '$rowData[0]'";

        if(!mysqli_query($conn, $sql)) {
            print("Error: ".mysqli_error($conn));
        }
    }

    function fillDatabase($conn, $element, $rowData) {
        $sql = "INSERT INTO players (playerID, FName, LName, webname, position, FPoints, form, teamID)
        VALUES ('$rowData[0]', '$rowData[1]', '$rowData[2]', '$rowData[6]', '', '', '$rowData[4]', '$rowData[5]')";

        if(!mysqli_query($conn, $sql)) {
            if(mysqli_errno($conn) !== 1062) {
                print("Error: ".mysqli_error($conn));
            }
        }
    }

    function getTeamPoints($conn, $playerIds) {
        $playerPoints = [];
        for($i = 0; $i < count($playerIds); $i++) {
            $getPointsSql = "SELECT FPoints FROM players WHERE playerID = '$playerIds[$i]'";
            $getPoints = mysqli_query($conn, $getPointsSql)
                or die (mysqli_error($conn));
            
            $result = mysqli_fetch_array($getPoints);
            array_push($playerPoints, $result[0]);
        }
        return $playerPoints;
    }

    function updateFantasyPoints($conn) {
        $getTeamsSql = "SELECT * FROM fantasy_teams";

        $teams = mysqli_query($conn, $getTeamsSql)
            or die (mysqli_error($conn));

        while($row = mysqli_fetch_array($teams)) {
            $currentDate = date("Y-m-d H:i:s");
            $dayBefore = date("Y-m-d H:i:s", strtotime($currentDate.'-1 days'));

            $playerIDs = [$row["p1"], $row["p2"], $row["p3"], $row["p4"], $row["p5"], $row["p6"], 
            $row["p7"], $row["p8"], $row["p9"], $row["p10"], $row["p11"]];
            
            $totalPoints = array_sum(getTeamPoints($conn, $playerIDs));
            
            if($row['totalPoints'] !== NULL) {
                $sql = "UPDATE fantasy_teams SET totalPoints = '$totalPoints' WHERE FteamID = '". $row["FteamID"] ."'";
                mysqli_query($conn, $sql)
                    or die($conn);
                
                if($row["creationDate"] < $dayBefore) {
                    $pointsEarned = $totalPoints - $row["totalPoints"];
                    $sql2 = "UPDATE fantasy_teams SET score = score + $pointsEarned WHERE FteamID = '". $row["FteamID"] ."'";

                    mysqli_query($conn, $sql2)
                        or die (mysqli_error($conn));
                }
            }else {
                $sql = "UPDATE fantasy_teams SET totalPoints = '$totalPoints' WHERE FteamID = '". $row["FteamID"] ."'";
                mysqli_query($conn, $sql)
                    or die($conn);
            }
        }
    }
    
?>