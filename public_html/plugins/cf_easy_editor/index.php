<?php 
    namespace CF_EASY_EDITOR;
    define('CF_EASY_EDITOR_BUILDER_TYPE', 'cf_easy_editor');
    require_once("libs/App.php");

    new class extends libs\App{
        protected $editor_name;
        protected $install_url;
        protected $app_name;
        protected $app_type;
        protected $plugin_path;
        protected $plugin_url;

        function __construct(){
            self::doConfig();
            parent::__construct();
            self::start();
        }
        private function doConfig(){
            global $app_variant;
            $this->editor_name= "Easy Editor";
            $this->app_type= $app_variant;
            $this->app_name= (($app_variant==='shopfunnels')? 'ShopFunnels':(($app_variant==='coursefunnels')? 'CourseFunnels':'CloudFunnels'));
            $this->install_url= get_option('install_url');
            $this->plugin_path= plugin_dir_path(__FILE__);
            $this->plugin_url= plugin_dir_url(__FILE__);
        }
        private function start(){
            parent::init();
        }
    };
?>