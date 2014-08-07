<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/

$settings = array(
    'screen_name' => your_screen_name,
    'consumer_key' => your_consumer_key,
    'consumer_secret' => your_consumer_secret
    'oauth_access_token' => your_oauth_access_token,
    'oauth_access_token_secret' => your_oauth_access_token_secret,
);


$url = 'https://api.twitter.com/1.1/friends/ids.json';
$getfield = '?screen_name='.$settings['screen_name'];
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$obj = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$json = json_decode($obj);

$id_arr = [];
foreach ($json->ids as $key ) {
    array_push($id_arr, $key);
}

// ---------------- unfollow users who dont follow ----------------

$url = 'https://api.twitter.com/1.1/followers/ids.json';
$requestMethod = 'GET';
$getfield = '?screen_name='.$settings['user'];
$obj = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$json = json_decode($obj);

$url = 'https://api.twitter.com/1.1/friendships/destroy.json';
$requestMethod = 'POST';
$twitter = new TwitterAPIExchange($settings);

foreach ($json->ids as $key) {
    if(!array_key_exists($key, $id_arr)){
        $postfields = array('user_id' => $key);
        $obj = $twitter->setPostfields($postfields)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
    }
}

echo 'friends(past): '.count($id_arr)."<br>";
echo 'followers: '.count($json->ids)."<br>";

