<?php

if (!defined("CF_SOCIAL_SHARE_PLUGIN_DIR_PATH")) {
    define("CF_SOCIAL_SHARE_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
}

if (!defined("CF_SOCIAL_SHARE_PLUGIN_URL")) {
    define("CF_SOCIAL_SHARE_PLUGIN_URL", plugin_dir_url(__FILE__));
}
if (!defined("CF_SOCIAL_SHARE_URL")) {
    define("CF_SOCIAL_SHARE_URL", plugins_url(__FILE__));
}

if (!class_exists('CFsocial_base')) {
    require_once('controller/controller.php');
    class CFsocial_base extends CFSOCIAL_controller
    {
        var $config = false;
        var $method = 'cf_social_share';


        function __construct()
        {
            self::getConfig();
            self::createMenu();
            self::settingFormAjax();
            self::socialInstallTable();
            self::addShortCode();


            add_action("cf_head", [$this, "loadUserScript"]);

            if (isset($_GET['page']) && in_array($_GET['page'], array('cf_socialforms', 'cf_social_share_setting'))) {
                add_action("admin_head", [$this, "addStyle"]);
                add_action("admin_head", [$this, "addScript"]);
            }
        }

        function loadUserScript()
        {

            echo "<link rel='stylesheet' href='" . CF_SOCIAL_SHARE_PLUGIN_URL . "assets/css/user.css'>";
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

        function createMenu()
        {
            add_action('admin_menu', function () {
                $logo_url = plugins_url('assets/img/logo.png', __FILE__);

                add_menu_page('Add Social', 'Social Share', 'cf_socialforms', function () {
                    require_once('view/add_forms.php');
                }, $logo_url, 'Add Social Networks');

                add_submenu_page('cf_socialforms', 'Social Share Setting', 'Social share Setting', 'cf_social_share_setting', function () {
                    require_once('view/social_setting.php');
                });
            });
        }

        public function addStyle()
        {
            $v = 0;
            if (isset($this->config->version)) {
                $v = $this->config->version;
            }
            echo "<link rel='stylesheet' href='" . CF_SOCIAL_SHARE_PLUGIN_URL . "/assets/css/style.css?v=" . $v . "'>";
            echo "<link rel='stylesheet' href='" . CF_SOCIAL_SHARE_PLUGIN_URL . "assets/css/sweetalert2.min.css?v=" . $v . "'>";
        }

        public function addScript()
        {
            $v = 0;
            if (isset($this->config->version)) {
                $v = $this->config->version;
            }
            $valid_pages = array('cf_socialforms', 'cf_social_share_setting');
            if (isset($_GET['page']) && in_array($_GET['page'], $valid_pages)) {

                echo "<script src='assets/js/node_modules/js-base64/base64.js?v=" . get_option('qfnl_current_version') . "'></script>";
                echo "<script src='assets/js/request.js?v=" . get_option('qfnl_current_version') . "'></script>";
            }
            echo "<script src='" . CF_SOCIAL_SHARE_PLUGIN_URL . "/assets/js/script.js?v=" . $v . "' ></script>";
            echo "<script src='" . CF_SOCIAL_SHARE_PLUGIN_URL . "/assets/js/jscolor.js?v=" . $v . "' ></script>";
            echo "<script src='" . CF_SOCIAL_SHARE_PLUGIN_URL . "assets/js/sweetalert2.all.min.js?v=" . $v . "' ></script>";
        }

        function settingFormAjax()
        {
            add_action('cf_ajax_cf_social_add_action', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->AddSocialData($_REQUEST);
            });

            add_action('cf_ajax_cf_social_preview', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->previewData($_REQUEST);
            });

            add_action('cf_ajax_cf_social_update', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->updateSocialData($_REQUEST);
            });

            add_action('cf_ajax_cf_social_delete', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->DeteteData($_REQUEST);
            });
            add_action('cf_ajax_cf_social_setting', function () {
                $forms_ob = $this->load('setting_controller');
                $forms_ob->addSocialSetting($_REQUEST);
            });
        }

        function socialInstallTable()
        {
            register_activation_hook(function () {
                require_once('controller/social_install.php');
                cfsocialsharetable();
            });
        }

        function addShortCode()
        {

            add_shortcode('cf_Social_share', function ($args) {
                $content = "";
                ob_start();
                require_once('view/shortcode.php');
                echo "<link rel='stylesheet'  href='" . get_option('install_url') . "/assets/fontawesome/css/all.min.css'>";
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            });
        }
    }
    new CFsocial_base();
}
