<?php 
    session_start();
    require_once("config.php");
    //set logged in and fantasy team to false
    $loggedIn = false;
    $fTeam = false;
    //if user is logged in set logged in to true
    if(isset($_SESSION["uName"])) {
        $loggedIn = true;
    }
    //if user presses the logout button log out the user
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
            <!-- Nav Bar -->
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="chatrooms.php">Chats</a></li>
                    <li><a href="fantasy.php">Fantasy</a></li>
                    <li><a href="#">About</a></li>
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
                    //if user is not logged in just display login button
                    }else {
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
                    <p class="go-chat-text">Socialise with fans</p>
                    <!-- Link to chatrooms page -->
                    <form action="chatrooms.php" method="POST">
                        <input type="submit" name="goChatrooms" value="Chatrooms" class="go-btn">
                    <form>
                    <p class="go-fantasy-text">Create a Fantasy Team</p>
                    <!-- link to fantasy football page -->
                    <form action="fantasy.php" method="POST">
                        <input type="submit" name="goFantasy" value="Fantasy" class="go-btn">
                    <form>
                </div>
            </div>
            <div class="right">
                <div class="top-right">
                    <?php 
                    // display PLFA logo
                    echo "<div class='top-info'><div class='img-box' style='width:200px;'><img src='images/PLFA_logos/PLFA_logo_final.png' 
                    class='profile-pic' alt='Profile Pic'/ style='width:200px;'></div>";
                    echo "<p>Premier League Fan App (PLFA)</p></div>"; ?>
                </div>
                <div class="user-container">
                    <h2>About Us</h2>
                    <p class="about-text">Hello football fanatics, welcome to the Premier League Fan web application! (PLFA).
                    PLFA is a service that all football fans can find useful, whether you are looking for entertainment, football information or
                    a way to socialise with other fans like yourselves PLFA has got you covered. Across this website you will see football related
                    data such as match fixtures/results, league standings, football news and more. Socialise with other fans on our chatrooms page,
                    there you will find dedicated chatrooms for each team in the Premier League. If you're looking for entertainment then take part in
                    our fantasy football competition, head to the fantasy page to create a team.</p>
                </div>
            </div>
        </div>
    </body>
</html>