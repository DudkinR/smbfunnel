<?php
class CFPay_square_payment
{
    function __construct()
    {
    }

    function doPayment($payment_setup, $product, $callback_url)
    {
        $user_data = get_requested_order();
        $data = array('name' => '', 'email' => '');

        $credentials = json_decode($payment_setup['credentials']);
        $_SESSION['app_id'] = $credentials->app_id;
        $_SESSION['access_token'] = $credentials->access_token;
        $_SESSION['location_id'] = $credentials->location_id;
        //print_r($credentials);
        if (isset($user_data['data'])) {
            if (isset($user_data['data']['name'])) {
                $data['name'] = $user_data['data']['name'];
            }
            if (isset($user_data['data']['email'])) {
                $data['email'] = $user_data['data']['email'];
            }
        }
        //============================||=========================

        $array = array();
        $sheepingcharge = 0;
        $tax = 0;
        $totalprice = 0;
        $err = "";
        $allproductdetail = "";
        $aKey = $credentials->app_id;

        $secret = $credentials->access_token;
        $order_data_array = $_SESSION['order_form_data' . get_option('site_token')];
        $all_price_detail = $product;

        //'itemarr','sheepingcharge','tax','totalprice','currency','total', 'allproductdetail'
        if (is_array($all_price_detail)) {
            foreach ($all_price_detail as $all_price_detail_index => $all_price_detail_val) {
                ${$all_price_detail_index} = $all_price_detail_val;
            }
        }
 
        $_SESSION['storeURL'] = $credentials->logo_url;
        $_SESSION['storeName'] = $credentials->storeName;
        $_SESSION['tax'] = $tax;
        $_SESSION['sheepingcharge'] = $sheepingcharge;
        $_SESSION['amount'] = $totalprice;
        $_SESSION['productArray'] = $product['items'];


        $allproductdetail .= "<hr/>Total Price: " . number_format($totalprice, 2) . " " . $currency . "<br/>";
        $allproductdetail .= "Tax: " . number_format($tax, 2) . " " . $currency . "<br/>";
        $allproductdetail .= "Shipping Charge: " . number_format($sheepingcharge, 2) . " " . $currency;


        $_SESSION['total_paid'] = $total;
        $_SESSION['payment_currency'] = $currency;
        $_SESSION['environment'] = $credentials->cctype;

            
        if(isset($order_data_array['data']['firstname']) && isset($order_data_array['data']['lastname'])){
            $_SESSION['userName'] =  $order_data_array['data']['firstname'].' '.$order_data_array['data']['lastname'];
        }else{
            $_SESSION['userName'] =  $order_data_array['data']['name'];    
        }
        
        $_SESSION['email'] =  $order_data_array['data']['email'];
        

        $price = $total;
        $name = "User";
        if (isset($order_data_array['data']['name'])) {
            $name = $order_data_array['data']['name'];
        } else if (isset($order_data_array['data']['firstname'])) {
            $name = $order_data_array['data']['firstname'];
            if (isset($order_data_array['data']['lastname'])) {
                $name .= " " . $order_data_array['data']['lastname'];
            }
        }
        if (isset($_GET['execute']) && $_GET['execute'] == 1) {


            try {

                return array(
                    'payer_name' => $name,
                    'payer_email' => $order_data_array['data']['email'],
                    'payment_id' =>  $_GET['payment_id'],
                    'total_paid' => $_SESSION['total_paid'],
                    'payment_currency' => $_SESSION['payment_currency']
                );
            } catch (Exception $e) {
                //print('Error: ' . $e->getMessage());
                return 0;
            }
        } else {
            try {
                
                include 'card.php';

            } catch (Exception $e) {
                //handle exception
                echo "Exception: " . $e->getMessage();
            }
        }
    }
}
?>