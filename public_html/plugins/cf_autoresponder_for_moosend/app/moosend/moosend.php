<?php
class CFAuto_moosend_autores
{
function __construct()
{

}
function moosend($credentials_jsn,$email,$name)
{
	$credentials_jsn=json_decode($credentials_jsn);
	$listid = $credentials_jsn->listid;
	$apikey = $credentials_jsn->apikey;

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

	$url='https://api.moosend.com/v3/subscribers/'.$listid.'/subscribe.json?apikey='.$apikey;
	
	$json = json_encode([
    'Name' => $name,
    'Email' => $email, 
]);


	$ch = curl_init($url);
//	curl_setopt($ch, CURLOPT_HTTPHEADER, array('token: '.$apitoken.'','secret: '.$apisecret.''));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	$result = curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$objmoos = json_decode($result);
	

	if ($objmoos->Code === 0) {
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