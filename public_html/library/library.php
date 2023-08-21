<?php
class Library
{
	public $secure;
	public $info;
	public $app_version;
	function __construct()
	{
		$this->info = array();
		$this->app_version = 'na';
	}
	function getInfo($key = '@_@')
	{
		//get info array
		$arr = $this->info;
		if ($key == '@_@') {
			return $arr;
		} else {
			if (isset($arr[$key])) {
				return $arr[$key];
			} else {
				return 0;
			}
		}
	}
	function setInfo($key, $val)
	{
		//set value to information array
		if ($key == 'app_version') {
			$this->app_version = $val;
		}
		$arr = $this->info;
		$arr[$key] = $val;
		$arr['ip'] = self::getIP();
		$arr['protocol'] = self::getProtocol();
		$this->info = $arr;
	}
	function getProtocol()
	{
		if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {

			return "https://";
		}
		return "http://";
	}
	function getIP()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if (isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	function secure()
	{
		require_once("security.php");
		$ob = new SecureClass();
		return $ob;
	}

	function loadVue($type = "dev")
	{
		//load vue as dev or prodction mode
		if (isset($GLOBALS['vue_loaded'])) {
			return '';
		}
		$GLOBALS['vue_loaded'] = 1;

		if ($type == "dev") {
			return '<script src="assets/js/vue.js"></script>';
		} else {
			return '<script src="assets/js/vue-production.js"></script>';
		}
	}
	function loadJSTranslator($relative = true)
	{

		$setup_token = time();
		if (function_exists('get_option') && get_option('qfnl_setup_token')) {
			$setup_token = get_option('qfnl_setup_token');
		}

		$version = (function_exists('qfnl_current_version') && get_option('qfnl_current_version')) ? (get_option('qfnl_current_version')) : 0;

		$relative_cache_location = '<script src="./lang/cache.js?v=' . $setup_token . '"></script>';
		$absolute_cache_location = '';

		if ($relative) {
			$script = $relative_cache_location . '
			<script src="./assets/js/html_entities.js?v=' . $version . '"></script>
			<script src="./assets/js/lang.js?v=' . $version . '"></script>';
		} else if (function_exists('get_option')) {
			$install_url = get_option('install_url');
			$cache_base = $install_url;

			$script = '<script src="' . $cache_base . '/lang/cache.js?v=' . $setup_token . '"></script>
			<script src="' . $install_url . '/assets/js/html_entities.js?v=' . $version . '"></script>
			<script src="' . $install_url . '/assets/js/lang.js?v=' . $version . '"></script>
			';
		} else {
			$script = "";
		}
		return $script;
	}
	function loadBootstrap($absolute = false)
	{
		$main_url = "";

		if (function_exists('get_option')) {
			if ($absolute && get_option('install_url')) {
				$main_url = rtrim(get_option('install_url'), "/");
				$main_url .= "/";
			}
		}

		return '
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!--@cfbootstrap@-->
		<link rel="stylesheet" href="'.$main_url.'assets/bootstrap/css/bootstrap.min.css">
		<script src="'.$main_url.'assets/js/jquery-3.4.1.min.js"></script>
		<script src="'.$main_url.'assets/bootstrap/js/bootstrap.bundle.min.js"></script>
		<!--/@cfbootstrap@-->';
	
	}
	function loadJquery()
	{
		return '<script src="assets/js/jquery-3.4.1.min.js"></script>';
	}
	function loadScript($str)
	{
		//loadspecificscript
		if (isset($GLOBALS['loaded_scripts']) && in_array($str, $GLOBALS['loaded_scripts'])) {
			return '';
		} else {
			if (!isset($GLOBALS['loaded_scripts'])) {
				$GLOBALS['loaded_scripts'] = array($str);
			} else {
				array_push($GLOBALS['loaded_scripts'], $str);
			}
		}

		$bs = "";
		if ($str == "request") {
			$bs = "<script src='assets/js/node_modules/js-base64/base64.js?version=" . $this->app_version . "'></script>";
		}
		return $bs . "<script src='assets/js/" . $str . ".js?version=" . $this->app_version . "'></script><script src='assets/js/sweetalert2.all.min.js'></script>";
	}
	function loadStyle($str)
	{
		//load specific css
		return "<link rel='stylesheet' href='assets/css/" . $str . ".css?version=" . $this->app_version . "'></link>
				<link rel='stylesheet' href='assets/css/sweetalert2.min.css'></link>
				";
	}
	function loadChartJs()
	{
		$chart = self::loadScript('chart');
		return '
<link href="assets/css/anychart-ui.min.css" rel="stylesheet" type="text/css">
<link href="assets/css/anychart-font.min.css" rel="stylesheet" type="text/css">
<script src="assets/js/anychart-base.min.js"></script>
<script src="assets/js/anychart-ui.min.js"></script>
<script src="assets/js/anychart-exports.min.js"></script>' . $chart;
	}
	function loadUser()
	{
		//load user data
		require_once("user_data.php");
		self::setInfo('load', $this);
		$info_arr = self::getInfo();
		$ob = new Userdata($info_arr);
		//self::setInfo('valid_purchase',$ob->userDataIsValid(0));
		return $ob;
	}
	function loadFunnel()
	{
		//load funnel control library
		require_once("funnel.php");
		self::setInfo('load', $this);
		$ob = new Funnel(self::getInfo());
		return $ob;
	}
	function loadFunnelCloner()
	{
		require_once("Clone_funnel.php");
		self::setInfo('load', $this);
		$ob = new Clone_funnel(self::getInfo());
		return $ob;
	}
	function view($title, $header, $content, $footer, $data_arr = array(), $plugin_page = false)
	{
		//load view
		$info = self::getInfo();
		if (!get_option('is_valid_user')) {
			$content = "createuser.php";
		}
		$plugin_menues = array();
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
			$plugin_loader->processInit('admin_init');
			$plugin_menues = $plugin_loader->processAdminMenu();
		}
		require_once($info['view_dir'] . "/view_container.php");
	}
	function loadOptin()
	{
		//load optin
		require_once('optin_control.php');
		self::setInfo('load', $this);
		$data = self::getInfo();
		$ob = new Optincontrol($data);
		return $ob;
	}
	function loadMember()
	{
		require_once("membership.php");
		$info = self::getInfo();
		$ob = new Membershipqfnl($info);
		return $ob;
	}
	function createlist()
	{
		require_once('savelist.php');
		self::setInfo('load', $this);
		$dbinfo = self::getInfo();
		$data = new CreateList($dbinfo);
		return $data;
	}
	function loadSMTP()
	{
		require_once("smtp_control.php");
		$info = self::getInfo();
		$ob = new Smtpcontrol($info);
		return $ob;
	}
	function loadSMTPData()
	{
		require_once("smtp.php");
		//load smtp
		$info_arr = self::getInfo();
		$ob = new Smtp($info_arr);
		return $ob;
	}
	function isPlusUser()
	{
		$type = 0;
		if (function_exists('get_option') && get_option('valid_user_data')) {
			$data = json_decode(cf_enc(get_option('valid_user_data'), 'decrypt'));
			if (isset($data->product_permissions) && $data->product_permissions == 'pro') {
				$type = 1;
			}
		}
		return $type;
	}
	function loadSell()
	{
		require_once("sell_control.php");
		self::setInfo('load', $this);
		$info = self::getInfo();
		$ob = new Sellcontrol($info);
		return $ob;
	}
	function loadSequence()
	{
		require_once("sequence.php");
		self::setInfo('load', $this);
		$info = self::getInfo();
		$obj = new Sequence($info);
		return $obj;
	}
	function loadMailComposer()
	{
		require_once("compose_mail.php");
		self::setInfo('load', $this);
		$info = self::getInfo();
		return new ComposeMail($info);
	}
	function loadPayment()
	{
		require_once("payment.php");
		$info = self::getInfo();
		$object = new payment($info);
		return $object;
	}
	function loadScheduler()
	{
		require_once("schedule_api.php");
		$info = self::getInfo();
		$ob = new QmlrSchedular($info);
		return $ob;
	}
	function loadCbMailer($data)
	{
		self::setInfo('load', $this);
		$info = self::getInfo();
		require_once("ajaxmailsend.php");
		sendSequencedMail($info, $data);
	}
	function loadmultiuser()
	{
		//load multi user data
		require_once("multiuser.php");
		self::setInfo('load', $this);
		$info_arr = self::getInfo();
		$ob = new Multisuer($info_arr);
		return $ob;
	}
	function loadSettings()
	{
		//load setting
		require_once("settings.php");
		self::setInfo('load', $this);
		$info = $this->getInfo();
		$ob = new Settings($info);
		return $ob;
	}
	function loadUnsubscribePage()
	{
		$info = self::getInfo();
		require_once($info['view_dir'] . "/do_unsubscribe.php");
	}
	function loadGdpr()
	{
		require_once("gdpr.php");
		self::setInfo('load', $this);
		$info = $this->getInfo();
		$ob = new Gdprcontrol($info);
		return $ob;
	}
	function loadAutoUpdater()
	{
		require_once("autoupdate.php");
		$info = $this->getInfo();
		$ob = new Autoupdate($info);
		return $ob;
	}
	function loadIntegrations()
	{
		$info = self::getInfo();
		require_once("integrations.php");
		$ob = new Integrations($info);
		return $ob;
	}
	function isProUser()
	{
		$type = 0;
		if (function_exists('get_option') && get_option('valid_user_data')) {
			$data = json_decode(cf_enc(get_option('valid_user_data'), 'decrypt'));
			if (isset($data->product_permissions) && $data->product_permissions == 'pro') {
				$type = 1;
			}
		}
		return $type;
	}
	function loadFourHunderdFour()
	{
		$basedir = self::getInfo('base_dir');
		if (!cf_dir_exists($basedir)) {
			$temp_dir = str_replace('\\', '/', __FILE__);
			$basedir = str_replace('/library/library.php', '', $temp_dir);
		}
		$load_template = (isset($_GET['loadtemplate'])) ? $_GET['loadtemplate'] : get_option('default_404_page_template');
		$file = $basedir . "/assets/default-404-template/" . $load_template . "/index.php";
		$file = str_replace("//", '/', $file);
		require_once($file);
	}
	function cloneURL()
	{
		require_once("clone_url.php");
		self::setInfo('load', $this);
		$ob = new Clone_url(self::getInfo());
		return $ob;
	}
	function loadPlugins()
	{
		require_once("Plugin.php");
		self::setInfo('load', $this);
		$ob = new Plugin(self::getInfo());
		return $ob;
	}
	function loadMedia()
	{
		require_once('media_control.php');
		self::setInfo('load', $this);
		$ob = new Media_control(self::getInfo());
		return $ob;
	}
	function loadMediaBox()
	{
		$header = $this->loadVue();
		$header .= $this->loadScript('vuex');
		$header .= $this->loadScript('request');
		$header .= $this->loadStyle('style');

		$body = "<div id='cf_media_app' style='display:none;'>
		<media_app v-bind:view='view' use_mode='external'></media_app>
		</div>";

		$footer = $this->loadScript('media_storage');
		$footer .= $this->loadScript('media_components/app');
		$footer .=$this->loadScript('media_components/plupload.full.min');
		$footer .= $this->loadScript('media');

		return $header . $body . $footer;
	}
}
