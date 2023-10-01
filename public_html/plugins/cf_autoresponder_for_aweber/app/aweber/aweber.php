<?php
class CFAuto_aweber_autores
{
function __construct()
{

}
function aweber($credentials_jsn,$email,$name)
{
	$data=json_decode($credentials_jsn);
	$arr=array(
		'aweber_auth_token'=>$data->appid,
		'aweber_url_token'=>$data->listid,
		'name'=>$name,
		'email'=>$email
	);
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"http://cloudfunnels.in/membership/api/aweber/add_subscriber");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
	$res=curl_exec($ch);
	if(trim($res)==='1')
	{
		return 1;
	}
	else
	{
		return 0;
	}
}
}

?>