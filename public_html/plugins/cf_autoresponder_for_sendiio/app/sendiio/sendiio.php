<?php
class CFAuto_sendiio_autores
{
function __construct()
{

}
function sendiio($credentials_jsn,$email,$name="")
{
	$credentials_jsn=json_decode($credentials_jsn);
	$listid = $credentials_jsn->listid;
	$apitoken = $credentials_jsn->apikey;
	$apisecret = $credentials_jsn->accesstoken;

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

	$url = 'https://sendiio.com/api/v1/lists/subscribe/json';

	$send_arr=array('email_list_id' => $listid,
	'email' => $email);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('token: '.$apitoken.'','secret: '.$apisecret.''));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $send_arr);
	$result = curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$objv = json_decode($result);

	if ($objv->error === 0) {
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