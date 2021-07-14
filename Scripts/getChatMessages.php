<?php 
    @date_default_timezone_set("Europe/London"); 
    require_once("../config.php"); 
    // Get what chatroom the user is viewing
    $chatroom = $_REQUEST["cr"];
    // Get the type of account the user is viewing from
    $accountType = $_REQUEST["accType"];
    // calculate the date 24 hours before current date
    $date = date("Y-m-d H:i:s");
    $messagesFrom = date("Y-m-d H:i:s", strtotime("-48 hours"));
    //SQL statement to find messages from a chatroom that were sent in the last 24 hours
    $findMessagesSQL = "SELECT * FROM messages 
        WHERE teamID = $chatroom AND
        dateTime BETWEEN '".$messagesFrom."' AND '$date'";
    //execute query
    $messages = mysqli_query($conn, $findMessagesSQL)
        or die(mysqli_error($conn));
    //if database response is not empty
    if($messages->num_rows !== 0) {
        //loop over all the messages
        foreach($messages as $message) {
            //SQL statement to find if the current user is in the banned users table
            $checkUserSQL = "SELECT * FROM banned_users WHERE username = '".$message["username"]."'";
            $banStatus = false;
            $result = mysqli_query($conn, $checkUserSQL)
                or die(mysqli_error($conn));
            //if the user is in the banned users table
            if($result->num_rows !== 0) {
                //loop over all the ban entries
                foreach($result as $bannedMsg) {
                    //if the user is permenantly banned set ban status to true
                    if($bannedMsg["unBan"] == NULL) {
                        $banStatus = true;
                    }
                    //if unban date is greater that the current date set ban status to true
                    else if($bannedMsg["unBan"] > date("Y-m-d H:i:s")) {
                        $banStatus = true;
                    }
                }
            }
            //find what team the use who sent the message supports
            $findUserSQL = "SELECT teamID FROM users WHERE username='".$message["username"]."'";
            $user = mysqli_fetch_array(mysqli_query($conn, $findUserSQL));
            //find the logo of the team the user supports
            $findTeamSQL = "SELECT logo FROM teams WHERE teamID = ".$user["teamID"]."";
            $team = mysqli_fetch_array(mysqli_query($conn, $findTeamSQL));
            //change format of date and time
            $newDate = date("d-m-Y H:i", strtotime($message["dateTime"]));
            //if the user is not banned
            if($banStatus == false) {
                //output the message
                echo "<div class='message-container id='messageContainer'>
                    <div class='left-msg'>
                        <p class='message-text'>".$message["text"]."</p>
                    </div>
                    <div class='right-msg'>
                        <img class='user-team-logo' src='".$team["logo"]."' alt='team Logo'>
                        <p class='uName-text'>".$message["username"]."</p>
                        <p class='message-date'>$newDate</p>
                    </div>";
                //if the user who is viewing the account is a chat moderator add a ban button to the message
                if($accountType !== "Member") {
                    echo "<div class='ban-msg'>
                    <button id='banBtn' class='ban-btn' onclick='banUser(\"".$message['username']."\")'>Ban User</button>
                    </div>";
                }
                echo "</div>";
            }
        }
    }else {
        echo "";
    }
    //sleep for 1 second
    sleep(1);
?>