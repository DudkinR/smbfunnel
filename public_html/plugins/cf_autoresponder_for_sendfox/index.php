<?php
if( !defined("EXIT_FORM_PLUGIN_DIR_PATH")  )
{
	define( "EXIT_FORM_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("EXIT_FORM_PLUGIN_URL") )
{
	define("EXIT_FORM_PLUGIN_URL", plugins_url( )."sendfox");
}

if(!class_exists('Cfsendfox_index'))
{
  
    require_once('controller/controller.php');
	class Cfsendfox_index extends sendfox_controller
    {
        var $pref='sendfox';
        var $config=false;
        function __construct()
        {
            self::getConfig();
            self::doInstall();
            // self::doUninstall();
            self::createMenuandSubmenu();
            add_action('admin_head',function(){
				self::loadScripts();
              
			});
            $forms_ob=$this->load('forms_control');
            $forms_ob->loadMethods();
            $forms_ob->__construct();

            self::takleAjaxRequest();
           
        }
        function createMenuandSubmenu()
        {
          
            add_action('admin_menu',function(){
                add_menu_page('CF SendFox ','CFSendFox','cfsendfox_setups',function(){
                    require_once('view/settings.php');
                },'','All Setups');
                add_submenu_page('cfsendfox_setups','CF Sendfox Settings','Settings','cfsendfox_Setting',function(){
                    require_once('view/edit_setup.php');
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
    	function loadScripts()
		{
			$valid_pages=array('cfsendfox_Setting', 'cfsendfox_setups');
			if(isset($_GET['page']) && in_array($_GET['page'], $valid_pages))
			{
				echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";

				echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
			}
		}

        function doInstall(){
            
                global $mysqli;
                global $dbpref;
                $table=$dbpref.'cfsendfox_autoresponders';
                $mysqli->query("CREATE TABLE  IF NOT EXISTS `".$table."` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title`  text NOT NULL,
                  `type` text NOT NULL,
                  `email`  text NOT NULL,
                  `listid` text NOT NULL,
                  `apikey` text NOT NULL,
                  `date_created` varchar(255) NOT NULL,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                            
        }
    
    //     function doUninstall(){
    //         register_deactivation_hook(function(){
    //             global $mysqli;
    //             global $dbpref;
    //             $table=$dbpref.'cfsendfox_autoresponders';
    //     });
    // }



    public function takleAjaxRequest(){

		add_action( 'cf_ajax_myPopupFormAjax', function(){
			
			$forms_ob=$this->load('forms_control');
             $forms_ob->getAjaxRequest( $_REQUEST );
             print_r($_REQUEST);
			});
		}
    }
    new Cfsendfox_index();
}
?>


