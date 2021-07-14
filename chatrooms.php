<?php 
    //start session
    session_start();
    require_once("config.php");
    $loggedIn = false;
    //connect to DB
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //If the user is lpgged in
    if(isset($_SESSION["uName"])) {
        $loggedIn = true;
        //find the user's favourite team from the DB
        $findTeamSql = "SELECT * FROM teams WHERE teamID='".$_SESSION["team"]."'";
        $teamInfoResult = mysqli_query($conn, $findTeamSql)
        or die (mysqli_error($conn));
        $teamInfo = mysqli_fetch_array($teamInfoResult);
        //find user's info from DB
        $findInfoSql = "SELECT * FROM users WHERE username='".$_SESSION["uName"]."'";
        $result = mysqli_query($conn, $findInfoSql)
            or die(mysqli_error($conn));
        $userInfo = mysqli_fetch_array($result);
    }
    //if logout button is pressed end session and return to homepage
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
    <link rel="stylesheet" href="CSS/chatroomsCss.css">
    <title>Chatrooms</title>
    <script>
        sessionStorage.setItem("roomID", 0);
        sessionStorage.setItem("accType", "Member");
    </script>
</head>
<body>
    <header id="nav">
        <a href="index.php" class="logo"><img src="images/PLFA_logos/PLFA_logo_final.png" alt="PLFA Logo"></a>

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
                    echo "<a id='uName' href='account.php' class='uNameText'><b>".$_SESSION['name']."</b></a>";
                    echo "<form action='' method='POST'>
                        <input type='submit' name='logout' value='LOGOUT' class='logout-button'>
                    </form>";
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
        <!-- Chatroom Selection Menu -->
        <div class="left" id="left">
        <?php 
            //if user is logged in
            if($loggedIn) {
                //find all the teams i nthe DB
                $getTeamsSQL = "SELECT * FROM teams";
                $result = mysqli_query($conn, $getTeamsSQL);
                //loop over each team and create a menu button for that team
                foreach($result as $team) {
                    echo "<button class='chat-button' onclick='getMessages(".$team["teamID"].", \"".$_SESSION["accType"]."\")'>
                        <img src='".$team["logo"]."' alt='team logo'>
                        <label>".$team["name"]."</label>
                        </button>";
                }
            }
        ?>
        </div>
        <!-- Chatroom Contianer -->
        <div class="right">
        <?php 
            //if the user is signed in
            if($loggedIn) {
                //create the chatroom contianer
                echo '
                <h2 id="roomTitle"><u>General</u></h2>
                <div class="chatroom-container" id="chatroomContainer">
                    <hr>
                    <div class="messages-container" id="messagesContainer"></div> 
                    <div class="send-message-box" id="sendMsgBox">
                        <textarea name="textBox" id="textBox" 
                        cols="30" rows="10" placeholder="Type message here..." 
                        onclick="this.placeholder=\'\'" onfocusout="this.placeholder=\'Type message here...\'"></textarea>
                        <button class="send-msg-btn" id="sendMsgBtn" onclick="sendMessage(\''.$_SESSION["uName"].'\', document.getElementById(\'textBox\').value)">Send</button>
                    </div>
                </div>';
            //if not signed in ask user to go sign in
            }else {
                echo "<div style='width: 80%; display: block; margin: 20% auto;'><h2>You must be signed in to chat</h2>
                    <form action='login.php' method='POST'>
                    <input type='submit' name='submit' value='Login' class='log-btn' 
                    onclick='location.href(\"login.php\")' style='margin: 10px 45%;'></div>
                    </form>";
            } ?>
        </div>
    </div>
    <script>
        //this function uses AJAX to get the chat messages from the database
        function getMessages(chatroomID, accountType) {
            //create a session item to store the ID of the room being viewed and the user's account type
            sessionStorage.setItem("roomID", chatroomID);
            sessionStorage.setItem("accType", accountType);
            //switch the chatroom ID to determine what the title of the room is
            switch(chatroomID) {
                case 0:
                    document.getElementById("roomTitle").innerHTML = "<u>General</u>";
                    break;
                case 1:
                    document.getElementById("roomTitle").innerHTML = "<u>Arsenal</u>";
                    break;
                case 2:
                    document.getElementById("roomTitle").innerHTML = "<u>Aston Villa</u>";
                    break;
                case 3:
                    document.getElementById("roomTitle").innerHTML = "<u>Brighton and Hove Albion</u>";
                    break;
                case 4:
                    document.getElementById("roomTitle").innerHTML = "<u>Burnley</u>";
                    break;
                case 5:
                    document.getElementById("roomTitle").innerHTML = "<u>Chelsea</u>";
                    break;
                case 6:
                    document.getElementById("roomTitle").innerHTML = "<u>Crystal Palace</u>";
                    break;
                case 7:
                    document.getElementById("roomTitle").innerHTML = "<u>Everton</u>";
                    break;
                case 8:
                    document.getElementById("roomTitle").innerHTML = "<u>Fulham</u>";
                    break;
                case 9:
                    document.getElementById("roomTitle").innerHTML = "<u>Leicester City</u>";
                    break;
                case 10:
                    document.getElementById("roomTitle").innerHTML = "<u>Leeds United</u>";
                    break;
                case 11:
                    document.getElementById("roomTitle").innerHTML = "<u>Liverpool</u>";
                    break;
                case 12:
                    document.getElementById("roomTitle").innerHTML = "<u>Manchester City</u>";
                    break;
                case 13:
                    document.getElementById("roomTitle").innerHTML = "<u>Manchester United</u>";
                    break;
                case 14:
                    document.getElementById("roomTitle").innerHTML = "<u>Newcastle United</u>";
                    break;
                case 15:
                    document.getElementById("roomTitle").innerHTML = "<u>Sheffield United</u>";
                    break;
                case 16:
                    document.getElementById("roomTitle").innerHTML = "<u>Southampton</u>";
                    break;
                case 17:
                    document.getElementById("roomTitle").innerHTML = "<u>Tottenham Hotspur</u>";
                    break;
                case 18:
                    document.getElementById("roomTitle").innerHTML = "<u>West Bromwich Albion</u>";
                    break;
                case 19:
                    document.getElementById("roomTitle").innerHTML = "<u>West Ham United</u>";
                    break;
                case 20:
                    document.getElementById("roomTitle").innerHTML = "<u>Wolverhampton Wanderers</u>";
                    break;
            }
            //create xmlhttp object
            var xmlhttp = new XMLHttpRequest();
            //when state changes
            xmlhttp.onreadystatechange = function() {
                if(this.readyState == 4 && this.status == 200) {
                    //store the message container in a variable
                    var container = document.getElementById("messagesContainer");
                    //check if the message container is being displayed on the screen
                    if(container !== null) {
                        //display the returned messages in the chat container
                        document.getElementById("messagesContainer").innerHTML = this.responseText;
                        //repeat the get messages function on a timer so the messages are updated regularly
                        setTimeout(getMessages(sessionStorage.getItem("roomID"), sessionStorage.getItem("accType")), 10000);
                    }
                }
            };
            //prepare GET request to the PHP that returns the chatroom messages
            xmlhttp.open("GET", "Scripts/getChatMessages.php?cr=" + chatroomID + "&accType=" + accountType, true);
            //send request
            xmlhttp.send();
            //execute the updateScroll funciton that keeps the user scrolled to the bottom messages
            updateScroll();
        }
        //this function displays the ban menu when a user is selected to be banned by a chat mod
        function banUser(uName) {
            //chnage the HTML of the chatroom container to the ban menu
            document.getElementById("chatroomContainer").innerHTML = "<div class='ban-menu-container'><form action='Scripts/banUser.php' method='POST' autocomplete='off'>" +
            "<h3>Ban user: " + uName + "</h3><br>" +
            "<textarea name='reason' class='ban-reason' id='banReason' placeholder='Reason for ban...' cols='30' rows='10'" +
            "onclick='this.placeholder=\"\"' onfocusout='this.placeholder=\"Type message here...\"' required></textarea><br>" +
            "<label class='ban-label'>Length of Ban: </label><br>" +
            "<select name='length' required>" +
            "<option value='30'>30 Mins</option>" +
            "<option value='60'>1 Hour</option>" +
            "<option value='720'>12 Hours</option>" +
            "<option value='1440'>1 Day</option>" +
            "<option value='perm'>Permanent</option></select><br>" +
            "<input type='hidden' name='uname' value='" + uName + "'>" +
            "<input type='submit' name='submit' value='Ban User' class='ban-usr-btn'>" +
            "</form><br><button class='cancel-btn' onclick='refreshPage()'>Cancel</button></div>";
            //hide the chatroom side menu
            document.getElementById("left").innerHTML = "";
        }
        //this function uploads uploads a message to the database
        function sendMessage(uName, text) {
            //create xmlhttp request object
            var xmlhttp = new XMLHttpRequest();
            //when state is ready execute function
            xmlhttp.onreadystatechange = function() {
                if(this.readState == 4 && this.status == 200) {
                    //once the message has been uploaded to the database refresh the messages
                    getMessages(sessionStorage.getItem("roomID"), sessionStorage.getItem("accType"));
                }
            };
            //prepare GET request to the upload messages script
            xmlhttp.open("GET", "Scripts/uploadMsg.php?cr=" + sessionStorage.getItem("roomID")
            + "&uName=" + uName + "&text=" + text, true);
            //send request
            xmlhttp.send();
            //empty text box
            document.getElementById("textBox").value = "";
        }
        
        function refreshPage() {
            window.location.reload();
        }

        function updateScroll() {
            var x = document.getElementById("messagesContainer");
            x.scrollTop = x.scrollHeight;
        }
    </script>
</body>
</html>