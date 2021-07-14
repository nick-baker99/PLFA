<?php 
    //start a session
    session_start();
    require_once("../config.php");
    //store the username and password passed in the GET request
    $uName = $_REQUEST["uName"];
    $pWord = $_REQUEST["pWord"];
    //$url = $_SERVER["HTTP_REFERER"];

    //pass login details and mySQL connector to the check details function
    if(!checkDetails($conn, $uName, $pWord)) {
        //if the details were not found in the database output error message
        echo "Incorrect Login Details";
    }else {
        //if the details were found return status 'found'
        echo "found";
    }

    //function takes in a mySQL connector and login details and checks if they are in the database
    function checkDetails($conn, $uName, $pWord) {
        //prepare SQL statement to find users with the username that was passed to the function
        $checkUserSql = "SELECT * FROM users WHERE username='$uName' AND password='$pWord'";
        //execute the SQL query and store the result
        $result = mysqli_query($conn, $checkUserSql)
            or die (mysqli_error($conn));
        //if no users were found return false
        if($result->num_rows == 0) {
            return false;
        }else {
            //if a user was found fetch the table row as an array
            $row = mysqli_fetch_array($result);
            //create session variables storing details of the user
            $_SESSION["uName"] = $uName;
            $_SESSION["name"] = $row["FName"];
            $_SESSION["team"] = $row["teamID"];
            $_SESSION["loggedIn"] = true;
            $_SESSION["accType"] = $row["AccountType"];
            //return true
            return true;
        }
    }
?>
