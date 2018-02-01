<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('classDecoder.php');

if (isset( $_GET['call_func']) and isset( $_GET['msisdn'])) {
    if ($_GET['call_func'] === 'decode_msisdn') {
        $msisdn = $_GET['msisdn'];
        //echo "decode_msisdn " . $msisdn;

        $msisdnToDecode = new decoder($msisdn);
        $msisdnToDecode->decode_number();
    }
}
            
?>