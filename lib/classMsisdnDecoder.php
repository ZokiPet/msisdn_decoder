<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class msisdnDecoder {

    /**
     * @var string Stores the MSISDN input value
     */
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
    
    /** @var const MSISDN_JSON_DATA_FILE_URI - Relative path to msisdn_data.json file */
    const MSISDN_JSON_DATA_FILE_URI = __DIR__ . '/data/msisdn_data.json';
    //const MSISDN_JSON_DATA_FILE_URI = '/data/msisdn_data.json';
    
    
    public function __construct($msisdn) {
        $this->msisdn_number = $msisdn;
    }

    public function set_msisdn_number($new_msisdn) {        
        $this->msisdn_number = $new_msisdn;
    }

    public function get_msisdn_number() {
        return $this->msisdn_number;
    }
    
    /**
     * Handling unexpected errors
     * Custom decoder error handler method that intercepts all unexpected error during MSIDN number decoding 
     * and returns an associative array with info regarding the error.
     * 
     * @param int $errno
     * @param string $errstr
     * 
     * #return array Returns an associative array with description of error.
     */
    private function decoder_error_handler($errno, $errstr) { 
        
//        $response =['Status' => 'Unexpected error in data processing!',
//                    $errno => $errstr];
        $response['Status'] = 'Unexpected error in data processing!';
        $response[$errno] = $errstr;        
        exit(json_encode($response));
    }

    /** 
     * Get data from JSON file and return JSON decoded array
     * 
     * Check if file exist, then loads it 
     * If the data is valid JSON, returns array otherwise return false
     * 
     * @param string $json_uri Relative path to json file to load
     * 
     * @return array An array of JSON decoded data or False if file is missing or invalid json
     */
    private function get_data_from_json_file($json_uri) {

        //$json_uri = __DIR__ . $json_uri; 
        if (is_file($json_uri)) {
            $json = json_decode(file_get_contents($json_uri));
            if (json_last_error() === JSON_ERROR_NONE) { 
                return $json;   // Return json 
            }
            else { return false; }  // Return false if invalid json
        }
        else 
        {
            return false;   // Retrun False if file is missing
        }
    }
    
    /**
     * Cleaning input MSISDN string
     * In the first step removes all non-numeric characters from input parameter<br/>
     * In the second step removes leading double-zeros from MSISDN string
     * 
     * @param string $input_number A raw MSISDN number 
     * @return string If cleaned number is consisting of digit otherwise return False
     */
    function clean_msisdn_number_input($input_number) {
        
        $clean_nr = preg_replace("/[^0-9]/", "", $input_number); // Clean all non digit chars from input string
        $clean_nr = preg_replace("/(^00)/", "", $clean_nr); // Clean leading double-zeros '00' if any, at the begining of the string
        if (ctype_digit($clean_nr)) {
            return $clean_nr;   // If all characters in the $clean_nr string are numerical return cleaned string
        }
        else
        {
            return false;   // otherwise return false
        }
    }
    
    /**
     * Return array with decoded data or error info
     * After successful decoding array with Country, ISO code, CC, MNC, SN and MNO 
     * is returned with Status code of '1' and message for success.
     * 
     * Otherwise Status code <1 is returned with Message describing the error of decoding process.
     * 
     * @param string $response_code
     * @return array Returns associative array with decoded data and decoding status
     */
    public function prepare_response($response_code) {

        if ($response_code == '1') {
            $response["Country"] = $this->countryName;
            $response["ISO"] = $this->countryIsoCode;
            $response["CC"] = $this->countryDialingCode;
            $response["MNC"] = $this->mnoNumber;
            $response["SN"] = $this->subscriberNumber;
            $response["MNO"] = $this->mnoName;
            $response["Status"] = '1';
            $response["Message"] = 'MSISDN number decoded.';
        } elseif ($response_code == '0') {
            $response["Country"] = $this->countryName;
            $response["ISO"] = $this->countryIsoCode;
            $response["CC"] = $this->countryDialingCode;
            $response["MNC"] = $this->mnoNumber;
            $response["SN"] = $this->subscriberNumber;
            $response["MNO"] = $this->mnoName;
            $response["Status"] = '0';
            $response["Message"] = 'MSISDN number partialy decoded. MNC is invalid/not in database.';                        
        } elseif ($response_code == '-1') {
            $response["Status"] = '-1';
            $response["Message"] = 'MSISDN number decoding failed. CC code is invalid/not in database.';
        } elseif ($response_code == '-7') {
            $response["Status"] = '-7';
            $response["Message"] = 'File is missing or JSON data invalid!';
        } elseif ($response_code == '-9') {
            $response["Status"] = '-9';
            $response["Message"] = 'MSISDN number too short! Please enter valid MSISDN number with 7-15 digits.';
        }
                
        return $response;
    }

    /**
     * Decoding MSISDN number
     * 
     * Method try to decode MSISDN from value stored in $msisdn_number by matching CC and MNC data 
     * from associative array loaded from JSON file. 
     * 
     * Result from decoding process is returned in associative array 
     * 
     *  
     * @return array Returns associative array with decoded data and decoding status
     */
    public function decode_msisdn_number() {

        $clean_msisdn = $this->clean_msisdn_number_input($this->get_msisdn_number());
        $response_code = self::STATUS_INVALID_MSISDN_NUMBER;    // Set initial decode status

        if (strlen($clean_msisdn) >= 7) {
            $msisdn_data = $this->get_data_from_json_file(self::MSISDN_JSON_DATA_FILE_URI); // Loading json decoding data in php array
            $response_code = self::STATUS_JSON_FILE_MISSING_INVALID;
            if (is_array($msisdn_data)) {
                $response_code = self::STATUS_CC_CODE_INVALID_MISSING;
                foreach ($msisdn_data as $countries) {  // Loop for each Country in array to match with input msisdn nr.
                    $match = "/^".$countries->cc."/";

                    if (preg_match($match, $clean_msisdn)) {    // if CC match found, get CC, ISO & Country form array and proceed to match MNC
                        $this->countryDialingCode = $countries->cc;
                        $this->countryIsoCode = $countries->iso_2;
                        $this->countryName = $countries->country;
                        $response_code = self::STATUS_MNO_CODE_INVALID_MISSING;
                        // Loop for matched CC and each MNC code in array to match with the input MSISDN nr.
                        foreach ($countries->operators as $operator) {  
                            $match = "/^".$this->countryDialingCode.$operator->mnc."/";
                            //$match = "/^".$this->countryDialingCode."/";
                            if (preg_match($match, $clean_msisdn)) {
                                // Loop doesn't break on first match, continues to look for matches with longer pattern
                                // for ex. Country: Turkey
                                // {"mnc": "54", "operator": "Vodafone"},
                                // {"mnc": "54285", "operator": "KKTC Telsim"},
                                // if msisdn -> 9054285123456 loop first will match Vodafone (mnc:54) but will continue and find the final match for KKTC Telsim (mnc:54285)
                                if (strlen($operator->mnc) > strlen($this->mnoNumber)) {  // If already matched, but current MNO lengthy than previously matched MNO, use current lengthy match
                                    $this->mnoName = $operator->operator;
                                    $this->mnoNumber = $operator->mnc;
                                    $this->subscriberNumber = preg_replace($match, "", $clean_msisdn);
                                    $response_code = self::STATUS_MSISDN_NUMBER_DECODED;
                                    //break;
                                }
                            }    
                        }
                        break;
                    }
                } 
            } 
        }
        restore_error_handler();
        // return status of decoding process
        return $this->prepare_response($response_code);
        //return $response_code;
    }   
}
