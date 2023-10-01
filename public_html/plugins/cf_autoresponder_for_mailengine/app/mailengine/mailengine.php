<?php
class CFAuto_mailengine_autores
{
function __construct()
{

}
function Mailengine($credentials,$email,$name )
{

	$arr=json_decode($credentials);
	//  print_r($arr);
	  $apikey =$arr->apikey;

	  $apiurl = $arr->apiurl;

		   $listid = $arr->listid;
	   //   echo $email;
	   //   echo $name;   
			   if(filter_var($email,FILTER_VALIDATE_EMAIL))
			   {
		   
				   $ch=curl_init();
				   curl_setopt($ch,CURLOPT_URL,$apiurl);
				   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				   curl_setopt($ch,CURLOPT_POST,true);
				   curl_setopt($ch,CURLOPT_POSTFIELDS,array('wqaddsubscriber'=>1,'api_auth_key'=>$apikey,'list_id'=>$listid,'name'=>$name,'email'=>$email));
				   $res=curl_exec($ch);
				   curl_close($ch);
				   $res=json_decode($res);
				   $jsnerr=json_last_error();
				   if($jsnerr ===0)
				   {
					   return $res->added;
				   }
				   else
				   {
					   return false;
				   }
			   }
			   else
			   {
				   return 0;
			   }
}
}

?>