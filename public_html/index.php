<?php
// 'Access-Control-Allow-Origin' header is present on the requested resource" связана с политикой Same-Origin веб-безопасности, которая предотвращает запросы к ресурсам с других доменов, если сервер не предоставляет соответствующие заголовки CORS 
header("Access-Control-Allow-Origin: *");
$current_base_dir = str_replace("\\", "/", __DIR__);
require_once($current_base_dir . "/gcp/gcp.php");
require_once($current_base_dir . '/library/lang.php');
session_start();
require_once("library/esc_html.php");
if (isset($_GET["cfhttp"])) {
	foreach ($_GET as $cfhttp_data_index => $cfhttp_data_val) {
		$_GET[$cfhttp_data_index] = js_html_entity_decode(base64_decode($cfhttp_data_val));
		$_REQUEST[$cfhttp_data_index] = js_html_entity_decode(base64_decode($cfhttp_data_val));
	}
}
if (isset($_POST["cfhttp"])) {
	foreach ($_POST as $cfhttp_data_index => $cfhttp_data_val) {
		$_POST[$cfhttp_data_index] = js_html_entity_decode(base64_decode($cfhttp_data_val));
		$_REQUEST[$cfhttp_data_index] = js_html_entity_decode(base64_decode($cfhttp_data_val));
	}
}

$auth_pages = array('create_config', 'login', 'logout', 'forgot_password');
if (!is_file($GLOBALS["config_file"])) {
	if ($_GET['page'] !== 'create_config') {
		header("Location: index.php?page=create_config");
		//exit;
	}
}


//print_r($_SESSION);
class FunnelIndex
{
	var $load;
	var $view_dir;
	var $asset_dir;
	var $config_pages;
	var $base_dir;
	function __construct()
	{
		
		date_default_timezone_set('UTC');
		$dir = str_replace("\\", '/', __DIR__);
		$this->base_dir = rtrim($dir, '/');
		
		require_once($this->base_dir . "/library/library.php");

		$this->load = new Library();

		$this->view_dir = $dir . "/views";
		$this->asset_dir = $dir . "/assets";

		$this->config_pages = array('create_config', 'login', 'forgot_password', 'do_payment', 'schedule_api', 'mail_track', 'do_unsubscribe', 'do_redirect', 'data_requests', 'load_static_scripts', 'api_request', 'show_cf_narand', 'mmbr_lgout', 'do_payment_execute', 'ajax', 'callback_api', 'schedule_api_runserver');

		if (is_file($GLOBALS["config_file"])) {
			require_once($GLOBALS["config_file"]);
			doInitRoute();

			//language_handler_script
			$executable_language = (get_option('app_language'));
			if (!$executable_language) {
				$executable_language = "lang_english_en";
				add_option('app_language', $executable_language);
			}
			getCachedTranslation($executable_language);

			$this->load->setInfo('mysqli', $mysqli);
			$this->load->setInfo('dbpref', $dbpref);
			$this->load->setInfo('base_dir', $dir);
			if (function_exists('get_option') && get_option('qfnl_current_version')) {
				$this->load->setInfo('app_version', get_option('qfnl_current_version'));
			}

			$user = $this->load->loadUser();
			$hasuser = $user->getAllUsers();

			//print_r($hasuser);

			if ($hasuser) {
				if (isset($_GET['page'])) {
					if ($_GET['page'] == 'create_config') {
						header('Location: index.php?page=login');
					}
				}
				$this->load->setInfo('view_dir', $this->view_dir);
			} else {
				unlink($GLOBALS["config_file"]);
				header("location: index.php");
				//exit;
			}
		}
		
		if (function_exists('get_option')) {
			//this block should be present at the end of this constructor
			
			$main_load = $this->load;
			require_once($this->base_dir . "/library/plugin_options.php");
			 //echo "We are here goooooo111"; exit; 
		}
		
	}
	function loadPage()
	{
		//load page
		$page = 'index';
		$load = $this->load->secure();

		if (isset($_GET['page'])) {
			$page = $_GET['page'];

			if (!in_array($page, $this->config_pages)) {
				$userdetail = $this->load->loadUser();
				if (!$userdetail->isLoggedin()) {
					if (!in_array($_GET['page'], $this->config_pages)) {
						$temp_index_site_token = get_option('site_token');

						$get_page = $_GET['page'];
						unset($_GET['page']);

						foreach ($_GET as $gt_index => $gt_value) {
							$get_page .= "&" . $gt_index . "=" . $gt_value;
						}

						$_SESSION['last_visited_page' . $temp_index_site_token] = $get_page;
					}
					header('Location: index.php?page=login');
					die();
				} elseif (!$userdetail->hasPermission()) {
					if($_SESSION['access' . get_option('site_token')]==='admin'&&$page!=='new_admin') {
					header('Location: index.php?page=no_permission');
					die();
					}
					
				}
			}
			$GLOBALS['inside_administration_page'] = true;
		}

		if (count($_GET) < 1 || !isset($_GET['page'])) {
			$hasfunnelinbase_ob = $this->load->loadFunnel();
			if ($hasfunnelinbase_ob->do_route && count($_GET) < 1) {
				$hasfunnelsId = $hasfunnelinbase_ob->hasFunnelInBase();
				if ($hasfunnelsId) {
					self::loadFunnelView($hasfunnelsId, 1);
				} else {
					header("Location: " . get_option('install_url') . "/index.php?page=login");
				}
			} else {
				require_once($this->base_dir . '/controller/router.php');
			}
		} else {
			require_once($this->base_dir . '/controller/router.php');
		}
	}
	function loadFunnelView($hasfunnelsId = 0, $autoloadbase_index = 0)
	{
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			//init code
			
			$plugin_loader = $GLOBALS['plugin_loader'];
			$plugin_loader->processInit();
		}
		$ob = $this->load;
		$mysqli = $this->load->getInfo('mysqli');
		$dbpref = $this->load->getInfo('dbpref');

