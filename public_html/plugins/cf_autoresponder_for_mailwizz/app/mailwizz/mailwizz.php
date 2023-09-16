<?php
class CFAuto_mailwizz_autores
{
function __construct()
{

}
function mailwizz($credentials_jsn, $email="", $name="", $test = false)
{
   global $mysqli;
   global $pref;

	$file=plugin_dir_path(__FILE__);
	$file .="mailwizz-php-sdk-master/MailWizzApi/Autoloader.php";
	require_once($file);

	MailWizzApi_Autoloader::register();

	$email = $mysqli->real_escape_string($email);
	$name = $mysqli->real_escape_string($name);
	
	if(filter_var($email,FILTER_VALIDATE_EMAIL)) {
		$jsonarr = $credentials_jsn; 
		$jsondecode = json_decode($jsonarr);
		$config = new MailWizzApi_Config([
		    'apiUrl'        => $jsondecode->apiurl,
		    'publicKey'     => $jsondecode->apikey,
		    'privateKey'    => $jsondecode->apikey,
		]);
		
		MailWizzApi_Base::setConfig($config);
		
		$endpoint = new MailWizzApi_Endpoint_ListSubscribers();
		
		//$names = explode(' ', $name);

		$namearr=explode(' ',$name);
		$firstname=$namearr[0];
		$arrlen=count($namearr);
		$lastname="";

		if($arrlen>1)
		{
			$lastname=$namearr[$arrlen-1];
		}
		
		$response = $endpoint->create($jsondecode->listid, [
		    'EMAIL'    => $email, // the confirmation email will be sent!!! Use valid email address
		    'FNAME'    => $firstname,
		    'LNAME'    => $lastname
		]);
		
		//PC::debug(json_encode($response));
		
		
		if ($response->getIsSuccess()) {
			return 1;	
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
}

?>