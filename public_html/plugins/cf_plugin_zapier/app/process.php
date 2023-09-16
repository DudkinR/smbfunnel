<?php
namespace CFZAP_zapier_addon\zapier;
class Cfzap_processor
{

    var $api_url;
    var $zap_token;
    var $load;
    function __construct($arr)
    {
        $this->api_url="http://cloudfunnels.in/membership/api/api_zapier";
        $this->zap_token=hash_hmac('sha1',get_option('zapier_token'),get_option('site_token'));
        $this->pref=$arr['pref'];
        if(isset($_POST['createzapapintid']))
        {
            self::addToZapierIntegration();
         }

    }
    function addToZapierIntegration()
    {
        if(get_option('valid_user_data'))
        {
            
            $paymentdata=json_decode(cf_enc(get_option('valid_user_data'),'decrypt'));
            if(isset($paymentdata->custemail))
            {
                $callbackurl=get_option('install_url');
                $callbackurl .="/index.php?page=callback_api&action=zapir_api_callback";
                $arr=array('add_to_zap'=>1,'purchase_email'=>$paymentdata->custemail,'order_code'=>'','cf_verification_code'=>$this->zap_token,'callback_url'=>$callbackurl);
                $ch=curl_init();
                curl_setopt($ch,CURLOPT_URL,$this->api_url);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_POST,true);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
                $res=curl_exec($ch);
                curl_close($ch);
                $res=json_decode($res);
                $api = $res->api_id;
                if(isset($res->api_id))
                {
                    if(!get_option('zapier_auth_id'))
                    {
                        add_option('zapier_auth_id',$res->api_id);
                    }
                    else
                    {
                        update_option('zapier_auth_id',$res->api_id);
                    }
                    return 1;
                }
            }
        }
        return 0;
    
    }
    function showLeadsToZapier($auth_id)
    {

        $userDataIsValid = self::userDataIsValid();
        if($userDataIsValid)
        {

            if($auth_id==$this->zap_token)
            {  
                return self::getOptionForSpecifcPagesforZapier();
            }
            else
            {
                return 0;
            }
        }

        }    
    function userDataIsValid($recheck=0)
	{

		if(get_option('cookie_token'))
		{
			$cookie_token=(int)get_option('cookie_token');
			$current_cookie_time=time();
			if(($current_cookie_time-$cookie_token))
			{
				update_option('cookie_token',$current_cookie_time);
			}
		}
		else
		{
			update_option('cookie_token',time());
		}

		if($recheck===0)
		{
			if(get_option('is_valid_user'))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
    }
    function getOptionForSpecifcPagesforZapier()
	{
		global $mysqli;
		global $dbpref;
		$table=$dbpref."quick_optins";
		$qry=$mysqli->query("select `a`.id,`a`.name,`a`.email,`a`.extras,`a`.ipaddr,(select `valid_inputs` from `".$dbpref."quick_pagefunnel` where `id`=`a`.pageid) as `zap_valid_inputs` from `".$table."` as `a` where `pageid` in (select `id` from `".$dbpref."quick_pagefunnel` where `settings` like '%\"zapier_enable\":true%') and `send_zap` not in('1') limit 100");
		$token=get_option('site_token');
		$arr=array();
		while($r=$qry->fetch_assoc())
		{
			$r['id'] +=125;
			$r['id'] =hash_hmac('sha1',$r['id'],$token);
			$valid_inputs=explode(",",$r['zap_valid_inputs']);
			$extras=(array)json_decode($r['extras']);

			$data_arr=array('id'=>$r['id']);
			foreach($valid_inputs as $valid_input)
			{
				if(in_array($valid_input,array('name','email')))
				{
					$data_arr[$valid_input]=$r[$valid_input];
				}
				elseif(isset($extras[$valid_input]))
				{
					$data_arr[$valid_input]=$extras[$valid_input];
				}
			}

			array_push($arr,json_encode($data_arr));
		}
		$up=$mysqli->query("update `".$table."` set `send_zap`='1' where `pageid` in (select `id` from `".$dbpref."quick_pagefunnel` where `settings` like '%\"zapier_enable\":true%') and `send_zap` not in('1') limit 100");
        
		return "[".implode(",",$arr)."]";
	}

    }

?>