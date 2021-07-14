<?php
    //Based on code by James Mallison, see https://github.com/J7mbo/twitter-api-php
    //Based on practical workshop, see https://fetstudy.uwe.ac.uk/~p-chatterjee/2019-20/modules/dsa/workshop5.html
    ini_set('display_errors', 1);
    //Include the twitterAPIExchange.php file
    require_once('TwitterAPIExchange.php');
    //This function calls the Twitter API and returns the tweets
    function getTweets() {
        //Store the twitter API keys in an array
        $settings = array(
            'oauth_access_token' => '1972966322-nkKrtHRMnethf8OpCi0C3hVlGm34yPMiZmBIvYC',
            'oauth_access_token_secret' => 'ocMlsN5A0lUXsps5WWDEauj2TLPIUjXyxhKitJXTkMTsg',
            'consumer_key' => 'f9L1NWPffS65JUafoVXerCk36',
            'consumer_secret' => 'slJe0c1CSqFK9jGM93obZaxMGlwRfmXGqR5NKnjdvXVgazKCUW'
        );
        
        /** Perform a GET request **/
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getfield = '?q=%23premierLeague&count=5&tweet_mode=extended';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $data=$twitter->setGetfield($getfield)
                        ->buildOauth($url, $requestMethod)
                        ->performRequest();
        // Read the JSON into a PHP object
        $phpdata = json_decode($data, true);
        $tweet = "";
        //Loop through the status
        foreach ($phpdata["statuses"] as $status){
            //Store the tweet
            $tweet1 = $status["full_text"];
            //Store the profile picture link
            $profile_url = $status["user"]["profile_image_url_https"];
            //Store the display name
            $name = $status["user"]["screen_name"];
            //Create the HTML output containing the tweets
            $tweet = $tweet."<div class='tweet'>
            <div class='tweet-top'>
                <div class='tweet-pic-box'><img src='".$profile_url."' class='tweet-pic' alt='Profile Pic'></div>
                <div class='tweet-usr-box'><p class='tweet-usr'>$name</p></div>
            </div><div class='tweet-bottom'>
                <p class='tweet-text'>$tweet1</p>
            </div>
            </div>";
        }
        //return tweets output
        return $tweet;
    }
?>