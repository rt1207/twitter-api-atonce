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
echo '@'.$settings['screen_name'].PHP_EOL;
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
echo count($id_arr).PHP_EOL;

// ---------------- GET followers IDs ----------------
echo 'GET followers/ids...';

$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?screen_name='.$settings['screen_name'];
$obj = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$json = json_decode($obj);
$id_arr2 = $json->ids;
echo count($id_arr2).PHP_EOL;

// ---------------- followback users who follow you ----------------
echo 'POST friendships/create';

$url = 'https://api.twitter.com/1.1/friendships/create.json';
$requestMethod = 'POST';
$twitter = new TwitterAPIExchange($settings);

$num = 0;
foreach ($id_arr2 as $key) {
    if(!in_array($key, $id_arr)){
        echo '.'; $num++;
        $postfields = array('user_id' => $key, 'follow' => '1');
        $obj = $twitter->setPostfields($postfields)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        $json = json_decode($obj);
        if($json!=null){
            if(array_key_exists('errors',$json)){
                $err = $json->errors[0];
                if(property_exists($err,'message')) $err = $err->message;
                echo PHP_EOL;
                echo 'error: '.$err;
                $num--;
                $str = "You are unable to follow more people at this time. Learn more <a href='http://support.twitter.com/articles/66885-i-can-t-follow-people-follow-limits'>here</a>.";
                if($err==$str){
                    break;
                }
            }
        }
    }
}

echo PHP_EOL;
echo 'new friends: '.$num.PHP_EOL;
