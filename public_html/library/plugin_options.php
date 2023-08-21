<?php
$GLOBALS['loader']=$main_load;
$GLOBALS['plugin_loader']=$GLOBALS['loader']->loadPlugins();
$GLOBALS['inside_administration_page']=false;

function register_activation_hook($cb,$args=array())
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    array_push($plugin_loader->activation_callbacks,array($cb,$args));
}

function register_deactivation_hook($cb,$args=array())
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    array_push($plugin_loader->deactivation_callbacks,array($cb,$args));
}

function register_autoresponder($id,$name,$cb,$arr=array())
{
    if(is_numeric($id)||strpos($id," ")!==false)
    {
        throw new Exception("Custom autoresponder id should be non-numeric and should not contain any space");
    }
    $plugin_loader=$GLOBALS['plugin_loader'];
    $plugin_loader->autores_callbacks[$id]=array('name'=>$name,'cb'=>$cb,'args'=>$arr);
}

function register_payment_method($id,$detail,$cb,$arr=array(),$not_ipn=true)
{
    /*detail should contain 'title' && 'method'*/
    if(!is_array($detail) || !isset($detail['title']) || !isset($detail['method']))
    {
        throw new Exception("Second argument for the function `register_payment_method` should be an array which will contain two mandatory fields `title` and `method` and also you can add a Tax with a field `tax`(optional) amount that you want to apply as a percentage amount on total purchase");
    }
    elseif(!isset($detail['tax']))
    {
        $detail['tax']=0;
    }

    if(is_numeric($id)||strpos($id," ")!==false)
    {
        throw new Exception("Custom payment method id should be non-numeric and should not contain any space");
    }
    if(!$not_ipn)
    {
        $detail['method']=$detail['method'].'_ipn';
    }
    $plugin_loader=$GLOBALS['plugin_loader'];
    $plugin_loader->payment_methods_callbacks[$id]=array('credentials'=>$detail,'is_ipn'=>(!$not_ipn),'cb'=>$cb,'args'=>$arr);
}
function register_custom_lang($langs=array(), $cb=false)
{
    global $cf_available_languages;
    $plugin_loader= $GLOBALS['plugin_loader'];
    if(is_array($langs))
    {
        foreach($langs as $lang=>$data)
        {
            if(isset($data['file']) && is_string($data['file']))
            {
                if(!isset($data['name']) || !is_string($data['name']))
                {
                    $data['name']= $lang;
                }
                if(!isset($plugin_loader->langs[$lang]) ||  !is_array($plugin_loader->langs[$lang]))
                {
                    $plugin_loader->langs[$lang]= array();
                }
                array_push($plugin_loader->langs[$lang], array('name'=>$data['name'], 'file'=>$data['file'], 'cb'=> $cb));
                if(is_array($cf_available_languages) && !isset($cf_available_languages[$lang]))
                {
                    $cf_available_languages[$lang]= $data['name'];
                }
            }
            else
            {
                $warn= "There the language file missing for language name `".$lang."`";
                trigger_error($warn, E_USER_WARNING);
            }
        }
    }
    else
    {
        throw new Error("The first parameter for register_custom_lang will only be an associative array");
    }
}
function generate_custom_lang($cb= false)
{
    $index= get_option('app_language');
    require("lang.php");
    registerTranslation($index);
    if(is_callable($cb))
    {$cb(array('lang'=> $index));}
}
function get_requested_order()
{
    if(isset($_SESSION['order_form_data'.get_option('site_token')]) && $_SESSION['order_form_data'.get_option('site_token')])
    {
        $data= $_SESSION['order_form_data'.get_option('site_token')];
        if(isset($data['lists']))
        {
            $data['lists']= trim($data['lists'],'@');
            if(strlen($data['lists'])>0)
            {
                $data['lists']=explode('@',$data['lists']);
            }
            else
            {
                $data['lists']=array();
            }
        }

        if(isset($data['membership']))
        {
            $data['membership']=trim($data['membership'], ',');
            if(strlen($data['membership'])>0)
            {
                $data['membership']=explode(',', $data['membership']);
            }
            else
            {
                $data['membership']=array(); 
            }
        }

        if(isset($data['data']) && is_array($data['data'])  && !isset($data['data']['name']))
        {
            $data['data']['name']='';
            if(isset($data['data']['firstname']))
            {
                $data['data']['name']=$data['data']['firstname'];
            }
            if(isset($data['data']['lastname']))
            {
                $data['data']['name'] .=" ".$data['data']['lastname'];
            }
            if(strlen(trim($data['data']['name']))<1)
            {
                unset($data['data']['name']);
            }
        }

        return $data;
    }
    else
    {return false;}
}

