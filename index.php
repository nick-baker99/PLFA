<?php 
    session_start();
    @date_default_timezone_set("Europe/London"); 
    require_once("config.php");
    require_once("scripts/api-football-functions.php");
    require_once("scripts/twitterAPI-functions.php");
    $loggedIn = false;
    //if user is logged in
    if(isset($_SESSION["uName"])) {
        //set login variable to true
        $loggedIn = true;
    }
    //if user clicks the logout button delete session
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
    <link rel="stylesheet" href="CSS/indexCss.css">
    <title>Homepage</title>
    <script>
        //session to store the current size of the premier league table
        sessionStorage.setItem("size", "small");
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
                if($loggedIn) {
                    //if user is logged in
                    //display first name along with a logout button
                    echo "<a id='uName' href='account.php' class='uNameText'><b>".$_SESSION['name']."</b></a>";
                    echo "<form action='' method='POST'>
                        <input type='submit' name='logout' value='LOGOUT' class='logout-button'></form>";
                }else {
                    //if user is not logged in just display login button
                    echo "<form action='login.php' method='POST'>
                        <input type='submit' value='LOGIN' name='login' class='login-button'>
                        <input type='hidden' value='index.php' name='redirect'></form>";
                }
            ?>
        </div>
    </header>
    <div class="main-container" id="main">
        <div class="top" id="top">
            <h2 class="heading">The Premier League</h2>
        </div>
        <div class="left" id="left">
            <div class='side-menu'>
                <p class='fixture-title'>Todays Fixtures</p>
                <!-- Fixtures Widget -->
                <div class='fixtures-container'>
                <?php 
                    //get the current date
                    $date = date("Y-m-d");
                    //call the api to return fixtures on todays date
                    $todayFixtures = getFixtures($date, $apiFootKey, $apiFootHeader)->response;
                    //get the output HTML for the fixtures
                    $fixtureOutput = getFixturesOutput($todayFixtures);
                    //if the API returns fixtures then display them
                    if($fixtureOutput !== "") {
                        echo $fixtureOutput;
                    }
                    //if the API doesn't return any fixtures then tell the user there are no fixtures today
                    else {
                        echo "<p class='no-fixtures' style='margin: 15px auto;'>There are no Premier League Fixtures today</p>";
                    }
                    ?>
                </div>
                <!-- Football News widget -->
                <!-- it was planned to use an API to load these news stories automatically but I couldn't find a free API to do this
                so the news stories have been entered manually, this is something that I will fix in the future -->
                <div class="news-container">
                    <p class="news-heading">Football News</p>
                    <div class="news-item">
                        <div class="news-left">
                            <p>UEFA ready to punish Barcelona, Real Madrid and Juventus more harshly than the rest of
                                the Super League</p>
                        </div>
                        <div class="news-right">
                            <img src="images/news-photos/Aleksander-Ceferin-1.jpg" alt="News Photo" class="news-photo">
                        </div>
                    </div>
                    <div class="news-item">
                        <div class="news-left">
                            <p>Atletico Madrid fullback Trippier offered to Everton</p>
                        </div>
                        <div class="news-right">
                            <img src="images/news-photos/trippier.jpeg" alt="News Photo" class="news-photo">
                        </div>
                    </div>
                    <div class="news-item">
                        <div class="news-left">
                            <p>Revealed: Manchester United ready to try stunning swap deal to sign Premier League star</p>
                        </div>
                        <div class="news-right">
                            <img src="images/news-photos/solskjaer-soucek-lingard-man-utd.jpg" alt="News Photo" class="news-photo">
                        </div>
                    </div>
                    <div class="news-item">
                        <div class="news-left">
                            <p>Leeds face Man City competition for Ajax fullback Tagliafico</p>
                        </div>
                        <div class="news-right">
                            <img src="images/news-photos/tagliafico.jpeg" alt="News Photo" class="news-photo">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="middle" id="middle">
            <p class="welcome-title">Welcome!</p>
            <p class='welcome'>Hello football fanatics, welcome to the Premier League Fan web application! (PLFA).
                PLFA is a service that all football fans can find useful, whether you are looking for entertainment, football information or
                a way to socialise with other fans like yourselves PLFA has got you covered. Across this website you will see football related
                data such as match fixtures/results, league standings, football news and more. Socialise with other fans on our chatrooms page,
                there you will find dedicated chatrooms for each team in the Premier League. If you're looking for entertainment then take part in
                our fantasy football competition, head to the fantasy page to create a team.</p>
            <h3>Current Premier League Table</h3>
            <!-- Premier League Table -->
            <div class="pl-table" id="plTable">
                <?php 
                    //create the table headers
                    echo "<div class='table-head' id='tableHead'>
                        <div class='pos'><label class='head-text'>Position</label></div>
                        <div class='club'><label class='head-text'>Club</label></div>
                        <div class='played'><label class='head-text'>Played</label></div>
                        <div class='won'><label class='head-text'>W</label></div>
                        <div class='drawn'><label class='head-text'>D</label></div>
                        <div class='lost'><label class='head-text'>L</label></div>
                        <div class='gd'><label class='head-text'>GD</label></div>
                        <div class='points'><label class='head-text'>Points</label></div>
                    </div>";
                    //call the API to return the league standings
                    $response = callApi($standings, $apiFootHeader, $apiFootKey)->response[0]->league;
                    $i = 1;
                    //loop over the API response and output each team as a row in the league table
                    foreach($response->standings[0] as $team) {
                        echo "<div class='table-row' id='tableRow'>
                            <div class='pos'><label class='team-info'>".$i.".</label></div>
                            <div class='club'><img src='".$team->team->logo."' class='team-logo-img'><label class='team-info'>".$team->team->name."</label></div>
                            <div class='played'><label class='team-info'>".$team->all->played."</label></div>
                            <div class='won'><label class='team-info'>".$team->all->win."</label></div>
                            <div class='drawn'><label class='team-info'>".$team->all->draw."</label></div>
                            <div class='lost'><label class='team-info'>".$team->all->lose."</label></div>
                            <div class='gd'><label class='team-info'>".($team->all->goals->for - $team->all->goals->against)."</label></div>
                            <div class='points'><label class='team-info'><b>".$team->points."</b></label></div>
                        </div>";
                        $i++;
                    }
                ?>
            </div>
            <!-- button that when clicked expands the league table -->
            <button type="button" onclick="changeSize()" id="changeSize" class="change-size" value="0">Show more</button>
        </div>
        <div class="right" id="right">
            <div class='side-menu'>
                <!-- Twitter Widget -->
                <div class="twitter-widget">
                    <div class="twitter-logo-box"><img src="images/general/twitterLogo.png" alt="Twitter Logo" class="twit-logo"></div>
                    <?php echo getTweets(); ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function changeSize() {
            //get the current size of the league table which is stored in the session
            var currentSize = sessionStorage.getItem("size");
            //check if its small or large
            if(currentSize == "" || currentSize == "small") {
                //if the table is small and the button is pressed then change the size to big
                document.getElementById("main").style.height = "120%";
                document.getElementById("plTable").style.height = "auto";
                document.getElementById("changeSize").innerHTML = "Show less";
                sessionStorage.setItem("size", "big");
            //if the table is already big then change it back to small    
            }else {
                document.getElementById("main").style.height = "100%";
                document.getElementById("plTable").style.height = "193px";
                document.getElementById("changeSize").innerHTML = "Show more";
                sessionStorage.setItem("size", "small");
            }
        }
    </script>
</body>
</html>