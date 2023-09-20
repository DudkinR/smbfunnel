<?php
class Cfkirim
{
function __construct()
{

}
function kirim($authid,$authtoken,$listid,$name,$email)
{
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
$time = time();
$generated_token = hash_hmac("sha256",$authid."::".$authtoken."::".$time,$authtoken);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.kirim.email/v3/subscriber/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "lists=".$listid."&full_name=".$name."&email=".$email."" ,
  CURLOPT_HTTPHEADER => array(
    "Auth-Id: ".$authid,
    "Auth-Token: ".$generated_token,
    "Timestamp: ".$time,
    "Content-Type: application/x-www-form-urlencoded"
  ), 
));
$response = curl_exec($curl);
  curl_close($curl);
$obj = json_decode($response);
$st=$obj->status;
$response_list=$obj->data->list['0']->id;

    if($st=="success" && $response_list==$listid){
      return 1;
     } else{
      return 0;
           }  
  }
  else{
  return 0;
  }
}
}

?>