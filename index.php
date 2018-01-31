<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>MSISDN Decoder</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    </head>
    <body>
        <div id="container" class="px-5 py-5 pt-md-5 pb-md-4 mx-auto text-center" >
            <h1>MSISDN Decoder</h1>
            <p class="lead px-2 py-2">Please insert valid MSISDN number and press button to decode...</p>
            
            <div class="input-group mx-auto text-center" style="max-width:300px;">
                <!--<label for="msisdn">MSISDN:</label>--> 
                <input id="msisdn" class="form-control" type="text" name="msisdn">
                <div class="input-group-append">
                    <button type="button" id="msisdn_decode" class="btn btn-secondary">Decode</button>
                </div>
                <!--<button id="msisdn_decode" class="lead btn btn-sm btn-outline-primary px-4" type="button">MSISDN Decode</button>--> 
                <div id="result"></div>
                
            </div>
        </div>
        <?php
        // put your code here
        //echo "Debug test";
        ?>
        
        <script>
            $("#msisdn").on('keyup', function (e) {
                val = $("#msisdn").val();
                if (e.keyCode === 13) {
                    alert('MSISDN:'+val);
                }
            });
            
            $("#msisdn_decode").click(function() {
                msisdn = $("input[id=msisdn]").val();
            //alert("Msisdn: " + msisdn);
            
                
                
            })
        </script>
       
    </body>
</html>
