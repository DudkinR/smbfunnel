<?php
namespace CFPAY_peyment_addon\paystack;
use CFPAY_peyment_addon\paystack as CFPAY_paystack;

if(!class_exists('Cfpaymentaddon_index'))
{
    require_once('app/process.php');
    class Cfpaymentaddon_index extends CFPAY_paystack\Cfpay_processor
    {
        var $pref='cfpay';
        var $config=false;
        var $method='paystack';

        function __construct()
        {
            self::getConfig();
            self::doInstall();
            self::createMenuandSubmenu();
            self::includeHeaderScripts();
            self::includeFotterScripts();

            parent::__construct(array('pref'=>$this->pref));
            parent::registerAjaxRequest();
        }
        function createMenuandSubmenu()
        {
            //menues
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png', __FILE__);
                add_menu_page('Paystack','Paystack','cfpay_setups_'.$this->method,array($this,'createMenu'),$logo_url,'All Setups');
                add_submenu_page('cfpay_setups_'.$this->method,'Manage Setup(Paystack)','Create New','cfpay_setting_'.$this->method,function(){
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfpay_setups_'.$this->method, 'cfpay_setting_'.$this->method)))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cfpay_setting_'.$this->method)
                    {
                        echo "<script type='module' src='".plugins_url('/assets/js/script.js?v='.$version,__FILE__)."'></script>";
                    }
                    echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                    echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
                });
            }
        }
        function includeFotterScripts()
        {
            //footer scripts
            /*
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfpay_setups', 'cfpay_setting')))
            {
                add_action('admin_footer',function(){});
            }
            */
        }
        function doInstall(){
            
                global $mysqli;
                global $dbpref;
                $qry_str="create table if not exists `".$dbpref."cfpay_addon_credentials_".$this->method."`(
                    `id` int not null auto_increment,
                    `title` varchar(255) not null,
                    `method` varchar(255) not null, 
                    `credentials` text not null,
                    `tax` varchar(255) not null,
                    `added_on` datetime not null,
                    primary key(`id`)
                )
                    ";
                $mysqli->query($qry_str);
            
        }
    }
    new \CFPAY_peyment_addon\paystack\Cfpaymentaddon_index();
}
?>