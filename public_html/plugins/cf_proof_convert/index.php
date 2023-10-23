<?php
if( !defined("PROOFCONVERT_PLUGIN_DIR_PATH")  )
{
	define( "PROOFCONVERT_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("PROOFCONVERT_PLUGIN_URL") )
{
	define("PROOFCONVERT_PLUGIN_URL", plugins_url( )."cf_proof_convert");
}

if(!class_exists('CFProofConvert_base'))
{
	require_once('controller/controller.php');
	class CFProofConvert_base extends CFProofConvert_controller
	{
		var $pref='cfproof_convert_';
		var $config=false;
		function __construct()
		{	
			parent:: __construct();
			self::getConfig();
			self::doInstall();
			self::addCustomData();
			self::createMenu();

			//setup setting ajax
			self::takleAjaxRequest();
			//admin_headers
			add_action('admin_head',function(){
				self::loadScripts();
			});

			if(isset($_GET['page']) && in_array($_GET['page'],array($this->pref.'all_setup',$this->pref."setup_setting") ))
            {
				add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_footer",[ $this, "addScript" ] );
			}

			add_action('cf_footer',function(){
				
				$forms_ob=$this->load('setup');
				$v=0;

				if(isset($this->config->version))
				{
					$v=$this->config->version;
				}
				// load notification
				$forms_ob->loadUserSide($v);
			
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
				add_menu_page('CF Proof Convert','CF Proof Convert',$this->pref.'all_setup',function(){
					require_once('views/allsetup.php');
				},'','All Setups');

				if(isset($_GET['page']) && $_GET['page']==$this->pref.'setup_setting')
				{
					add_submenu_page($this->pref.'all_setup','CF Proof Convert: Settings','
					Setup Settings',$this->pref.'setup_setting',function(){require_once('views/setting.php');});
				}			
			});
		}

		function doInstall(){
			register_activation_hook(function(){
				require_once('controller/install.php');
				cfProofConvertInstall($this->pref);
			});
		}

		function addCustomData(){
			add_action("cf_sale", function($data,$provided_data){
				global $mysqli;
				global $dbpref;
				$user_id=$_SESSION['user' . get_option('site_token')];
				$access=$_SESSION['access' . get_option('site_token')];
				$table1=$dbpref.$this->pref."notification_data";
				if($data['success']==1 || $data['success']=="1")
				{
					$funnel_id=$data['payment_data'][0]['funnel_id'];
					$page_id=$data['payment_data'][0]['page_id'];
					$product_id=$data['payment_data'][0]['product_id'];
					$product_title=$data['payment_data'][0]['product_title'];
					$payment_data=json_decode($data['payment_data'][0]['data']);
					$name=$payment_data->payer_name;
					$email=$payment_data->payer_email;
					$city=isset($data['payment_data'][0]['shipping_data']['city'])?$data['payment_data'][0]['shipping_data']['city']:"";
					$country=isset($data['payment_data'][0]['shipping_data']['country'])?$data['payment_data'][0]['shipping_data']['country']:"";
					if($city!="" && $country!=""){
						$address=ucfirst($city).", ".ucfirst($country);
					}elseif($country==""){
						$address=ucfirst($city);
					}elseif($city==""){
						$address=ucfirst($country);
					}else{
						$address="";
					}
					$add_times=time();
          		$sql="INSERT INTO `".$table1."`(`name`, `email`,  `address`,`product_id`, `time`,`user_id`) VALUES ('".$name."','".$email."','".$address."',".$product_id.",'".$add_times."',".$user_id.")";
         		$mysqli->query($sql);
				}

			},array("a"));
		}

		function loadScripts()
		{
			$valid_pages=array('cfproof_convert_all_setup',"cfproof_convert_setup_setting");
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
			<script src='".PROOFCONVERT_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>
			";
		}

		public function addStyle(){
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<link rel='stylesheet' href='".PROOFCONVERT_PLUGIN_URL."/assets/css/style.css'>
			<script src='".PROOFCONVERT_PLUGIN_URL."/assets/js/multi_inputs.js?v=".$v."' ></script>";
		}

		public function takleAjaxRequest(){

			// logged in ajax
			add_action( 'cf_ajax_cfproof_convert_admin_ajax', function(){
				$setup_ob=$this->load('setup');
      			$setup_ob->getProofConvertSettingAjax( $_REQUEST );
			});
			add_action( 'cf_ajax_cfproof_convert_delete_ajax', function(){
				$setup_ob=$this->load('setup');
      			$setup_ob->deleteSetup( $_REQUEST );
			});
			// non logged in  ajax
			add_action("cf_ajax_nopriv_cfproof_convert_user_ajax",function(){
				$setup_ob=$this->load('setup');
				$setup_ob->getProofConvertNotification($_REQUEST);
			});
			add_action("cf_ajax_cfproof_convert_user_ajax",function(){
				$setup_ob=$this->load('setup');
				$setup_ob->getProofConvertNotification($_REQUEST);
			});

			// non logged in  ajax
			add_action("cf_ajax_nopriv_cfproof_convert_impression",function(){
				$setup_ob=$this->load('setup');
				$setup_ob->addImpressions($_REQUEST);
			});
			add_action("cf_ajax_cfproof_convert_impression",function(){
				$setup_ob=$this->load('setup');
				$setup_ob->addImpressions($_REQUEST);
			});
		}

	}
	new CFProofConvert_base();
}
?>