<?php

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

class CFAuto_mautic_autores
{
function __construct()
{

}
//generate Auth Mautic
function mautic($credentials,$name,$email,$doo="process")
{
	if(!filter_var($email,FILTER_VALIDATE_EMAIL))
	{
		return 0;
	}
	// $file=rtrim(str_replace("\\","/",__DIR__),"/");
	$file = plugin_dir_path(dirname(__FILE__,1));
	$file .="mautic/vendor/autoload.php";
	require_once($file);

	$credentials=json_decode($credentials);

	$settings = array(
		'userName'   => $credentials->appid,             // Create a new user       
		'password'   =>  $credentials->apikey             // Make it a secure password
	);
	
	// Initiate the auth object specifying to use BasicAuth
	$initAuth = new ApiAuth();
	$auth = $initAuth->newAuth($settings, 'BasicAuth');

	$apiUrl     = $credentials->apiurl;
	$api        = new MauticApi();
	$contactApi = $api->newApi("contacts", $auth, $apiUrl);

	if( $name===false){
		$name="Cloud Funnels";
	}

	$namearr=explode(' ',$name);
	$firstname=$namearr[0];
	$arrlen=count($namearr);
	$lastname="";

	if($arrlen>1)
	{
	$lastname=$namearr[$arrlen-1];
	}

	$data = array(
		'firstname' => $firstname,
		'lastname'  => $lastname,
		'email'     => $email,
		//'ipAddress' => $_SERVER['REMOTE_ADDR'],
		'overwriteWithBlank' => true,
	);
	$contact = $contactApi->create($data);
	
	if(is_array($contact) && isset($contact['contact']['id']) )
	{
		$listid=trim($credentials->listid);
		if(strlen($listid)>0)
		{
			$segmentApi = $api->newApi("segments", $auth, $apiUrl);
			$response = $segmentApi->addContact($listid, $contact['contact']['id']);
			if (isset($response['success'])) {
				return 1;
			}else{return 0;}
		}else
		{
			return 1;
		}
		// $contact['contact']['id'];
	}
	else
	{
		return 0;
	}
	die();
}
}

?>