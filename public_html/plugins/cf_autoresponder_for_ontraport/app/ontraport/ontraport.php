<?php
use markroland\Ontraport\Ontraport as Ontra;
class CFAuto_ontraport_autores
{
function __construct()
{

}
function Ontraport($credentials,$email,$name )
{
	

	$namearr=explode(' ',$name);
	$firstname=$namearr[0];
	$lastname="";
	$arrlen=count($namearr);
	if($arrlen>1)
	{
	$lastname=$namearr[$arrlen-1];
	}

	$jsonarr = $credentials; 
	$jsondecode = json_decode($jsonarr);
	// print_r($jsondecode);
	$apikey = $jsondecode->apikey;
	$appid = $jsondecode->appid;

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$file=plugin_dir_path(__FILE__);
	 $file .="api/src/Ontraport.php";
	require_once($file);

// 	$ob=new \CFAuto_ontraport_autores();
$client = new Ontra($appid,$apikey);
$response = $client->addContact(
	array(
			'Contact Information' => array(
			'First Name' => $firstname,
			'Last Name' => $lastname,
			'Email' => $email
		)
	)
);
// print_r($response);

$myXMLData =
"<?xml version='1.0' encoding='UTF-8'?>".$response;

$xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");

$json = json_encode($xml);
$array = json_decode($json,TRUE);
// print_r($array[status]);


if($array['status'] ==  "Success"){ 
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