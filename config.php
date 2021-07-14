<?php 
    $dbhost = "localhost";
    $dbUser = "root";
    $dbPassword = "";
    $db = "pl_fan_app";
    $leagueID = 39;

    $apiFootKey = "5f878d2bfemshef340f5e27b4eb9p1c876ajsnf6e969d86a20";
    $apiFootHeader = "https://api-football-beta.p.rapidapi.com/";

    // API URLS
    $last10 = "fixtures?last=10&league=$leagueID";
    $next10 = "fixtures?next=10&league=$leagueID";
    $standings = "standings?league=$leagueID&season=2020";

    $url = "https://fantasy.premierleague.com/api/bootstrap-static/";

    $conn = new mysqli($dbhost, $dbUser, $dbPassword, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>