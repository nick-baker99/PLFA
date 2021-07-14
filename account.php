<?php 
    session_start();
    require_once("config.php");
    $loggedIn = false;
    $fTeam = false;
    //if user is logged in
    if(isset($_SESSION["uName"])) {
        //set logged in variable to true
        $loggedIn = true;
        //sql statement to find the information about the user's favourite team
        $findTeamSql = "SELECT * FROM teams WHERE teamID='".$_SESSION["team"]."'";
        //execute sql query and save result
        $teamInfoResult = mysqli_query($conn, $findTeamSql)
        or die (mysqli_error($conn));
        $teamInfo = mysqli_fetch_array($teamInfoResult);
    }
    //sql statement to find user info using the session username
    $findInfoSql = "SELECT * FROM users WHERE username='".$_SESSION["uName"]."'";
    $result = mysqli_query($conn, $findInfoSql)
        or die(mysqli_error($conn));
    $userInfo = mysqli_fetch_array($result);
    //if the user has created a fantasy team
    if($userInfo["FteamID"] !== null) {
        //set fantasy team variable to true
        $fTeam = true;
    }
    //if the user clicks logout at any point end the session and go to homepage
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
        <link rel="stylesheet" href="CSS/accountCss.css">
        <title>My Account</title>
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
                    if($loggedIn) {
                        //if user is logged in
                        //display first name along with a logout button
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
                <div class='side-menu' style="text-align:center;">
                    <p class="fteam-label">Fantasy Football</p>
                    <?php 
                    // if the user has created a fantasy team before
                    if($fTeam) {
                        //find the team name and the score of the user's fantasy team 
                        $getFteam = "SELECT FteamName, score FROM fantasy_teams WHERE FteamID = ".$userInfo["FteamID"]."";
                        $result = mysqli_fetch_array(mysqli_query($conn, $getFteam));
                        //display fantasy team info
                        echo "<p class='fteam-name'>".$result["FteamName"]."</p>
                        <p class='fteam-score'>Points: ".$result["score"]."</p>";
                        //select all the fantasy teams and order by the score in descending order
                        $getAll = "SELECT FteamID, score FROM fantasy_teams ORDER BY score DESC";
                        $result = mysqli_query($conn, $getAll)
                            or die(mysqli_error($conn));
                        $i = 1;
                        //loop over fantasy teams until user's team is found and display what position in the league they are
                        while($row = mysqli_fetch_array($result)) {
                            if($row["FteamID"] == $userInfo["FteamID"]) {
                                echo "<p class='position'>League Position: $i</p>";
                            }
                            $i++;
                        }
                    }else {
                        //if user has not created a fantasy team make a link to create one
                        echo "<p class='no-team'>Create a Fantasy Football Team to see your score here</p>
                        <form action='fantasy.php' method='POST'>
                        <input type='submit' name='createFantasy' class='fantasy-btn' value='Create Fantasy Team'></form>";
                    }?>
                </div>
            </div>
            <div class="right">
                <div class="top-right">
                    <?php 
                    //display the logo of the user's favourite team
                    echo "<div class='top-info'><div class='img-box'><img src='".$teamInfo["logo"]."' class='profile-pic' alt='Profile Pic'/></div>";
                    //display username of logged in user
                    echo "<p>".$userInfo["username"]."</p></div>"; ?>
                </div>
                <div class="user-container">
                    <?php 
                        //display user info
                        echo "<p>First Name: ".$userInfo["FName"]."</p>
                        <p>Last Name: ".$userInfo["LName"]."</p>
                        <p>Your Team: ".$teamInfo["name"]."</p>
                        <p>Email: ".$userInfo["email"]."</p>";
                    ?>
                <!-- this button will allow the user the change their favouite team but it has not yet been implemented -->
                <button class="change-btn">Change Team</button>
                </div>
            </div>
        </div>
    </body>
</html>