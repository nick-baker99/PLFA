<?php 
    require_once("../config.php");
    @date_default_timezone_set("Europe/London");
    //get the current date
    $date = date("Y-m-d H:i:s");
    //SQL statement to insert the message into the database
    $sql = "INSERT INTO messages (text, dateTime, username, teamID)
    VALUES ('".$_REQUEST["text"]."', '$date', '".$_REQUEST["uName"]."', '".$_REQUEST["cr"]."')";
    //execute SQL query
    mysqli_query($conn, $sql)
        or die(mysqli_error($conn));
?>
