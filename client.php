<?php

$msisdn = filter_input(INPUT_GET, 'msisdn');

//chdir(__DIR__);
ini_set('default_charset', 'UTF-8');
ini_set('display_errors', '1');

# bootstrap
require('vendor/autoload.php');

# get the url of the server script
$url = getServerUrl();

# create our client object, passing it the server url
$Client = new JsonRpc\Client($url);

# set up our rpc call with a method and params
$method = 'decode';
$params = array($msisdn);
$success = false;
$success = $Client->call($method, $params);

//if ($success) { 
//  echo json_encode($Client->result);
    echo ($Client->output);
//}

function getServerUrl() {

    $path = dirname($_SERVER['PHP_SELF']) . '/server.php';
    $scheme = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . $path;
}
