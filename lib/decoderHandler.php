<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('classDecoder.php');

$get_func = filter_input(INPUT_GET, 'call_func');
//$get_param = filter_input(INPUT_GET, 'msisdn');
//if (isset( $_GET['call_func']) and isset( $_GET['msisdn'])) {
    //if ($_GET['call_func'] === 'decode_msisdn') {
    if ($get_func === 'decode_msisdn') {
        //$msisdn = $_GET['msisdn'];
        $msisdn = filter_input(INPUT_GET, 'msisdn');
        //echo "decode_msisdn " . $msisdn;

        $msisdnToDecode = new decoder($msisdn);
        $msisdnToDecode->decode_number();
    }
//}
            
?>