function add_action($name,$cb,$args=array())
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    if(!isset($plugin_loader->action_callbacks[$name]))
    {
        $plugin_loader->action_callbacks[$name]=array();
    }
    array_push($plugin_loader->action_callbacks[$name],array($cb,$args));
}
function do_action($name,$settings=array())
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    if(isset($plugin_loader->action_callbacks[$name]))
    {
        $plugin_loader->playCallbacks($plugin_loader->action_callbacks[$name],$settings);
    }
}
function add_filter($name,$cb,$args=array())
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    array_push($plugin_loader->filter_callbacks[$name],array($cb,$args));
}
function add_menu_page($page_title, $menu_title, $menu_slug, $cb, $icon_url='', $submenu= false)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    $arr=compact('page_title','menu_title','menu_slug','cb','icon_url','submenu');
    $plugin_loader->action_callbacks['admin_menues'][$menu_slug]=array($arr);
}
function add_submenu_page($parent_slug,$page_title,$menu_title,$menu_slug,$cb,$position=null)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    $arr=compact('parent_slug','page_title','menu_title','menu_slug','cb','position');
    if(isset($plugin_loader->action_callbacks['admin_menues'][$parent_slug]))
    {
        array_push($plugin_loader->action_callbacks['admin_menues'][$parent_slug],$arr);
        //'admin_submenues'
        $plugin_loader->action_callbacks['admin_submenues'][$menu_slug]=array($arr);
    }
}

function add_shortcode($scode,$cb,$args=array())
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    $plugin_loader->shortcodes[$scode]=array($cb,$args);
}

