<?php
namespace CFZAP_zapier_addon\zapier;
use CFZAP_zapier_addon\zapier as CFZAP_zapier;
define('CF_PLUGIN_FOR_ZAPIER_INIT', '1');
if(!class_exists('Cfzapieraddon_index'))
{
    require_once('app/process.php');
    class Cfzapieraddon_index extends CFZAP_zapier\Cfzap_processor
    {
        var $pref='cfzap';
        var $config=false;

        function __construct()
        {
            self::getConfig();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();
            
            parent::__construct(array('pref'=>$this->pref));
            $this->makeApi();
         
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png', __FILE__);
                add_menu_page('Zapier','Zapier','cfzapier_setups',array($this,'createMenu'),$logo_url,'All Setup');
              
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfzapier_setups')))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                  
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
                });
            }
        }
        public function makeApi()
        {
                add_action('cf_api_zapir_api_callback' ,function(){
                echo $this->showLeadsToZapier($_POST['cf_zap_auth']);
                });


        }
     
       
    }
    new \CFZAP_zapier_addon\zapier\Cfzapieraddon_index();
}
?>