<?php
if (!class_exists('CF_Simple_Shipping_form_controller')) {
    class CF_Simple_Shipping_form_controller
    {
        function __construct($arr)
        {
            global $app_variant;
            $app_variant = isset($app_variant) ? $app_variant : "coursefunnels";
            if ($app_variant == "shopfunnels") {
                $this->student = "Customer";
            } elseif ($app_variant == "cloudfunnels") {
                $this->student = "Member";
            } elseif ($app_variant == "coursefunnels") {
                $this->student = "Student";
            }
            $this->loader = $arr['loader'];
        }
        function text_to_avatar($txt)
        {
            $colors =  ['#003366', '#005580', '#049560', '#e68a00', '#e62e00', '#e6005c', '#660066', '#800040', '#990099', '#008000', '#73264d'];
            shuffle($colors);
            $txt = strtoupper(trim($txt));
            $txt = preg_replace('/(\s){2,}/', ' ', $txt);
            $avatar = "";
            if (strlen($txt) > 0) {
                $arr = array_slice(explode(" ", $txt), 0, 2);
                foreach ($arr as $word) {
                    $avatar .= substr($word, 0, 1);
                }
                $color = $colors[mt_rand(0, 10)];
                $av_len = count($arr);
                $avatar = "<div style='background-color: " . $color . "' cfs-studnet-store-avatar='true'><span>" . $avatar . "</span></div>";
            }
            return $avatar;
        }
        function getAllFunnels($funnel_id = false)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "quick_funnels";

            if ($funnel_id) {
                $id = $mysqli->real_escape_string($funnel_id);
                $qry = $mysqli->query("select `id`,`name` from `$table` WHERE `type`='membership' AND `id`=$id");
            } else {
                $qry = $mysqli->query("select `id`,`name` from `$table` WHERE `type`='membership'");
            }
            $arr = array();

            //////////////////////////////

            $arr = [];
            if ($qry->num_rows > 0) {
                while ($data = $qry->fetch_assoc()) {
                    $arr[] = $data;
                }
            }
            return $arr;
        }
        function getAllFunnelsCounts()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'quick_funnels';

            $qry = $mysqli->query("select count(`id`) as `total_setup` from `" . $table . "`");

            $r = $qry->fetch_object();
            return $r->total_setup;
        }
        function option_data($data, $page = 1)
        {
            //print_r($_GET);
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'shipping_options';
            $page = $mysqli->real_escape_string($page);
            $page = (int)$page;
            $page = ($page < 1) ? 1 : $page;
            $records_to_show = 10;
            $records_to_show = (int) $records_to_show;
            $page = ($page * $records_to_show) - $records_to_show;

            $limit_str = " limit " . $page . "," . $records_to_show;

            $search = "";

            if (isset($_POST['onpage_search'])) {

                $search = trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search = str_replace('_', '[_]', $search);
                $search = str_replace('%', '[%]', $search);
                $search = " and (`name` like '%" . $search . "%' or `cost` like '%" . $search . "%')";
            }

            $arr = array();

            $order_by = "`id` desc";
            if (isset($_GET['arrange_records_order'])) {
                $order_by = base64_decode($_GET['arrange_records_order']);
            }

            $date_between = dateBetween('createdon', null, true);

            if (strlen($date_between[0]) > 0) {
                if (strlen($search) > 0) {
                    $search .= $date_between[1];
                } else {
                    $search = " where" . $date_between[0];
                }
            }
            $method = $mysqli->real_escape_string("all_sales");            
            $qry = $mysqli->query("select * from `" . $table . "`  WHERE `funnelid` = " . $data . " " . $search . " order by " . $order_by . $limit_str);

            while ($r = $qry->fetch_object()) {
                array_push($arr, $r);
            }

            return $arr;
        }
        function shipping_option_delete($data)
        {
            global $mysqli;
            global $dbpref;

            $table1 = $dbpref . "shipping_options";
            if ($mysqli->query("DELETE FROM `" . $table1 . "` WHERE `id`='" .  $mysqli->real_escape_string($data['id']) . "'")) {
                echo 200;
            } else {
                echo 400;
            }
        }
        function delete_template($data)
        {
            global $mysqli;
            global $dbpref;

            $table1 = $dbpref . "mail_templates";
            if ($mysqli->query("DELETE FROM `" . $table1 . "` WHERE `id`='" .  $mysqli->real_escape_string($data['id']) . "'")) {
                echo 200;
            } else {
                echo 400;
            }
        }
        function edit_template($data)
        {
            global $mysqli;
            global $dbpref;

            $table1 = $dbpref . "mail_templates";

            $qry = $mysqli->query("select * from `" . $table1 . "`  WHERE `id` = " .  $mysqli->real_escape_string($data['id']) . "");

            $r = $qry->fetch_object();
            print_r(json_encode($r));
        }
        function sales_data($data, $page = 1)
        {
            //print_r($_GET);
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'ship_orders';
            $page = $mysqli->real_escape_string($page);
            $page = (int)$page;
            $page = ($page < 1) ? 1 : $page;
            $records_to_show = 10;
            $records_to_show = (int) $records_to_show;
            $page = ($page * $records_to_show) - $records_to_show;

            $limit_str = " limit " . $page . "," . $records_to_show;

            $search = "";

            if (isset($_POST['onpage_search'])) {

                $search = trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search = str_replace('_', '[_]', $search);
                $search = str_replace('%', '[%]', $search);
                $search = " and (`order_id` like '%" . $search . "%' or `user_name` like '%" . $search . "%' or `contact` like '%" . $search . "%')";
            }

            $arr = array();

            $order_by = "`id` desc";
            if (isset($_GET['arrange_records_order'])) {
                $order_by = base64_decode($_GET['arrange_records_order']);
            }

            $date_between = dateBetween('createdon', null, true);

            if (strlen($date_between[0]) > 0) {
                if (strlen($search) > 0) {
                    $search .= $date_between[1];
                } else {
                    $search = " where" . $date_between[0];
                }
            }
            $method = $mysqli->real_escape_string("all_sales");
            
            $qry = $mysqli->query("select * from `" . $table . "`  WHERE `funnelid` = " . $data . " " . $search . " order by " . $order_by . $limit_str);

            while ($r = $qry->fetch_object()) {
                array_push($arr, $r);
            }

            return $arr;
        }
        function mail_template($page = 1)
        {
            //print_r($_GET);
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'mail_templates';
            $page = $mysqli->real_escape_string($page);
            $page = (int)$page;
            $page = ($page < 1) ? 1 : $page;
            $records_to_show = 10;
            $records_to_show = (int) $records_to_show;
            $page = ($page * $records_to_show) - $records_to_show;

            $limit_str = " limit " . $page . "," . $records_to_show;

            $search = "";

            if (isset($_POST['onpage_search'])) {

                $search = trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search = str_replace('_', '[_]', $search);
                $search = str_replace('%', '[%]', $search);
                $search = " where (`template_name` like '%" . $search . "%' or `subject` like '%" . $search . "%')";
            }

            $arr = array();

            $order_by = "`id` desc";
            if (isset($_GET['arrange_records_order'])) {
                $order_by = base64_decode($_GET['arrange_records_order']);
            }

            $date_between = dateBetween('createdon', null, true);

            if (strlen($date_between[0]) > 0) {
                if (strlen($search) > 0) {
                    $search .= $date_between[1];
                } else {
                    $search = " where" . $date_between[0];
                }
            }

            $qry = $mysqli->query("select * from `" . $table . "`  " . $search . " order by " . $order_by . $limit_str);

            while ($r = $qry->fetch_object()) {
                array_push($arr, $r);
            }

            return $arr;
        }
        function order_data($id)
        {
            global $mysqli;
            global $dbpref;
            $arr = [];

            $table = $dbpref . "ship_orders";

            $qry = $mysqli->query("SELECT * FROM `" . $table . "` WHERE `id` = '" .  $mysqli->real_escape_string($id) . "'");
            if (mysqli_num_rows($qry) > 0) {
                while ($r = $qry->fetch_object()) {
                    array_push($arr, $r);
                }

                return $arr;
            }
        }
        function shipping_options($id)
        {
            global $mysqli;
            global $dbpref;
            $arr = [];            
            $table = $dbpref . "shipping_options";

            $qry = $mysqli->query("SELECT * FROM `" . $table . "` WHERE `funnelid` = '" . $mysqli->real_escape_string($id) . "'");
            if (mysqli_num_rows($qry) > 0) {
                while ($r = $qry->fetch_object()) {
                    array_push($arr, $r);
                }

                return $arr;
            }
        }
        function save_shipping_options($data)
        {
            global $mysqli;
            global $dbpref;
            $arr = [];

            $table = $dbpref . "shipping_options";
            if ($data['option_id'] == '') {
                $check = $mysqli->query("SELECT `name` FROM `" . $table . "` WHERE `name` = '" .  $mysqli->real_escape_string($data['name']) . "' AND `funnelid` = '".$data['funnelid']."'");
                if (mysqli_num_rows($check) > 0) {
                    echo 201;
                } else {

                    $row = "INSERT INTO `$table` SET `name` = '" . $mysqli->real_escape_string($data['name']) . "', `cost` = '" .  $mysqli->real_escape_string($data['cost']) . "',`funnelid` = '" .  $mysqli->real_escape_string($data['funnelid']) . "'";

                    if ($mysqli->query($row) === TRUE) {
                        echo 200;
                    }
                }
            } else {
                $check = $mysqli->query("SELECT `name` FROM `" . $table . "` WHERE `name` = '" .  $mysqli->real_escape_string($data['name']) . "' AND `id` <> '" . $mysqli->real_escape_string($data['option_id']) . "' AND `funnelid` = '".$data['funnelid']."'");
                if (mysqli_num_rows($check) > 0) {
                    echo 201;
                } else {
                    $row = "UPDATE `$table` SET `name` = '" .  $mysqli->real_escape_string($data['name']) . "', `cost` = '" .  $mysqli->real_escape_string($data['cost']) . "',`funnelid` = '" . $mysqli->real_escape_string($data['funnelid']) . "' WHERE `id` = '" .  $mysqli->real_escape_string($data['option_id']) . "'";
                    if ($mysqli->query($row) === TRUE) {
                        echo 200;
                    }
                }
            }
        }
        function save_template($data)
        {

            global $mysqli;
            global $dbpref;

            $table = $dbpref . "mail_templates";
            if ($data['id'] == '') {

                $check = $mysqli->query("SELECT `template_name` FROM `" . $table . "` WHERE `template_name` = '" .  $mysqli->real_escape_string($data['name']) . "'");
                if (mysqli_num_rows($check) > 0) {
                    echo 201;
                } else {

                    $row = "INSERT INTO `$table` SET `template_name` = '" .  $mysqli->real_escape_string($data['name']) . "', `subject` = '" .  $mysqli->real_escape_string($data['subject']) . "', `content` = '" .  $mysqli->real_escape_string($data['content']) . "'";

                    if ($mysqli->query($row) === TRUE) {
                        echo 200;
                    }
                }
            } else {
                $check = $mysqli->query("SELECT `template_name` FROM `" . $table . "` WHERE `template_name` = '" .  $mysqli->real_escape_string($data['name']) . "' AND `id` <> '" . $mysqli->real_escape_string($data['id']) . "' ");
                if (mysqli_num_rows($check) > 0) {
                    echo 201;
                } else {
                    $row = "UPDATE `$table` SET `template_name` = '" .  $mysqli->real_escape_string($data['name']) . "', `subject` = '" .  $mysqli->real_escape_string($data['subject']) . "', `content` = '" .  $mysqli->real_escape_string($data['content']) . "' WHERE `id` = '" .  $mysqli->real_escape_string($data['id']) . "'";
                    if ($mysqli->query($row) === TRUE) {
                        echo 200;
                    }
                }
            }
        }
        function save_tracking_info($data)
        {
            global $mysqli;
            global $dbpref;

            $table = $dbpref . "ship_orders";

            $qry = "UPDATE `" . $table . "` SET `tracking_number` = '" .  $mysqli->real_escape_string($data['tracking_number']) . "',`carrier_service` = '" .  $mysqli->real_escape_string($data['carrier_service']) . "',`carrier_url` = '" .  $mysqli->real_escape_string($data['carrier_url']) . "' WHERE `id` = '" .  $mysqli->real_escape_string($data['id']) . "'";
            if ($mysqli->query($qry) === TRUE) {
                echo 200;
            }
        }
        function order_history($data)
        {
            $products = [];
            $payment_method = [];
            $order_id = '';
            $funnel = '';
            $amount = '0';
            $ship_date = '';
            $currency = '';
            $name ='';

            if (isset($_SESSION['order_form_data' . get_option('site_token')]['payment_method']))
                $payment_method = $_SESSION['order_form_data' . get_option('site_token')]['payment_method'];

            if (isset($_SESSION['total_paid'.get_option('site_token')]))
                $amount = $_SESSION['total_paid'.get_option('site_token')];

            if (isset($_SESSION['selected_product_to_buy_' . get_option('site_token')]))
                $products = serialize($_SESSION['selected_product_to_buy_' . get_option('site_token')]);

            if (isset($_SESSION['order_form_data' . get_option('site_token')]['funnel_id']))
                $funnel = $_SESSION['order_form_data' . get_option('site_token')]['funnel_id'];

            if (isset($_SESSION['order_form_data'. get_option('site_token')]['data']))
                $shippingdata = serialize($_SESSION['order_form_data'. get_option('site_token')]['data']);

            if (isset($_SESSION['payment_currency'. get_option('site_token')]))
                $currency = $_SESSION['payment_currency' . get_option('site_token')];

            if(isset($_SESSION['current_payment_cofirmation'.get_option('site_token')]['name']))
                $name = $_SESSION['current_payment_cofirmation'.get_option('site_token')]['name'];

                
            if (isset($_SESSION['order_form_data' . get_option('site_token')]['data']['email']))
                $email = $_SESSION['order_form_data' . get_option('site_token')]['data']['email'];


            global $mysqli;
            global $dbpref;


            $table = $dbpref . "ship_orders";
            do {
                $order_id = uniqid();
              
            } while (mysqli_num_rows($mysqli->query("SELECT `id` FROM `" . $table . "` WHERE `order_id` = '" .  $order_id . "'")) > 0);

            if (isset($name)) {


                $mysqli->query("INSERT INTO `" . $table . "` SET `order_id` = '" .  $mysqli->real_escape_string($order_id) . "',`funnelid` ='" .  $mysqli->real_escape_string($funnel) . "', `user_name` = '" .  $mysqli->real_escape_string($name) . "',`contact` = '" .  $mysqli->real_escape_string($email) . "',`shippingdata` ='" .  $mysqli->real_escape_string($shippingdata) . "',`amount` = '" .  $mysqli->real_escape_string($amount) . "',`currency`='" . $mysqli->real_escape_string($currency) . "',`products` = '" .  $mysqli->real_escape_string($products) . "',`payment_method`='" .  $mysqli->real_escape_string($payment_method) . "',`carrier_service`='null',`carrier_url`='null',`shipment_method`='".$mysqli->real_escape_string($_SESSION['selected_method'])."',`payment_id`='null',`tracking_number`='null',`status`='ordered'");
            }
        }

        function send_email($all_data)
        {
            $purchased_products = '';
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "ship_orders";
            $table3 = $dbpref . "quick_smtp_setting";            
            $qry = $mysqli->query("SELECT * FROM `" . $table . "` WHERE `id` = '" .  $mysqli->real_escape_string($all_data['emailer_id']) . "'");
            if (mysqli_num_rows($qry) > 0) {
                $r = $qry->fetch_assoc();
            }
            // echo '<pre>';
            $products = unserialize($r['products']);
            foreach ($products as $value) {
                $table2 = $dbpref . "all_products";
                $sql2 = $mysqli->query("SELECT `title`,`is_variant`,`parent_product` FROM `" . $table2 . "` WHERE `id` = '" .  $mysqli->real_escape_string($value['product']) . "'");
                if (mysqli_num_rows($sql2) > 0) {
                    while ($qry2 = mysqli_fetch_assoc($sql2)) {
                        if ($qry2['is_variant'] == '1') {

                            $sql3 = mysqli_fetch_assoc($mysqli->query("SELECT `title`,`parent_product` FROM `" . $table2 . "` WHERE `id` = " .  $mysqli->real_escape_string($qry2['parent_product']) . ""));
                            $product_name = $sql3['title'] . '&nbsp;X&nbsp;' . $value["quantity"];
                        } else {
                            $product_name = $qry2['title'] . '&nbsp;X&nbsp;' . $value["quantity"];
                        }
                    }
                }
                $purchased_products .=  $product_name . ',';
            }
   

            $mail = $this->replaceshortcode($r['currency'], $all_data['subject'], $all_data['content'], $purchased_products, $r['amount'], $r['user_name'], $r['contact'], $r['shipment_method'], $r['tracking_number'], $r['carrier_service'], $r['carrier_url'], $r['order_id']);

            $data = array(
                'mailer' => $all_data['sender'],

                'name' => $r['user_name'],

                'email' => $r['contact'],

                'subject' => $mail['subject'],

                'body' => $mail['body'],

            );
            $sql4 = mysqli_fetch_assoc($mysqli->query("SELECT `replyemail` FROM `" . $table3 . "` WHERE `id` = " . $all_data['sender'] . ""));

            $data2 = array(
                'mailer' => $all_data['sender'],

                'name' => $r['user_name'],

                'email' => $sql4['replyemail'],

                'subject' => $mail['subject'],

                'body' => $mail['body'],

            );
            if (cf_mail($data) == TRUE) {
                echo 200;
            } else {
                echo 400;
            }
            cf_mail($data2);
        }
        function replaceshortcode($currency = "USD", $subject = '', $content = '', $products = '', $prices = '', $name = '', $email = '', $shipment_method = '', $tracking_number = '', $carrier_service = '', $carrier_url = '', $order_id)
        {
            $sub = "";
            $prices = sprintf("%1\$.2f", $prices);
            $cont = "";
            $shortcodes = ['{amount}', '{name}', '{email}', '{shipping_method}', '{currency}', '{products}', '{carrier_service}', '{tracking_number}', '{carrier_url}', '{orderid}'];
            $shortcodes_v = ['{amount}' => $prices, '{name}' => $name, '{email}' => $email, '{shipping_method}' => $shipment_method, '{carrier_service}' => $carrier_service, '{currency}' => $currency, '{products}' => $products, '{tracking_number}' => $tracking_number, '{carrier_url}' => $carrier_url, '{orderid}' => $order_id];

            foreach ($shortcodes as $shortcode) {
                $sub = $subject;
                if (stristr($sub, $shortcode)) {
                    $sub = str_ireplace($shortcode, $shortcodes_v[$shortcode], $sub);
                }
                $subject = $sub;
                $cont = $content;
                if (stristr($cont, $shortcode)) {
                    $cont = str_ireplace($shortcode, $shortcodes_v[$shortcode], $cont);
                }
                $content = $cont;
            }
            return ['subject' => $sub, 'body' => $content];
        }
        function selected_method($data){

            if(!(isset($_SESSION['selected_method_cost'])))     
                $_SESSION['selected_method_cost'] = $data['delivery_cost'];  

            if(!(isset($_SESSION['selected_method'])))         
                $_SESSION['selected_method'] = $data['delivery_type'];    
            
            
        }
    }
}
