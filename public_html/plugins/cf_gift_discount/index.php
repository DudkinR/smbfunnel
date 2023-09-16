<?php

if( !defined("CFGIFT_DISCOUNT_PLUGIN_DIR_PATH")  )
{
	define( "CFGIFT_DISCOUNT_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}
if( !defined("CFGIFT_DISCOUNT_PLUGIN_URL") )
{
	define( "CFGIFT_DISCOUNT_PLUGIN_URL", plugin_dir_url( __FILE__ ) );
}
if( !defined("CFGIFT_DISCOUNT_PLUGIN_URL_URL") )
{
	define("CFGIFT_DISCOUNT_PLUGIN_URL_URL", plugins_url( )."cf_gift_discount");
}

if(!class_exists('CFgiftdiscount_base'))
{
	require_once('controller/controller.php');
	class CFgiftdiscount_base extends CFgiftdiscount_controller
	{
		var $pref='giftcards';
		var $config=false;
		var $laod;
		
		function __construct()
		{	
			parent:: __construct();
			$this->getConfig();
			$this->doInstall();
			$this->registerShortCodes();
			self::addLanguage();
			self::generateLanguage();
			$this->createMenu();
			do_session_start();

			//setup setting ajax
			$this->takleAjaxRequest();
			//admin_headers

			add_action('admin_head',function(){
				$this->loadScripts();
			});
			add_action('cf_head',function($data){
				if($data['category']=="checkout" || $data['category']=="orderform" || $data['category']=="cart")
				{
					echo '<link rel="stylesheet" type="text/css" href="'.get_option('install_url').'/assets/fontawesome/css/all.css"  />';
				}
			});
			

			//Filter data
			add_filter('the_checkout_data', function( $current_orderer_detail, $info ){
				set_session('cfdisc_order_data',$current_orderer_detail);
				set_session('cfdisc_order_data_info',$info);
				// print_r($current_orderer_detail);
				$giftcard = $this->load('redeem');
				// check giftcard coce set or not
				if( isset( $_GET['oto_removed'] ) )
				{
					if( has_session('cfredeem_discount_successfully') )
					{
						$current_orderer_detail['subtotal_price']=$current_orderer_detail['subtotal_price'];
						unset_session('cfredeem_discount_successfully');
						unset_session('cfdisc_oldallproductdetail');
					}
					if( has_session('cfredeem_giftcard_successfully') )
					{
						$current_orderer_detail['subtotal_price']=$current_orderer_detail['subtotal_price'];
						unset_session('cfredeem_giftcard_successfully');
						unset_session('cfdisc_oldallproductdetail');
					}
				}
				elseif( isset($_GET['cfdisc_redeem_giftcard']) )
				{
					$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					if( has_session('cfredeem_giftcard_successfully') )
					{
						$cfredeem = get_session('cfredeem_giftcard_successfully');
						if( $cfredeem['status'] == 0  )
						{
							$response = $giftcard->redeemGiftCard( $_GET['cfdisc_redeem_giftcard'],$current_orderer_detail,$info );
							set_session('cfredeem_giftcard_successfully',$response);
						}
					}else{
						$response = $giftcard->redeemGiftCard( $_GET['cfdisc_redeem_giftcard'],$current_orderer_detail,$info );
						set_session('cfredeem_giftcard_successfully',$response);
						if($response['status'])
						{
						echo "<script>window.location='".$url."';</script>";
						}else{
						echo "<script>window.location='".$url."';</script>";
						}
					}
				}
				elseif( isset($_GET['cfdisc_redeem_discount']))
				{

					$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					if( has_session('cfredeem_discount_successfully') )
					{
						$cfredeem = get_session('cfredeem_discount_successfully');
						if( $cfredeem['status'] == 0  )
						{
							$response = $giftcard->redeemDiscount( $_GET['cfdisc_redeem_discount'],$current_orderer_detail,$info );
							set_session('cfredeem_discount_successfully',$response);

						}
					}else{
						$response = $giftcard->redeemDiscount( $_GET['cfdisc_redeem_discount'],$current_orderer_detail,$info );
						set_session('cfredeem_discount_successfully',$response);
						if($response['status'])
						{
							echo "<script>window.location='".$url."';</script>";
						}else{
							echo "<script>window.location='".$url."';</script>";
						}
					}
				}
				elseif( isset( $_GET['cfdisc_redeem_revert'] ) ||  isset( $_GET['oto_removed'] ) )
				{
					if( has_session('cfredeem_giftcard_successfully') )
					{
						$cfredeem = get_session('cfredeem_giftcard_successfully');
						$current_orderer_detail['subtotal_price']=$cfredeem['data']['original_total'];
						unset_session('cfredeem_giftcard_successfully');
						unset_session('cfdisc_oldallproductdetail');
					}
				}
				elseif( isset( $_GET['cfdisc_revert_discount'] ) ||  isset( $_GET['oto_removed'] ) )
				{
					if( has_session('cfredeem_discount_successfully') )
					{
						$cfredeem = get_session('cfredeem_discount_successfully');
						$current_orderer_detail['subtotal_price']=$cfredeem['data']['original_total'];
						unset_session('cfredeem_discount_successfully');
						unset_session('cfdisc_oldallproductdetail');
					}
				}else{
					if($info['checkout_page']==1)
					{
						unset_session('cfredeem_giftcard_successfully');
						unset_session('cfdisc_oldallproductdetail');
						unset_session('cfredeem_discount_successfully');
					}
				}

				
				if( has_session('cfredeem_giftcard_successfully') &&  ( isset($_GET['cfdisc_redeem_giftcard'] ) || isset($_GET['page'] ) ) )
				{
					$cfredeem = get_session('cfredeem_giftcard_successfully');
					if( $cfredeem['status'] == 1  )
					{
							$current_orderer_detail['subtotal_price']=$cfredeem['data']['subtotal_price'];
							if(isset($current_orderer_detail['total_price']))
							{
								$current_orderer_detail['total_price']=$current_orderer_detail['total_price']-$cfredeem['data']['for_restore'];
							}elseif(isset($current_orderer_detail['total'])){
								$current_orderer_detail['total']=$current_orderer_detail['total']-$cfredeem['data']['for_restore'];
							}
							if( isset( $current_orderer_detail['allproductdetail'] ) )
							{
								set_session('cfdisc_oldallproductdetail',$current_orderer_detail['allproductdetail']);
								$current_orderer_detail['allproductdetail'] .= "<ul> <li>Gif card (".$cfredeem['data']['gift_code'].") (Price: ".$cfredeem['data']['for_restore']." ".$cfredeem['data']['currency'].")</li> </ul> ";
							}
					}
				}elseif( has_session('cfredeem_discount_successfully')  &&  ( isset($_GET['cfdisc_redeem_discount'] ) || isset($_GET['page'] ) ) ){
					$cfredeem = get_session('cfredeem_discount_successfully');
					if( $cfredeem['status'] == 1  )
					{
							$current_orderer_detail['subtotal_price']=$cfredeem['data']['subtotal_price'];
							if(isset($current_orderer_detail['total_price']))
							{
								$current_orderer_detail['total_price']=$current_orderer_detail['total_price']-$cfredeem['data']['for_restore'];
							}elseif(isset($current_orderer_detail['total'])){
								$current_orderer_detail['total']=$current_orderer_detail['total']-$cfredeem['data']['for_restore'];
							}

							if( isset( $current_orderer_detail['allproductdetail'] ) )
							{
								set_session('cfdisc_oldallproductdetail',$current_orderer_detail['allproductdetail']);
								$current_orderer_detail['allproductdetail'] .= "<ul> <li>Discount (".$cfredeem['data']['gift_code'].") (".$cfredeem['data']['percentage']."% worth of ".$cfredeem['data']['for_restore']." ".$cfredeem['data']['currency'].")</li> </ul>";
							}
					}
				}
				elseif( has_session('cfredeem_discount_successfully')  ) {
						unset_session('cfredeem_giftcard_successfully');
						unset_session('cfdisc_oldallproductdetail');
				}
				elseif( has_session('cfredeem_discount_successfully')  ) {
					unset_session('cfdisc_oldallproductdetail');
					unset_session('cfredeem_discount_successfully');
				}
				if( ( isset( $current_orderer_detail['total'] ) && $current_orderer_detail['total'] == 0)  || ( isset( $current_orderer_detail['total_price'] ) && $current_orderer_detail['total_price'] == 0 ) )
				{
				
					$_SESSION['custom_payment_method'.get_option('site_token')]='temp';
					if( isset($info['payment_method']->title)  )
					{
						$info['payment_method']->title="temp";
						$info['payment_method']->method="temp";
						$info['payment_method']->tax="0";
						$info['payment_method']->id="temp";
						$info['payment_method']->credentials="";
						$info['order_session']['payment_method']="temp";
						$_SESSION['order_form_data'.get_option('site_token')]['payment_method']='temp';
					}else{
						$temp_m=[];
						$temp_m['title']="temp";
						$temp_m['method']="temp";
						$temp_m['tax']="0";
						$temp_m['id']="temp";
						$temp_m['credentials']="";
						$info['payment_method']=(object)$temp_m;
						$info['order_session']['payment_method']="temp";
						$_SESSION['order_form_data'.get_option('site_token')]['payment_method']='temp';
					}
				}
				return $current_orderer_detail;
			});
			$payment  = $this->load('payment');
			$payment->registerPaymentMethod();

			// call the registerPaymentMethod  and add Notification in issue giftcard table after payment

			add_action("cf_sale", function( $data ,$provided_data ){
				unset($_SESSION['custom_payment_method'.get_option('site_token')]);
                if($data['success']==1 || $data['success']=="1")
                {
					$redeem  = $this->load('redeem');
					if( has_session('cfredeem_giftcard_successfully') )
					{
						$redeem->addNotificationAfterGiftcardRedeem( $data );
					}
					// call the registerPaymentMethod  and add Notification in issue giftcard table after payment
					elseif( has_session('cfredeem_discount_successfully') )
					{
						$redeem->addNotificationAfterDiscountRedeem( $data );
					}
					else
					{
						$redeem->IfGiftProductThenCreateGiftCard( $data );
					}
				}
			},array('a'));
			

			add_action('cf_footer',function(){
				self::loadScriptsinFrontEnd();
			});
			
			if(isset($_GET['page']) && in_array($_GET['page'],array('cfdiscount_giftcards','cfdiscount_add_giftproduct','cfdiscount_giftcards_products','cfdiscount_discount','cfdiscount_giftcard_settings','cfdiscount_add_giftproduct','cfdiscount_giftcard_timeline','cfdiscount_discount_timeline') ))
            {
				add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_footer",[ $this, "addScript" ] );
			}
		}
		function addLanguage()
		{
			$file = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/hindi_hi.json";
			$file1 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/arabic_ar.json";
			$file2 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/danish_da.json";
			$file4 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/dutch_nl.json";
			$file5 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/english_en.json";
			$file6 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/french_fr.json";
			$file7 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/german_de.json";
			$file8 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/greek_gr.json";
			$file9 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/italian_itl.json";
			$file10 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/japanese_ja.json";
			$file11 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/korean_ko.json";
			$file12 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/malay_ml.json";
			$file13 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/norwegian_no.json";
			$file14 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/polish_pl.json";
			$file15 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/portuguese_po.json";
			$file16 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/romanian_ro.json";
			$file17 = CFGIFT_DISCOUNT_PLUGIN_DIR_PATH."/assets/lang/spanish_sp.json";
			
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
				
				global  $app_variant;
				$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
				$logo_url=plugins_url('assets/img/icon.png', __FILE__);
				add_menu_page(t('Gift Cards'),t('Gift Cards'),'cfdiscount_giftcards',function(){ require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/gift_cards.php');},$logo_url,t('Gift Cards'));
				add_submenu_page('cfdiscount_giftcards',t('Discount'),t('Discount'),'cfdiscount_discount',function(){require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/discounts.php');});
				
				if($app_variant=="shopfunnels"){
					add_submenu_page('cfdiscount_giftcards',t('Gift Card Products'),t('Gift Card Products'),'cfdiscount_giftcards_products',function(){require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/gift_products.php');});
					if(isset($_GET['page']) && $_GET['page']=='cfdiscount_add_giftproduct')
					{
						add_submenu_page('cfdiscount_giftcards',t('Add Gift Product'),t('Add Gift Product'),'cfdiscount_add_giftproduct',function(){require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/add_giftproduct.php');});
					}
					
				}
				if(isset($_GET['page']) && $_GET['page']=='cfdiscount_giftcard_timeline')
				{
					add_submenu_page('cfdiscount_giftcards',t('Gift Card Timeline'),t('Gift Card Timeline'),'cfdiscount_giftcard_timeline',function(){require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/giftcard_timeline.php');});

				}
				add_submenu_page('cfdiscount_giftcards',t('Gift Card And Discount setting'),t('Settings'),'cfdiscount_giftcard_settings',function(){require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/settings.php');});

				if(isset($_GET['page']) && $_GET['page']=='cfdiscount_discount_timeline')
				{
					add_submenu_page('cfdiscount_giftcards',t('Discount Timeline'),t('Discount Timeline'),'cfdiscount_discount_timeline',function(){require_once(CFGIFT_DISCOUNT_PLUGIN_DIR_PATH.'views/discount_timeline.php');});
				}
				
				
			});
		}

		function doInstall(){
			register_activation_hook(function(){
				require_once('controller/install.php');
				CFDiscount_Install();
			});
		}

		function loadScripts()
		{
			$valid_pages=array('cfdiscount_giftcards','cfdiscount_discount','cfdiscount_giftcard_settings','cfdiscount_add_giftproduct','cfdiscount_discount_timeline','cfdiscount_giftcard_timeline','giftcards_products','cfdiscount_giftcards_products') ;
			if(isset($_GET['page']) && in_array($_GET['page'], $valid_pages))
			{
				echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";

				echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
			}
		}
		function loadScriptsinFrontEnd()
		{
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}

			echo "<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/apply_gift_card.js?v=".$v."' ></script>";
		}

		public function addScript(){
			$v=0;
			global $app_variant;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			if($app_variant=="shopfunnels")
			{
				echo "<script type='text/javascript' src='assets/js/currency.js'></script>";
			}
			echo "
			<script type='text/javascript' src='assets/js/jscolor.js'></script>
			<script type='text/javascript' src='assets/js/request.js'></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/jquery.multiselect.js?v=".$v."' ></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/intlTelInput.min.js?v=".$v."' ></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/intlTelInput-jquery.min.js?v=".$v."' ></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/jquery-customselect.js?v=".$v."' ></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/data.min.js?v=".$v."' ></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/gift_cards.js?v=".$v."' ></script>
			<script src='".CFGIFT_DISCOUNT_PLUGIN_URL."/assets/js/apply_gift_card.js?v=".$v."' ></script>
			";
		}

		public function addStyle(){
			$v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}

			echo "<link rel='stylesheet' href='".CFGIFT_DISCOUNT_PLUGIN_URL."assets/css/intlTelInput.min.css'>
			<link rel='stylesheet' href='".CFGIFT_DISCOUNT_PLUGIN_URL."assets/css/jquery.multiselect.css'>
			<link rel='stylesheet' href='".CFGIFT_DISCOUNT_PLUGIN_URL."assets/css/jquery-customselect.css'>
			<link rel='stylesheet' href='".CFGIFT_DISCOUNT_PLUGIN_URL."assets/css/giftcard.css'>";
		}

		public function takleAjaxRequest(){

			// logged in ajax
			add_action( 'cf_ajax_savegiftcards_ajax', function(){
				$giftcard=$this->load('giftcard');
				if( $_POST['savegiftcards']=="save" )
				{
					echo $giftcard->createGiftCard($_POST);
					die();
				}else{
					echo $giftcard->updateGiftCard($_POST);
					die();
				}
			});
			// logged in ajax
			add_action( 'cf_ajax_savediscountcode_ajax', function(){
				$giftcard=$this->load( 'discount' );
				echo $giftcard->createDiscount( $_POST );
				die();
			});
			add_action( 'cf_ajax_savegiftcards_customer', function(){
				$giftcard=$this->load('giftcard');
				echo $giftcard->addMemberThroughGiftCard($_POST);
				die();
			});
			add_action( 'cf_ajax_nopriv_savegiftcards_customer', function(){
				$giftcard=$this->load('giftcard');
				echo $giftcard->addMemberThroughGiftCard($_POST);
				die();
			});

			add_action( 'cf_ajax_savegiftcards_send_email', function(){
				$giftcard=$this->load('giftcard');
				echo $giftcard->sendCode($_POST);
				die();
			});
			
			add_action( 'cf_ajax_savegiftsetting_ajax', function(){
				$discount=$this->load('discount');
				echo $discount->settings($_POST);
				die();
			});

			add_action( 'cf_ajax_nopriv_savegiftcards_send_email', function(){
				$giftcard=$this->load('giftcard');
				echo $giftcard->sendCode($_POST);
				die();
			});
			add_action( 'cf_ajax_savegiftproducts_ajax', function(){
				$giftcard=$this->load('giftcard');
				echo $giftcard->createGiftCardProduct($_POST);
				die();
			});
			add_action( 'cf_ajax_nopriv_savegiftproducts_ajax', function(){
				$giftcard=$this->load('giftcard');
				echo $giftcard->createGiftCardProduct($_POST);
				die();
			});
			add_action( 'cf_ajax_discp_aval_giftcard', function(){
				$giftcard=$this->load('redeem');
				if( isset( $_POST['param'] ) && $_POST['param']=="save" )
				{
					$result = $giftcard->checkGiftCard( $_POST );
					echo $result;
					die();
				}
			});
			add_action( 'cf_ajax_nopriv_discp_aval_giftcard', function(){
				$giftcard=$this->load('redeem');
				if( isset( $_POST['param'] ) && $_POST['param']=="save" )
				{
					$result = $giftcard->checkGiftCard( $_POST );
					echo $result;
					die();
				}
			});

			add_action( 'cf_ajax_cfdiscp_aval_discount', function(){
				$giftcard=$this->load('redeem');
				if( isset( $_POST['param'] ) && $_POST['param']=="save" )
				{
					$result = $giftcard->checkDiscount( $_POST );
					echo $result;
					die();
				}
			});
			add_action( 'cf_ajax_nopriv_cfdiscp_aval_discount', function(){
				$giftcard=$this->load('redeem');
				if( isset( $_POST['param'] ) && $_POST['param']=="save" )
				{
					$result = $giftcard->checkDiscount( $_POST );
					echo $result;
					die();
				}
			});
		}
		function registerShortCodes()
        {
            //product media generation
            add_shortcode('giftcard_box', function($args){
                
				$content= "";
				ob_start();
					$this->load->view('apply_gift_card');
					$content= ob_get_contents();
				ob_end_clean();
				return $content;
            });

			//product media generation
			add_shortcode('discount_box', function($args){
	
				$content= "";
				ob_start();
					$this->load->view('apply_discount');
					$content= ob_get_contents();
				ob_end_clean();
				return $content;
			});
        }
		function getMembers()
		{
			global $mysqli;
			global $dbpref;
			$table = $dbpref."quick_member";
			$qry_str="SELECT `name`,`email`,`id` FROM `$table` WHERE `email`!=''";
			$arr=array();
			$qry=$mysqli->query($qry_str);
			if($qry->num_rows>0)
			{
				while($r=$qry->fetch_assoc())
				{
					array_push($arr,$r);
				}
			}
			return $arr;

		}
	}
	new CFgiftdiscount_base();
}
?>