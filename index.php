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
        <!--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    </head>
    <body>
        <div id="container" class="px-5 py-5 pt-md-5 pb-md-4 mx-auto text-center" >
            <h1>MSISDN Decoder</h1>
            <p class="lead px-2 py-1">Please insert valid MSISDN number and press Decode button</p>
            
            <div class="input-group mx-auto text-center" style="max-width:300px;">
                <input id="msisdn" class="form-control" placeholder="+49 89 353510" type="text" name="msisdn" value="38970123456">
                <div class="input-group-append">
                    <button type="button" id="msisdn_decode" class="btn btn-secondary">Decode</button>
                </div>
            </div>
            <div id="result"  class="mt-5 col-7 mx-auto text-center">
                <!--AJAX response goes here-->
            </div>            
        </div>
        
        <script>
            $("#msisdn").on('keyup', function (e) {
                //val = $("#msisdn").val();
                if (e.keyCode === 13) {
                    alert('MSISDN:'+val);
                }
            });
            
            $("#msisdn_decode").click(function() {
                msisdn = $("input[id=msisdn]").val();
//                alert("Msisdn: " + msisdn);
                $( "div#result" ).html( "" );
                $.ajax({
                    method: "GET",
                    dataType: "json",
                    contentType: "application/json",
                    // Cross domain ajax call
                    //url: "http://different.domain.com/test2/lib/ajaxHandler.php?call_func=decode_msisdn&msisdn="+msisdn,
                    // Same domain ajax call
                    url: "lib/ajaxHandler.php?call_func=decode_msisdn&msisdn="+msisdn,
                    timeout: 4000,
                    cache: false
                }).done(function(result){
                    //alert(result);
                    html_result="";
                    $.each(result, function(key, value){
                        html_result += '<div class="row pb-1"\n>';
                        html_result += '<div class="bg-light col-5">'+key+'</div>\n';
                        html_result += '<div class="bg-info text-white col-7">'+value+'</div>\n';
                        html_result += '</div>';
                    });
                    $("div#result").html(html_result);
                }).fail(function(jqXHR, textStatus){
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connected.\n Verify Your Connection.';
                    } else if (jqXHR.status === 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status === 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (textStatus === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (textStatus === 'timeout') {
                        msg = 'Time out error.';
                    } else if (textStatus === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    //msg += '<div class="bg-danger text-white col-4">'+msg+'</div>';
                    //html_result += '<div class="bg-danger text-white col-4">'+value+'</div>\n';
                    //html_result += '</div>';
                    $('div#result').html('<div class="px-1 py-2 bg-danger text-white">'+msg+'</div>');
                });
            });
        </script>
       
    </body>
</html>
