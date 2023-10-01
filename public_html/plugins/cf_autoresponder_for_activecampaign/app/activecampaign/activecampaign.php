<?php
class Cfautores_activecampaign_processor
{
function __construct()
{

}
function Activecampaign($credentials,$email,$name )
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
	$apiurl = $jsondecode->apiurl;
	$listid = $jsondecode->listid;
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	require_once("api/includes/ActiveCampaign.class.php");
	$ac = new ActiveCampaign($apiurl,$apikey);
	// print_r($ac->credentials_test());
	if ($ac->credentials_test() == 1) {
			$contact = array(
		"first_name" => $firstname,
		"last_name" => $lastname,
		"email"     => $email,
		"p[".$listid."]"      => $listid,
		"status[".$listid."]" => 1, // "Active" status and 2 for "Unsubscribed" Status

	);
	$contact_sync = $ac->api("contact/sync", $contact);
	if (!(int)$contact_sync->success) {
		// request failed
		return 0;
	}
    // successful request
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