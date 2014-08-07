<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');

$settings = array(
    'screen_name' => your_screen_name, // owner of your app
    'consumer_key' => your_consumer_key,
    'consumer_secret' => your_consumer_secret,
    'oauth_access_token' => your_oauth_access_token,
    'oauth_access_token_secret' => your_oauth_access_token_secret
);

// ---------------- GET friends IDs ----------------
echo 'GET friends/ids...';

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
echo 'done'.PHP_EOL;

// ---------------- GET followers IDs ----------------
echo 'GET followers/ids...';

$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?screen_name='.$settings['user'];
$obj = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$json = json_decode($obj);
echo 'done'.PHP_EOL;

// ---------------- unfollow users who dont follow ----------------
echo 'POST friendships/destroy';

$url = 'https://api.twitter.com/1.1/friendships/destroy.json';
$requestMethod = 'POST';
$twitter = new TwitterAPIExchange($settings);

$num = 0;
foreach ($json->ids as $key) {
    echo '.';
    if(!in_array($key, $id_arr)){
        $postfields = array('user_id' => $key);
        $obj = $twitter->setPostfields($postfields)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
    $num++;
    }
}

echo 'done'.PHP_EOL;
echo 'unfollow '.$num." friends<br>";
