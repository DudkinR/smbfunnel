<?php
class CFAuto_mailchimp_autores
{
function __construct()
{

}
function Mailchimp($credentials,$email,$name )
{
	$namearr=explode(' ',$name);
	$firstname=$namearr[0];
	$arrlen=count($namearr);
	$lastname="";
	if($arrlen>1)
	{
	$lastname=$namearr[$arrlen-1];
	}

	 $jsonarr =$credentials; 
	$jsondecode = json_decode($jsonarr);
	//print_r($jsondecode);
	$apikey = $jsondecode->apikey;
	$listid = $jsondecode->listid;

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

$data_center = substr($apikey,strpos($apikey,'-')+1);
 
$url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $listid .'/members';
 
$json = json_encode([
    'email_address' => $email,
    'status'        => 'subscribed', //pass 'subscribed' or 'pending'
    'merge_fields'  => array(
        'FNAME' => $firstname,
        'LNAME'    => $lastname,
         ),
]);
 
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apikey);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
$result = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
// echo $status_code;
// print_r($result);
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