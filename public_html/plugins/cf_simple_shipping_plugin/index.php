<?php

if (!defined("EXIT_FORM_CFSIMPLE_SHIPPING_DIR_PATH")) {
    define("EXIT_FORM_CFSIMPLE_SHIPPING_DIR_PATH", plugin_dir_path(__FILE__));
}

if (!defined("EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL")) {
    define("EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL", plugin_dir_url(__FILE__));
}

if (!class_exists('CF_Simple_Shipping_base')) {
    require_once('controller/controller.php');
    class CF_Simple_Shipping_base extends CF_Simple_Shipping_controller
    {
        var $config = false;

        function __construct()
        {
            self::getConfig();
            self::createMenu();
            self::doTableInstall();
            $this->shippingAjax();

            do_session_start();


            self::addShortCode();
            add_action('cf_footer', function ($data) {
                $this->loadUserSideScript();
            });

            add_filter('the_checkout_data', function ($current_orderer_detail, $info) {


                if (isset($current_orderer_detail['total_price'])) {
                    if (isset($_SESSION['selected_method_cost']))
                        $current_orderer_detail['shipping_charge'] =  $_SESSION['selected_method_cost'];
                    $current_orderer_detail['total_price'] = $current_orderer_detail['total_price'] + $current_orderer_detail['shipping_charge'];
                } else {
                    if (isset($_SESSION['selected_method_cost']))
                        $current_orderer_detail['sheepingcharge'] =  $_SESSION['selected_method_cost'];
                    $current_orderer_detail['total'] = $current_orderer_detail['total'] + $current_orderer_detail['sheepingcharge'];
                }
                return $current_orderer_detail;
            });

            add_action("cf_sale", function ($data, $info) {
                if ($data['success'] == 1 || $data['success'] == "1") {
                    $forms_ob = $this->load('form_controller');
                    $forms_ob->order_history($data);
                }
            });

            if (isset($_GET['page']) && in_array($_GET['page'], array('all_shipping', 'send_shipping_update', 'message_templates'))) {
                add_action("admin_head", [$this, "addStyle"]);
                add_action("admin_head", [$this, "addScript"]);
            }
            global $mysqli;
            global $dbpref;
            $mail_table = $dbpref . 'mail_templates';
            $check = "SELECT `id` FROM `" . $mail_table . "`";
            if ($mysqli->query($check) == TRUE) {
                $sql_row = $mysqli->query("SELECT `id` FROM `" . $mail_table . "`");
                if (mysqli_num_rows($sql_row) == 0) {
                    self::mail_templates();
                }
            }
        }
        function addShortCode()
        {
            add_shortcode('delivery_type', function ($args) {
                $content = "";
                ob_start();
                require_once('view/choose_delivery_type.php');
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
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
        function createMenu()
        {
            add_action('admin_menu', function () {
                add_menu_page(t('CF Simple Shipping: Manage Shipping'), t('CF Simple Shipping'), 'all_shipping', function () {
                    require_once('view/shipping_options.php');
                }, plugins_url('assets/img/logo.png', __FILE__), t('Shipping Options'));
                if (isset($_GET['email'])) {
                    add_submenu_page('all_shipping', t('Send Shipping Update'), t('Send Shipping Update'), 'send_shipping_update', function () {
                        require_once('view/message.php');
                    });
                } else if (isset($_GET['orderid'])) {
                    add_submenu_page('all_shipping', t('Send Shipping Update'), t('Send Shipping Update'), 'send_shipping_update', function () {
                        require_once('view/order.php');
                    });
                } else {
                    add_submenu_page('all_shipping', t('Send Shipping Update'), t('Send Shipping Update'), 'send_shipping_update', function () {
                        require_once('view/send_shipping_update.php');
                    });
                }

                add_submenu_page('all_shipping', t('Message Templates'), t('Message Templates'), 'message_templates', function () {
                    require_once('view/message_templates.php');
                });
            });
        }

        function addStyle()
        {
            $v = 0;
            if (isset($this->config->version)) {
                $v = $this->config->version;
            }
            echo "
            <link rel='stylesheet' href='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/css/style.css'>
            <link rel='stylesheet' href='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/css/sweetalert2.min.css'>
            <link rel='stylesheet' href='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/css/select2.min.css'>
            ";
        }

        function addScript()
        {
            $v = 0;
            if (isset($this->config->version)) {
                $v = $this->config->version;
            }
            echo "
            <script type='text/javascript' src='assets/js/jscolor.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/script.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/sweetalert2.all.min.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/multi_inputs.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/drag_n_drop.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/crs.min.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/handlebars.js'></script>
            <script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/select2.min.js'></script>
            <script src='assets/js/node_modules/js-base64/base64.js?v=" . get_option('qfnl_current_version') . "'></script>
            <script src='assets/js/request.js?v=" . get_option('qfnl_current_version') . "'></script>
            ";
        }

        function loadUserSideScript()
        {
            $v = 0;
            if (isset($this->config->version)) {
                $v = $this->config->version;
            }
            echo "<script type='text/javascript' src='" . EXIT_FORM_CFSIMPLE_SHIPPING_PLUGIN_URL . "assets/js/user_script.js'></script>";
        }



        function doTableInstall()
        {
            register_activation_hook(function () {
                require_once('controller/table_install.php');
                cfshippingcreative_install_table();
            });
        }
        public function shippingAjax()
        {
            add_action('cf_ajax_shipping_option_delete', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->shipping_option_delete($_REQUEST);
            });
            add_action('cf_ajax_save_tracking_info', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->save_tracking_info($_REQUEST);
            });
            add_action('cf_ajax_save_shipping_options', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->save_shipping_options($_REQUEST);
            });
            add_action('cf_ajax_save_template', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->save_template($_REQUEST);
            });
            add_action('cf_ajax_delete_template', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->delete_template($_REQUEST);
            });
            add_action('cf_ajax_edit_template', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->edit_template($_REQUEST);
            });
            add_action('cf_ajax_send_email', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->send_email($_REQUEST);
            });
            add_action('cf_ajax_nopriv_selected_method', function () {
                $forms_ob = $this->load('form_controller');
                $forms_ob->selected_method($_REQUEST);
            });
        }
        function mail_templates()
        {
            global $mysqli;
            global $dbpref;
            $table2 = $dbpref . 'mail_templates';
            $content1 = '<p>Hi {name},</p>
            <p>Thank you for placing an order with us.&nbsp;</p>
            <p><span style="color: #626262;">We have received your order -&nbsp;</span><span style="color: #626262; font-weight: bold;">{orderid}. </span>We are on it, we will notify you when your order is successfully placed.</p>
            <p><span style="color: #626262;">Your Order of - {Products} amount of the order is {amount}.</span></p>
            <p>Thanks</p>';

            $row = "INSERT INTO `$table2` SET `template_name` = 'New Order', `subject` = 'Your order - {orderid} is on processing, Thank you for shopping', `content` = '" .  $mysqli->real_escape_string($content1) . "'";
            $mysqli->query($row);

            $content2 = "<p>Hello&nbsp;{name},</p>
            <p>Your order&nbsp;- {orderid} has been canceled. Please place it again</p>
            <p>Thanks</p>";

            $row2 = "INSERT INTO `$table2` SET `template_name` = 'Cancel Order', `subject` = 'Your order - {orderid} has been canceled, Please place it again', `content` = '" .  $mysqli->real_escape_string($content2) . "'";
            $mysqli->query($row2);

            $content3 = "<p>Hi {name},</p>
            <p>your order - <strong>{orderid}</strong> is placed, Thank you for shopping.</p>
            <p>Your Order of - {Products} amount of the order is {amount}.</p>
            <p>Thanks</p>";

            $row3 = "INSERT INTO `$table2` SET `template_name` = 'Order complete', `subject` = 'Your order - {orderid} is successfully placed', `content` = '" .  $mysqli->real_escape_string($content3) . "'";
            $mysqli->query($row3);

            $content4 = "<p>Hi {name},</p>
            <p>We got your refund request for order - <strong>{orderid}</strong>, Sorry for the inconvenience.&nbsp;</p>
            <p>Your Order of - {Products}. Your order is of amount&nbsp;<span style='color: #626262;'>{amount}.</span>&nbsp;</p>
            <p>Thanks</p>";

            $row4 = "INSERT INTO `$table2` SET `template_name` = 'Refund', `subject` = 'We got your refund request', `content` = '" .  $mysqli->real_escape_string($content4) . "'";
            $mysqli->query($row4);
        }
    }
    new CF_Simple_Shipping_base();
}
