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
            return preg_replace("/[^0-9,+]/", "", $this->number);
	}

	function decode_number() {
            
            /* loading CC data from JSON */
            $url = '../data/cc_codes.json';
            $data = file_get_contents($url);
            $cc_codes = json_decode($data);

            $this->set_number($this->sanitize_number());

            /*	Loop through COuntry codes to detect Country and Country ISO code */
            foreach ($cc_codes as $cc) {
/*			echo 'the Number -->'.$this->number . '--';
                //echo $cc->country_code . ' ---> ';
                //$match = "^\+?[".$cc->country_code."]{1,14}$^";
                $match = "/^".$cc->country_code."/";
                echo $match . ' ----> ';
*/			
                $match = "/^".$cc->country_code."/";

                if (preg_match($match, $this->number)) {
                    $countryDialingCode = $cc->country_code;
                    $countryIsoCode = $cc->iso_code_2;
                    $countryName = $cc->country;
                    break;
                }
            }
            /* loading MNO data from PHP file coresponding to country code */
            //echo "mathed CC: " . $cc->country_code;
            $mnoFile="../data/carriers/".$countryDialingCode.".php";
            //echo "MNO file is ---> " . $mnoFile;
            if (is_file($mnoFile)) {
                $mnoArray = include $mnoFile;
            }
            else { 
                echo "MNO file is missing !!!";
                /* HANDLE AN ERROR*/ 
            }
            /* Loop through MNO code for detected Country Code */
            foreach ($mnoArray as $mnoCode => $value) {
                //echo "MNO CODE --->" . $mnoCode . $value .'<br/>';
                //echo "MNO CODE --->" . $mnoCode .'<br/>';
                $match = "/^".$mnoCode."/";
                if (preg_match($match, $this->number)) {
                    //echo 'match --> '.$match . 'number ---> ' . $this->number . ' Provider Name' . $value . '<br/>';
                    //$countryDialingCode
                    $mnoName = $value;
                    $subscriberNumber = preg_replace($match, "", $this->number);
                    //echo $subscriberNumber;
                    $mno_number = preg_replace("/^".$countryDialingCode."/", "", $mnoCode);

                    //header('Content-Type: application/json');
//                    echo "<br/>";
//                    echo "countryName ---> ". $countryName; echo "<br/>";
//                    echo "countryIsoCode ---> ". $countryIsoCode; echo "<br/>";
//                    echo "countryDialingCode ---> ". $countryDialingCode; echo "<br/>";
//                    echo "subscriberNumber ---> ". $subscriberNumber; echo "<br/>";
//                    echo "mno_number ---> ". $mno_number; echo "<br/>";
//                    echo "Operator Name ---> ". $mnoName; echo "<br/>";					

                    break;
                }
            }
            
//            return array(
//                "countryName" => $countryName,
//                "countryIsoCode" => $countryIsoCode,
//                "countryDialingCode" => $countryDialingCode,
//                "subscriberNumber" => $subscriberNumber,
//                "mno_number" => $mno_number,
//                "Operator Name" => $mnoName
//            );
            //$response = [];
            $response =[$countryName, $countryIsoCode, $countryDialingCode, $subscriberNumber, $mno_number, $mnoName];            
            header('Content-Type: application/json');
            echo json_encode($response); 
        }
        
        
        
        
        
        
        
        
        
}