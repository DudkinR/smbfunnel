<?php

class CFPay_Coinpayment_payment
{
    function __construct()
    {
    }
    function doPayment($payment_setup, $product, $callback_url)
    {
        $user_data = get_requested_order();

        $data = array('name' => '', 'firstname' => '', 'lastname' => '', 'email' => '');
        $credentials = json_decode($payment_setup['credentials']);

        if (isset($user_data['data'])) {
            if (isset($user_data['data']['firstname'])) {
                $data['firstname'] = $user_data['data']['firstname'];
            }
            if (isset($user_data['data']['lastname'])) {
                $data['lastname'] = $user_data['data']['lastname'];
            }
            if (isset($user_data['data']['name'])) {
                $data['name'] = $user_data['data']['name'];
            }
            if (isset($user_data['data']['email'])) {
                $data['email'] = $user_data['data']['email'];
            }
        }
        if (!isset($_GET['execute'])) {
            self::initiate($payment_setup['tax'], $credentials, $product, $callback_url, $data);
        } else {
            return self::callBack($_POST, $credentials, $product, $data);
        }
    }

    function initiate($coinpayment_tax, $credentials, $product, $callback_url, $user_data)
    {
        if (has_session('coinpayment_pay_token')) {
            unset_session('coinpayment_pay_token');
        }
        foreach ($product as $index => $val) {
            ${$index} = $val;
        }

        $firstname = $user_data['firstname'] ?? $user_data['name'];
        $lastname = $user_data['lastname'] ?? '';
        $email = $user_data['email'];
        $address = isset($user_data['address']) ? $user_data['address'] : '';
        $cartTotal = number_format(sprintf('%.2f', $total), 2, '.', '');
        $myvalue = $allproductdetail;
        $item_name = trim($myvalue);

        $item_name = str_replace('<li>', '', $item_name);
        $item_name = str_replace('</li>', ' ', $item_name);
        $item_name = str_replace('<br>', ' ', $item_name);

        $notify_url = plugin_dir_url(dirname(__FILE__)) . "coinpayment/notify.php";

        $succesUrl = get_option('install_url') . "/index.php/?page=do_payment&execute=1&status=success";
        $errorUrl  = get_option('install_url') . "/index.php/?page=do_payment&execute=1&status=cancel";

        $data = array(
            // Merchant details
            'merchant' => $credentials->merchant_id,
            'cmd' => '_pay',
            'reset' => 1,
            'email' => $email,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'taxf' => $coinpayment_tax,
            'currency' => $currency,
            'want_shipping' => 1,
            'amountf' => number_format(sprintf('%.2f', $cartTotal), 2, '.', ''),
            'item_name' => $item_name,
            'allow_extra' => 1,
            'ipn_url' => $notify_url,
            'success_url' => $succesUrl,
            'cancel_url' => $errorUrl,
        );

        $api_url = 'https://www.coinpayments.net/index.php';

        $htmlForm = '<form name="myForm" id="myForm"  action="' . $api_url . '" method="post" target="_top">';
        foreach ($data as $name => $value) {
            $htmlForm .= '<input name="' . $name . '" type="hidden"  value="' . $value . '" />';
        }
        $htmlForm .= '<input style="display:none;" type="submit" value="Pay Now" /></form>';
        echo $htmlForm;
        echo "<script>document.forms['myForm'].submit();</script>";
    }

    function callBack($credentials, $product, $user_data)
    {
        if (isset($_GET['status']) && isset($_GET['session_id']) && isset($_GET['execute'])) {
            if ($_GET['status'] == "success") {
                $pfParamString = self::callFile();
                if ($pfParamString === '') return 0;

                $response_pay = explode("&", $pfParamString);
                $payer_name = explode("=", $user_data['first_name'])[1] . ' ' . explode("=", $user_data['last_name'])[1];
                $payer_email = $user_data['email'];
                $payment_id = $response_pay[5];
                $total_paid = $response_pay[8];

                $payer_email = urldecode(explode("=", $payer_email)[1]);
                $payment_id = explode("=", $payment_id)[1];
                $total_paid = explode("=", $total_paid)[1];

                foreach ($product as $index => $val) {
                    ${$index} = $val;
                }

                $valid_response_status = self::validateResponse($credentials, $user_data, $product);
                $arr = array(
                    'payer_name' => $payer_name,
                    'payer_email' => $payer_email,
                    'payment_id' => $payment_id,
                    'total_paid' => $total_paid,
                    'payment_currency' => $currency
                );

                if ($valid_response_status) {
                    return $arr;
                }
                return 0;
            } elseif ($_GET['status'] == "cancel") {
                return 0;
            }
        }
    }

    function validateResponse($credentials, $user_data, $product)
    {
        $cp_merchant_id = $credentials->merchant_id;
        $cp_ipn_secret = $credentials->ipn_secret;

        foreach ($product as $index => $val) {
            ${$index} = $val;
        }

        $order_currency = $currency;
        $order_total = number_format(sprintf('%.2f', $cartTotal), 2, '.', '');

        $pfParamString = self::callFile();
        if ($pfParamString !== "") {
            $response_pay = explode("&", $pfParamString);

            $response_data = array();

            foreach ($response_pay as $key => $val) {
                $key = explode("=", $val)[0];
                $value = explode("=", $val)[1];
                $response_data[$key] = $value;
            }

            foreach ($response_data as $index => $val) {
                ${$index} = $val;
            }

            $request = $post_data;
            $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));

            if (($ipn_mode != 'hmac') || empty($HTTP_HMAC) || (($merchant === '') || $merchant != trim($cp_merchant_id))) {
                return 0;
            }

            if (!hash_equals($hmac, $HTTP_HMAC) || $ipn_type != 'button' || $currency1 != $order_currency || $order_total < $order_total) {
                return 0;
            }

            if ($status >= 100 || $status == 2) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }

    function callFile()
    {
        $file_path = plugin_dir_path(dirname(__FILE__)) . "coinpayment/newfile.txt";
        if (filesize($file_path) > 0) {
            $myfile = fopen($file_path, "r") or die("Unable to open file!");
            $query_string = fread($myfile, filesize($file_path));
            $pfParamString = $query_string;
            fclose($myfile);
            return $pfParamString;
        }
        return "";
    }
}//class ends here 
