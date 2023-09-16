<?php
if( !defined("CF_GOOGLE_RECAPTCHA_PLUGIN_DIR_PATH")  ) {
	define( "CF_GOOGLE_RECAPTCHA_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}

if( !defined("CF_GOOGLE_RECAPTCHA_PLUGIN_URL") ) {
    define("CF_GOOGLE_RECAPTCHA_PLUGIN_URL", plugin_dir_url( __FILE__ ));
}

if(!class_exists('Cf_google_recaptcha_base'))
{
    require_once('controller/controller.php');
    class Cf_google_recaptcha_base extends CFrecaptcha_controller
    {
       
        var $config=false;
        var $method='google_recaptcha';

        function __construct()
        {
            self::getConfig();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();
            self::addShortCode();
            self::registerAjaxRequest();
            self::doInstall();


            add_action("cf_head",[$this,"loadUserScript"]);
            add_action("cf_footer",[$this,"loadUserfooterScript"]);

         
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png',__FILE__);
                add_menu_page('Google Recaptcha','Google Recaptcha','cf_setups_'.$this->method,array($this,'createMenu'),$logo_url,'All Setups');
                add_submenu_page('cf_setups_'.$this->method,'Manage Setup(Google Recaptcha)','Create New','cf_setting_'.$this->method,function(){
                    require_once('view/edit_setup.php');
                });
            });
        }
        function createMenu(){
            require_once('view/settings.php');
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
        function includeHeaderScripts()
        {
            //header scripts
            if(isset($_GET['page']) && in_array($_GET['page'],array('cf_setups_'.$this->method, 'cf_setting_'.$this->method)))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cf_setting_'.$this->method)
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/script.js?v='.$version,__FILE__)."'></script>";
                        echo "<script src='".CF_GOOGLE_RECAPTCHA_PLUGIN_URL."/assets/js/sweetalert.min.js?v=".$version."'></script>";

                    }
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
           


                });
            }
        }
        function loadUserScript()
        {
            echo "<script src='".CF_GOOGLE_RECAPTCHA_PLUGIN_URL."/assets/js/api.js' async defer></script>";

        }
        function loadUserfooterScript()
        {
            echo "<script src='".CF_GOOGLE_RECAPTCHA_PLUGIN_URL."/assets/js/user.js'></script>";

        }
        function doInstall(){
			register_activation_hook(function(){
				require_once('controller/install.php');
				cfrecaptchaDoInstall();
			});
		}
        function addShortCode()
        {
            add_shortcode( "cf_google_recaptcha_v2", function($params){
				if(isset($params['id'])) {
                    $v=0;
				    if(isset($this->config->version)) {
				    	$v=$this->config->version;
				    }

				    $id = $params['id'];
                //    echo $id;
				    ob_start();
				    $forms_ob = $this -> load('process_controller');
				    $forms_ob -> getFormUI($id,$v);
				    $data = ob_get_clean();
				    return $data;
				}
			});
            add_shortcode( "cf_google_recaptcha_v3", function($params){
				if(isset($params['id'])) {
                    $v=0;
				    if(isset($this->config->version)) {
				    	$v=$this->config->version;
				    }

				    $id = $params['id'];
                    // echo $id;
				    ob_start();
				    $forms_ob = $this -> load('process_controller');
				    $forms_ob -> getFormUIv3($id,$v);
				    $data = ob_get_clean();
				    return $data;
				}
			});
        }
        function registerAjaxRequest(){
            add_action('cf_ajax_cf_google_recaptcha_save',function(){

                if( $_POST['google_recaptcha_version'] == "" )
                {
                    echo json_encode(array("status"=>0, "message"=>"Please provide data for version."));
                    die();
                }
                if( $_POST['google_recaptcha_title'] == "" )
                {
                    echo json_encode(array("status"=>0, "message"=>"Please provide data for Title."));
                    die();
                }
                elseif($_POST['google_recaptcha_site_key'] == ""){
                    echo json_encode(array("status"=>0, "message"=>"Please provide data for Site Key."));
                    die();
                }
                elseif($_POST['google_recaptcha_secret_key'] == ""){
                    echo json_encode(array("status"=>0, "message"=>"Please provide data for Secret Key."));
                    die();
                }
               else{   
                    $load = $this->load("form_controller");
                    $load->doSaveUpdateSetup($_REQUEST);
                }
            });
        }

    }
    new Cf_google_recaptcha_base();
}
?>