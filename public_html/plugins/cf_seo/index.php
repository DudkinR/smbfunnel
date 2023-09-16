<?php

if( !defined("CFSEO_PLUGIN_DIR_PATH") )
{
    define( "CFSEO_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__) );
}

if( !defined( "CFSEO_PLUGIN_URL" ) )
{
    define("CFSEO_PLUGIN_URL", plugins_url( )."cf_seo");
}

/*
Seo class
*/
if( !class_exists( 'CFSEO_base' ) )
{
    require_once("controller/controller.php");
    class CFSEO_base extends CFSEO_base_controller
    {
        public $pref="cfseo_";
        public $config=false;
        
        public function __construct(){
            self::getConfig();
            self::createMenu();
            self::getAjax();
            self::doInstall();
            self::doUninstall();
            self::createXMLSitemap();
            
            if( isset($_GET['page']) && in_array($_GET['page'],array('cfseo_dashboard', 'cfseo_setting', 'cfseo_social')) ){
                add_action("admin_head",[$this,"addStyle"]);
                add_action("admin_head",[$this,"addScript"]);

            }

            // add seo data in user side
            add_action("cf_head",function(){
                
                $load=$this->load("setup");
                $load->loadAllSeoSetup( $this->config->version );
            });
        }

        // this function get plugin version version from config file
        public function getConfig()
        {
            if(!$this->config)
            {
                $file=CFSEO_PLUGIN_DIR_PATH;
                $fp=fopen($file."config.json","r");
                $data=json_decode(fread($fp,filesize($file.'config.json')));
                fclose($fp);
                if(isset($data->version))
                {
                    $this->config=$data;
                }
            }
        }

        public function addStyle(){
            $v=0;
            if(isset($this->config->version))
            {
                $v=$this->config->version;
            }
            echo "<link rel='stylesheet' href='".CFSEO_PLUGIN_URL."/assets/css/style.css'>";
        }

        public function addScript(){
            $v=0;
            if(isset($this->config->version))
            {
                $v=$this->config->version;
            }
            echo "<script src='".CFSEO_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>";
            echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";

            echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
        }

        // this function create menu and submenu
        public function createMenu()
        {
            add_action("admin_menu" , function(){
                add_menu_page("CF SEO", "CF SEO", $this->pref.'dashboard',function(){
                    require_once CFSEO_PLUGIN_DIR_PATH.'view/allsetup.php';

                },"");
                add_submenu_page($this->pref."dashboard","CF SEO","All Setup", $this->pref.'dashboard',function(){
                    require_once CFSEO_PLUGIN_DIR_PATH.'view/allsetup.php';

                });
                add_submenu_page($this->pref."dashboard","CF SEO setting","Setup setting", $this->pref.'setting',function(){
                    require_once CFSEO_PLUGIN_DIR_PATH.'view/setting.php';

                });
                add_submenu_page($this->pref."dashboard","CF SEO Social","Social Setup", $this->pref.'social',function(){
                    require_once CFSEO_PLUGIN_DIR_PATH.'view/social_setting.php';

                });
            });
        }

        // this function create database table
        public function doInstall()
        {
            register_activation_hook(function(){
                require_once('controller/install.php');
                cfSeoDoInstall($this->pref);
            });

        }
        public function doUninstall()
        {
            register_deactivation_hook(function(){
                delete_option("cfseo_page_ids");
            });
        }

        // this function handle ajax request
        public function getAjax(){
            add_action("cf_ajax_cfseo_save_ajax",function(){
                $load = $this->load("setup");
                $load->handleFormData($_REQUEST);
           });
            add_action("cf_ajax_cfseo_delete_ajax",function(){
                $load = $this->load("setup");
                $load->DeleteSeoData($_REQUEST);
           });
            add_action("cf_ajax_cfseo_save_webmaster_ajax",function(){
                $load = $this->load("setup");
                $load->handleWebmasterData($_REQUEST);
           });
            add_action("cf_ajax_cfseo_save_social_ajax",function(){
                $load = $this->load("setup");
                $load->handleSocialAccountData( $_REQUEST );
           });

        }

        //this function create xmlsite map

        function createXMLSitemap()
        {
        $fnlss = get_funnels();
        $fnls=array_reverse($fnlss);
        $create_site_map='<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $create_site_map.='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.PHP_EOL;   
            foreach ( $fnls as $f ) 
                {
                $pages=get_funnel_pages($f['id']);
                // $pages=array_reverse($pagess);
                foreach ($pages as $page) {

                    $page_url=str_ireplace("@@qfnl_install_url@@",get_option('install_url'),$page['url']);
                    $create_site_map.=PHP_EOL.'   <url>'.PHP_EOL;
                    $create_site_map.='     <loc>'.$page_url.'</loc>'.PHP_EOL;
                    $create_site_map.='     <lastmod>'.date('Y-m-d\TH:i:sP').'</lastmod>'.PHP_EOL;
                    $create_site_map.='   </url>'.PHP_EOL;
                }
            }
        $create_site_map.='</urlset>';
        $file_dir=plugin_dir_path(dirname(__FILE__,3)).'sitemap.xml';
        $file=fopen($file_dir,"w") or die("Sorry file not exist");
        fwrite($file,$create_site_map);
        fclose($file);
        }
    }
    new CFSEO_base();
}

?>