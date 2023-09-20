<?php
if(!class_exists('Cfkirim_index'))
{
    require_once('app/process.php');
    class Cfkirim_index extends Cfkirim_processor
    {
        var $pref='cfautores';
        var $config=false;
        var $autores='kirim';

                function __construct()
        {
            self::getConfig();
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
                add_menu_page('CF kirim Addon','CFkirim','cfkirim_setups',function(){
                    require_once('app/settings.php');
                },'','All Setups');
                add_submenu_page('cfkirim_setups','CF kirim Settings','Settings','cfkirim_setting',function(){
                    require_once('app/edit_setup.php');
                });
            });
        }
        function getConfig()
        {
            if(!$this->config)
            {
                $file=plugin_dir_path(__FILE__);
             //   echo $file;
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfkirim_setups', 'cfkirim_setting')))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cfkirim_setting')
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfkirim_setups', 'cfkirim_setting')))
            {
                add_action('admin_footer',function(){});
            }
        }
 

    }
    new Cfkirim_index();
}
?>