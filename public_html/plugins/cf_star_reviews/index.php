<?php
if( !defined("CFPRODUCT_REV_PLUGIN_DIR_PATH")  )
{
	define( "CFPRODUCT_REV_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("CFPRODUCT_REV_PLUGIN_URL") )
{
	define("CFPRODUCT_REV_PLUGIN_URL", plugin_dir_url( __FILE__ ) );
}
if( !defined("CFPRODUCT_REV_PLUGIN_URL_URL") )
{
	define("CFPRODUCT_REV_PLUGIN_URL_URL", plugins_url( )."cf_star_reviews");
}

if(!class_exists('CFProduct_review_base'))
{
	require_once('controller/controller.php');
	class CFProduct_review_base extends CFProduct_review_controller
	{
		var $pref='cfproduct_review_';
		var $config=false;
		var $funnel_id=false;
		function __construct()
		{	
			parent:: __construct();
			$this->getConfig();
			$this->doInstall();
			$this->createMenu();
			self::addLanguage();
			self::generateLanguage();

			//setup setting ajax
			$this->takleAjaxRequest();
			//admin_headers
			add_action('admin_head',function(){
				$this->loadScripts();
			});
			if(isset($_GET['page']) && in_array($_GET['page'],array($this->pref.'all',$this->pref."setting") ))
            {
				add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_footer",[ $this, "addScript" ] );
			}
			add_action('cf_head',function( $data ){
				
				$this->funnel_id = $data['funnel_id'];
				$this->loadUserSide();
			});
			add_action('cf_footer',function( $data ){
				
				$this->loadUserSideScript();
			});

			$this->createShortCode();
		}
		function addLanguage()
		{
			$file = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/hindi_hi.json";
			$file1 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/arabic_ar.json";
			$file2 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/danish_da.json";
			$file4 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/dutch_nl.json";
			$file5 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/english_en.json";
			$file6 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/french_fr.json";
			$file7 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/german_de.json";
			$file8 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/greek_gr.json";
			$file9 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/italian_itl.json";
			$file10 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/japanese_ja.json";
			$file11 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/korean_ko.json";
			$file12 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/malay_ml.json";
			$file13 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/norwegian_no.json";
			$file14 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/polish_pl.json";
			$file15 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/portuguese_po.json";
			$file16 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/romanian_ro.json";
			$file17 = CFPRODUCT_REV_PLUGIN_DIR_PATH."/assets/lang/spanish_sp.json";
			
			register_custom_lang(array(
				"lang_hindi_hi"=>array('file'=>$file),
				"lang_arabic_ar"=>array('file'=>$file1),
				"lang_danish_da"=>array('file'=>$file2),
				"lang_dutch_nl"=>array('file'=>$file4),
				"lang_english_en"=>array('file'=>$file5),
				"lang_french_fr"=>array('file'=>$file6),
				"lang_german_de"=>array('file'=>$file7),
				"lang_greek_gr"=>array('file'=>$file8),
				"lang_italian_itl"=>array('file'=>$file9),
				"lang_japanese_ja"=>array('file'=>$file10),
				"lang_korean_ko"=>array('file'=>$file11),
				"lang_malay_ml"=>array('file'=>$file12),
				"lang_norwegian_no"=>array('file'=>$file13),
				"lang_polish_pl"=>array('file'=>$file14),
				"lang_portuguese_po"=>array('file'=>$file15),
				"lang_romanian_ro"=>array('file'=>$file16),
				"lang_spanish_sp"=>array('file'=>$file17)
			),function($stat, $par){
			});
		}
		function generateLanguage()
		{
			if(!get_option("sf_custom_language_init"))
			{
				$current= get_option('app_language');
				if($current && strlen(trim($current))>0)
				{
					generate_custom_lang();
					add_option("sf_custom_language_init",1);
				}
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
				$logo_url=plugins_url('assets/image/icon.png', __FILE__);
				add_menu_page(t('Star Reviews'),t('Star Reviews'),$this->pref.'all',function(){
					require_once('views/allreview.php');
				},$logo_url,t('All Reviews'));
				add_submenu_page($this->pref.'all',t('Settings'),t('Settings'),$this->pref.'setting',function(){
					require_once(CFPRODUCT_REV_PLUGIN_DIR_PATH.'/views/settings.php');});		
			});
		}
		function doInstall(){
			register_activation_hook(function(){
				require_once('controller/install.php');
				cfproduct_review_install();
			});
		}

		function loadScripts()
		{
			$valid_pages=array('cfproduct_review_all',"cfproduct_review_setting");
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
			<script src='".CFPRODUCT_REV_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>
			";
		}

		public function addStyle(){
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<link rel='stylesheet' href='".CFPRODUCT_REV_PLUGIN_URL."/assets/css/style.css'>";
		}
		public function loadUserSide()
		{
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<link rel='stylesheet' href='".get_option('install_url')."/assets/fontawesome/css/all.css'>";
			echo "<link rel='stylesheet' href='".CFPRODUCT_REV_PLUGIN_URL."assets/css/star-rating-svg.css'>";
			echo "<link rel='stylesheet' href='".CFPRODUCT_REV_PLUGIN_URL."assets/css/user_style.css'>";
			echo "<script src='".CFPRODUCT_REV_PLUGIN_URL."assets/js/jquery.star-rating-svg.js?v=".$v."' ></script>";
		}
		function loadUserSideScript()
		{
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<script src='".CFPRODUCT_REV_PLUGIN_URL."assets/js/user_script.js?v=".$v."' ></script>";
		}
		public function takleAjaxRequest(){

			add_action( 'cf_ajax_cfproreviews_reviews', function(){
				$setup=$this->load('setup');
				global $mysqli;
				$bulk = $mysqli->real_escape_string($_REQUEST['bulk']);

				if($bulk=="delete_reivew")
				{
					$setup->reviewDelete($_REQUEST,true );
				}
				else if($bulk=="appr")
				{
					$setup->reviewApproved($_REQUEST,"all",true );
				}
				else if($bulk=="read")
				{
					$setup->reviewMarkasRead($_REQUEST,"all",true );
				}
				else if($bulk=="all_data")
				{
					$setup->BulkReviewAction( $_REQUEST );

				}
			});

			//Add like dislike
			add_action("cf_ajax_nopriv_cfproreviews_like", function(){
				
				global $mysqli;
				$type = $mysqli->real_escape_string( trim( $_REQUEST['type'] ));

				if( $type == "like" ) 
				{
					$addWatch = $this->load("setting");
					$addWatch->addLike( $_REQUEST );
				}else if( $type == "dislike" ) 
				{
					$addWatch = $this->load("setting");
					$addWatch->addDisLike( $_REQUEST );
				}
			});
			//Add like dislike
			add_action("cf_ajax_cfproreviews_like", function(){
				
				global $mysqli;
				$type = $mysqli->real_escape_string( trim( $_REQUEST['type'] ));
				if( $type == "like" ) 
				{
					$addWatch = $this->load("setting");
					$addWatch->addLike( $_REQUEST );
				}else if( $type == "dislike" ) 
				{
					$addWatch = $this->load("setting");
					$addWatch->addDisLike( $_REQUEST );
				}
			});
			
			//Add reviews
			add_action("cf_ajax_nopriv_cfproreviews_addreview", function(){
				$setting = $this->load("setting");
				$setting->addReview( $_REQUEST );
			});
			add_action("cf_ajax_cfproreviews_addreview", function(){
				$setting = $this->load("setting");
				$setting->addReview( $_REQUEST );
			});
			//Delete review
			add_action("cf_ajax_nopriv_cfproreviews_delete", function(){
				$setting = $this->load("setting");
				$setting->deleteReview( $_REQUEST );
			});
			//Delete review
			add_action("cf_ajax_cfproreviews_delete", function(){
				$setting = $this->load("setting");
				$setting->deleteReview( $_REQUEST );
			});

			
			add_action("cf_ajax_saveproduct_review_ajax", function(){
				$setting = $this->load("setting");
				echo $setting->settings( $_REQUEST );
				die();
			});
			add_action("cf_ajax_cfproreviews_reset_setting", function(){
				$setting = $this->load("setting");
				echo $setting->resetSettings( $_REQUEST );
				die();
			});
		}
		public function createShortCode( )
		{
			add_shortcode("cfproduct_reviews", function($params){
				global $mysqli;
				if( isset( $_GET['product'] ) )
				{
					$id = $mysqli->real_escape_string( trim( $_GET['product']  ) );
					$come_from_get=true;
				}
				elseif( isset( $params['id'] )) 
				{
					$id = $mysqli->real_escape_string( trim( $params['id']) );
					$come_from_get=false;
				}
				else{
					$id=false;
				}

				if($id)
				{

					$show = false;
					$read = false;
	
					if( isset( $params['show'] ) )
					{
						$show = $mysqli->real_escape_string( trim( $params['show'] ) );
					}
					
					if( isset( $params['read'] ) )
					{
						$read = $mysqli->real_escape_string( trim( $params['read'] ) );
					}
					
					$v = 0;
					if( isset( $this->config->version ) )
					{
						$v = $this->config->version;
					}	
	
					ob_start();
					$review_ob = $this->load('setup');
					try{
						$review_ob->getAllReviewUI( $id, $v, $this->funnel_id,$come_from_get, $show,$read );
					}catch(Exception $e)
					{
						echo $e;
					}
					$data      = ob_get_clean();
					return $data;

				}
			});
		}

	}
	new CFProduct_review_base();
}
?>