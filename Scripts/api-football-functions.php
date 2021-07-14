<?php 
    $apiFootKey = "5f878d2bfemshef340f5e27b4eb9p1c876ajsnf6e969d86a20";
    $apiFootHeader = "https://api-football-beta.p.rapidapi.com/";

    //This function calls an API and returns the response
    function callApi($endpoint, $header, $key) {
        //initialise a cURL to call the API
        $ch = curl_init();

        //set options of the cURL by using the arguements passed to the function
        curl_setopt_array($ch, [
            CURLOPT_URL => $header.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-key: $key"
            ],
        ]);
        //execute the API call and store in a variable
        $response = curl_exec($ch);
        //if the API call fails store the error message
        if(curl_errno($ch)) {
            $err_msg = curl_error($ch);
        }
        //close cURL
        curl_close($ch);
        //if there was an error return the error message
        if(isset($err_msg)) {
            return $err_msg;
        }else {
            //if no error return the API response
            return json_decode($response);
        }
    }
    //function that calls the api and returns the fixtures on a given date
    function getFixtures($date, $key, $header) {
        $getFixturesDate = "fixtures?date=$date&league=39&season=2020";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $header.$getFixturesDate,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-key: $key"
            ],
        ]);

        $response = curl_exec($ch);
        if(curl_errno($ch)) {
            $err_msg = curl_error($ch);
        }
        curl_close($ch);
        if(isset($err_msg)) {
            return $err_msg;
        }else {
            //return fixtures
            return json_decode($response);
        }
    }

    //this function takes in the API response and generates a HTML output
    function getFixturesOutput($fixtures) {
        //output string
        $output = "";
        //if the fixtures variable is not empty
        if(!empty($fixtures)) {
            //loop over each fixture
            foreach($fixtures as $fixture) {
                //generate a row containing the fixture information
                $output = $output."<div class='fixture'>
                <div class='tlogo'>
                <img src='".$fixture->teams->home->logo."' class='team-logo' alt='Home Logo'></div>
                <div class='tname'><p>".$fixture->teams->home->name."</p></div><div class='goals'>";
                //convert the fixture date to london timezone
                $fixtureDate = date("Y-m-d H:i", strtotime($fixture->fixture->date." BST"));
                //add 90 minutes the the game start time to find the game finish time
                $finishTime = date("Y-m-d H:i", strtotime($fixtureDate." +90 minutes"));
                //get the current date and time
                $currentDate = date("Y-m-d H:i");
                //check if the game is currently being played
                if($currentDate >= $fixtureDate && $currentDate <= $finishTime) {
                    //if game is live add live to the CSS class
                    $output = $output."<p class='goals-home-live'>".$fixture->goals->home."</p>
                    <p class='seperator-live'>|</p>
                    <p class='goals-away-live'>".$fixture->goals->away."</p>";
                //check if the game is finished
                }else if($currentDate >= $fixtureDate) {
                    //if it has output the score
                    $output = $output."<p class='goals-home'>".$fixture->goals->home."</p>
                    <p class='seperator'>|</p>
                    <p class='goals-away'>".$fixture->goals->away."</p>";
                //if the game has not started yet just output the start time
                }else {
                    $output = $output."<p class='game-time'>".date("H:i", strtotime($fixtureDate))."</p>";
                }
                $output = $output."</div><div class='tlogo'><img src='".$fixture->teams->away->logo."' class='team-logo' alt='Away Logo'></div>
                <div class='tname'><p>".$fixture->teams->away->name."</p></div></div>";
            }
        }
        //return HTML output
        return $output;
    }

?>