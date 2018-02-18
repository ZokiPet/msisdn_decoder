<?php

chdir(__DIR__);
ini_set('default_charset', 'UTF-8');

# we don't want any PHP errors being output
ini_set('display_errors', '1');

# so we will log them. Exceptions will be logged as well
ini_set('log_errors', '1');
ini_set('error_log', 'server-errors.log');

# bootstrap for the example directory
require('vendor/autoload.php');
//require('bootstrap.php');
# classMsisdnDecoder 
require('lib/classMsisdnDecoder.php');

# set up our method handler class
$methods = new ServerMethods();

# create our server object, passing it the method handler class
$Server = new JsonRpc\Server($methods);

# and tell the server to do its stuff
$Server->receive();

/**
 * Our methods class
 */
class ServerMethods {

    public $error = null;

    public function decode($msisdn) {
        $msisdnToDecode = new msisdnDecoder($msisdn);
        $decoding_response = $msisdnToDecode->decode_msisdn_number();
        return $decoding_response;
    }

}
