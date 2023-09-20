<?php
namespace CFPBL_pabbly_addon\pabbly;
use CFPBL_pabbly_addon\pabbly as CFPBL_pabbly;
if(!defined('CF_PLUGIN_FOR_PABBLY_INIT')) {
    define('CF_PLUGIN_FOR_PABBLY_INIT', '1');
}
if( !defined("EXIT_FORM_CFPABBLY_PLUGIN_URL") ) {
    define("EXIT_FORM_CFPABBLY_PLUGIN_URL", plugin_dir_url( __FILE__ ));
}
if(!class_exists('Cfpabblyaddon_index'))
{
    require_once('app/process.php');
    class Cfpabblyaddon_index extends CFPBL_pabbly\Cfpbl_pabbly
    {
        var $pref='cfpab';
        var $config=false;

        function __construct()
        {
            self::getConfig();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();
            
            parent::__construct(array('pref'=>$this->pref));
            register_activation_hook(function() {
                get_option('pabbly_post_rows')!==null ? update_option('pabbly_post_rows', 0) : add_option('pabbly_post_rows', 0);
                
                if(get_option('pabbly_webhook_data')!==null) {
                    add_option('pabbly_webhook_data', '');
                }
            });

            add_action( 'cf_ajax_cfpabblyajax', function(){
                $this->save_requested_data( $_REQUEST );
            });

            self::makeApi();
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png', __FILE__);
                add_menu_page('Pabbly','Pabbly','cfpabbly_setups',array($this,'createMenu'),$logo_url,'All Setup');
              
            });
        }
        function createMenu(){
            require_once('app/edit_setup.php');
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfpabbly_setups')))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }
                  
                    echo "<link rel='stylesheet' href='".EXIT_FORM_CFPABBLY_PLUGIN_URL."assets/css/style.css?v=".get_option('qfnl_current_version')."'>";
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='".EXIT_FORM_CFPABBLY_PLUGIN_URL."assets/js/script.js?v=".get_option('qfnl_current_version')."'></script>";
                });
            }
        }

        function makeApi()
        {
            add_action('cf_api_pabbly_api_callback' ,function(){
                if(isset($_POST['send_to_webhook']) && $_POST['send_to_webhook']===get_option('site_token') && get_option('pabbly_post_rows')) {
                    $pabbly_all_data = json_decode(stripcslashes(get_option('pabbly_webhook_data')));
                    if(count($pabbly_all_data)>0) {
                        foreach($pabbly_all_data as $key=>$value) {
                            if(isset($value->status) && $value->status) {
                                $this->sendDetailsToPabbly($value->url, count($pabbly_all_data)-$key);
                            }
                        }
                    }
                }
            });
        }
    }
    new \CFPBL_pabbly_addon\pabbly\Cfpabblyaddon_index();
}
