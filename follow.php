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

foreach ($settingsarr as $settings) {
// ---------------- GET search tweets ----------------
echo '@'.$settings['screen_name'].PHP_EOL;
    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $q_arr = ['teamfollowback','mgwv','%E7%9B%B8%E4%BA%92'];
    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange($settings);
    $name_arr = [];

foreach ($q_arr as $key) {
    echo 'GET search/tweets '.$key.'...';

    $getfield = '?q='.$key.'&result_type=mixed&count=10&include_user_entities=false';
    $obj = $twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest();
    $json = json_decode($obj);
    echo count($json->statuses).PHP_EOL;

    foreach ($json->statuses as $key ) {
        if($key->user->following||$key->user->follow_request_sent) continue;
        else{
            $name = $key->user->screen_name;
            array_push($name_arr, $name);
        }
    }
}

// ---------------- follow searched ----------------
echo 'POST friendships/create';

$url = 'https://api.twitter.com/1.1/friendships/create.json';
$requestMethod = 'POST';
$twitter = new TwitterAPIExchange($settings);

$num = 0;
foreach ($name_arr as $key ) {
    echo '.'; $num++;
    $postfields = array(
        'screen_name' => $key, 
        'follow' => '1'
    );
    $obj = $twitter->buildOauth($url, $requestMethod)
                 ->setPostfields($postfields)
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

echo $num.PHP_EOL.PHP_EOL;
