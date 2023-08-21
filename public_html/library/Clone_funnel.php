<?php
class Clone_funnel
{
    var $mysqli;
	var $dbpref;
	var $load;
    var $ip;
    var $funnel_ob;

    function __construct($arr)
    {
        $this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		$this->load=$arr['load'];
        $this->ip=$arr['ip'];
        $this->funnel_ob=$this->load->loadFunnel();
    }
    function request($token)
    {
        $pr=$this->load->isPlusUser();
        if(!$pr){echo "Please upgrade to pro for using this feature.";}
        $token=explode("@@cfbrk@@",base64_decode($token));
        if(!(is_array($token)&&(count($token)===2)))
        {
            return false;
        }
        if(filter_var($token[0],FILTER_VALIDATE_URL))
        {
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$token[0]);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,array('copy_funnel'=>$token[1]));
            $res=curl_exec($ch);
            if(curl_errno($ch))
            {
                return false;
            }
            curl_close($ch);
            return $res;
        }
        else
        {
            return false;
        }
    }
    function createMap($id)
    {
        $token=$this->funnel_ob->decryptFunnelToken($id);
        $funnel_url=false;
        if($token)
        {
            $id=$token['id'];
            $funnel_url=$this->funnel_ob->getFunnel($id,"`baseurl`,`type`,`token`");
            if($funnel_url->token !==$token['token'])
            {
                $funnel_url=false;
            }
        }
        
        if($funnel_url)
        {
            $funnel_type=$funnel_url->type;
            $funnel_url=str_replace("@@qfnl_install_url@@",get_option('install_url'),$funnel_url->baseurl);
            $mysqli=$this->mysqli;
            $pref=$this->dbpref;
            $table=$pref."quick_pagefunnel";
            $id=$mysqli->real_escape_string($id);
            $qry=$mysqli->query("select `name`,`title`,`metadata`,`filename`,`pageheader`,`pagefooter`,`category`,`templateimg`,`valid_inputs` as `inputs`,`settings`,`level`,`type`,`hasabtest` from `".$table."` where `funnelid`='".$id."' and type in('a','b') order by `type`");
            $arr=array();

            if($qry->num_rows>0)
            {
                while($r=$qry->fetch_object())
                {
                    $content_ob=$this->funnel_ob->readContent($id,$r->level,$r->type);

                    if(!strpos($content_ob['html'],'</body>'))
                    {
                        $content_ob['html']="<html><head></head><body>".$content_ob['html']."</body></html>";
                    }

                    $content=str_replace("</body>","<style>".$content_ob['css']."</style>",$content_ob['html']);

                    $r->page_content=$content;
                    $r->page_url=$funnel_url."/".$r->filename;
                    $r->funnel_type=$funnel_type;
                    array_push($arr,$r);
                }
            }
            return json_encode($arr);
        }
        else
        {
            echo "-@CF-NaN@-";
        }
    }
    function getPageContent($funnel,$page,$ab='a')
    {
        $page=$this->funnel_ob->getPageFunnelDataByFolder($funnel,$page,$ab,"level");
        $content=false;
        if($page)
        {
            $content=$this->funnel_ob->readContent($funnel,$page->level,$ab);
        }

        return json_encode(array('content'=>$content));
    }
}
?>