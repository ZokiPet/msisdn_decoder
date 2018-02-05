<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class msisdnDecoder {

    public $msisdn_number;
    private $countryName = '';
    private $countryIsoCode = '';
    private $countryDialingCode = '';
    private $subscriberNumber = '';
    private $mnoNumber = '';
    private $mnoName = '';
    
    const STATUS_INVALID_MSISDN_NUMBER = "-9";
    const STATUS_JSON_FILE_MISSING_INVALID = "-7";
    const STATUS_CC_CODE_INVALID_MISSING = "-1";
    const STATUS_MNO_CODE_INVALID_MISSING = "0";
    const STATUS_MSISDN_NUMBER_DECODED = "1";
    
    const MSISDN_JSON_DATA_FILE_URI = '../data/msisdn_data.json';


    
    public function __construct($msisdn) {
        $this->msisdn_number = $msisdn;
    }

    public function set_msisdn_number($new_msisdn) {        
        $this->msisdn_number = $new_msisdn;
    }

    public function get_msisdn_number() {
        return $this->msisdn_number;
    }
    
    private function error_handler($errno, $errstr) { 
        
        $response =['Status' => 'Unexpected error in data processing!',
                    $errno => $errstr];
        exit(json_encode($response));
    }

    private function get_data_from_json_file($json_uri) {
        
        /* @var $json_url type string*/
        if (is_file($json_uri)) {
            $json = json_decode(file_get_contents($json_uri));
            if (json_last_error() === JSON_ERROR_NONE) { 
                return $json;
            }
            else { return false; }
        }
        else 
        {
            return false;
        }
    }
    
    private function clean_msisdn_number_input($input_number) {
        
        $clean_nr = preg_replace("/[^0-9]/", "", $input_number);
        $clean_nr = preg_replace("/(^00)/", "", $clean_nr);
        if (ctype_digit($clean_nr)) {
            return $clean_nr;
        }
        else
        {
            return false;
        }
    }
    
    private function prepare_response($response_code) {

        if ($response_code == '1') {
            $response =["countryName" => $this->countryName,
                        "countryIsoCode" => $this->countryIsoCode,
                        "countryDialingCode" => $this->countryDialingCode,
                        "mnoNumber" => $this->mnoNumber,
                        "subscriberNumber" => $this->subscriberNumber,
                        "Operator Name" => $this->mnoName,
                        "Decode Status" => '1',
                        "Decode Description" => 'MSISDN number decoded.'];
        } elseif ($response_code == '0') {
            $response =["countryName" => $this->countryName,
                        "countryIsoCode" => $this->countryIsoCode,
                        "countryDialingCode" => $this->countryDialingCode,
                        "mnoNumber" => $this->mnoNumber,
                        "subscriberNumber" => $this->subscriberNumber,
                        "Operator Name" => $this->mnoName,
                        "Decode Status" => '0',
                        "Decode Description" => 'MSISDN number partialy decoded.<br>MNO is invalid/not in database.'];
        } elseif ($response_code == '-1') {
            $response =["Decode Status" => '-1',
                        "Decode Description" => 'MSISDN number decoding failed.<br>CC code is invalid/not in database'];            
        } elseif ($response_code == '-7') {
            $response =["Success" => '-1',
                        "Last error" => 'File is missing or JSON data invalid!'];
        } elseif ($response_code == '-9') {
            $response =["Success" => '-1',
                        "Last error" => 'MSISDN number too short!<br>Please enter valid MSISDN number with 7-15 digits.'];            
        }
        
        return $response;
    }

    public function decode_msisdn_number() {

        set_error_handler([$this, 'error_handler'], E_ALL);
        /**
         * @var $clean_msisdn type string
         */
        $clean_msisdn = $this->clean_msisdn_number_input($this->get_msisdn_number());
        $response_code = self::STATUS_INVALID_MSISDN_NUMBER;
        if (strlen($clean_msisdn) >= 7) {
            // loading data from JSON
            /**
             * @var $msisdn_data type array
             */
            $msisdn_data = $this->get_data_from_json_file(self::MSISDN_JSON_DATA_FILE_URI);
            $response_code = self::STATUS_JSON_FILE_MISSING_INVALID;
            if (is_array($msisdn_data)) {
                $response_code = self::STATUS_CC_CODE_INVALID_MISSING;
                foreach ($msisdn_data as $countries) {
                    //return array('country'=>$countries->country);
                    //echo [$countries->iso_2 . ' ---> '];
                    //echo [$countries->cc . '<br/>'];
                    $match = "/^".$countries->cc."/";

                    if (preg_match($match, $clean_msisdn)) {
                        $this->countryDialingCode = $countries->cc;
                        $this->countryIsoCode = $countries->iso_2;
                        $this->countryName = $countries->country;
                        $response_code = self::STATUS_MNO_CODE_INVALID_MISSING;
                    
                        foreach ($countries->operators as $operator) {
                            $match = "/^".$this->countryDialingCode.$operator->mnc."/";
                            if (preg_match($match, $clean_msisdn)) {
                                $this->mnoName = $operator->operator;
                                $this->mnoNumber = $operator->mnc;
                                $this->subscriberNumber = preg_replace($match, "", $clean_msisdn);
                                
                                $response_code = self::STATUS_MSISDN_NUMBER_DECODED;
                                break;
                            }    
                        }	
                    }
                } 
            } 
        }
        restore_error_handler();
        // return status of decoding process
        return $this->prepare_response($response_code);
    }   
}