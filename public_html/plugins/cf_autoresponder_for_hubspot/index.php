<?php
namespace CFAuto_autoresponder_addon\hubspot ;
use CFAuto_autoresponder_addon\hubspot as CFAUTO_hubspot;

if(!class_exists('Cfautoresaddon_index'))
{
    require_once('app/process.php');
    class Cfautoresaddon_index extends CFAUTO_hubspot\Cfautores_processor
    {
        var $pref='cfautores';
        var $config=false;
        var $autores='hubspot';

        function __construct()
        {
            self::getConfig();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();

            parent::__construct(array('pref'=>$this->pref));
            parent::registerAjaxRequest();
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png', __FILE__);
                add_menu_page('Hubspot','Hubspot','cfautores_setups_'.$this->autores,array($this,'createMenu'),$logo_url,'All Setups');
                add_submenu_page('cfautores_setups_'.$this->autores,'Manage Setup(Hubspot)','Create New','cfautores_setting_'.$this->autores,function(){
                    require_once('app/edit_setup.php');
                });
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
        function includeHeaderScripts()
        {
            //header scripts
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfautores_setups_'.$this->autores, 'cfautores_setting_'.$this->autores)))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cfautores_setting_'.$this->autores)
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/script.js?v='.$version,__FILE__)."'></script>";
                    }
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
                });
            }
        }
    }
    new \CFAuto_autoresponder_addon\hubspot\Cfautoresaddon_index();
}
?>