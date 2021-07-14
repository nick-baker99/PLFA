<?php 
    session_start();
    require_once("config.php");
    require_once("Scripts/fantasy_functions.php");
   
    $loggedIn = false;
    $fTeam = false;
    
    //if user is signed in
    if(isset($_SESSION["uName"])) {
        $loggedIn = true;
        //SQL statement that checks if the user has created a fantasy team
        $checkTeamSQL = "SELECT * FROM users WHERE username='".$_SESSION["uName"]."' AND FTeamID IS NOT NULL";
        //execute query
        $result = mysqli_query($conn, $checkTeamSQL)
            or die(mysqli_error($conn));
        $r = mysqli_fetch_array($result);
        //if user has fantasy team
        if($result->num_rows !== 0) {
            //find their fantasy team
            $findTeamSql = "SELECT * FROM fantasy_teams WHERE FteamID = ".$r['FteamID']."";
            $fTeamInfo = mysqli_fetch_array(mysqli_query($conn, $findTeamSql));
            //array to store the names of the players in their fantasy team
            $playerNames = [];
            //loop eleven times for each player in the team
            for($i = 1; $i <= 11; $i++) {
                //find the name and fantasy points of the player
                $selectSql = "SELECT webname, FPoints FROM players WHERE playerID = ".$fTeamInfo["p".$i]."";
                $result = mysqli_fetch_array(mysqli_query($conn, $selectSql));
                array_push($playerNames, $result["webname"]);
            }
            //find the amount of points the players have
            $points = findPlayerScores($conn, $fplData, $r["FteamID"]);
            //set fantasy team status to true
            $fTeam = true;
        }else {
            //fantasy team status is false
            $fTeam = false;
        }
    }
    //if user clicks logout button end session
    if(isset($_POST["logout"])) {
        session_destroy();
        header('Location: index.php');
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/generalCss.css">      
    <link rel="stylesheet" href="CSS/fantasyCss.css">
    <title>Fantasy Football</title>
    <script>
        //when page is first loaded delete session items for the player names
        for(var i = 1; i <= 11; i++) {
            sessionStorage.removeItem("p"+i);
        }
    </script>
</head>
<body>
    <header id="nav">
        <a href="index.php" class="logo"><img src="images/PLFA_logos/PLFA_logo_final.png" alt="PLFA Logo"></a>
        <!-- nav bar -->
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="chatrooms.php">Chats</a></li>
                <li><a href="fantasy.php">Fantasy</a></li>
                <li><a href="aboutUs.php">About</a></li>
            </ul>
        </nav>
        <div class="user-login">
            <?php 
                //if user is logged in
                //display first name along with a logout button
                if($loggedIn) {
                    echo "<a id='uName' href='account.php' class='uNameText'><b>".$_SESSION['name']."</b></a>";
                    echo "<form action='' method='POST'>
                        <input type='submit' name='logout' value='LOGOUT' class='logout-button'>
                    </form>";
                }else {
                    //if user is not logged in just display login button
                    echo "<form action='login.php' method='POST'>
                        <input type='submit' value='LOGIN' name='login' class='login-button'>
                        <input type='hidden' value='index.php' name='redirect'>
                    </form>";
                }
            ?>
        </div>
    </header>
    <div class="main">
        <div class="left">
            <!-- left-hand side menu for fantasy team -->
            <div class="selection-menu">
            <?php 
                if($loggedIn) {
                    //if the user is logged in
                    if($fTeam) {
                        //if the user has made a fantasy team before display the information about the fantasy team
                        echo "<div class='team-info'><label class'team-name-label'>Team Name:</label>
                        <br>
                        <label class='team-name'>".$fTeamInfo['FteamName']."</label>
                        <br>
                        <label class='points-label'>Points: ".$fTeamInfo['score']."</label></div>";
                        //select all fantasy teams from database and order by score descending
                        $selectFteams = "SELECT * FROM fantasy_teams
                        ORDER BY score DESC";
                        $result = mysqli_query($conn, $selectFteams);
                        //this variable counts the number of loops
                        $x = 1;
                        echo "<div class='league-table'>
                        <div class='title-row'>
                        <div class='pos-title'><label>Position</label></div>
                        <div class='tName-title'><label>Team Name</label></div>
                        <div class='points-title'><label>Points</label></div>
                        </div>";
                        //loop over fantasy teams
                        foreach($result as $fTeam) {
                            //display the the top 4 fantasy teams
                            if($x <= 4) {
                                //if the fantasy team is the user's fantasy team highlight it in pink
                                if($fTeam["FteamID"] == $r["FteamID"]) {
                                    echo "<div class='fteam-row' style='background-color:#e90052;'>";
                                }else {
                                    echo "<div class='fteam-row'>";
                                }
                                echo "<div class='pos'><label>".$x."</label></div>
                                <div class='tName'><label>".$fTeam['FteamName']."</label></div>
                                <div class='points'><label>".$fTeam['score']."</label></div></div>";
                            }else if($fTeam['FteamID'] == $r['FteamID']) {
                                echo "<div class='fteam-row-user'>
                                <div class='pos'><label>".$x."</label></div>
                                <div class='tName'><label>".$fTeam['FteamName']."</label></div>
                                <div class='points'><label>".$fTeam['score']."</label></div>
                                </div>";
                            }
                            $x++;
                        }
                        echo "</div>";
                    }else {
                        echo "
                        <div class='team-selection' style='text-align:center'>
                        <label class='team-name-label'>Team Name</label>
                        <br>
                        <input type='text' name='teamName' class='menu-box' id='teamName'>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Goalkeeper: </label><label id='p1'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Defender: </label><label id='p2'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Defender: </label><label id='p3'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Defender: </label><label id='p4'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Defender: </label><label id='p5'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Midfielder: </label><label id='p6'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Midfielder: </label><label id='p7'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Midfielder: </label><label id='p8'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Midfielder: </label><label id='p9'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Forward: </label><label id='p10'></label>
                        <br>
                        <img src='images/general/player_icon.png' alt='Player Icon' width='30px' height='30px'>
                        <label>Forward: </label><label id='p11'></label>
                        <br>
                        <div style='margin: 20px 0;'>
                        <button onclick='resetPlayers()'>Reset</button>
                        <button onclick='submitPlayers(\"".$_SESSION["uName"]."\")'>Submit</button>
                        </div>
                        <p id='error'></p></div>";
                    }
                }
                
            ?>
            </div>
        </div>
        <div class="right">
            <div class='fantasy-team-container'>
            <?php 
                if($loggedIn) {
                    if($fTeam) {
                        //display the player icon, the player's name and score for each position in the team
                        echo "
                        <div class='formation-forwards'>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-for' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[9].": ".$points[9]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-for' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[10].": ".$points[10]."
                                </label>
                            </div>
                        </div>
                        <div class='formation-midfielders'>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-mid' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[5].": ".$points[5]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-mid' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[6].": ".$points[6]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-mid' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[7].": ".$points[7]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-mid' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[8].": ".$points[8]."
                                </label>
                            </div>
                        </div>
                        <div class='formation-defenders'>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-def' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[1].": ".$points[1]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-def' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[2].": ".$points[2]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-def' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[3].": ".$points[3]."
                                </label>
                            </div>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-def' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[4].": ".$points[4]."
                                </label>
                            </div>
                        </div>
                        <div class='formation-goalkeeper'>
                            <div class='icon-slot'>
                                <img src='images/general/player_icon.png' class='player-icon-gkp' alt='Player Icon'>
                                <br>
                                <label class='player-name'>
                                ".$playerNames[0].": ".$points[0]."
                                </label></div>
                            </div>
                        </div>
                        ";
                    }else {
                        //create 11 player icons with search boxes underneath
                        //when the search boxes are clicked show a list of players the user can select
                        echo "
                            <form autocomplete='off'>
                            <div class='forwards'>
                                <div class='forward' id='forward1'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP10' onfocus='showPlayers(this.value, 10, \"Forward\")' 
                                    onfocusout='hidePlayers(this.value, 10)' onkeyup='showPlayers(this.value, 10, \"Forward\")'
                                    placeholder='Forward'>
                                    <br>
                                    <div id='showP10' class='result'></div>
                                </div>
                                <div class='forward' id='forward2' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP11' onfocus='showPlayers(this.value, 11, \"Forward\")' 
                                    onfocusout='hidePlayers(this.value, 11)' onkeyup='showPlayers(this.value, 11, \"Forward\")'
                                    placeholder='Forward'>
                                    <div id='showP11' class='result'></div>
                                </div>
                            </div>
                            <div class='midfielders'>
                                <div class='midfielder' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP6' onfocus='showPlayers(this.value, 6, \"Midfielder\")' 
                                    onfocusout='hidePlayers(this.value, 6)' onkeyup='showPlayers(this.value, 6, \"Midfielder\")'
                                    placeholder='Midfielder'>
                                    <div id='showP6' class='result'></div>
                                </div>
                                <div class='midfielder' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP7' onfocus='showPlayers(this.value, 7, \"Midfielder\")' 
                                    onfocusout='hidePlayers(this.value, 7)' onkeyup='showPlayers(this.value, 7, \"Midfielder\")'
                                    placeholder='Midfielder'>
                                    <div id='showP7' class='result'></div>
                                </div>
                                <div class='midfielder' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP8' onfocus='showPlayers(this.value, 8, \"Midfielder\")' 
                                    onfocusout='hidePlayers(this.value, 8)' onkeyup='showPlayers(this.value, 8, \"Midfielder\")'
                                    placeholder='Midfielder'>
                                    <div id='showP8' class='result'></div>
                                </div>
                                <div class='midfielder' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP9' onfocus='showPlayers(this.value, 9, \"Midfielder\")' 
                                    onfocusout='hidePlayers(this.value, 9)' onkeyup='showPlayers(this.value, 9, \"Midfielder\")'
                                    placeholder='Midfielder'>
                                    <div id='showP9' class='result'></div>
                                </div>
                            </div>
                            <div class='defenders'>
                                <div class='defender' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP2' onfocus='showPlayers(this.value, 2, \"Defender\")' 
                                    onfocusout='hidePlayers(this.value, 2)' onkeyup='showPlayers(this.value, 2, \"Defender\")'
                                    placeholder='Defender'>
                                    <div id='showP2' class='result'></div>
                                </div>
                                <div class='defender' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP3' onfocus='showPlayers(this.value, 3, \"Defender\")' 
                                    onfocusout='hidePlayers(this.value, 3)' onkeyup='showPlayers(this.value, 3, \"Defender\")'
                                    placeholder='Defender'>
                                    <div id='showP3' class='result'></div>
                                </div>
                                <div class='defender' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP4' onfocus='showPlayers(this.value, 4, \"Defender\")' 
                                    onfocusout='hidePlayers(this.value, 4)' onkeyup='showPlayers(this.value, 4, \"Defender\")'
                                    placeholder='Defender'>
                                    <div id='showP4' class='result'></div>
                                </div>
                                <div class='defender' class='player-container'>
                                    <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                    <br>
                                    <input type='text' id='searchP5' onfocus='showPlayers(this.value, 5, \"Defender\")' 
                                    onfocusout='hidePlayers(this.value, 5)' onkeyup='showPlayers(this.value, 5, \"Defender\")'
                                    placeholder='Defender'>
                                    <div id='showP5' class='result'></div>
                                </div>
                            </div>
                            <div class='goalkeeper' class='player-container'>
                                <img src='images/general/player_icon.png' class='player-icon' alt='Player Icon'>
                                <br>
                                <input type='text' id='searchP1' onfocus='showPlayers(this.value, 1, \"Goalkeeper\")' 
                                onfocusout='hidePlayers(this.value, 1)' onkeyup='showPlayers(this.value, 1, \"Goalkeeper\")'
                                placeholder='Goalkeeper'>
                                <div id='showP1' class='result'></div>
                            </div>
                            </form>";
                    }
                }else {
                    //if user is not logged in then ask them to sign in
                    echo "<div style='width: 80%; display: block; margin: 20% auto;'><h2>Sign in to play</h2>
                        <form action='login.php' method='POST'>
                        <input type='submit' name='submit' value='Login' class='log-btn' 
                        onclick='location.href(\"login.php\")' style='margin: 10px 45%;'></div>
                        </form>";
                }
            ?>
            </div>
        </div>
    </div>
    <script>
        //this function displays players from the database when the user is selecting a positon
        function showPlayers(str, num, position) {
            //display the box that the player names appear in
            document.getElementById("showP"+num).style.zIndex = 1;
            //create xmlhttp object
            var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if(this.readyState == 4 && this.status == 200) {
                        //show players returned from the PHP script
                        document.getElementById("showP"+num).innerHTML = this.responseText;
                    }
                };
            //if the user has not searched for a player
            if(str.length == 0) {
                //prepare GET request and send
                xmlhttp.open("GET", "Scripts/getPlayerNames.php?num=" + num + "&pos=" + position, true);
                xmlhttp.send();
            }else {
                //if the user has searched for a player by name add their input to the GET request
                xmlhttp.open("GET", "Scripts/getPlayerNames.php?q=" + str + "&num=" + num + "&pos=" + position, true);
                xmlhttp.send();
            }
        }
        //this function hide the players box when the user clicks off the search box
        async function hidePlayers(str, num) {
            sessionStorage.removeItem("p"+num);
            await new Promise(r => setTimeout(r, 100));
            if(sessionStorage.getItem("p"+num) == null) {
                
                document.getElementById("showP"+num).style.zIndex = -1;
            }
        }
        //this function selects a player for the team
        function selectPlayer(player, num) {
            //create array to store the players currently selected
            var players = [];
            //fill array with players already selected that are stored in sessions
            for(var i = 1; i < 11; i++) {
                if(i !== num) {
                    players.push(sessionStorage.getItem("p"+i));
                }
            }
            //check if the player that is being selected has not already been selected
            if(players.indexOf(player) !== -1) {
                //if the player has already been selected output error
                document.getElementById("searchP"+num).value = "";
                document.getElementById("error").innerHTML = "Player already selected";
            }else {
                //if player hasn't been selected disable search box for that player
                document.getElementById("searchP"+num).disabled = true;
                //add player to session storage
                sessionStorage.setItem("p"+num, player);
                //display the selected player's name underneath the player icon
                document.getElementById("showP"+num).innerHTML = "Player Selected: " + sessionStorage.getItem("p"+num);
                document.getElementById("p"+num).innerHTML = sessionStorage.getItem("p"+num);
                document.getElementById("showP"+num).style.height = "5%";
            }
        }
        //this function deletes all the session items for the player names and resets the text boxes
        function resetPlayers() {
            for(var i = 1; i <= 11; i++) {
                sessionStorage.removeItem("p"+i);
                document.getElementById("searchP"+i).value = "";
                document.getElementById("searchP"+i).disabled = false;
                document.getElementById("showP"+i).style.zIndex = -1;
                document.getElementById("p"+i).innerHTML = "";
                document.getElementById("teamName").value = "";
                document.getElementById("error").innerHTML = "";
                document.getElementById("showP"+i).style.height = "8%";
            }
        }
        //this function uses AJAX to upload the team the the database
        function submitPlayers(uName) {
            //get the team name the user input
            var tName = document.getElementById("teamName").value;
            //an array to store player names
            var players = [];
            //loop over the session items for the players and add them to the array
            for(var i = 1; i <= 11; i++) {
                if(sessionStorage.getItem("p"+i) !== null) {
                    players.push(sessionStorage.getItem("p"+i));
                }
            }
            //if the user has entered a team name
            if(tName !== "") {
                //if 11 players have been selected
                if(players.length == 11) {
                    //create a xmlhttp request
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                    if(this.readyState == 4 && this.status == 200) {
                        //once the request has been executed reload the page
                        location.reload();
                    }
                };
                //make a get request and pass the players, username and team name
                xmlhttp.open("GET", "Scripts/createTeam.php?q=" + players
                + "&uName=" + uName + "&tName=" + tName, true);
                //send request
                xmlhttp.send();
                //if the user hasn't selected 11 players print error
                }else {
                    document.getElementById("error").innerHTML = "You have not selected enough players";
                }
            //if user hasn't entered a team name print error
            }else {
                document.getElementById("error").innerHTML = "Enter a team name";
            }
        }
    </script>
</body>
</html>