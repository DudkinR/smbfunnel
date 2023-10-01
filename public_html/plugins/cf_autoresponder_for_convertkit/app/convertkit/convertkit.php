<?php
class CFAuto_convertkit_autores
{
function __construct()
{

}
function convertkit($credentials,$email,$name)
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

	$curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.convertkit.com/v3/forms/'.$listid.'/subscribe?api_key='.$apikey.'&email='.$email.'&first_name='.$firstname,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
    ));
    
    $response = curl_exec($curl);
    
   // echo $response;
    
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
      if($httpcode == "200")
      {
         return $httpcode; 
      }
      else{
          return 0;
      }
}
}
}

?>