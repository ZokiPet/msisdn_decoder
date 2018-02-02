<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class decoder {

    var $number;

    function __construct($msisdn) {
        $this->number = $msisdn;
    }

    function set_number($new_msisdn) {        
        $this->number = $new_msisdn;
    }

    function get_number() {
        return $this->number;
    }

    function sanitize_number() {
        $this->set_number(preg_replace("/[^0-9,+]/", "", $this->number));
        $this->set_number(preg_replace("/(^00)/", "", $this->number));
    }

    function decode_number() {

        $countryName = 'n/a';
        $countryIsoCode = 'n/a';
        $countryDialingCode = 'n/a';
        $subscriberNumber = 'n/a';
        $mnoNumber = 'n/a';
        $mnoName = 'n/a';
        
        /* loading CC data from JSON */
        $url = '../data/cc_codes.json';
        $data = file_get_contents($url);
        $cc_codes = json_decode($data);

        //$this->set_number($this->sanitize_number());
        $this->sanitize_number();
        //echo $this->number;

        /*	Loop through COuntry codes to detect Country and Country ISO code */
        foreach ($cc_codes as $cc) {
			
            $match = "/^".$cc->country_code."/";

            if (preg_match($match, $this->number)) {
                $countryDialingCode = $cc->country_code;
                $countryIsoCode = $cc->iso_code_2;
                $countryName = $cc->country;
                break;
            }
        }
        /* loading MNO data from PHP file coresponding to country code */
        $mnoFile="../data/carriers/".$countryDialingCode.".php";
        if (is_file($mnoFile)) {
            $mnoArray = include $mnoFile;
            /* Loop through MNO code for detected Country Code */
            foreach ($mnoArray as $mnoCode => $value) {
                $match = "/^".$mnoCode."/";
                if (preg_match($match, $this->number)) {
                   $mnoName = $value;
                   $subscriberNumber = preg_replace($match, "", $this->number);
                   $mnoNumber = preg_replace("/^".$countryDialingCode."/", "", $mnoCode);
                   break;
               }
           }
        }
        else { 
            //No data file for detected countryDialingCode";
            /* HANDLE AN ERROR*/ 
            $mnoName = 'n/a';
            $subscriberNumber = 'n/a';
            $mnoNumber = 'n/a';
        }
        //$response = [];
        //$response =[$countryName, $countryIsoCode, $countryDialingCode, $subscriberNumber, $mno_number, $mnoName];
        $response =["countryName" => $countryName,
                    "countryIsoCode" => $countryIsoCode,
                    "countryDialingCode" => $countryDialingCode,
                    "subscriberNumber" => $subscriberNumber,
                    "mnoNumber" => $mnoNumber,
                    "Operator Name" => $mnoName];
        header('Content-Type: application/json');
        echo json_encode($response); 
    }   
}