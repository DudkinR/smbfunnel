<?php
class Cfsendfox
{

function __construct()
{

}

function sendfox($apikey,$listid,$name,$email)
{
    if(filter_var($email,FILTER_VALIDATE_EMAIL))
    {            

$curl = curl_init();

$aurl = "https://api.sendfox.com/contacts?email=".$email."&lists=".$listid."&first_name=".$name;
curl_setopt_array($curl, array(
CURLOPT_URL =>$aurl,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_HTTPHEADER => array(
"Authorization: Bearer $apikey"
),
));

$response = curl_exec($curl);
$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// print_r($status_code);
curl_close($curl);
if($status_code == 200)
{
return $status_code;
}
else{
return 0;
}




    }
    else{
      return 0;
    }


  }



}
?>