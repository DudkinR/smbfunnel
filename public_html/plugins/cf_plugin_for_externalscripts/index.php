<?php
namespace CFINT_integration_addon\integration;
use CFINT_integration_addon\integration as CFPINT_integration;
define('CF_EXTERNAL_SCRIPT_LOADED', '1');



if(!class_exists('Cfintegrationaddon_index'))
{
    require_once('app/process.php');
    class Cfintegrationaddon_index extends CFPINT_integration\Cfint_processor
    {
        var $pref='cfint';
        var $config=false;

        function __construct()
        {
            self::getConfig();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();

            parent::__construct(array('pref'=>$this->pref));
            parent::registerAjaxRequest();
            parent::registereditAjaxRequest();
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png', __FILE__);
                add_menu_page('External Scripts','External Scripts','cfexscript_setups',array($this,'createMenu'),$logo_url,'All Setup');
              
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfexscript_setups')))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cfexscript_setups')
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/script.js?v='.$version,__FILE__)."'></script>";
                    }
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
                });
            }
        }
     
       
    }
    new \CFINT_integration_addon\integration\Cfintegrationaddon_index();
}
?>