		$index_exists = 0;

		$parsed_url = parse_url($_SERVER['REQUEST_URI']);
		if (isset($parsed_url['query'])) {

			parse_str($parsed_url['query'], $get_query_args);
			foreach ($get_query_args as $get_query_args_index => $get_query_args_data) {
				$_GET[$get_query_args_index] = $get_query_args_data;
			}
		}

		if (isset($_GET['get_funnel']) || $autoloadbase_index == 1) {
			if ($autoloadbase_index === 1) {
				$_GET['get_funnel'] = "";
			}
			 $_GET['get_funnel'] = rtrim($_GET['get_funnel'], "/");

			$isjscss = 0;

			$required_file = "";

			if (preg_match("/(\.(js|css))+$/", $_GET['get_funnel'])) {
				++$isjscss;

				$required_file .= "public_funnels/" . $_GET['get_funnel'];


				if (is_file($required_file)) {
					$currentscriptextension = pathinfo($required_file);
					if ($currentscriptextension['extension'] == 'js') {
						header('content-type: application/javascript');
					} else {
						header('content-type: text/' . $currentscriptextension['extension']);
					}
				}
			} else {
			
				$getMembershipDataLoad = self::membershipdataload($hasfunnelsId);
				$get_funnel_page = $_GET['get_funnel'];
				if ($getMembershipDataLoad[1] === "register") {
					$get_funnel_page = $getMembershipDataLoad[2] .'/'. $getMembershipDataLoad[0];
					$get_funnel_page = str_replace('//','/', $get_funnel_page);
				}
				$required_file .= "public_funnels/" . $get_funnel_page . "/index.php";	
				// die($required_file);
			}	
			$required_file = str_replace("//", "/", $required_file);
			if (is_file($required_file)) {
				++$index_exists;
/*				echo $required_file;
				$allFunctions = get_defined_functions();
				$userFunctions = $allFunctions['user'];
				foreach ($userFunctions as $functionName) {
					echo $functionName . "<br>";
				}*/
				echo $required_file." 8888888888 get_funnel<br> ";
				require_once($required_file);
			}
		}
		if ($index_exists < 1) {

			//echo "<h1>--Something Wrong--</h1>";
			$this->load->loadFourHunderdFour();
		}
	}

	function membershipdataload($funnelId)
	{

		$mysqli = $GLOBALS['mysqli'];
		$dbpref = $GLOBALS['dbpref'];
		$get_explode = explode("/", $_GET['get_funnel']);
		$page_name = count($get_explode) == 1 ? explode("@-", $get_explode[0])[0] : explode("@-", $get_explode[1])[0];
		$pagetable = $dbpref . "quick_pagefunnel";
		$funneltable = $dbpref . "quick_funnels";
		if ($funnelId === 0) {
			$filename = $mysqli->query("SELECT `id`,`flodername` FROM `" . $funneltable . "` WHERE `flodername`='@@qfnl_install_dir@@/" . $get_explode[0] . "' OR `flodername`='@@qfnl_install_dir@@'");
			if ($filename) {
				if ($res = $filename->fetch_object()) {
					$funnelId = $res->id;
					$floderName = str_replace(['@@qfnl_install_dir@@/','@@qfnl_install_dir@@'],'',$res->flodername);					
				}
			}
		}

		$query = "";
		$category = "optin";
		$qry = $mysqli->query("SELECT `filename`,`category` FROM `" . $pagetable . "` WHERE `funnelid`=" . $funnelId . " AND filename LIKE '" . $page_name . "%'");
		if ($qry) {
			if ($res = $qry->fetch_object()) {
				$query = $res->filename;
				$category = $res->category;
			}
		}

		return [$query, $category, $floderName];
	}
}

$ob = new FunnelIndex();
if (!isset($_GET['funnel_view']) || !(get_option('qfnl_router_mode') == '1') || isset($_GET['cf-admin']) || isset($_GET['cf-login'])) {
	if (isset($_GET['cf-admin']) || isset($_GET['cf-login'])) {
		$currenturl = getProtocol();
		$currenturl .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/index.php?page=login";
		$currenturl = preg_replace("/(cf-login|cf-admin)+(\/)*/", "", $currenturl);
		 $currenturl;
		header("location: " . $currenturl . "");
	}
	$ob->loadPage();
} elseif (isset($_GET['funnel_view'])) {
	$ob->loadFunnelView(0);
}
