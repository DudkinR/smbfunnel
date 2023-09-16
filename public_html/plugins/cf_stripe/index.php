<?php
namespace CFPAY_peyment_addon\stripe;
use CFPAY_peyment_addon\stripe as CFPAY_stripe;
if(!class_exists('Cfpaymentaddon_index'))
{
    require_once('app/process.php');
    class Cfpaymentaddon_index extends CFPAY_stripe\Cfpay_processor
    {
        var $pref='cfpay';
        var $config=false;
        var $method='stripe';// do not change this ever// If you are changing the then just to subscriptionBasedPayment on Stripe_payment.php file and 
        // search payment_method and change this value. example $fielddata['payment_method'] = "stripe";
        var $funnel_id = false;
        var $membership = false;

        function __construct()
        {
            self::getConfig();
            self::doInstall();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();
            self::makeApiCall();
            self::addShortCode();
            add_action('cf_head',function($data){
                $this->membership=$data['category'];
                $this->funnel_id=$data['funnel_id'];
                $url = get_option("install_url")."/assets/fontawesome/css/all.css";
                echo '<link rel="stylesheet" href="'.$url.'">';
                
            });

            parent::__construct(array('pref'=>$this->pref));
            parent::registerAjaxRequest();
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/icon.png', __FILE__);
                add_menu_page('Stripe','Stripe','cfpay_setups_'.$this->method,array($this,'createMenu'),$logo_url,'All Setups');
                add_submenu_page('cfpay_setups_'.$this->method,'Manage Setup(Stripe)','Create New','cfpay_setting_'.$this->method,function(){
                    require_once('app/edit_setup.php');
                });
                if($_GET['page']=='cfpay_billing_btn_'.$this->method )
                {
                    add_submenu_page('cfpay_setups_'.$this->method,'Manage Button Billing','Manage Billing Button','cfpay_billing_btn_'.$this->method,function(){
                        require_once('app/billing.php');
                    });
                }
            });
        }
        function createMenu(){
            require_once('app/settings.php');
        }
        function getConfig()
        {
            if(!$this->config)
            {
                $file=plugin_dir_path(__FILE__);
                //echo $file;
                $fp=fopen($file.'config.json','r');
                $data=json_decode(fread($fp,filesize($file.'config.json')));
                fclose($fp);
                if(isset($data->version))
                {
                    $this->config=$data;
                }
            }
        }
        function doInstall(){
            register_activation_hook(function(){
                global $mysqli;
                global $dbpref;
                $qry_str="create table if not exists `".$dbpref."stripe_setting`(
                    `id` int NOT NULL AUTO_INCREMENT,
                    `stripe_id` bigint(20)  NOT NULL,
                    `setting` text NOT NULL,
                    `email_subject` text,
                    `email_content` text,
                    `card_html` text,
                    `return_url` text NOT NULL,
                    `email_days` int DEFAULT '10',
                    PRIMARY KEY (`id`),
                    KEY `".$dbpref."stripe_setting_ibfk_1` (`stripe_id`),
                    CONSTRAINT `".$dbpref."stripe_setting_ibfk_1` FOREIGN KEY (`stripe_id`) REFERENCES `".$dbpref."payment_methods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ";
                $mysqli->query($qry_str);
                $qry_str2="create table if not exists `".$dbpref."reminders`(
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `user_id` bigint(20) NOT NULL,
                    `subscription_id` varchar(255) NOT NULL,
                    `status` enum('0','1') NOT NULL,
                    `open_link` enum('0','1') NOT NULL,
                    `count` int(11) NOT NULL DEFAULT 0,
                    `sent_date` VARCHAR(255) DEFAULT 0,
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ";
                $mysqli->query($qry_str2);
                $qry_str1="create table if not exists `".$dbpref."subscriptions`(
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `status` enum('active','paused','canceled') DEFAULT 'active',
                    `payment_type` enum('payment','subscription') NOT NULL DEFAULT 'subscription',
                    `payment_method` varchar(255) NOT NULL,
                    `credentials` text NOT NULL,
                    `last_payment_id` text NOT NULL,
                    `last_payment_detail` text DEFAULT NULL,
                    `customer_id` text DEFAULT NULL,
                    `subscription_id` text DEFAULT NULL,
                    `subscription_detail` text DEFAULT NULL,
                    `expires_on` bigint(20) NOT NULL DEFAULT 0,
                    `activated_on` datetime NOT NULL DEFAULT current_timestamp(),
                    `added_on` datetime DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ";
                $mysqli->query($qry_str1);
            });
        }
        function includeHeaderScripts()
        {
            //header scripts
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfpay_setups_'.$this->method, 'cfpay_setting_'.$this->method, 'cfpay_billing_btn_'.$this->method )))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cfpay_setting_'.$this->method )
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/script.js?v='.$version,__FILE__)."'></script>";
                    }
                    if($_GET['page']=='cfpay_billing_btn_'.$this->method )
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/billing.js?v='.$version,__FILE__)."'></script>";
                        echo "<link type='text/css' href='".plugins_url('/assets/css/style.css?v='.$version,__FILE__)."'  rel='stylesheet' >";
                    }
                    if($_GET['page']=='cfpay_billing_btn_'.$this->method  )
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/billing.js?v='.$version,__FILE__)."'></script>";
                        echo "<link type='text/css' href='".plugins_url('/assets/css/style.css?v='.$version,__FILE__)."'  rel='stylesheet' >";
                    }
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script type='text/javascript' src='assets/js/jscolor.js'></script>
                    ";
                });
            }
        }
        function makeApiCall()
        {
            add_action("cf_api_cf_stripe_events" ,function(){
                $stripe_id = 0;
                $stripe_id =  isset($_GET['stripe_id'])?cf_enc($_GET['stripe_id'],"decrypt"):0;
                if( $stripe_id )
                {
                    $file=plugin_dir_path(__FILE__);
                    $file .="app/stripe/Stripe_payment.php";
                    require_once($file);
                    $ob=new \CFPay_Stripe_payment();
                    $ob->listenWebhookEvent($stripe_id,$this->method);
                }
                die();
            });
            
            add_action("cf_api_cf_stripe_load_reminder" , function(){
                $this->loadForReminders();
            });

            add_action("cf_api_s_reminder" , function(){
                $this->sendForReminders();
            });
        }
        function addShortCode()
        {
            add_shortcode("cfstripe_btn", function($args)
            {
                if($this->membership=="membership")
                {
                    if( isset( $args['id'] ) )
                    {
                        $id=$args['id'];
                        $button_srt='';
                        ob_start();
                        $file=plugin_dir_path(__FILE__);
                        $file .="app/billing_btn.php";
                        require $file;
                        $button_srt.=ob_get_clean();
                        return $button_srt;
                    }
                }
            });
        }
        
        function loadForReminders()
        {
            global $mysqli;
            global $dbpref;
            $file=plugin_dir_path(__FILE__);
            $file .="app/stripe/Stripe_payment.php";
            require_once($file);
            $ob=new \CFPay_Stripe_payment();
            $date= date('d-m-Y');
            $now= time();
            if( get_option('cfstripe_reminder_days') ){
                $dayss = (int)get_option('cfstripe_reminder_days');
                $days_10=  $now+ ($dayss*24*60*60);    
            }else{
                $days_10=  $now+ (10*24*60*60);  
            }
            
            $reminder=$dbpref."reminders";
            $all_sales=$dbpref."all_sales";
            $subscriptions=$dbpref."subscriptions";
            $table=$dbpref."stripe_setting";
            $sql = "SELECT s.`subscription_id`,s.`id`,s.`subscription_detail`,s.`expires_on`,sl.`purchase_email`,sl.`purchase_name`,sl.`productid`,sl.`payment_id`,sl.`paymentmethod`,sl.`membership` FROM  `$subscriptions` as s INNER JOIN `$all_sales` as sl ON s.`last_payment_id`=sl.`payment_id` WHERE s.`expires_on` >'($now-1)' AND s.`expires_on` <= '$days_10'";
            $run = $mysqli->query($sql);
            if( $run->num_rows > 0 )
            {
                
                while( $data =  $run->fetch_object() )
                {
                    $diff   = (((int) ( $data->expires_on ) )-(time()));
                    $diff   = floor( $diff/( 24*60*60 ) );
                    $prdata = get_product( $data->productid );
                    $pname  = $prdata['title'];
                    $subscriptions = json_decode($data->subscription_detail);
                    $paymentmethod = $data->paymentmethod;
                    $idss = explode("_",$paymentmethod);
                    $ids = array_reverse($idss);
                    $token['subs_id'] = $data->id;
                    $id = $ids[0];
                    $s_id = $data->subscription_id;
                    $email = $data->purchase_email;
                    $name = $data->purchase_name;
                    $rem = "SELECT `id`,`sent_date` FROM `$reminder` WHERE `subscription_id`='$s_id' LIMIT 1";
                    $run3 = $mysqli->query($rem);
                    if( $run3->num_rows == 0 )
                    {
                        $rdata=[
                            'user_id'=>$data->membership,
                            'subscription_id'=>$s_id,
                            'status'=>'0',
                            'open_link'=>'0',
                            'count'=>'0',
                            'sent_date'=>time(),
                        ];
                        $ob->insert( $reminder, $rdata );

                        $row = $mysqli->query("SELECT `email_subject`,`email_content`,`return_url` FROM `$table` WHERE `stripe_id`=$id LIMIT 1");
                        if($row->num_rows>0)
                        {
                            $edata = $row->fetch_object();
                            if(!empty($edata->return_url))
                            {
                                $token['return_url'] =$edata->return_url;

                            }else{
                                $token['return_url'] =get_option("install_url");
                            }
                            $token_e = cf_enc(json_encode($token));
                            $billing_url = get_option("install_url")."/index.php?page=callback_api&action=s_reminder&ssubscription_token=$token_e";
                            if( !empty($edata->email_subject) )
                            {
                                $email_subject = $edata->email_subject;
                            }else{
                                $email_subject = "Your membership of the {product_name} is going to be expired in {expire_date} day(s)";
                            }
                            if( !empty($edata->email_content) )
                            {
                                $email_body = $edata->email_content;
                            }else{
                                $email_body ='<p>Hi {name},</p><p>Your membership of the {product_name} is going to be expired in {expire_date} day(s).</p><p>Renew your <a title="Billing URL" href="{billing_url}" target="_blank" rel="noopener">billing URL</a> to enjoy uninterrupted service before it expires.</p><p>Please <a title="click here" href="{billing_url}" target="_blank" rel="noopener">click here</a>&nbsp; for the renewal</p><p>Cheers!</p>';
                            }
                            $email_data =  $this->replaceshortcode($name,$email,$pname,$billing_url,$diff,$email_subject,$email_body);
                            $edata=[
                                "",
                                "name"=>$name,
                                "email"=>$email,
                                "subject"=>$email_data['subject'],
                                "body"=>$email_data['body']
                            ];
                            
                            $check = cf_mail( $edata );
                            if($check)
                            {
                                $ob->update( $reminder, ['status'=>'1'],['user_id'=>$data->membership,'subscription_id'=>$s_id] );
                            }else{
                                echo "Unable to send email";
                            }
                        }
                    }else{
                        $remin_data = $run3->fetch_object();
                        // $sent_plugin_next_days = $remin_data->sent_date+(1*60);
                        $sent_plugin_next_days = $remin_data->sent_date+(1*24*60*60);
                        $current_time = time();
                        if( $sent_plugin_next_days <= $current_time )
                        {
                            $sql3="UPDATE `$reminder` SET `open_link`='1',`status`='1',`count`=`count`+1, `sent_date`='$current_time' WHERE `subscription_id`='$s_id'";
                            $mysqli->query( $sql3 );
                            $row = $mysqli->query("SELECT `email_subject`,`email_content`,`return_url` FROM `$table` WHERE `stripe_id`=$id LIMIT 1");
                            if($row->num_rows>0)
                            {
                                $edata = $row->fetch_object();
                                if(!empty($edata->return_url))
                                {
                                    $token['return_url'] =$edata->return_url;
    
                                }else{
                                    $token['return_url'] =get_option("install_url");
                                }
                                $token_e = cf_enc(json_encode($token));
                                $billing_url = get_option("install_url")."/index.php?page=callback_api&action=s_reminder&ssubscription_token=$token_e";
                                if( !empty($edata->email_subject) )
                                {
                                    $email_subject = $edata->email_subject;
                                }else{
                                    $email_subject = "Your membership of the {product_name} is going to be expired in {expire_date} day(s)";
                                }
                                if( !empty($edata->email_content) )
                                {
                                    $email_body = $edata->email_content;
                                }else{
                                    $email_body ='<p>Hi {name},</p><p>Your membership of the {product_name} is going to be expired in {expire_date} day(s).</p><p>Renew your <a title="Billing URL" href="{billing_url}" target="_blank" rel="noopener">billing URL</a> to enjoy uninterrupted service before it expires.</p><p>Please <a title="click here" href="{billing_url}" target="_blank" rel="noopener">click here</a>&nbsp; for the renewal</p><p>Cheers!</p>';
                                }
                                $email_data =  $this->replaceshortcode($name,$email,$pname,$billing_url,$diff,$email_subject,$email_body);
                                $edata=[
                                    "",
                                    "name"=>$name,
                                    "email"=>$email,
                                    "subject"=>$email_data['subject'],
                                    "body"=>$email_data['body']
                                ];
                                
                                $check = cf_mail( $edata );
                                if($check)
                                {
                                    $ob->update( $reminder, ['status'=>'1'],['user_id'=>$data->membership,'subscription_id'=>$s_id] );
                                }else{
                                    echo "Unable to send email";
                                }
                            }
                        }   
                    }
                }
            }
            die();
        } 
        function replaceshortcode( $name,$email,$pname,$billing_url,$expire_date,$subject,$content)
        {
            $sub="";
            $cont="";
            $shortcodes=['{name}','{email}','{verification_url}','{product_name}','{expire_date}','{billing_url}'];
            $shortcodes_v=['{name}'=>$name,'{email}'=>$email,'{product_name}'=>$pname,'{expire_date}'=>$expire_date,'{billing_url}'=>$billing_url];

            foreach($shortcodes as $shortcode)
            {
                $sub =$subject;
                if( stristr( $sub,$shortcode ) )
                {
                    $sub=str_ireplace( $shortcode, $shortcodes_v[$shortcode] , $sub );
                }
                $subject=$sub;
                $cont =$content;
                if( stristr( $cont,$shortcode ) )
                {
                    $cont=str_ireplace( $shortcode, $shortcodes_v[$shortcode] , $cont );
                }
                $content=$cont;
            }
            return [ 'subject'=>$sub,'body'=>$content ];
        }
        function sendForReminders()
        {
            $token =  isset($_GET['ssubscription_token'])?cf_enc($_GET['ssubscription_token'],"decrypt"):0;
            if( $token )
            {
                global $mysqli;
                global $dbpref;
                $file=plugin_dir_path(__FILE__);
                $file .="app/stripe/Stripe_payment.php";
                require_once($file);
                $ob=new \CFPay_Stripe_payment();

                $reminder=$dbpref."reminders";
                $subscriptions=$dbpref."subscriptions";
                $to = json_decode($token);
                $subs_id = $mysqli->real_escape_string( $to->subs_id );
                $return_url = $mysqli->real_escape_string( $to->return_url );

                $sql = "SELECT `last_payment_id`,`credentials`,`subscription_id` FROM `$subscriptions` WHERE `id`=$subs_id";
                $run1 = $mysqli->query($sql);
                if( $run1->num_rows > 0 )
                {
                    $data = $run1->fetch_object();
                    $credentials = json_decode( $data->credentials );
                    $subscription_id = $data->subscription_id;
                    $session_id = $data->last_payment_id;
                    $rem = "SELECT `id` FROM `$reminder` WHERE `subscription_id`='$subscription_id'";
                    $run3 = $mysqli->query($rem);
                    if( $run3->num_rows > 0 )
                    {
                        
                        $sql3="UPDATE `$reminder` SET `open_link`='1',`status`='1',`count`=`count`+1 WHERE `subscription_id`='$subscription_id'";
                        $mysqli->query( $sql3 );
                    }
                    $ob->oepnCustomerPortal( $credentials, $session_id, $return_url );
                    die();
                }

            }

        }
    }
    new \CFPAY_peyment_addon\stripe\Cfpaymentaddon_index();
}
?>
