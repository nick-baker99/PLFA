<?php 
    session_start();
    require("config.php");
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/generalCss.css">
    <link rel="stylesheet" href="CSS/loginCss.css">
    <title>Log in</title>
</head>
<body>
    <header>
    <a href="index.php" class="logo"><img src="images/PLFA_logos/PLFA_logo_final.png" alt="PLFA Logo"></a>
    </header>
    <!-- Login Contianer -->
    <div class="login-box">
        <div class="login-form">
            <form autocomplete="off">
                <!-- Login Form -->
                <input type="text" name="UName" placeholder="Username" class="form-items" id="uName" required>
                <input type="password" name="PWord" placeholder="Password" class="form-items" id="pWord" required>
                <br>
                <p id="loginFail" class="login-fail"></p>
                <!-- Login button that when pressed calls the javascript function that checks the details -->
                <button type="button" class="log-btn" id="loginBtn" onclick="attemptLogin()">LOGIN</button>
            </form>
        </div>
        <p class="create-acc-msg">Dont have an account?</p>
        <!-- Create account button redirects user to create account page -->
        <form action="create_account.php" method="POST">
            <input type="submit" name="create-acc" value="Create Account" class="create-acc-btn">
        </form>
    </div>
    <script>
        //this function uses AJAX to check if the login detials are correct
        function attemptLogin() {
            //find out what the last page visited was
            var oldUrl = document.referrer;
            //store the values of the username and password boxes
            var uName = document.getElementById("uName").value;
            var pWord = document.getElementById("pWord").value;
            //if the username and password boxes are not empty
            if(uName !== "" && pWord !== "") {
                //create a new object for the xmlhttp request class
                var xmlhttp = new XMLHttpRequest();
                //when the status of the xmlhttp object changes execute this function
                xmlhttp.onreadystatechange = function() {
                    //when the response is ready
                    if(this.readyState == 4 && this.status == 200) {
                        //if the PHP script returns 'found'
                        if(this.responseText == "found") {
                            //redirect user to last visited page
                            window.location.href = oldUrl;
                        //if the script doesn't find the details in the database display an error message
                        }else {
                            document.getElementById("loginFail").innerHTML = this.responseText;
                        }
                    }
                };
                //prepare a GET request to the PHP script that checks the login detials
                //pass the username and password in the GET request
                xmlhttp.open("GET", "Scripts/checkLoginDetails.php?uName=" + uName +"&pWord=" + pWord, false);
                //send the request
                xmlhttp.send();
            }
        }
    </script>
</body>
</html>