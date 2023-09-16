<?php
if( !defined("QUIZ_PLUGIN_DIR_PATH")  )
{
	define( "QUIZ_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("QUIZ_PLUGIN_URL") )
{
	define("QUIZ_PLUGIN_URL", plugins_url( )."cf_surveyor");
}

if(!class_exists('CFquiz_base'))
{
	require_once('controller/controller.php');
	class CFquiz_base extends CFquiz_controller
	{
		var $pref='cfquiz_';
		var $config=false;
		function __construct()
		{	
			parent:: __construct();
			self::getConfig();
			self::doInstall();
			self::createMenu();
			self::takleAjaxRequest();
			self::takleusersideAjaxRequest();
			
			//admin inits
			add_action('admin_init',function(){
				//CSV export
				$optin_controller=$this->load('optin_control');
				$optin_controller->doExportToCSV();
				$optin_controller->storeLeads();
			});

			//inits
			add_action('init',function(){
				$optin_controller=$this->load('quizs_control');
				$optin_controller->initquizsubmit22();
			});

			//admin_headers
			add_action('admin_head',function(){
				self::loadScripts();
			});

			if(isset($_GET['page']) && in_array($_GET['page'],array('cfquiz_all_quizs', 'cfquiz_all_optins',"cfquiz_popup_quizs",'cfquiz_view_response') ))
            {
				add_action("admin_head", [ $this,"addStyle" ] );
				add_action("admin_footer",[ $this, "addScript" ] );
			}
			self::createShordCode();
			add_action('cf_footer',function(){
				$quizs_ob=$this->load('quizs_control');
				$v=0;
				if(isset($this->config->version))
				{
					$v=$this->config->version;
				}
				$quizs_ob->loadGlobalquizs($v);
			});
		}
		function getConfig()
        {
            if(!$this->config)
            {
                $file=plugin_dir_path(__FILE__);
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
				add_menu_page('CF Surveyor: Popup surveys','CF Surveyor','cfquiz_all_quizs',function(){
					require_once('views/allquizs.php');
				},'','All Setups');

				if(isset($_GET['page']) && $_GET['page']=='cfquiz_popup_quizs')
				{
					add_submenu_page('cfquiz_all_quizs','CF Surveyor: Survey Settings','Surveyor Settings','cfquiz_popup_quizs',function(){require_once('views/popupSetting.php');});
				}

				add_submenu_page('cfquiz_all_quizs','CF Surveyor: Optins','Optins','cfquiz_all_optins',function(){
					require_once(QUIZ_PLUGIN_DIR_PATH.'/views/optins.php' );
				});

				if(isset($_GET['page']) && $_GET['page']=='cfquiz_add_questions')
				{
				add_submenu_page('cfquiz_all_quizs','CF Surveyor: Questions','Add Questions','cfquiz_add_questions',function(){
					require_once(QUIZ_PLUGIN_DIR_PATH.'/views/add_questions.php' );});	
				}
				if(isset($_GET['page']) && $_GET['page']=='cfquiz_view_response')
				{
				add_submenu_page('cfquiz_all_quizs','CF Surveyor: Response','View Response','cfquiz_view_response',function(){
					require_once(QUIZ_PLUGIN_DIR_PATH.'/views/view_response.php' );});	
				}

			});
		}

		function doInstall(){
			register_activation_hook(function(){
				require_once('controller/install.php');
				cfquizDoInstall($this->pref);
			});
		}

		function loadScripts()
		{
			$valid_pages=array('cfquiz_all_optins', 'cfquiz_all_quizs',"cfquiz_popup_quizs");
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
			<script src='".QUIZ_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>
			";
		}


		public function addStyle(){
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<link rel='stylesheet' href='".QUIZ_PLUGIN_URL."/assets/css/style.css'>
			<script src='".QUIZ_PLUGIN_URL."/assets/js/multi_inputs.js?v=".$v."' ></script>";
		}

		public function takleAjaxRequest(){
		add_action( 'cf_ajax_myPopupQuizAjax', function(){
			$quizs_ob=$this->load('quizs_control');
             $quizs_ob->getAjaxRequest( $_REQUEST );
			});
		}
		
		public function takleusersideAjaxRequest(){
		add_action( 'cf_ajax_nopriv_myPopupUserSideQuizAjax', function(){
			die();
			});  
        }

		public function createShordCode( )
		{
			add_shortcode( "cfquiz_shortcode", function($params){
				if(isset($params['id']))
				{
					$v=0;
				if(isset($this->config->version))
				{
					$v=$this->config->version;
				}
					$id=$params['id'];
					ob_start();
					$quizs_ob=$this->load('quizs_control');
					$quizs_ob->getquizUI($id,$v);
					$data=ob_get_clean();
					return $data;
				}
			});
		}
	}
	new CFquiz_base();
}
?>