function do_shortcode($str,$scode=false){
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->doShortcode($scode,$str);
}
function cf_create_nonce($str)
{
    $load=$GLOBALS['loader'];
    $secure_ob=$load->secure();
    return $secure_ob->setToken($str);
}
function cf_verify_nonce($token,$str)
{
    $load=$GLOBALS['loader'];
    $secure_ob=$load->secure();
    $matched=$secure_ob->matchToken($token,$str);
    return ($matched)? true:false;
}
function is_admin()
{
    $load=$GLOBALS['loader'];
    $user_ob=$load->loadUser();
    if($user_ob->isLoggedin())
    {
        return $GLOBALS['inside_administration_page'];
    }
    else
    {
        return false;
    }
}
function current_user_can()
{
    $load=$GLOBALS['loader'];
    $user_ob=$load->loadUser();
    $site_token=get_option('site_token');
    if($user_ob->isLoggedin() && isset($_SESSION['permission_page_arr'.$site_token]))
    {
        return $_SESSION['permission_page_arr'.$site_token];
    }
    else
    {
        return false;
    }
}
//-----deal with app users
function get_user($id){
    //id or email
    //by default id or specify email or id in an array
    $arr=(is_array($id))? $id:array('id'=>$id);
    $data=get_users($arr);
    if(is_array($data) && count($data)>0)
    {
        return $data[0];
    }
    else
    {
        return false;
    }
}
function get_loggedin_user()
{
    $load=$GLOBALS['loader'];
    $user_ob=$load->loadUser();
    $site_token=get_option('site_token');
    if(isset($_SESSION['user'.$site_token]))
    {
        return get_user($_SESSION['user'.$site_token]);
    }
    else
    {
        return false;
    }
}
function get_users($arr=array())
{
    //$arr should contain `id` `email` or nothing  
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->processGetUsers($arr);
}
//------------------------
function do_session_start()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
function do_session_destroy()
{
    //do_session_start();
    session_destroy();
}
function has_session($name)
{
    $site_token=get_option('site_token');
    if(isset($_SESSION[$name.$site_token]))
    {
        return true;
    }
    else
    {
        return false;
    }
}
function get_session($name)
{
    $site_token=get_option('site_token');
    if(isset($_SESSION[$name.$site_token]))
    {
        return $_SESSION[$name.$site_token];
    }
    else
    {
        return null;
    }
}
function set_session($data,$val=null)
{
    $site_token=get_option('site_token');
    if(is_array($data))
    {
        foreach($data as $index=>$val)
        {
            if(!is_string($index))
            {
                throw new Exception("Session name should be a string");
            }
            $_SESSION[$index.$site_token]=$val;
        }
    }
    else if(is_string($data))
    {
        $_SESSION[$data.$site_token]=$val;
    }
    else
    {
        throw new Exception("Invalid type of arguments provided");
    }
}
function unset_session($name)
{
    if(is_array($name))
    {
        foreach($name as $index=>$val)
        {
            unset($name[$index]);
        }
    }
    else
    {
        $site_token=get_option('site_token');
        if(isset($_SESSION[$name.$site_token]))
        {
            unset($_SESSION[$name.$site_token]);
        }
    }
}
function has_cookie($name)
{
    $site_token=get_option('site_token');
    return (isset($_COOKIE[$name.$site_token]))? true:false; 
}
function get_cookie($name)
{
    $site_token=get_option('site_token');
    if(has_cookie($name))
    {
        return $_SESSION[$name.$site_token];
    }
    else
    {
        return null;
    }
}
function set_cookie($name,$val,$time,$path="/")
{
    $site_token=get_option('site_token');
    return setcookie($name.$site_token, $val, $time, $path);
}
function plugin_dir_path( $file )
{
    $path=dirname($file);
    $path=rtrim(str_replace("\\","/",$path),"/");
    $plugin_loader=$GLOBALS['plugin_loader'];
    $base_dir=$plugin_loader->base_dir;

    if(strpos($path, $base_dir) ===0)
    {
        return $path.'/';
    }
    else
    {
        return false;
    }
}
function plugin_dir_url($file)
{
    $path=dirname($file);
    $path=rtrim(str_replace("\\","/",$path),"/");
    $plugin_loader=$GLOBALS['plugin_loader'];
    $base_dir=$plugin_loader->base_dir;
    if(strpos($path,$base_dir)===0)
    {
        $path=trim(str_replace($base_dir,"",$path),'/');
        $url=rtrim(trim(get_option('install_url')),'/');
        return $url.'/'.$path."/";
    }
    else
    {
        return "";
    }
}
function plugins_url($str="",$path="")
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    $base_dir=$plugin_loader->base_dir;

    if(strlen(trim($path))<1)
    {
        $path=rtrim($base_dir,'/');
        $path .="/plugins/something.png";
    }

    $url=plugin_dir_url($path);
    $str=trim(trim($str),'/');
    return $url.$str;
}
function get_funnels($type="",$select="*")
{
    //add limit -1 to get all funnels
    $loader=$GLOBALS['loader'];
    $funnel_ob=$loader->loadFunnel();
    $all_funnels=$funnel_ob->getFunnel(-1,$select,$type);
    $funnels=array();
    if($all_funnels && $all_funnels->num_rows)
    {
        while($r=$all_funnels->fetch_assoc())
        {
            $r['url']=$r['baseurl'];
            unset($r['baseurl']);
            unset($r['flodername']);
            unset($r['pagecount']);
            unset($r['labelhtml']);
            unset($r['firstpage']);

            $r['valid_inputs']=explode(',',trim($r['validinputs']));
            unset($r['validinputs']);
            $r['url']=str_replace('@@qfnl_install_url@@',get_option('install_url'),$r['url']);
            $r['smtp']=$r['primarysmtp'];
            unset($r['primarysmtp']);
            array_push($funnels,$r);
        }
    }
    return $funnels;
}
function get_funnel($id,$select="`id`,`name`,`baseurl` as `url`,`type`,`validinputs` as `valid_inputs`,`primarysmtp` as `smtp`,`date_created`,`token`")
{
    $loader=$GLOBALS['loader'];
    $funnel_ob=$loader->loadFunnel();
    $data=$funnel_ob->getFunnel($id,$select);
    $data=(is_object($data))? ((array)$data):false;
    $data['url']=str_replace('@@qfnl_install_url@@',get_option('install_url'),$data['url']);
    $data['valid_inputs']=explode(',',trim($data['valid_inputs'],','));
    return $data;
}
function get_page_by_id($id)
{
    $loader=$GLOBALS['loader'];
    $funnel_ob=$loader->loadFunnel();
    $data=$funnel_ob->getPageBYId($id);

    if($data)
    {
        //$data['file_name']=$data['filename'];
        //unset($data['filename']);
        $funnel_ob->fixPageDetailName($data);
    }
    
    return $data;
}
function get_page($funnel_id,$level=1,$type='a')
{
    $loader=$GLOBALS['loader'];
    $funnel_ob=$loader->loadFunnel();
    $data= $funnel_ob->getPageFunnel($funnel_id,$type,$level,'funnelid');
    if(is_object($data))
    {
        $data=(array)$data;
        $funnel=$funnel_ob->getFunnel($data['funnelid']);
        if($funnel)
        {
        	$url=$funnel->baseurl;
			$url=str_replace('@@qfnl_install_dir@@',get_option('install_url'),$url);

			$data['funnel_id']=$data['funnelid'];
			unset($data['funnelid']);
            $data['url']=$url.'/'.$data['filename'];
            $funnel_ob->fixPageDetailName($data);
        }
    }
    return $data;
}
function get_funnel_pages($funnel_id)
{
    $loader=$GLOBALS['loader'];
    $funnel_ob=$loader->loadFunnel();
    return $funnel_ob->getPagesForFunnel($funnel_id);
}
function add_funnel_meta($data,$key,$meta_value)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->addFunnelMeta($data,$key,$meta_value,"add");
}
function get_funnel_meta($data,$key)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->getFunnelMeta($data,$key);
}
function delete_funnel_meta($data,$key)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->deleteFunnelMeta($data,$key);
}
function update_funnel_meta($data,$key,$meta_value)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->addFunnelMeta($data,$key,$meta_value,"update");
}
function get_sales($data)
{
   /*
   pass -1 to get all
   $data can be only payment_id as string or
   an array of specific fields
   product_id, payment_method_id, membership_id, funnel_id, payer_email, payment_id
   */

  $plugin_loader=$GLOBALS['plugin_loader'];
  return $plugin_loader->processGetSales($data);
}
function cf_mail($data)
{
    /*$arr=array(
        'mailer'=>$smtpid,
        'name'=>$name,
        'email'=>$email,
        'subject'=>$emailsubject,
        'body'=>$email_body,
        'unsubscription_message'=>$unsubscribemsg,
        'debug'=>$debug,
        'from_name'=>$ufromname,
        'from_email'=>$ufromemail,
        'reply_name'=>$ureplyname,
        'reply_email'=>$ureplyemail
    );*/
    $smtp_id=(isset($data['mailer']))? $data['mailer']:get_option('default_smtp');

    if(strtolower($smtp_id)=='api')
    {
        throw new Exception('Unknown SMTP');
    }

    $name=(isset($data['name']))? $data['name']:'';

    if(!isset($data['email']))
    {
        throw new Exception("Recipient email not found, field name should be `email`.");
    }

    $email=(isset($data['email']))? $data['email']:'';
    $subject=(isset($data['subject']))? $data['subject']:'';
    $body=(isset($data['body']))? $data['body']:'';
    $unsubscribemsg=(isset($data['unsubscription_message']))? $data['unsubscription_message']:'';
    $debug=(isset($data['debug']))? $data['debug']:0;
    if($debug)
    {
        $debug=1;
    }
    $ufromname=(isset($data['from_name']))? $data['from_name']:'';
    $ufromemail=(isset($data['from_email']))? $data['from_email']:'';

    $ureplyname=(isset($data['reply_name']))? $data['reply_name']:'';
    $ureplyemail=(isset($data['reply_email']))? $data['reply_email']:'';

    $loader=$GLOBALS['loader'];
    $sequence_ob=$loader->loadSequence();
    $stat=$sequence_ob->sendMail($smtp_id,$name,$email,$subject,$body,$unsubscribemsg,$debug,$ufromname,$ufromemail,$ureplyname,$ureplyemail);
    return ($stat)? true:false;
}
//Work with membership
function get_login_action_url($login_url, $action_url, $reset_on_register= false)
{
    $url= false;

    $action_url= base64_encode($action_url);
    $reg_reset= ($reset_on_register)? 1:0;

    if(filter_var($login_url, FILTER_VALIDATE_URL))
    {
        $url= $login_url;
    }
    else if(is_numeric($login_url))
    {
        $funnel_id= $login_url;
        $pages= get_funnel_pages($funnel_id);
        if(is_array($pages) && count($pages)>0)
        {
            foreach($pages as $index=>$page)
            {
                if($page['category']==='login')
                {
                    $url= $page['url'];
                    break;
                }
            }
        }
    }
    else if(is_array($login_url) && isset($login_url['funnel_id']) && isset($login_url['page_id']))
    {
        $funnel_id= (int) $login_url['funnel_id'];
        $page_id= (int) $login_url['page_id'];
        $page= get_page_by_id($page_id);

        if(isset($page['funnel_id']) && $funnel_id>0 && ($funnel_id===(int)$page['funnel_id']) && $page['category']==="login")
        {
            $url= $page['url'];
        }
    }

    if(is_string($url))
    {
        $url= str_replace('@@qfnl_install_url@@', get_option('install_url'), $url);
    }

    if(filter_var($url, FILTER_VALIDATE_URL))
    {
        $url .= (strpos($url, '?')===false)? '?':'&';
        $url .= "after_login_action=".$action_url;
        $url .= "&reset_on_register=".$reg_reset;
    }
    return $url;
}
function is_member_loggedin($funnel_id)
{
    //to get it in work pass membership funnel id
    $ssn=(isset($_SESSION['qfnl_membership_'.get_option('site_token').'_'.$funnel_id.'']))? true:false;
    return $ssn;
}
function get_current_member($funnel_id)
{
    if(isset($_SESSION['qfnl_membership_'.get_option('site_token').'_'.$funnel_id.'']))
    {
        $ssn=$_SESSION['qfnl_membership_'.get_option('site_token').'_'.$funnel_id.''];

       // print_r($ssn);

        if(isset($ssn['email']))
        {
            $data=get_members(
                array(
                    'funnel_id'=>$funnel_id,
                    'email'=>$ssn['email']
                )
            );
            if(is_array($data) && count($data)>0)
            {return $data[0];}
        }
    }
    return false;
}
function get_products_for_member($funnel_id,$member_email, $active_only=false)
{
    $loader=$GLOBALS['loader'];
    $membership_ob=$loader->loadMember();
    return $membership_ob->getProductAccessForaMember($funnel_id, $member_email, $active_only);
}
function get_member($id)
{
    //$id maybe member id or condition array
    $ask_for=(is_array($id))? $id:(array('id'=>$id));
    $data=get_members($ask_for);

    if(is_array($data) && count($data)>0)
    {
        return $data[0];
    }
    else
    {return false;}
}
function get_members($data=-1)
{
    /*
    -1 to get all
    id
    funnel_id
    email
    valid_only
    verified_only
    verify_code
    */
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->processGetMembership($data);
}
function get_product($id,$by='id')
{
    //$by==id|product_id
    $data=get_products(array($by=>$id));
    if(is_array($data) && count($data)>0)
    {
        return $data[0];
    }
    else
    {
        return false;
    }
}
function get_products($data=-1)
{
    /*
    -1 to get all
    `id`
    `product_id`
    */
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->processGetProducts($data);
}
function get_smtp($id)
{
    $data=get_smtps($id);
    if(is_array($data) && count($data)>0)
    {
        return $data[0];
    }
    return false;
}
function get_smtps($data=-1)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->processGetSMTPs($data);
}
function get_list($id,$show_subscribers=false)
{
    if($id>0)
    {
        $lists=get_lists($id, $show_subscribers);
        if(is_array($lists) && count($lists)>0)
        {
            return $lists[0];
        }
    }
    return false;
}
function get_lists($id=-1,$show_subscribers=false)
{
    //$id=-1 to get all
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->processGetLists($id, $show_subscribers);
}
function add_to_list($list_id,$data)
{
    //list_id
    /*
    array('name','email','additional_data','process_sequence')
    */
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->addUpDelSubscriberToList($list_id,$data,'add');
}
function update_list_user($subscriber_id,$data)
{
    //subscriber id
    //array('name','email','additional_data'
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->addUpDelSubscriberToList($subscriber_id,$data,'update');
}
function delete_list_user($subscriber_id)
{
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->addUpDelSubscriberToList($subscriber_id,array(),'delete');
}
function get_list_user($id,$email=false)
{
    //if only id $id=subscriber id
    //if $id & $email 
    $plugin_loader=$GLOBALS['plugin_loader'];
    return $plugin_loader->processGetSubscriber($id,$email);
}
function cf_media($show= true)
{
    $loader= $GLOBALS['loader'];
    $media= $loader->loadMediaBox();
    if($show)
    {
        echo $media;
    }
    else
    {
        return $media;
    }
}
/*====================================================================*/
//--------------All Functions Should be defined before this------------ 
/*=====================================================================*/
$active_plugins=$GLOBALS['plugin_loader']->getPlugins('active');
try
{
    foreach($active_plugins as $active_plugin)
    {
        ob_start();
        require_once($active_plugin->start);
        $active_plugins_output_data=ob_get_clean();
        $active_plugins_output_data=trim(trim(trim($active_plugins_output_data,'\n'),'\r\n'));
        echo $active_plugins_output_data;
    }
}catch(Exception $e){ echo $e->getMessage(); }
?>