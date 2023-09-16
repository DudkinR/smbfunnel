<?php
if( !defined("CFADDSTUDENT_PLUGIN_DIR_PATH")  )
{
	define( "CFADDSTUDENT_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("CFADDSTUDENT_PLUGIN_URL") )
{
	define("CFADDSTUDENT_PLUGIN_URL",  plugin_dir_url( __FILE__ ) );
}
if( !defined("CFADDSTUDENT_PLUGIN_URL_DIR") )
{
	define("CFADDSTUDENT_PLUGIN_URL_DIR", plugins_url( )."cf_add_student");
}

if(!class_exists('CFaddstudent_base'))
{
	require_once('controller/controller.php');
	class CFaddstudent_base extends CFaddstudent_controller
	{

		var $pref='cfbulk_members_';
		var $config=false;
		var $student='';
		var $funnel_type='';
		function __construct()
		{	
			global $app_variant;
			$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
			if( $app_variant == "shopfunnels" ){
				$this->student = "Customer";
				$this->funnel_type = "Store";
	
			}
			elseif( $app_variant == "cloudfunnels" ){
				$this->student = "Member";
				$this->funnel_type = "Funnel";
	
			}
			elseif( $app_variant == "coursefunnels" ){
				$this->student = "Student";
				$this->funnel_type = "Course Funnel";
	
			}
			parent:: __construct();
			self::getConfig();
			self::createMenu();

			//setup setting ajax
			self::takleAjaxRequest();
			//admin_headers
			add_action('admin_head',function(){
				self::loadScripts();
			});

			if(isset($_GET['page']) && in_array($_GET['page'],array($this->pref."add",$this->pref."members",$this->pref."funnels") ))
            {
				add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_footer",[ $this, "addScript" ] );
			}
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
				$logo_url=plugins_url('assets/image/logo.png', __FILE__);
				add_menu_page( 'Bulk Members importer','Bulk Members importer',$this->pref.'funnels',function(){
					require_once('views/allsetup.php');
				},$logo_url,$this->funnel_type.'s');

				if(isset($_GET['page']) && $_GET['page']==$this->pref.'add')
				{
					add_submenu_page($this->pref.'funnels','Add '.ucfirst($this->student),'
					Add '.ucfirst($this->student),$this->pref.'add',function(){ require_once('views/addstudent.php'); } );
				}
				if(isset($_GET['page']) && $_GET['page']==$this->pref.'members')
				{
					add_submenu_page($this->pref.'funnels',ucfirst($this->student).'s',ucfirst($this->student).'s',$this->pref.'members',function(){ require_once('views/students.php'); } );
				}			
			});
		}

		function loadScripts()
		{
			$valid_pages=array("cfbulk_members_funnels","cfbulk_members_add","cfbulk_members_members");
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
			if( isset($_GET['page']) && $_GET['page']==$this->pref.'members' )
			{
				echo "<script src='".CFADDSTUDENT_PLUGIN_URL."/assets/js/import.js?v=".$v."' ></script>";
			}

			echo "<script src='".CFADDSTUDENT_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>";
			echo "<script type='text/javascript' src='" . CFADDSTUDENT_PLUGIN_URL . "/assets/js/select2.min.js'></script>";

		}

		public function addStyle(){
			echo "<link rel='stylesheet' href='".CFADDSTUDENT_PLUGIN_URL."/assets/css/style.css'>";
			echo "<link rel='stylesheet' href='" . CFADDSTUDENT_PLUGIN_URL . "/assets/css/select2.min.css'>";
		}

		public function takleAjaxRequest(){

			// logged in ajax
			add_action( 'cf_ajax_cfaddstudentm_admin_ajax', function(){
				$setup_ob=$this->load('setup');
      			echo $setup_ob->addStudentManually( $_REQUEST );
			});
			add_action( 'cf_ajax_cfdeletestudentm_ajax', function(){
				$setup_ob=$this->load('setup');
      			$setup_ob->updatedelete( $_REQUEST,'delete' );
			});
			add_action( 'cf_ajax_cfcancelstudentm_ajax', function(){
				$setup_ob=$this->load('setup');
      			$setup_ob->updatedelete( $_REQUEST,'cancel' );
			});
			add_action( 'cf_ajax_cfstudent_bulk', function(){
				$setup_ob=$this->load('setup');
      			echo $setup_ob->addBulkStuden( $_REQUEST );
				  die();
			});
			add_action( 'cf_ajax_cfaddstudent_courses', function(){
				$setup_ob=$this->load('setup');
      			$setup_ob->getSingleStudentCourses( $_REQUEST );				  
			});
		}

	}
	new CFaddstudent_base();
}
?>