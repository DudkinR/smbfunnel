<?php
class CFAuto_sendinblue_autores
{
function __construct()
{

}
function Sendinblue($credentials,$data=array() )
{
	$FIRSTNAME="";
	$LASTNAME = "";
	$CITY = "";
	$email = "";
	if(isset($data['email'])) {
		$email = $data['email'];
	}	
	
	$credentials_jsn=json_decode($credentials);
	$apikey = $credentials_jsn->apikey;
	$groupid= (int) $credentials_jsn->listid;
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
	{  
		$curl = curl_init();

		$attributes = array();

		if(isset($data['name'])){
			$name = $data['name'];
		}else{
			$name = '';
		}
		$newname = explode(" ", $name);
		
		
		$attributes['NAME']=$name;
		if(isset($newname[0])  && !empty($newname[0]) ) {
				$attributes['FIRSTNAME'] = $newname[0];	
		}else{
			$attributes['FIRSTNAME'] = '';	
		}
		if(isset($newname[1])  && !empty($newname[1])) {
			$attributes['LASTNAME'] = $newname[1];	
		}else{
			$attributes['LASTNAME'] = '';	
		}
		$post_fields = json_encode(array(
			"attributes" => $attributes,
			"updateEnabled" => false,
			"email" => $email,
			"listIds" =>[$groupid]
		));
	

		$http_header = array(
			"Accept: application/json",
			"Content-Type: application/json",
			"api-key: ".$apikey
		);

		// print_r($http_header);

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://api.sendinblue.com/v3/contacts',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$post_fields,
		  CURLOPT_HTTPHEADER => $http_header,
		));		
		$response = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($status_code == 200 || $status_code == 201) 
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