<?php
if (!defined("SIMPLE_FORM_PLUGIN_DIR_PATH")) {
	define("SIMPLE_FORM_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
}
if (!defined("SIMPLE_FORM_PLUGIN_URL")) {
	define("SIMPLE_FORM_PLUGIN_URL", plugins_url() . "cf_scarcity_jeet");
}
if (!class_exists("CfScarcityjeet")) {
	require_once('controller/controller.php');
	class CfScarcityjeet extends CFScarcityJeet_controller
	{
		public $pref = 'cf_scarcity_jeet_all_forms';
		public $config = false;

		function __construct()
		{
			$this->getConfig();
			$this->doInstall();
			$this->cfScarcityJeetCreateMenu();
			$this->takleAjaxRequest();
			$this->createShortCode();

			add_action("cf_head", [$this, "loadUserScript"]);
			if (isset($_GET['page']) && in_array($_GET['page'], array('cf_scarcity_jeet_all_forms'))) {
				add_action("admin_head", [$this, "addStyle"]);
				add_action("admin_footer", [$this, "addScript"]);
			}
			add_action('admin_head', function () {
				self::loadScripts();
			});
		}
		function getConfig()
		{
			if (!$this->config) {
				$file = plugin_dir_path(__FILE__);
				$fp = fopen($file . 'config.json', 'r');
				$data = json_decode(fread($fp, filesize($file . 'config.json')));
				fclose($fp);
				if (isset($data->version)) {
					$this->config = $data;
				}
			}
		}

		function loadUserScript()
		{
			echo "<link rel='stylesheet' href='" . SIMPLE_FORM_PLUGIN_URL . "/assets/css/animate.css'>";
		}
		public function addStyle()
		{
			$v = 0;
			if (isset($this->config->version)) {
				$v = $this->config->version;
			}
			echo "<link rel='stylesheet' href='" . SIMPLE_FORM_PLUGIN_URL . "/assets/css/style.css'>";
		}

		public function takleAjaxRequest()
		{
			add_action('cf_ajax_myCfScarcityJeet', function () {
				print_r($_REQUEST);
			});
		}
		function loadScripts()
		{
			$valid_pages = array('cf_scarcity_jeet_all_optins', 'cf_scarcity_jeet_all_forms', "cf_scarcity_jeet_popup_forms");
			if (isset($_GET['page']) && in_array($_GET['page'], $valid_pages)) {
				echo "<script type='text/javascript' src='assets/js/jscolor.js'></script>";
			}
		}
		public function addScript()
		{
			$v = 0;
			if (isset($this->config->version)) {
				$v = $this->config->version;
			}
			echo "<script src='" . SIMPLE_FORM_PLUGIN_URL . "/assets/js/script.js?v=" . $v . "' ></script>";
		}
		function cfScarcityJeetCreateMenu()
		{
			add_action('admin_menu', function () {
				$logo_url=plugins_url('assets/images/scarcity_logo.png', __FILE__);
				add_menu_page('cf scarcity jeet: Popup Forms', 'cf scarcity jeet', 'cf_scarcity_jeet_all_forms', function () {
					require_once('view/option.php');
				},$logo_url,'All Setups');
			});
		}

		function doInstall()
		{
			register_activation_hook(function () {
				require_once('controller/install.php');
				cfScarcityJeetDoInstall($this->pref);
			});
		}


		public function createShortCode()
		{
			global $mysqli;
			global $dbpref;
			global $themeData;
			$table = $dbpref . "scarcity_jeet";
			if ($result = $mysqli->query("SHOW TABLES LIKE '" . $table . "'")) {
				if ($result->num_rows == 1) {
					$returnOptions = $mysqli->query("SELECT `theme`,`apply_to`,`cfscarcity_page_url`,`funnels` FROM `" . $table . "`");
					if ($returnOptions->num_rows > 0) {
						$data = $returnOptions->fetch_assoc();
						$themeData = $data;
						if ($GLOBALS['themeData']['apply_to'] == 'shortcodeOnly') {
							add_shortcode("cf_scarcity_jeet", function () {
								if (isset($this->config->version)) {
									$v = $this->config->version;
									ob_start();
									require_once(SIMPLE_FORM_PLUGIN_DIR_PATH . 'view/' . $GLOBALS['themeData']['theme'] . '.php');
								}
								$data = ob_get_clean();
								return $data;
							});
						} else {
							$all_funnels = get_funnels();
							$all_urls = [];
							$install_url = get_option('install_url');
							$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
							$actual_link = rtrim($actual_link, '/\\');

							foreach ($all_funnels as $funnel_page) {
								$pages = get_funnel_pages($funnel_page['id']);
								foreach ($pages as $page_url) {
									$page = str_ireplace("@@qfnl_install_url@@", $install_url, $page_url['url']);
									array_push($all_urls, $page);
								}
							}
							if ($GLOBALS['themeData']['apply_to'] == 'funnels') {
								$f_setup = explode(",", $GLOBALS['themeData']['funnels']);
								if (in_array("all", $f_setup)) {
									add_action('cf_footer', function () {
										ob_start();
										require_once(SIMPLE_FORM_PLUGIN_DIR_PATH . 'view/' . $GLOBALS['themeData']['theme'] . '.php');
									});
								} else {
									foreach ($f_setup as  $f_value) {
										if ($f_value != "f" || $f_value != "all") {
											$pages = get_funnel_pages($f_value);
											foreach ($pages as $page) {
												$page_url = str_ireplace("@@qfnl_install_url@@", $install_url, $page['url']);

												if ($page_url == $actual_link) {
													add_action('cf_footer', function () {
														ob_start();
														require_once(SIMPLE_FORM_PLUGIN_DIR_PATH . 'view/' . $GLOBALS['themeData']['theme'] . '.php');
													});
												}
											}
										}
									}
								}
							} else {
								if (!empty($GLOBALS['themeData']['cfscarcity_page_url'])) {
									$selected_url = $GLOBALS['themeData']['cfscarcity_page_url'];
									$specific_page_url = explode("\\r\\n", rtrim($mysqli->real_escape_string($selected_url), "\\r\\n"));
									foreach ($specific_page_url as $page_url) {
										$page_url = rtrim($page_url, '/\\');

										if (in_array($page_url, $all_urls)) {
											if ($actual_link == $page_url) {
												add_action('cf_footer', function () {
													ob_start();
													require_once(SIMPLE_FORM_PLUGIN_DIR_PATH . 'view/' . $GLOBALS['themeData']['theme'] . '.php');
												});
											}
										}
									}
								}
							}
						}
					}
				}
			} else {
				$GLOBALS['themeData']['theme'] = 'theme_a';
			}
		}
	}
	new CfScarcityjeet();
}
