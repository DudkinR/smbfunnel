<?php
if( !defined("EXIT_FORM_RESPO_PLUGIN_DIR_PATH")  ) {
	define( "EXIT_FORM_RESPO_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}

if( !defined("EXIT_FORM_RESPO_PLUGIN_URL") ) {
    define("EXIT_FORM_RESPO_PLUGIN_URL", plugin_dir_url( __FILE__ ));
}

if (!class_exists('CFRespo_base')) {
    require_once('controller/controller.php');
    class CFRespo_base extends CFRespo_controller {
        var $pref = 'cfrespo_';
        var $config = false;

        function __construct() {

            self::getConfig();
            self::doInstall();
            self::createMenu();
            self::settingFormAjax();

            add_action('admin_init',function(){
				$optin_controller=$this->load('optin_control');
                $optin_controller->doExportToCSV();
				$optin_controller->storeLeadsRespo();
			});

            add_action('init',function(){
				$optin_controller=$this->load('forms_control');
				$optin_controller->initFormSubmit();
			});

            //admin_headers
			add_action('admin_head',function(){
				self::loadScripts();
			});

            if(isset($_GET['page']) && in_array($_GET['page'],array('cf_popup_all_forms', 'cf_new_form', 'cf_popup_created_forms') )) {
                add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_head",[ $this, "addScript" ] );
			}

            self::addShortCode();

            add_action('cf_footer',function(){
				$forms_ob = $this->load('forms_control');
				$v=0;
				if(isset($this->config->version)) {	
					$v=$this->config->version;
				}
				$forms_ob->loadGlobalForms($v);
			});
        }

        function getConfig() {
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

        function doInstall() {
            register_activation_hook(function () {
                require_once('controller/tbl_install.php');
                cf_respo_dbTable($this->pref);
            });
        }

        function createMenu() {
            add_action('admin_menu', function () {
                add_menu_page('CF Popup: Lead Generator', 'CF Popup', 'cf_popup_all_forms', function () {
                    require_once('view/respo_all_forms.php');
                }, plugins_url('assets/img/logo.png', __FILE__), 'All Setting');


                if (isset($_GET['page']) && $_GET['page'] == 'cf_new_form') {
                    add_submenu_page('cf_popup_all_forms', 'Add New Form', 'Add New Form', 'cf_new_form', function () {
                        require_once('view/respo_popup_settings.php');
                    });
                }

                add_submenu_page('cf_popup_all_forms', 'All Created Forms', 'Optins', 'cf_popup_created_forms', function () {
                    require_once('view/respo_popup_details.php');
                });
            });
        }


        function addStyle() {
            $v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "<link rel='stylesheet' href='".EXIT_FORM_RESPO_PLUGIN_URL."/assets/css/main.css'>
			<script src='".EXIT_FORM_RESPO_PLUGIN_URL."/assets/js/multi_inputs.js?v=".$v."' ></script>";
        }

        function addScript() {
            $v=0;
			if(isset($this->config->version)) {
				$v=$this->config->version;
			}

            echo "
            <script type='text/javascript' src='assets/js/jscolor.js'></script>

			<script type='text/javascript' src='assets/js/tinymce/jquery.tinymce.min.js'></script>
			<script type='text/javascript' src='assets/js/tinymce/tinymce.min.js'></script>
            <script src='".EXIT_FORM_RESPO_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>
            ";
        }

        function loadScripts() {
			$valid_pages=array('cf_popup_all_forms', 'cf_new_form', 'cf_popup_created_forms');
			if(isset($_GET['page']) && in_array($_GET['page'], $valid_pages)) {
				echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";

				echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
			}
		}

        function settingFormAjax() {
            add_action( 'cf_ajax_settingsPageData', function(){
                $forms_ob=$this->load('forms_control');
                $forms_ob->getAjaxRequest( $_REQUEST );
            });
        }

        function addShortCode() {
            add_shortcode( "cf_popup", function($params){
				if(isset($params['id'])) {
                    $v=0;
				    if(isset($this->config->version)) {
				    	$v=$this->config->version;
				    }

				    $id = $params['id'];
				    ob_start();
				    $forms_ob = $this -> load('forms_control');
				    $forms_ob -> getFormUI($id,$v);
				    $data = ob_get_clean();
				    return $data;
				}
			});
        }

        function setTheContent( $id ) {
            add_filter( 'the_content', function($content, $settings, $args){
                $content.="<script>alert('Hello');</script>";
                return $content;
            });
        }
    }
    new CFRespo_base();
}
