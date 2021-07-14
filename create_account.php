<?php
    //start a session
    session_start();
    require_once("config.php");
    //if the form was submitted
    if(isset($_POST["submit"])) {
        //check if the username is unique
        if(checkUsername($conn, $uName) == true) {
            //if it is unique set validation variable to true
            $validateUsername = true;
        }else {
            //if not unique set to false
            $validateUsername = false;
        }
        //check if pasword match
        if(checkPasswords($pw1, $pw2) == true) {
            $validatePasswords = true;
        }else {
            $validatePasswords = false;
        }
        //if the username is unique and the passwords match
        if($validateUsername == true AND $validatePasswords == true) {
            //pass the form details to the add user function to add to the database
            if(addUser($conn, $_POST["uName"], $_POST["fName"], $_POST["lName"], $_POST["email"], $_POST["pWord1"], $_POST["favTeam"], "Member")) {
                //if there are no errors then create a sesison for the user
                $_SESSION["uName"] = $uName;
                $_SESSION["name"] = $fName;
                $_SESSION["loggedIn"] = true;
                $_SESSION["accType"] = "Member";
                $_SESSION["team"] = $favTeam;
                //redirect user to homepage
                header('Location: index.php');
            //if there was an error uploading to the databse output error
            }else {
                echo "Error";
            }
        }
    }
    //this function takes in user details and attempts to upload them to the database
    function addUser($conn, $uName, $fName, $lName, $email, $pWord, $teamID, $accType) {
        //prepare SQL statement to insert details into database
        $addUserSql = "INSERT INTO users (username, FName, LName, email, password, teamID, AccountType)
        VALUES ('$uName', '$fName', '$lName', '$email', '$pWord', '$teamID', '$accType')";
        //execute query and return true if there are no errors
        if(mysqli_query($conn, $addUserSql)) {
            return true;
        //if there are errors display error message and return false
        }else {
            echo mysqli_error($conn);
            return false;
        }
    }
    //this function checks if two given passwords match
    function checkPasswords($pw1, $pw2) {
        //if they match return true if not return false
        if($pw1 == $pw2) {
            return true;
        }else {
            return false;
        }
    }
    //this function checks if a given username already exists in the database
    function checkUsername($conn, $uName) {
        //SQL statement to find users with the given username
        $checkUserSql = "SELECT * FROM users WHERE username='$uName'";
        //ecxecute query
        $checkUserResult = mysqli_query($conn, $checkUserSql)
            or die (mysqli_error($conn));
        //if there are no users with that username return true
        if($checkUserResult->num_rows == 0) {
            return true;
        //if username is in use return false
        }else {
            return false;
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/generalCss.css">
    <link rel="stylesheet" href="CSS/createAccCss.css">
    <title>Create Account</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo"><img src="images/PLFA_logos/PLFA_logo_final.png" alt="PLFA Logo"></a>
    </header>
    <!-- Create Account Form Contianer -->
    <div class="create-acc-box">
        <form action="" method="POST" autocomplete="off">
            <div class="names">
                <!-- Name Inputs -->
                <input type="text" name="fName" placeholder="First Name" class="text-box" required>
                <input type="text" name="lName" placeholder="Last Name" class="text-box" required>
            </div>
            <br>
            <!-- Username Input -->
            <input type="text" name="uName" placeholder="Username" class="uName-box" required>
            <?php 
                //if the user submits the form but the userame is already taken display error message
                if(isset($_POST["submit"])) {
                    if($validateUsername == false) {
                        echo "<p style='color: red'; ><b>This Username Is Already Taken</b></p>";
                    }
                }
            ?>
            <br>
            <!-- Email Adress Input -->
            <input type="text" name="email" placeholder="Email" class="email" required>
            <br>
            <div class="pwords">
                <!-- Passwords Input -->
                <input type="password" name="pWord1" id="pWord1" placeholder="Password" class="pword-box" required>
                <input type="password" name="pWord2" id="pWord2" placeholder="Confirm Password" class="pword-box" required>            
            </div>
            <br>
            <?php 
                //if the user submits the form and the passwords don't match display error
                if(isset($_POST["submit"])) {
                    if($validatePasswords == false) {
                        echo "<p style='color: red';><b>Passwords Do Not Match</b></p>";
                    }
                }
            ?>
            <!-- Favourite Team drop-down -->
            <select name="favTeam" id="favTeam" required>
                <option value="">Select Team</option>
                <option value="1">Arsenal</option>
                <option value="2">Aston Villa</option>
                <option value="3">Brighton</option>
                <option value="4">Burnley</option>
                <option value="5">Chelsea</option>
                <option value="6">Crystal Palace</option>
                <option value="7">Everton</option>
                <option value="8">Fulham</option>
                <option value="9">Leicester</option>
                <option value="10">Leeds</option>
                <option value="11">Liverpool</option>
                <option value="12">Man City</option>
                <option value="13">Man United</option>
                <option value="14">Newcastle</option>
                <option value="15">Sheffield United</option>
                <option value="16">Southampton</option>
                <option value="17">Tottenham</option>
                <option value="18">West Brom</option>
                <option value="19">West Ham</option>
                <option value="20">Wolves</option>
            </select>
            <input type="submit" name="submit" id="submit" value="Create Account" class="create-acc-btn">
        </form>
    </div>
    
</body>
</html>