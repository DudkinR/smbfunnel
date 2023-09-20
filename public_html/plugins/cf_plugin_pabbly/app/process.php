<?php
namespace CFPBL_pabbly_addon\pabbly;
class Cfpbl_pabbly
{

    var $api_url;
    var $pab_loop=-1;
    var $load;
    function __construct($arr)
    {
        $this->api_url="";
        $this->pref=$arr['pref'];
    }
    function save_requested_data($post)
    {
        global $mysqli;
        $post_data = $mysqli->real_escape_string($post['form_data']);
        if (isset($post['savepabblybtn'])) {
            update_option('pabbly_webhook_data', $post_data);
            echo json_encode(array("status"=>1,"msg"=>"Form saved successfully"));
            return;
        }
        echo json_encode(array(
            "status"=>0,
            "msg"=>"Something went wrong"
        ));
        return;
    }
    function sendDetailsToPabbly($url='', $key=-1)
    {
        $this->api_url=$url;
        $this->pab_loop=$key;

        if(($this->pab_loop != -1) && ($this->api_url!=="")) {
            $is_lead = self::showLeadsToPabbly();
            if($is_lead != '[]') {
                $ch=curl_init();
                curl_setopt($ch,CURLOPT_URL,$this->api_url);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_POST,true);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$is_lead);
                $res=curl_exec($ch);
                curl_close($ch);
                
                $this->api_url="";
                $this->pab_loop=-1;
                return 1;
            }
        }
        return 0;
    }

    function showLeadsToPabbly()
    {
        $userDataIsValid = self::userDataIsValid();
        if($userDataIsValid)
        {
            return self::getOptionForSpecifcPagesforPabbly();
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
    function getOptionForSpecifcPagesforPabbly()
	{
		global $mysqli;
		global $dbpref;
		$table=$dbpref."quick_optins";
		$qry=$mysqli->query("select `a`.id,`a`.name,`a`.email,`a`.extras,`a`.ipaddr,(select `valid_inputs` from `".$dbpref."quick_pagefunnel` where `id`=`a`.pageid) as `pab_valid_inputs` from `".$table."` as `a` where `pageid` in (select `id` from `".$dbpref."quick_pagefunnel` where `settings` like '%\"pabbly_enable\":true%') and `send_pab` not in('1') limit 100");
		$token=get_option('site_token');
		$arr=array();
        if(isset($qry->num_rows) && $qry->num_rows > 0) {
            while($r=$qry->fetch_assoc())
            {
                $r['id'] +=125;
                $r['id'] =hash_hmac('sha1',$r['id'],$token);
                $valid_inputs=explode(",",$r['pab_valid_inputs']);
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
            if($this->pab_loop===1) {
                $up=$mysqli->query("update `".$table."` set `send_pab`='1' where `pageid` in (select `id` from `".$dbpref."quick_pagefunnel` where `settings` like '%\"pabbly_enable\":true%') and `send_pab` not in('1') limit 100");
            }
        }
        return "[".implode(",",$arr)."]";
	}

    }

?>