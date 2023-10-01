<?php
class CFAuto_hubspot_autores
{
function __construct()
{

}
function Hubspot($credentials,$email,$name )
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

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$arr = array(
            'properties' => array(
                array(
                    'property' => 'email',
                    'value' => $email
                ),
            array(
                    'property' => 'firstname',
                    'value' => $firstname
                ),
            array(
            		'property' => 'lastname',
            		'value' => $lastname
            )
        )
        );
        $post_json = json_encode($arr);
       
        $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $apikey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($ch);
        curl_close($ch);

        if ($status_code == 200) {
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