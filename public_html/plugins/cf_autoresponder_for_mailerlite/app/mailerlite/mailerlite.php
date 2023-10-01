<?php
class CFAuto_mailerlite_autores
{
function __construct()
{

}
function Mailerlite($credentials,$email,$name )
{
	$credentials_jsn=json_decode($credentials);
	$apikey = $credentials_jsn->apikey;
	$groupid = $credentials_jsn->listid;
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
	{
		
		$data = [
		  "email" =>  $email,
		  "name"  =>  $name,
		  "type"=>"active"
		];
		$data=json_encode($data);
        $ch = curl_init();

        
        curl_setopt_array($ch, array(
          CURLOPT_URL             =>   "https://api.mailerlite.com/api/v2/groups/".$groupid."/subscribers",
          CURLOPT_RETURNTRANSFER  =>   true,
          CURLOPT_ENCODING        =>   "",
          CURLOPT_MAXREDIRS       =>   10,
          CURLOPT_TIMEOUT         =>   0,
          CURLOPT_FOLLOWLOCATION  =>   true,
          CURLOPT_HTTP_VERSION    =>   CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST   =>   "POST",
          CURLOPT_POSTFIELDS      =>   $data,
          CURLOPT_HTTPHEADER => array(
            "X-MailerLite-ApiKey: ".$apikey."",
            "Content-Type: application/json",
            )
        ));
        
		$response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($status_code == 200) 
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}
}

?>