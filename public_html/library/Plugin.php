<?php
class Plugin{
    var $mysqli;
	var $dbpref;
	var $load;
	var $ip;
	var $base_dir;
    var $secure;
    var $base_url;
    var $plugin_table;

    
    var $activation_callbacks=array();
    var $deactivation_callbacks=array();
    var $action_callbacks=array(
        'cf_head'=>array(),
        'cf_footer'=>array(),
        'init'=>array(),
        'admin_menu'=>array(),
        'admin_menues'=>array(),
        'admin_submenues'=>array(),
        'app_menu'=>array(),
        'admin_init'=>array(),
        'admin_head'=>array(),
        'admin_footer'=>array(),
        'cf_sale'=>array(),
        //cf_ajax
        //cf_ajax_nopriv
        //'cf_autoresponder'=>array(),
        //'cf_payment_methods'=>array(),
        'cf_funnel_create'=>array(),
        'cf_funnel_delete'=>array(),
        'cf_funnel_page_create'=>array(),
        'cf_funnel_page_level_change'=>array(),
        'cf_funnel_page_delete'=>array(),
        'cf_funnel_page_setup_change'=>array(),
        'cf_funnel_page_content_change'=>array(),
        'cf_member_create'=>array(),
        'cf_member_update'=>array(),
        'cf_member_delete'=>array(),
        'cf_product_create'=>array(),
        'cf_product_update'=>array(),
        'cf_product_delete'=>array(),
    );
    var $autores_callbacks=array();
    var $payment_methods_callbacks=array();
    var $filter_callbacks=array(
        'the_content'=>array(),
        'the_email_content'=>array(),
        'the_email_subject'=>array(),
        'the_checkout_data'=> array(),
    );

    var $temp_products= [];

    var $shortcodes=array();
    var $langs= array();

