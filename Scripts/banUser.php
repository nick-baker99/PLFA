<?php 
    //start session
    session_start();
    date_default_timezone_set('Europe/London');
    require_once("../config.php");
    //if form is submitted
    if(isset($_POST["submit"])) {
        //get the current date and time
        $date = date("Y-m-d H:i:s");
        //if the length of ban selected in the drop-down is permanent
        if($_POST["length"] == "perm") {
            //SQL insert the ban into the banned users table with the unBan date being NULL
            $sql = "INSERT INTO banned_users (username, bannedBy, reason, date, unBan)
            VALUES ('".$_POST["uname"]."', '".$_SESSION["uName"]."', '".$_POST["reason"]."',
            '$date', NULL)";
        }else {
            //add the length of time to the current date to get unBan date
            $unBan = date("Y-m-d H:i:s", strtotime($date."+ ".$_POST["length"]." minutes"));
            //insert SQL
            $sql = "INSERT INTO banned_users (username, bannedBy, reason, date, unBan)
            VALUES ('".$_POST["uname"]."', '".$_SESSION["uName"]."', '".$_POST["reason"]."',
            '$date', '$unBan')";
        }
        //execute query
        mysqli_query($conn, $sql)
            or die(mysqli_error($conn));
        //redirect user back to the chatrooms page
        header('Location: ../chatrooms.php');
    }
?>
