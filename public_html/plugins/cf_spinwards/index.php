<?php
if (!defined("EXIT_FORM_PLUGIN_DIR_PATH")) {
	define("EXIT_FORM_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
}
if (!defined("EXIT_FORM_PLUGIN_URL")) {
	define("EXIT_FORM_PLUGIN_URL", plugins_url() . "cf_spinner");
}

if (!class_exists('CFSpinner')) {
	require_once('controller/controller.php');
	class CFSpinner extends CFSpinner_controller
	{
		var $pref = 'CFSpinner_';
		var $config = false;
		function __construct()
		{
			self::getConfig();
			self::createMenu();
			self::doInstall();
			self::createShortCode();
			self::getFormrequest();
			self::takleAjaxRequest();
			add_action('admin_head', function () {
				self::loadScripts();
			});
			add_action('admin_init', function () {
				$optin_controller = $this->load('forms_control');
				$optin_controller->doExportToCSV();
				$optin_controller->sendmail();
			});
			if (isset($_GET['page']) && in_array($_GET['page'], array('CFSpinner_allwheels', 'CFSpinner_settingform'))) {
				add_action("admin_footer", [$this, "addScript"]);
			}

			add_action('cf_head', function () {
				self::loadCSSinUSer();
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
		function doInstall()
		{
			register_activation_hook(function () {
				require_once('controller/install.php');
				cfspinnerinstall($this->pref);
			});
		}
		function loadCSSinUSer()
		{
			$plugin_url = plugin_dir_url(__FILE__);
			$plugin_url = rtrim($plugin_url, "/");
			$plugin_url2 = plugin_dir_url(dirname(__FILE__, 2));
			$plugin_url2 = rtrim($plugin_url2, "/");
			echo "<script src='" . $plugin_url . "/assets/js/Winwheel.js' type='text/javascript'></script>";
			echo  "<script src='" . $plugin_url . "/assets/js/TweenMax.min.js' type='text/javascript'></script>";
			echo "<link rel='stylesheet' href='" . $plugin_url . "/assets/css/bootstrap.min.css'>";
			echo "<link rel='stylesheet' href='" . $plugin_url . "/assets/css/style.css'>";
			echo  "<script src='" . $plugin_url . "/assets/js/jquery-3.5.1.min.js' type='text/javascript'></script>";
			echo  "<script src='" . $plugin_url . "/assets/js/multi_inputs.js' type='text/javascript'></script>";

			echo "<script src='" . $plugin_url . "/assets/js/bootstrap.min.js' type='text/javascript'></script>";
			echo  "<script src='" . $plugin_url . "/assets/js/main.js' type='text/javascript'></script>";
			echo "<link rel='stylesheet' href='" . $plugin_url . "/assets/css/sweetalert2.min.css'>";
			echo "<script src='" . $plugin_url . "/assets/js/sweetalert2.all.min.js' type='text/javascript'></script>";
		}

		function createMenu()
		{
			add_action('admin_menu', function () {
				add_menu_page('CF Spinawards: lucky wheel', 'CF Spinawards', 'CFSpinner_allwheels', function () {
					require_once('views/allwheels.php');
				}, '', 'All Wheels');
				add_submenu_page('CFSpinner_allwheels', 'CF Spinawards: setting form', 'Wheel Form Setting', 'CFSpinner_settingform', function () {
					require_once('views/settingform.php');
				});
			});
		}


		public function createShortCode()
		{
			add_shortcode("show_wheel", function ($params) {
				if (isset($params['id'])) {

					$v = 0;
					if (isset($this->config->version)) {
						$v = $this->config->version;
					}

					$id = $params['id'];
					ob_start();
					$forms_ob = $this->load('forms_control');
					$forms_ob->getWheelui($id, $v);
					$data = ob_get_clean();
					return $data;
				}
			});
		}
		public function takleAjaxRequest()
		{
			add_action('cf_ajax_spinnerwheelAjax', function () {
				$forms_ob = $this->load('forms_control');
				$forms_ob->getAjaxRequest($_REQUEST);
			});
		}

		public function getFormrequest()
		{
			add_action('cf_ajax_spinnerwheelform', function () {
				$forms_ob = $this->load('forms_control');
				$forms_ob->getFormrequestdata($_REQUEST);
			});

			add_action('cf_ajax_settingform', function () {
				$forms_ob = $this->load('forms_control');
				$forms_ob->Storesetting($_REQUEST);
				//  print_r($_REQUEST);
			});

			add_action('cf_ajax_spinnerwheelprice', function () {
				$forms_ob = $this->load('forms_control');
				$forms_ob->updatePriceuser($_REQUEST);
			});
		}
		function loadScripts()
		{
			$valid_pages = array('CFSpinner_allwheels', 'CFSpinner_settingform');
			if (isset($_GET['page']) && in_array($_GET['page'], $valid_pages)) {
			}
			$plugin_url = plugin_dir_url(__FILE__);
			$plugin_url = rtrim($plugin_url, "/");
			echo "<script src='assets/js/node_modules/js-base64/base64.js?v=" . get_option('qfnl_current_version') . "'></script>";
			echo "<script src='assets/js/request.js?v=" . get_option('qfnl_current_version') . "'></script>";
			echo "<script src='" . $plugin_url . "/assets/js/Winwheel.js' type='text/javascript'></script>";
			echo  "<script src='" . $plugin_url . "/assets/js/TweenMax.min.js' type='text/javascript'></script>";
			echo  "<script src='" . $plugin_url . "/assets/js/multi_inputs.js' type='text/javascript'></script>";

			echo "<script src='" . $plugin_url . "/assets/js/main.js' type='text/javascript'></script>";
			echo "<link rel='stylesheet' href='" . $plugin_url . "/assets/css/style.css'>";
		}


		public function addScript()
		{
			$v = 0;
			if (isset($this->config->version)) {
				$v = $this->config->version;
			}
			echo "
			<script type='text/javascript' src='assets/js/jscolor.js'></script>
			<script type='text/javascript' src='assets/js/tinymce/jquery.tinymce.min.js'></script>
			<script type='text/javascript' src='assets/js/tinymce/tinymce.min.js'></script>
			";
		}
	}
	new CFSpinner();
}
