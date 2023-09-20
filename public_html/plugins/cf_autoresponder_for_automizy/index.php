<?php
if(!class_exists('Cfautoresponder_index'))
{
    require_once('app/process.php');
    class Cfautoresponder_index extends Cfautores_processor
    {
        var $pref='cfautores';
        var $config=false;
        function __construct()
        {
            self::getConfig();
            self::doInstall();
            self::doUninstall();
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
                add_menu_page('CF Autoresponder Addon','CFAutomizy','cfautores_setups',function(){
                    require_once('app/settings.php');
                },'','All Setups');
                add_submenu_page('cfautores_setups','CF Automizy Settings','Settings','cfautores_setting',function(){
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfautores_setups', 'cfautores_setting')))
            {
                add_action('admin_head',function(){
                    $version=0;
                    if(isset($this->config->version))
                    {
                        $version=$this->config->version;
                    }

                    if($_GET['page']=='cfautores_setting')
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
            if(isset($_GET['page']) && in_array($_GET['page'],array('cfautores_setups', 'cfautores_setting')))
            {
                add_action('admin_footer',function(){});
            }
        }
        function doInstall(){
            
                global $mysqli;
                global $dbpref;
                $table=$dbpref.'cfplugin_autoresponders';
                $mysqli->query("CREATE TABLE  IF NOT EXISTS `".$table."` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `autoresponder` varchar(255) NOT NULL,
                  `autoresponder_name` varchar(255) NOT NULL,
                  `autoresponder_detail` text NOT NULL,
                  `exf` text NOT NULL,
                  `date_created` varchar(255) NOT NULL,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                            
        }
    
        function doUninstall(){
            register_deactivation_hook(function(){
                global $mysqli;
                global $dbpref;
                $table=$dbpref.'cfplugin_autoresponders';
               //$mysqli->query("DROP TABLE  IF EXISTS `".$table."`;");//temporarily disabled
        });
    }

    }
    new Cfautoresponder_index();
}
?>