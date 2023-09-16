<?php
	class CFAuto_pursueapp_autores
	{
		function __construct()
		{

		}
		function Pursueapp($credentials,$email,$name )
		{

			if(filter_var($email,FILTER_VALIDATE_EMAIL))
			{
				$jsonarr = $credentials; 
				$jsondecode = json_decode($jsonarr);
				$apikey = $jsondecode->apikey;
				$listid = $jsondecode->listid;
				$apiurl="https://pursueapp.in/lists/api/add_subscriber";
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$apiurl);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($ch,CURLOPT_POST,true);
				curl_setopt($ch,CURLOPT_POSTFIELDS,array('apiKey'=>$apikey,'listId'=>$listid,'name'=>$name,'email'=>$email));
				$res=curl_exec($ch);
				$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				$res=json_decode($res);
				$jsnerr=json_last_error();
				;
				$status = $res->status;
			
				if($status)
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