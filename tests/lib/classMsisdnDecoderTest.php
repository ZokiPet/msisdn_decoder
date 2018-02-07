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

    /**
     * @var \RemoteWebDriver
     */

/*    protected $webDriver;

    public function setUp() {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function tearDown() {
        $this->webDriver->close();
    }

    protected $url = 'http://www.netbeans.org/';

    public function testSimple() {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'NetBeans'
        $this->assertContains('NetBeans', $this->webDriver->getTitle());
    }   
*/
  
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
        //print_r($MsisdnDecodingArray);
        
        $this->assertArrayHasKey('Country', $MsisdnDecodingArray);
        $this->assertArrayHasKey('ISO Code', $MsisdnDecodingArray);
        $this->assertArrayHasKey('CC', $MsisdnDecodingArray);
        $this->assertArrayHasKey('MNC', $MsisdnDecodingArray);
        $this->assertArrayHasKey('SN', $MsisdnDecodingArray);
        $this->assertArrayHasKey('MNO', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Decoding Status', $MsisdnDecodingArray);
        $this->assertArrayHasKey('Decoding Description', $MsisdnDecodingArray);
        
        $this->assertEquals($MsisdnDecodingArray['Country'], 'Germany');
        $this->assertEquals($MsisdnDecodingArray['ISO Code'], 'DE');
        $this->assertEquals($MsisdnDecodingArray['CC'], '49');
        $this->assertEquals($MsisdnDecodingArray['MNC'], '151');
        $this->assertEquals($MsisdnDecodingArray['MNO'], 'T-Mobile (GSM/UMTS)');
        $this->assertEquals($MsisdnDecodingArray['SN'], '123456');
        $this->assertEquals($MsisdnDecodingArray['Decoding Status'], '1');
        $this->assertEquals($MsisdnDecodingArray['Decoding Description'], 'MSISDN number decoded.');
        
    }
}
