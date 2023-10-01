<?php
class CFAuto_getresponse_autores
{
function __construct()
{

}
function Getresponse($credentials,$email,$name )
{
	$jsonarr = $credentials; 
	$jsondecode = json_decode($jsonarr);
	// print_r($jsondecode);
	$apikey = $jsondecode->apikey;
	// $accesstoken = $jsondecode->accesstoken;
	$campaignid = $jsondecode->campaignid;

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

$addcontacturl = 'https://api.getresponse.com/v3/contacts/';
$data = array (
'name' => $name,
'email' => $email,
'dayOfCycle' => 0, //Autoresponder Day
'campaign' => array('campaignId'=> $campaignid)
);  
$data_string = json_encode($data); 
$ch = curl_init($addcontacturl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'X-Auth-Token: api-key '.$apikey,
)           
);                                                                                         

$result = curl_exec($ch); 
// print_r($result);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
// echo $status_code;//Returns status code 202 on success
if ($status_code == 202) {
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