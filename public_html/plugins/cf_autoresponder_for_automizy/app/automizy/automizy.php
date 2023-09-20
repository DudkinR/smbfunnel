<?php
class Cfautomizy
{

function __construct()
{

}

function automizy($apikey,$listid,$name,$email)
{
  $accesstoken = $apikey;
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $url='https://gateway.automizy.com/v2/smart-lists/'.$listid.'/contacts';
  $json = '{
      "email":"'.$email.'",
      "customFields":{
          "firstname":"'.$name.'"
                      }
              }';
  $header[] = "Authorization: Bearer ".$accesstoken;
  $header[] = 'Content-Type: application/json';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  $result = curl_exec($ch);
  $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  //echo "<script>console.log(".print_r($result).");</script>";
  $objauto = json_decode($result);
    if ($objauto->status == "ACTIVE") {
      //echo "<script>console.log('Successfully entered into autoresponder list');</script>";
    return 1;
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