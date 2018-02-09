<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'classMsisdnDecoder.php';

 if(isset($_POST['call_func'])){
    $post_func = filter_input(INPUT_POST, 'call_func'); 
    if ($post_func === 'decode_msisdn') {

        $msisdn = filter_input(INPUT_POST, 'msisdn');

        $msisdnToDecode = new msisdnDecoder($msisdn);
        $decoding_response = $msisdnToDecode->decode_msisdn_number();

        // Header option that allows cross domain AJAX requests fron ANY domain
        header('Access-Control-Allow-Origin: *');

        echo json_encode($decoding_response); 
    }
}        