    function __construct($arr)
    {
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		if(isset($arr['load']))
		{
		$this->load=$arr['load'];
		$this->secure=$this->load->secure();
		}
		$this->ip=$arr['ip'];

		if(isset($arr['base_dir']))
		{
		$this->base_dir=$arr['base_dir'];
        }
        $this->plugin_table=$this->dbpref.'qfnl_plugins';
    }
    function getConfig($plugin,$folder)
    {
        $dbpref=$this->dbpref;
        $mysqli=$this->mysqli;
        $table=$this->plugin_table;
        $plugin=$mysqli->real_escape_string($plugin);
        $file=$folder."/config.json";
        $base_url=rtrim(get_option('install_url'),"/");
        $base_url .="/plugins/".$plugin;

        if(is_file($file) && filesize($file)>0)
        {
            $fp=fopen($file,'r');
            $data=fread($fp,filesize($file));
            fclose($fp);

            $data=json_decode($data);
            if(isset($data->start))
            {
                $data->active=false;
                $data->registered=false;

                $data->start=self::filterAttr($data->start);
                $data->start=$folder."/".$data->start;
                $data->dir=$folder;
                $data->url=$base_url;

                if(isset($data->logo))
                {
                    $data->logo=trim($data->logo);
                    if(strlen($data->logo)>0 && !filter_var($data->logo,FILTER_VALIDATE_URL))
                    {
                        $data->logo=self::filterAttr($data->logo);
                        $data->logo=$base_url."/".$data->logo;
                    }
                }

                $qry=$mysqli->query("select `status` from `".$table."` where `base_dir`='".$plugin."'");
                if($qry->num_rows>0)
                {
                    $r=$qry->fetch_object();
                    $data->registered=true;
                    if($r->status=='1')
                    {
                        $data->active=true;
                    }
                }
                $data->temp_id=time();
                $data->temp_id .=$data->temp_id.mt_rand(0,10000000000);
                $data->temp_id .=substr(str_shuffle('xcvbnmsdfghjkwertyuiop1234567890'),0,5);
                return $data;
            }
        }
        return false;
    }
    function filterAttr($attr)
    {
        $attr=trim($attr);
        $attr=trim($attr,".");
        $attr=trim($attr,"/");
        $attr=trim($attr,"\\");
        $attr=str_replace("\\","/",$attr);
        return $attr;
    }
    function getPlugins($type="all")
    {
        $dir=$this->base_dir."/plugins";
        $folders=scandir($dir);
        $plugins=array();
        for($i=0;$i<count($folders);$i++)
        {
            if(in_array($folders[$i],array('.','..')))
            {
                continue;
            }
            $folder=self::filterAttr($folders[$i],'/');
            $plugin_name=$folder;
            $folder=$dir."/".$folder;
            $config_file=$folder."/config.json";
            $has_config=false;
            if(@is_file($config_file))
            {
                $config=self::getConfig($plugin_name,$folder);
                if($config)
                {
                    $config->temp_id .=$i;
                    $plugins[$plugin_name]=$config;
                }
            }
        }
        if($type=="all")
        {
            return $plugins;
        }
        if(in_array($type,array('active','inactive')))
        {
            foreach($plugins as $index=>$plugin)
            {
                if((!$plugin->active && $type=='active')||($plugin->active && $type=="inactive"))
                {
                    unset($plugins[$index]);
                }
            }
            return $plugins;
        }
        else
        {
            return array();
        }
    }
    function processActivation($plugin,$doo="activate")
    {
        try
        {
            if(!in_array($doo,array('activate','deactivate')))
            {
                throw new Exception("Invalid Command");
            }
            $mysqli=$this->mysqli;
            $dbpref=$this->dbpref;
            $table=$this->plugin_table;

            $plugin=$mysqli->real_escape_string($plugin);
            $base_dir=$this->base_dir;
            $pid=$plugin;
            $plugin=self::getConfig($plugin,$base_dir."/plugins/".$plugin);

            $do_check_version=function()use(&$plugin){
                if(isset($plugin->required_version) && ($plugin->required_version>(get_option('qfnl_current_version'))))
                    {
                        throw new Exception('To activate this plugin you need minimum CloudFunnels version '.$plugin->required_version.', your current CloudFunnels version is '.get_option('qfnl_current_version'));
                    }
            };
            
            if($plugin)
            {
                $date=date('Y-m-d H:i:s');
                if($plugin->registered)
                {
                    $stat=($doo=='activate')? 1:0;

                    if($stat)
                    {
                        $do_check_version();
                    }

                    $qry=$mysqli->query("update `".$table."` set `status`='".$stat."', `activated_on`='".$date."' where `base_dir`='".$pid."'");
                }
                else if($doo=='activate')
                {
                    //qfnl_current_version

                    $do_check_version();

                    $plugin_version='0';
                    if(isset($plugin->version))
                    {
                        $plugin_version=$mysqli->real_escape_string($plugin->version);
                    }
                    
                    $qry=$mysqli->query("insert into `".$table."` (`base_dir`,`destin_version`,`status`,`activated_on`) values ('".$pid."','".$plugin_version."','1','".$date."')");
                }

                if(!isset($qry)|| !$qry)
                {
                    throw new Exception("Unable to process this request");
                }
                else
                {
                    require_once($plugin->start);
                    if($doo=='activate')
                    {
                        foreach($this->activation_callbacks as $cb)
                        {
                            if(is_array($cb))
                            {
                                if(is_array($cb[0]))
                                {
                                    $temp_func=$cb[0][1];
                                    $cb[0][0]->$temp_func($cb[1]);
                                }
                                else
                                {
                                    $cb[0]($cb[1]);
                                }
                            }
                        }
                    }
                    else if($doo=='deactivate')
                    {
                        foreach($this->deactivation_callbacks as $cb)
                        {
                            if(is_array($cb))
                            {
                                if(is_array($cb[0]))
                                {
                                    $temp_func=$cb[0][1];
                                    $cb[0][0]->$temp_func($cb[1]);
                                }
                                else
                                {
                                    $cb[0]($cb[1]);
                                }
                            }
                        }
                    }
                    return 1;
                }
            }
            else
            {
                throw new Exception("Unable to detect as a plugin");
            }

        }catch(Exception $e){ return $e->getMessage(); }
    }
    function checkForUpdate($plugin_id,$detailed)
    {
        $stat=array("error"=>false,'update'=>false,'file'=>'',"version"=>"0.0","data"=>"","required_cf_version"=>"0.0");
        $base_dir=$this->base_dir;
        $plugin=self::getConfig($plugin_id,$base_dir."/plugins/".$plugin_id);
        if($plugin && isset($plugin->auto_update_callback_url) && isset($plugin->version))
        {
            $url=$plugin->auto_update_callback_url;
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_TIMEOUT,10);
            $res=curl_exec($ch);
            if(curl_errno($ch))
            {
                $err_msg=curl_error($ch);
                $stat["error"]=true;
                $stat["data"]=$err_msg;
            }
            curl_close($ch);

            if(!$stat['error'])
            {
                $data=json_decode($res);
                if(isset($data->version) && ($data->version>$plugin->version) && isset($data->file) && filter_var($data->file,FILTER_VALIDATE_URL))
                {
                    if($data->version>$plugin->version)
                    {
                        $stat['update']=true;
                        $stat['version']=$data->version;
                        if(isset($data->message))
                        {
                            $stat["data"]=$data->message;
                        }
                        if(isset($data->required_cf_version))
                        {$stat['required_cf_version']=$data->required_cf_version;}
                        $stat['file']=$data->file;
                    }
                    elseif($data->version===$plugin->version)
                    {
                        $stat['version']=$data->version;
                    }
                }
            }
        }
        return $stat;
    }
    function uploadPlugin($zip_file,$do_update=false)
    {
        $base_dir=$this->base_dir;
        $temp_dir=$base_dir."/public-assets/temp_plugins";
        $plugins_dir=$base_dir."/plugins";
        try
        {
            $file=$zip_file;
            $tmp_name=explode('.',basename($file));
            if(strtolower($tmp_name[count($tmp_name)-1])=='zip')
            {
                $zip=new ZipArchive();
                $zip->open($file);

                array_pop($tmp_name);
                $dir_name=implode(".",$tmp_name);
                $arr=array();
                for($i=0;$i<$zip->numFiles;$i++)
                {
                    $stat=$zip->statIndex($i);
                    $name=self::filterAttr($stat['name']);
                    array_push($arr,$name);
                }
                $has_config=false;
                if(in_array('config.json',$arr))
                {
                    $has_config=true;
                    $dir=$plugins_dir.'/'.$dir_name;
                    if(!self::getConfig($dir_name,$dir)||$do_update)
                    {
                        if(!$do_update)
                        {cf_rmdir($dir);}
                        mkdir($dir);
                        $zip->extractTo($dir);
                        $zip->close();
                    }
                    else
                    {
                        $zip->close();
                        throw new Exception("There already a plugin exist with same directory name");
                    }
                }
                elseif(!strpos($arr[0],'/') && in_array($arr[0].'/config.json',$arr))
                {
                    $has_config=true;
                    $dir=$plugins_dir."/".$arr[0];
                    if(!self::getConfig($arr[0],$dir)||$do_update)
                    {
                        if(!$do_update)
                        {cf_rmdir($dir);}
                        $zip->extractTo($plugins_dir);
                        $zip->close();
                    }
                    else
                    {
                        $zip->close();
                        throw new Exception("There already a plugin exist with same directory name");
                    }
                }
                else
                {
                    throw new Exception('There no valid configuration file exists');
                }

                if($has_config)
                {
                    return 1;
                }
            }
            else
            {
                throw new Exception("Please upload a valid zip file.");
            }
        }catch(Exception $err)
        {
            return $err->getMessage();
        }
    }
    function remotePluginInstall($file,$do_update=false,$plugin_id="")
    {
        try
        {
            if(!filter_var($file,FILTER_VALIDATE_URL))
            {
                throw new Exception("Invalid file URL provided");
            }
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$file);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            "Host: cloudfunnels.in"
            ));
            $res=curl_exec($ch);
            if(curl_errno($ch))
            {
                $err=curl_error($ch);
                curl_close($ch);
                throw new Exception($err);
            }
            curl_close($ch);

            $temp_dir=$this->base_dir."/public-assets/temp_plugins";
		    if(cf_dir_exists($temp_dir))
		    {
			    cf_rmdir($temp_dir);
		    }
		    mkdir($temp_dir);
            if($do_update)
            {
                $plugin_id=trim($plugin_id);
                if(strlen($plugin_id)<1)
                {
                    throw new Exception("Invalid plugin id provided");
                }
                //if(strlen($plugin_id))
                $zip_file=$temp_dir.'/'.$plugin_id.'.zip';
            }
            else
            {
                $zip_file=basename($file);
                $zip_file=$temp_dir.'/'.$zip_file;
            }
            $fp=fopen($zip_file,'w');
            fwrite($fp,$res);
            fclose($fp);
            if(!is_file($zip_file))
            {
                cf_rmdir($temp_dir);
                throw new Exception("Unable to download plugin");
            }
            $stat_data=self::uploadPlugin($zip_file,$do_update);
            cf_rmdir($temp_dir);
            return $stat_data;
        }catch(Exception $err)
        {
            return $err->getMessage();
        }
    }
    function deletePlugin($plugin)
    {
        $mysqli=$this->mysqli;
        $plugin=$mysqli->real_escape_string($plugin);
        $dir=$this->base_dir.'/plugins/'.$plugin;
        cf_rmdir($dir);
        $mysqli->query("delete from `".$this->plugin_table."` where `base_dir`='".$plugin."'");
        return 1;
    }
    function playCallbacks($cbs,$settings=array()){
        foreach($cbs as $cb)
        {
            if(is_array($cb))
            {
                if(is_array($cb[0]))
                {
                    $temp_func=$cb[0][1];
                    $data=$cb[0][0]->$temp_func($settings,$cb[1]);
                }
                else
                {
                    $data=$cb[0]($settings,$cb[1]);
                }
                //if($output && $data!==null){echo $data;}
            }
        }
    }
    function playCallbacksForFilter($cbs,$content,$settings=array())
    {
        foreach($cbs as $cb)
        {
            if(is_array($cb))
            {
                if(is_array($cb[0]))
                {
                    $temp_func=$cb[0][1];
                    $content=$cb[0][0]->$temp_func($content,$settings,$cb[1]);
                }
                else
                {
                    $content=$cb[0]($content,$settings,$cb[1]);
                }
                //if($output && $cont!==null){echo $data;}
            }
        }
        return $content;
    }
    function attachToContent($type,$settings){
        //$type=header||footer
        //attaching header footer scripts with add action
        $data="";
        ob_start();
        $arr=$this->action_callbacks[$type];
        $this->playCallbacks($arr,$settings);
        $data=ob_get_contents();
        ob_end_clean();
        return $data;
    }
    function processFilter($type,$content="",$settings=array())
    {
        //filter hook
        $cbs=$this->filter_callbacks[$type];
        return self::playCallbacksForFilter($cbs,$content,$settings);
    }
    function processInit($type='init')
    {
        $cbs=$this->action_callbacks[$type];
        self::playCallbacks($cbs,array());
    }
    function processAdminMenu($page=false)
    {
        $this->action_callbacks['admin_menues']=array();
        $this->action_callbacks['admin_submenues']=array();
        $cbs=$this->action_callbacks['admin_menu'];
        self::playCallbacks($cbs,array());

        if($page !==false && is_string($page) && strlen($page)>0)
        {
            if(isset($this->action_callbacks['admin_menues'][$page]))
            {
                return $this->action_callbacks['admin_menues'][$page];
            }
            else if(isset($this->action_callbacks['admin_submenues'][$page]))
            {
                if(isset($this->action_callbacks['admin_menues'][$this->action_callbacks['admin_submenues'][$page][0]['parent_slug']]))
                {
                    return $this->action_callbacks['admin_submenues'][$page];
                }
                else
                {
                    return false;
                }
            }
            else{return false;}
        }
        else
        {
            return $this->action_callbacks['admin_menues'];
        }
    }
    function doShortcode($scode,$str)
    {
        if(!$scode)
        {
            foreach($this->shortcodes as $index=>$cb)
            {
                $str=self::processShortcode($index,$str);
            }            
        }
        else
        {
            $str=self::processShortcode($scode,$str);
        }
        return $str;
    }
    function processShortcode($scode,$str)
    {
        if(!isset($this->shortcodes[$scode]))
        {
            return $str;
        }
        $s_cb=$this->shortcodes[$scode];

        $r_scode=preg_quote($scode);
	
	    $init_reg="/(\[".$r_scode.")(.(?!\[))*(\])+/";
	    $complete_reg="/(\[".$r_scode.")(.(?!\[))*(\])+(.(?!\[\/".$r_scode."))*(.)?(\[\/".$r_scode."\])+/";
	
        $play_scode=function($code,$str="")use($scode,&$s_cb)
        {
            $reg="/([a-zA-Z0-9_\-])+(=)+((\"(.(?!\"))*.\")|(\'(.(?!\'))*.\')|(\S+))/";
            $params=array();
            $code=trim(str_replace("[".$scode,"",$code));
            $code=rtrim($code,"]");
            preg_match_all($reg,$code,$arr);
            if(isset($arr[0]) && is_array($arr[0]))
            {
                foreach($arr[0] as $param)
                {
                    $param=preg_replace_callback("/=/",function($matches){return "@-cf-scode-brk-@";},$param,1);
                    $param=explode("@-cf-scode-brk-@",$param);
                    $params[$param[0]]=trim($param[1],"\"\'");
                }
            }
            //print_r($params);
            if(is_array($s_cb) && count($s_cb)>0)
            {
                if(is_array($s_cb[0]))
                {
                    //print_r($s_cb);
                    $temp_s_cb=$s_cb[0][1];
                    $str=$s_cb[0][0]->$temp_s_cb($params,$str,$s_cb[1]);
                }
                else
                {
                    //print_r($s_cb[1]);
                    $str=$s_cb[0]($params,$str,$s_cb[1]);
                }
            }
            return $str;
        };
	
	$cb_open_close=function($matches)use($complete_reg,$init_reg,$scode,$play_scode){
		$data=$matches[0];
		preg_match($init_reg,$data,$arr);
		$code=$arr[0];
		
		$data=rtrim($data,"[/".$scode."]");
		$data=preg_replace_callback($init_reg,function($matches){
			return "";
		},$data,1);
		$data=$play_scode($code,$data);
		return $data;
	};
	
	$cb_open=function($matches)use($init_reg,$scode,$play_scode){
		$data=$play_scode($matches[0]);
		return $data;
	};
	
	$str=preg_replace_callback($complete_reg,$cb_open_close,$str);
	$str=preg_replace_callback($init_reg,$cb_open,$str);
	return $str;
    }
    function processAjax($action)
    {
        $user_ob=$this->load->loadUser();
        if(!$user_ob->isLoggedin() && isset( $this->action_callbacks['cf_ajax_nopriv_'.$action] ))
        {
            self::playCallbacks($this->action_callbacks['cf_ajax_nopriv_'.$action],array());
        }
        if($user_ob->isLoggedin() && isset($this->action_callbacks['cf_ajax_'.$action]))
        {
            self::playCallbacks($this->action_callbacks['cf_ajax_'.$action],array());
        }
    }
    function processApi($action)
    {
        if(isset($this->action_callbacks['cf_api_'.$action] ))
        {
            self::playCallbacks($this->action_callbacks['cf_api_'.$action],array());
        }
    }
    function processGetFunnels($type="",$limit=5)
    {
        $loader=$this->load;
        $funnel_ob=$loader->loadFunnel();
        if(is_numeric($limit) && $limit===-1)
        {
            $limit=$funnel_ob->getCountFunnelsInDB();
        }
        $funnel_data=$funnel_ob->getAllFunnelForView(0,$type,$limit);
        $rows=$funnel_data['rows'];
        $arr=array();
        while($r=$rows->fetch_assoc())
        {
            array_push($arr,$r);
        }
        return $arr;
    }
    function createMetaDataArg($data)
    {
        $accepted=array('funnel_id'=>0,'page_level'=>'0','page_type'=>'ab');
        if(is_array($data))
        {
            if(!isset($data['funnel_id']))
            {
                throw new Exception("Missing index `funnel_id` in first argument.");
            }
            foreach($data as $index=>$val)
            {
                if(!isset($accepted[$index]))
                {
                    throw new Exception("Unknown index \"".$index."\" in first argument");
                }
                $accepted[$index]=$val;
            }
            $data=$accepted;
        }
        else
        {
            $data=array('funnel_id'=>$data,'page_level'=>'0','page_type'=>'ab');
        }
        return $data;
    }
    function addFunnelMeta($data,$key,$meta_value,$doo="add")
    {
        $mysqli=$this->mysqli;
        $dbpref=$this->dbpref;
        $table=$dbpref.'qfnl_funnel_meta';
        $key=$mysqli->real_escape_string($key);
        $meta_value=$mysqli->real_escape_string($meta_value);
        
        $qry_str="";
        $count=0;
        $data=self::createMetaDataArg($data);

        if($doo=='update')
        {
            foreach($data as $index=>$val)
            {
                ++$count;
                $val=$mysqli->real_escape_string($val);
                $qry_str .="`".$index."`='".$val."'";
                if($count<=2)
                {
                    $qry_str.=" and ";
                }
            }
        $mysqli->query("update `".$table."` set `value`='".$meta_value."' where ".$qry_str." and `key`='".$key."'");
        
        }
        else if($doo=='add')
        {
            if(self::getFunnelMeta($data,$key))
            {
                return false;
            }

            $attrs="(";
            $vals="(";
            foreach($data as $index=>$val)
            {
                $attrs .="`".$index."`,";
                $vals .="'".$val."',";
            }
    
            $attrs .="`key`,`value`)";
            $vals .="'".$key."','".$meta_value."')";

            $qry=$mysqli->query("insert into `".$table."` ".$attrs." values ".$vals."");
        }
        else
        {
            throw new Exception('Unknown command');
        }
        return true;
    }
    function getFunnelMeta($data,$key)
    {
        $mysqli=$this->mysqli;
        $dbpref=$this->dbpref;
        $table=$dbpref.'qfnl_funnel_meta';
        $key=$mysqli->real_escape_string($key);
        
        $qry_str="";
        $count=0;
        $data=self::createMetaDataArg($data);

        foreach($data as $index=>$val)
        {
            ++$count;
            $val=$mysqli->real_escape_string($val);
            $qry_str .="`".$index."`='".$val."'";
            if($count<=2)
            {
                $qry_str.=" and ";
            }
        }

        $qry=$mysqli->query("select `value` from `".$table."` where ".$qry_str." and `key`='".$key."'");
        
        if($qry->num_rows)
        {
            $r=$qry->fetch_object();
            return $r->value;
        }
        else
        {
            return false;
        }
    }
    function deleteFunnelMeta($data,$key)
    {
        $mysqli=$this->mysqli;
        $dbpref=$this->dbpref;
        $table=$dbpref.'qfnl_funnel_meta';
        $key=$mysqli->real_escape_string($key);
        
        $qry_str="";
        $count=0;
        $data=self::createMetaDataArg($data,$key);


        foreach($data as $index=>$val)
        {
            ++$count;
            $val=$mysqli->real_escape_string($val);
            $qry_str .="`".$index."`='".$val."'";
            if($count<=2)
            {
                $qry_str.=" and ";
            }
        }
        $qry=$mysqli->query("delete from `".$table."` where ".$qry_str." and `key`='".$key."'");
        return true;
    }
    function processAutoResponders($id,$name,$email,$data=array())
    {
        if(!is_array($data))
        {
            $data=array();
        }
        $data['name']=$name;
        $data['email']=$email;

        if(isset($this->autores_callbacks[$id]))
        {
            $ap=$this->autores_callbacks[$id];
            self::playCallbacks(array(array($ap['cb'],$ap['args'])),$data);
        }
        
    }
    function processPaymentMethod($id,$product_detail,$description)
    {
        if(isset( $this->payment_methods_callbacks[$id] ))
        {
            $loader=$this->load;
            $sell_ob=$loader->loadSell();
            $method=$this->payment_methods_callbacks[$id];

            if(is_array($product_detail))
            {
                $product_detail['description']=$description;
            }
            $cb=$method['cb'];
            $execution_url=rtrim(get_option('install_url'),'/');
            $execution_url .="/index.php?page=do_payment_execute";
            if(is_array($cb))
            {
                $func=$cb[1];
                $res= $cb[0]->$func($method['credentials'],$product_detail,$execution_url,$method['args']);
            }
            else
            {
                $res= $cb($method['credentials'],$product_detail,$execution_url,$method['args']);
            }
            return ($res)? $res:0; 
        }
        else
        {return 0;}
    }
    function triggerSales($data,$status)
    {
        $payment_setup=array();
        if(isset($_SESSION['order_form_data'.get_option('site_token')]))
        {
            $payment_setup=$_SESSION['order_form_data'.get_option('site_token')];
        }

       if(isset($payment_setup['membership']))
       {
            $payment_setup['membership_registration_pages']=explode(',',trim($payment_setup['membership'],','));

            unset($payment_setup['membership']);
       }

       if(isset($payment_setup['optional_products']))
       {
            $payment_setup['other_products']=$payment_setup['optional_products'];
            unset($payment_setup['optional_products']);
       }

       if(isset($payment_setup['lists']))
       {
            $payment_setup['lists']=explode('@',trim($payment_setup['lists'],'@'));
       }

        $arr=array(
            'success'=>($status)? true:false,
            'payment_data'=>$data,
            'payment_setup'=>$payment_setup
        );
        $cbs=$this->action_callbacks['cf_sale'];
        self::playCallbacks($cbs,$arr);
    }
    function processFunnelCreateDelete($funnel_id,$create=true){
        $cbs=($create)? ($this->action_callbacks['cf_funnel_create']):($this->action_callbacks['cf_funnel_delete']);
        self::playCallbacks($cbs,$funnel_id);
    }
    function processPageCreateDelete($arr,$create=true)
    {
        $cbs=($create)? ($this->action_callbacks['cf_funnel_page_create']):($this->action_callbacks['cf_funnel_page_delete']);
        self::playCallbacks($cbs,$arr);
    }
    function processPageLevelChange($funnel_id,$changes)
    {
        $cbs=$this->action_callbacks['cf_funnel_page_level_change'];
        $arr=array('funnel_id'=>$funnel_id,'changes'=>$changes);
        self::playCallbacks($cbs,$arr);
    }
    function processPageSetupChange($funnel_id,$level)
    {
        $cbs=$this->action_callbacks['cf_funnel_page_setup_change'];
        $arr=array(
            'funnel_id'=>$funnel_id,
            'level'=>$level
        );
        self::playCallbacks($cbs,$arr);
    }
    function processPageContentChange($funnel_id,$page_id,$level,$ab_type)
    {
        $cbs=$this->action_callbacks['cf_funnel_page_content_change'];
        $arr=array(
            'funnel_id'=>$funnel_id,
            'page_id'=>$page_id,
            'level'=>$level,
            'ab_type'=>$ab_type
        );
        self::playCallbacks($cbs,$arr);
    }
    function processGetSales($data)
    {   
        $mysqli=$this->mysqli;
        $loader=$this->load;

        $rplc_arr=array(
            'id'=>'id',
            'payment_id'=>'payment_id',
            'product_id'=>'productid',
            'payment_method_id'=>'paymentmethod',
            'membership_id'=>'membership',
            'funnel_id'=>'funnelid',
            'payer_email'=>'purchase_email'
        );

        $rplc_arr_original=array();
        foreach($rplc_arr as $index=>$val)
        {
            $rplc_arr_original[$val]=$index;
        }

        if(!is_array($data) && $data!==(-1))
        {
            $data=array('payment_id'=>$data);
        }
        elseif(is_array($data) && count($data)>0)
        {
            $new_data=array();
            foreach($data as $index=>$val)
            {
                if(isset($rplc_arr[$index]))
                {
                    $new_data[$rplc_arr[$index]]=$val;
                }
                else
                {
                    throw new Exception("Unable to recognize index `".$index."`");
                }
            }
            $data=$new_data;
        }
        else
        {
            $data=array();
        }

        $qry_str_arr=array();
        $qry_str="";
        if(count($data)>0)
        {
            foreach($data as $index=>$val)
            {
                $val=$mysqli->real_escape_string($val);
                array_push($qry_str_arr,"`".$index."`='".$val."'");
            }
            $qry_str =" where ".implode(' and ',$qry_str_arr); 
        }
        
        $sell_ob=$loader->loadSell();
        return $sell_ob->getSale('',$qry_str,$rplc_arr_original);
    }
    function processGetMembership($data)
    {
        $mysqli=$this->mysqli;
        $loader=$this->load;
        $membership_ob=$loader->loadMember();
        $qry_str_arr=array();
        $qry_str="";

        $allowed_fields=array('id', 'funnel_id', 'email', 'valid_only','verified_only','verify_code');

        if(is_array($data) && count($data)>0)
        {
            foreach($data as $index=>$val)
            {
                if(!in_array($index,$allowed_fields))
                {
                    throw new Exception("Unknown field `".$index."`");
                }
                $val=$mysqli->real_escape_string($val);
                if($index=='funnel_id')
                {
                    $index='funnelid';
                }
                elseif($index=='verified_only')
                {
                    $index='verified';
                    $val=($val)? 1:0;
                }
                elseif($index=='valid_only')
                {
                    $index='valid';
                    $val=($val)? 1:0;
                }
                elseif($index=='verify_code')
                {
                    $index='verifycode';
                }
                array_push($qry_str_arr,"`".$index."`='".$val."'");
            }
        }
        elseif(is_numeric($data) && $data>0)
        {
            array_push($qry_str_arr,"`id`='".$data."'");
        }
        elseif(filter_var($data,FILTER_VALIDATE_EMAIL))
        {
            array_push($qry_str_arr,"`email`='".$data."'");
        }
        if(count($qry_str_arr)>0)
        {
            $qry_str=" where ";
            $qry_str .=implode(' and ',$qry_str_arr);
        }

        return $membership_ob->getMemberDetailForPlugins($qry_str);
    }
    function processMembership($data,$doo='add')
    {
        $cbs=array();
        if($doo=='add')
        {
            $cbs=$this->action_callbacks['cf_member_create'];
        }
        elseif($doo=='update')
        {
            $cbs=$this->action_callbacks['cf_member_update'];
        }
        elseif($doo=='delete')
        {
            $cbs=$this->action_callbacks['cf_member_delete'];
        }
        else
        {
            return false;
        }
        self::playCallbacks($cbs,$data);
    }
    function processProduct($data,$doo='add')
    {
        $cbs=array();
        if($doo=='add')
        {
            $cbs=$this->action_callbacks['cf_product_create'];
        }
        elseif($doo=='update')
        {
            $cbs=$this->action_callbacks['cf_product_update'];
        }
        elseif($doo=='delete')
        {
            $cbs=$this->action_callbacks['cf_product_delete'];
        }
        self::playCallBacks($cbs,$data);
    }
    function processGetProducts($data)
    {
        $loader=$this->load;
        $sell_ob=$loader->loadSell();
        $qry_str_arr=array();
        $qry_str='';
        $accept_arr=array('id','product_id');
        if(is_array($data))
        {
            foreach($data as $index=>$val)
            {
                if(!in_array($index,$accept_arr))
                {
                    throw new Exception("Unknown field `".$index."`");
                }
                if($index=='product_id')
                {
                    $index='productid';
                }
                array_push($qry_str_arr,"`".$index."`='".$val."'");
            }
        }
        elseif(is_numeric($data) && $data>=0)
        {
            array_push($qry_str_arr,"`id`='".$data."'");
        }
        else if($data !==(-1))
        {
            return array();
        }
        if(count($qry_str_arr)>0)
        {
            $qry_str=" where ".implode(' and ',$qry_str_arr);
        }
        return $sell_ob->pluginGetProducts($qry_str);
    }
    function processGetSMTPs($data=-1)
    {
        $loader=$this->load;
        $smtp_ob=$loader->loadSMTP();
        $qry_str="";
        if(($data !='-1') && is_numeric($data))
        {
            $qry_str=" where `id`=".$data;
        }
        return $smtp_ob->pluginGetSMTPs($qry_str);
    }
    function processGetLists($id,$show_subscribers=false)
    {
        $loader=$this->load;
        $qry_str="";
        $lists_ob=$loader->createlist();
        $lists=false;
        if($id===(-1))
        {
            $lists=$lists_ob->getList($id,1);
        }
        else
        {
            $lists=$lists_ob->getList($id);
        }
        $arr=array();
        $getLeadsFromList=function($id)use(&$lists_ob){
            $subs=$lists_ob->getLeadsFromLists($id);
            $arr=array();
            if($subs)
            {
                while($r=$subs->fetch_assoc())
                {
                    $r['list_id']=$r['listid'];
                    unset($r['listid']);
                    $r['additional_data']=array();
                    $r['exf']=json_decode($r['exf']);
                    if(is_object($r['exf'])){
                        $r['additional_data']=(array)$r['exf'];
                    }
                    unset($r['exf']);
                    $r['ip']=$r['ipaddr'];
                    unset($r['ipaddr']);

                    array_push($arr,$r);
                }
            }
            return $arr;
        };
        if($lists)
        {   
            if($id>=0)
            {
                $r=(array)$lists;
                if($show_subscribers)
                {
                    $r['subscribers']=$getLeadsFromList($id);
                }
                array_push($arr,$r);
            }
            else
            {
                while($r=$lists->fetch_assoc())
                {
                    if($show_subscribers)
                    {
                        $r['subscribers']=$getLeadsFromList($r['id']);
                    }
                    array_push($arr,$r);
                }
            }
        }
        return $arr;
    }
    function addUpDelSubscriberToList($id,$data=array(),$doo='add'){
        //$doo should be add, update, delete
        $loader=$this->load;
        $lists_ob=$loader->createlist();
        $valid_fields=array('name','email','additional_data');

        if(in_array($doo,array('add','update')))
        {
            if(!is_array($data))
            {
                throw new Exception("Second parameter should be an array");
            }
            $in_arr=array(
                'name'=>'',
                'email'=>'',
                'additional_data'=>array(),
                'process_sequence'=>0
            );
            $up_arr=array();
            foreach($data as $index=>$val)
            {
                if(!in_array($index,$valid_fields))
                {
                    throw new Exception("Unknown index '".$index."'");
                }
                if($index=='email' && !filter_var($val,FILTER_VALIDATE_EMAIL))
                {
                    return false;
                }
                elseif($index=='additional_data' && !is_array($val))
                {
                    $in_arr['additional_data']=array();
                }
                if($doo=='add')
                {$in_arr[$index]=$val;}
                else
                {
                    if($index=='process_sequence'){continue;}
                    $up_arr[$index]=$val;
                }
            }
            //addToList($listid,$name,$email,$extra=array(),$multisequence=0)
            if($doo=='add')
            {
                $in=$lists_ob->addToList($id,$in_arr['name'],$in_arr['email'],$in_arr['additional_data'],0,$in_arr['process_sequence']);
                return $in;
            }
            else
            {
                //$lists_ob=$loader->createlist();
                if(isset($up_arr['additional_data']))
                {
                    $up_arr['exf']=$up_arr['additional_data'];
                    unset($up_arr['additional_data']);
                }
                $up=$lists_ob->pluginUpdateListUser($id,$up_arr);
                return $up;
            }
        }
        elseif($doo='delete')
        {
            return $lists_ob->pluginDeleteSubscriber($id);
        }
        else
        {return false;}
    }
    function processGetSubscriber($id,$email=false)
    {
        $loader=$this->load;
        $lists_ob=$loader->createlist();
        return $lists_ob->pluginGetSubscriber($id,$email);
    }
    function processTheEmailContent($content)
    {
        //the_email_content
        $cbs=$this->filter_callbacks['the_email_content'];
        return self::playCallbacksForFilter($cbs,$content,array());
    }
    function processTheEmailSubject($content)
    {
        //the_email_subject
        $cbs=$this->filter_callbacks['the_email_subject'];
        return self::playCallbacksForFilter($cbs,$content,array());
    }
    function processGetUsers($arr=array()){
        $mysqli=$this->mysqli;
        $loader=$this->load;
        $permission_arr=array('id','email');
        $qry_args=array();
        $qry_str="";
        foreach($arr as $index=>$val)
        {
            if(!in_array($index,$permission_arr))
            {
                throw new Exception('Unknown index '.$index);
            }
            $val=$mysqli->real_escape_string($val);
            $data=($index=='id')? "`id`=".$val."":"`".$index."`='".$val."'";
            array_push($qry_args,$data);
        }
        $user_ob=$loader->loadUser();
        if(count($qry_args)>0)
        {
            $qry_str=" where ";
            $qry_str .=implode('and', $qry_args);
        }
        return $user_ob->pluginGetAllUsers($qry_str);
    }
}
?>