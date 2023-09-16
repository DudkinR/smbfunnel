<?php
if( !defined("EXIT_FORM_PLUGIN_DIR_PATH")  )
{
	define( "EXIT_FORM_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("EXIT_FORM_PLUGIN_URL") )
{
	define("EXIT_FORM_PLUGIN_URL", plugins_url( )."cf_exito");
}

if(!class_exists('CFExito_base'))
{
	require_once('controller/controller.php');
	class CFExito_base extends CFExito_controller
	{
		var $pref='cfexito_';
		var $config=false;
		function __construct()
		{	
			parent:: __construct();
			self::getConfig();
			self::doInstall();
			self::createMenu();
			self::takleAjaxRequest();
			
			//admin inits
			add_action('admin_init',function(){
				//CSV export
				$optin_controller=$this->load('optin_control');
				$optin_controller->doExportToCSV();
				$optin_controller->storeLeads();
			});

			//inits
			add_action('init',function(){
				$optin_controller=$this->load('forms_control');
				$optin_controller->initFormSubmit();
			});

			//admin_headers
			add_action('admin_head',function(){
				self::loadScripts();
			});

			if(isset($_GET['page']) && in_array($_GET['page'],array('cfexito_all_forms', 'cfexito_all_optins',"cfexito_popup_forms") ))
            {
				add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_footer",[ $this, "addScript" ] );
			}
			
			// add_action( "cf_footer", [ $this, "addFrontEndScript" ] );
			self::createShordCode();
			add_action('cf_footer',function(){
				$forms_ob=$this->load('forms_control');
				$v=0;
				if(isset($this->config->version))
				{
					$v=$this->config->version;
				}
				$forms_ob->loadGlobalForms($v);
			});
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
		function createMenu()
		{
			add_action('admin_menu',function(){
				add_menu_page('CF Exito: Popup Forms','CF Exito','cfexito_all_forms',function(){
					require_once('views/allforms.php');
				},'','All Setups');

				if(isset($_GET['page']) && $_GET['page']=='cfexito_popup_forms')
				{
					add_submenu_page('cfexito_all_forms','CF Exito: Form Settings','Form Settings','cfexito_popup_forms',function(){require_once('views/popupSetting.php');});
				}

				add_submenu_page('cfexito_all_forms','CF Exito: Optins','Optins','cfexito_all_optins',function(){
					require_once(EXIT_FORM_PLUGIN_DIR_PATH.'/views/optins.php' );
				});			
			});
		}

		function doInstall(){
			register_activation_hook(function(){
				require_once('controller/install.php');
				cfExitoDoInstall($this->pref);
			});
		}

		function loadScripts()
		{
			$valid_pages=array('cfexito_all_optins', 'cfexito_all_forms',"cfexito_popup_forms");
			if(isset($_GET['page']) && in_array($_GET['page'], $valid_pages))
			{
				echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";

				echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
			}
		}


		public function addScript(){
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "
			<script type='text/javascript' src='assets/js/jscolor.js'></script>
			<script type='text/javascript' src='assets/js/tinymce/jquery.tinymce.min.js'></script>
			<script type='text/javascript' src='assets/js/tinymce/tinymce.min.js'></script>
			<script src='".EXIT_FORM_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>
			";
		}

		// public function addFrontEndScript(){
		// 	echo "<script src='".EXIT_FORM_PLUGIN_URL."/assets/js/userendScript.js' ></script>";
		// }

		public function addStyle(){
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<link rel='stylesheet' href='".EXIT_FORM_PLUGIN_URL."/assets/css/style.css'>
			<script src='".EXIT_FORM_PLUGIN_URL."/assets/js/multi_inputs.js?v=".$v."' ></script>";
		}

		public function takleAjaxRequest(){

		add_action( 'cf_ajax_myPopupFormAjax', function(){
			
			$forms_ob=$this->load('forms_control');
             $forms_ob->getAjaxRequest( $_REQUEST );
			});
		}
		public function createShordCode( )
		{
			add_shortcode( "cfexito_shortcode", function($params){
				if(isset($params['id']))
				{

					$v=0;
				if(isset($this->config->version))
				{
					$v=$this->config->version;
				}

					$id=$params['id'];
					ob_start();
					$forms_ob=$this->load('forms_control');
					$forms_ob->getFormUI($id,$v);
					$data=ob_get_clean();
					return $data;
				}
			});
		}
	}
	new CFExito_base();
}
?>