<?php
class CFAuto_constantcont_autores
{
function __construct()
{

}
function Constantcont($credentials,$email,$name )
{

	
	$namearr=explode(' ',$name);
	$firstname=$namearr[0];
	$arrlen=count($namearr);
	$lastname="";
	if($arrlen>1)
	{
	$lastname=$namearr[$arrlen-1];
	}

	$jsonarr = $credentials; 
	$jsondecode = json_decode($jsonarr);
	// print_r($jsondecode);
	$apikey = $jsondecode->apikey;
	$accesstoken = $jsondecode->accesstoken;
	 $listId = $jsondecode->listid;

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

	$url = "https://api.constantcontact.com/v2/contacts?email=".$email."&status=ALL&limit=50&api_key=".$apikey;

$header[] = "Authorization: Bearer ".$accesstoken;
$header[] = 'Content-Type: application/json';

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch));
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


if(!$response->results){
$url = "https://api.constantcontact.com/v2/contacts?action_by=ACTION_BY_VISITOR&api_key=".$apikey;
$body = '{
"lists": [
{
"id": "'.$listId.'"
}
],       
"confirmed": false,
"email_addresses": [
{
"email_address": "'.$email.'"
}
],
"first_name": "'.$firstname.'",
"last_name": "'.$lastname.'",
}';

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
// print_r($status_code); // Returns 201 on success
// print_r($response);
if ($status_code == 201) {
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
	else{
		return 0;
	}

}
}

?>