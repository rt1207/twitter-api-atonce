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

// ---------------- GET search tweets ----------------
echo '@'.$settings['screen_name'].PHP_EOL;
    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $q_arr = ['query'];// insert search query
    $count = 50; // how many tweets do you get
    $retweet_count = 30; // retweeted more than...
    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange($settings);
    $name_arr = [];

foreach ($q_arr as $key) {
    echo 'GET search/tweets '.$key.' RT>'.$retweet_count.'...';

    $getfield = '?q='.$key.'&result_type=mixed&count='.$count.'&include_user_entities=false';
    $obj = $twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest();
    $json = json_decode($obj);

    foreach ($json->statuses as $key ) {
        if($key->retweet_count > $retweet_count){
            array_push($name_arr, $key->id);
        }
    }
    echo count($name_arr).PHP_EOL;
}

// ---------------- retweet ----------------
echo 'POST statuses/retweet';

$requestMethod = 'POST';
$twitter = new TwitterAPIExchange($settings);

$num = 0;
foreach ($name_arr as $key ) {
    $url = 'https://api.twitter.com/1.1/statuses/retweet/'.$key.'.json';
    $postfields = array();
    $obj = $twitter->buildOauth($url, $requestMethod)
                 ->setPostfields($postfields)
                 ->performRequest();
    $json = json_decode($obj);
    if($json!=null){
        if(array_key_exists('errors',$json)){
            $err = $json->errors[0];
            if(property_exists($err,'message')) $err = $err->message;
            $str = 'Your account is suspended and is not permitted to access this feature.';
            if($err=='s'){ echo "'"; continue;}
            echo PHP_EOL;
            echo 'error: '.$err;
            if($err==$str) break;
        }else{
            echo '.'; $num++;
        }
    }
}

echo 'RT:'.$num.PHP_EOL.PHP_EOL;
