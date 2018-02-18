<?php

/*
 * Copyright (C) 2018 Sony
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of classMsisdnDecoderTest
 *
 * @author Sony
 */

require_once('../lib/classMsisdnDecoder.php');

class classMsisdnDecoderTest extends PHPUnit_Framework_TestCase {
  
    public function testThatWeCanConstructMsisdn() {
        $testMsisdn = new msisdnDecoder('49151123456');
        $this->assertEquals($testMsisdn->get_msisdn_number(), '49151123456'); 
    }
            
    public function testThatWeCanSetMsisdn() {
        $testMsisdn = new msisdnDecoder('');
        $testMsisdn->set_msisdn_number('49151234567');
        $this->assertEquals($testMsisdn->get_msisdn_number(), '49151234567');
    }
    
    public function testCleanPlusSignInMsisdnNumberInput() {
        $testMsisdn = new msisdnDecoder('');
        $this->assertEquals($testMsisdn->clean_msisdn_number_input('+49151123456'), '49151123456'); 
    }
    
    public function testCleanLeadingZerosInMsisdnNumberInput() {
        $testMsisdn = new msisdnDecoder('');
        $this->assertEquals($testMsisdn->clean_msisdn_number_input('0049151123456'), '49151123456'); 
    }
    
    public function testCleanNonNumericCharInMsisdnNumberInput() {
        $testMsisdn = new msisdnDecoder('');
        $this->assertEquals($testMsisdn->clean_msisdn_number_input('49@#$151/*-123456<>?'), '49151123456'); 
    }
    
    public function testThatWeCanDecodeMsisdnNumber() {
        $testMsisdn = new msisdnDecoder('49151123456');
        $MsisdnDecodingArray = $testMsisdn->decode_msisdn_number();
        
        $this->assertArrayHasKey('Country', $MsisdnDecodingArray);
        $this->assertArrayHasKey('ISO', $MsisdnDecodingArray);
        $this->assertArrayHasKey('CC', $MsisdnDecodingArray);
        $this->assertArrayHasKey('MNC', $MsisdnDecodingArray);
        $this->assertArrayHasKey('SN', $MsisdnDecodingArray);
        $this->assertArrayHasKey('MNO', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Status', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Message', $MsisdnDecodingArray);
        
        $this->assertEquals($MsisdnDecodingArray['Country'], 'Germany');
        $this->assertEquals($MsisdnDecodingArray['ISO'], 'DE');
        $this->assertEquals($MsisdnDecodingArray['CC'], '49');
        $this->assertEquals($MsisdnDecodingArray['MNC'], '151');
        $this->assertEquals($MsisdnDecodingArray['MNO'], 'T-Mobile (GSM/UMTS)');
        $this->assertEquals($MsisdnDecodingArray['SN'], '123456');
        $this->assertEquals($MsisdnDecodingArray['Status'], '1');
        $this->assertEquals($MsisdnDecodingArray['Message'], 'MSISDN number decoded.');
        
    }
    
        public function testThatMsisdnNumberIsInvalid() {
        $testMsisdn = new msisdnDecoder('49151');
        $MsisdnDecodingArray = $testMsisdn->decode_msisdn_number();
        
        $this->assertArrayHasKey('Status', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Message', $MsisdnDecodingArray);
        
        $this->assertEquals($MsisdnDecodingArray['Status'], '-9');
        $this->assertEquals($MsisdnDecodingArray['Message'], 'MSISDN number too short! Please enter valid MSISDN number with 7-15 digits.');
        
    }
    
    public function testThatMsisdnNumberHasIsInvalidCountryCode() {
        $testMsisdn = new msisdnDecoder('38811222222');
        $MsisdnDecodingArray = $testMsisdn->decode_msisdn_number();
        
        $this->assertArrayHasKey('Status', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Message', $MsisdnDecodingArray);
        
        $this->assertEquals($MsisdnDecodingArray['Status'], '-1');
        $this->assertEquals($MsisdnDecodingArray['Message'], 'MSISDN number decoding failed. CC code is invalid/not in database.');
        
    }

    public function testThatMsisdnNumberHasIsInvalidMobileNetworkCode() {
        $testMsisdn = new msisdnDecoder('38988123456');
        $MsisdnDecodingArray = $testMsisdn->decode_msisdn_number();
        
        $this->assertArrayHasKey('Status', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Message', $MsisdnDecodingArray);
        
        $this->assertEquals($MsisdnDecodingArray['Status'], '0');
        $this->assertEquals($MsisdnDecodingArray['Message'], 'MSISDN number partialy decoded. MNC is invalid/not in database.');
        
    }
}
