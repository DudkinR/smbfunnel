<?php

use function PHPSTORM_META\type;

class CFPay_iPaymu_payment
{
    function __construct()
    {
    }
    function doPayment($payment_setup, $product, $callback_url)
    {
        $user_data = get_requested_order();
        $data = array('name' => '', 'email' => '');

        $credentials = json_decode($payment_setup['credentials']);
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
        $va           = $credentials->client_secret; //get on iPaymu dashboard
        $secret       = $credentials->client_id; //get on iPaymu dashboard

        $err = '';
        $itemarr = array();
        $productName = array();
        $productPrice = array();
        $productQty = array();
        $sheepingcharge = 0;
        $tax = 0;
        $totalprice = 0;

        // $currency = "IDR";
        $producttitle = "";

        $allproductdetail = "";

        $order_data_array = $_SESSION['order_form_data' . get_option('site_token')];


        $all_price_detail = $product;
        //'itemarr','sheepingcharge','tax','totalprice','currency','total', 'allproductdetail'
        if (is_array($all_price_detail)) {
            foreach ($all_price_detail as $all_price_detail_index => $all_price_detail_val) {
                ${$all_price_detail_index} = $all_price_detail_val;
            }
        }

        //$product_name = $allproductdetail;
        $allproductdetail .= "<hr/>Total Price: " . number_format($totalprice, 2) . " " . $currency . "<br/>";
        $allproductdetail .= "Tax: " . number_format($tax, 2) . " " . $currency . "<br/>";
        $allproductdetail .= "Shipping Charge: " . number_format($sheepingcharge, 2) . " " . $currency;

        $charges = 0;
        $charges = $tax + $sheepingcharge;

        // $allproductdetail = str_replace("<br>", "\n", $allproductdetail);
        $_SESSION['total_paid' . get_option('site_token')] = $total;
        $_SESSION['payment_currency' . get_option('site_token')] = $currency;
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
        // echo '<pre>';
        // print_r($product['items']);
        // exit;

        foreach ($product['items'] as $val) {
            array_push($productName, $val['title']);
            array_push($productPrice, $val['price']);
            if(isset($val['quantity'])){
                array_push($productQty, $val['quantity']);
            }else{
                array_push($productQty, '1');
            }
        }
        if ($charges > 0) {
            array_push($productName, 'Tax');
            array_push($productPrice, $charges);
            array_push($productQty, '1');
        }


        if (isset($_GET['execute']) && $_GET['execute'] == 1) {

            if ($_GET['status'] == 'berhasil' || $_GET['status'] == 'succeed') {
                try {

                    return array(
                        'payer_name' => $name,
                        'payer_email' => $order_data_array['data']['email'],
                        'payment_id' =>  $order_data_array['product_id'],
                        'total_paid' => $_SESSION['total_paid' . get_option('site_token')],
                        'payment_currency' => $_SESSION['payment_currency' . get_option('site_token')]
                    );
                } catch (Exception $e) {
                    //print('Error: ' . $e->getMessage());
                    return 0;
                }
            } else {
                return 0;
            }
        } else {

            // $redirect_url=;
            //echo "<br>Redirect URL is ".$redirect_url;
            $product_name = "Product Purchase";

            try {
                if ($currency == 'IDR' || $currency == 'idr' || $currency == 'Idr' || $currency == 'idR' || $currency == 'Rp' || $currency == 'rp') {


                    // SAMPLE HIT API iPaymu v2 PHP //


                    // $url = 'https://my.ipaymu.com/api/v2/payment'; //url
                    // $url = 'https://sandbox.ipaymu.com/api/v2/payment';
                    if ($credentials->cctype == 0) {
                        $url = 'https://sandbox.ipaymu.com/api/v2/payment';
                    } else {
                        $url = 'https://my.ipaymu.com/api/v2/payment';
                    }



                    $method       = 'POST'; //method

                    //Request Body//
                    $body['product']    = $productName;
                    $body['qty']        = $productQty;
                    $body['price']      = $productPrice;
                    $body['tax']        = array('100', '200', '300');
                    $body['returnUrl']  = get_option('install_url') . "/index.php?page=do_payment&execute=1";
                    $body['cancelUrl']  = get_option('install_url') . "/index.php/?page=do_payment&execute=1&cancel=1";
                    $body['notifyUrl']  = get_option('install_url') . "/index.php?page=do_payment&execute=1";
                    //End Request Body//


                    //Generate Signature
                    // *Don't change this
                    $jsonBody     = json_encode($body, JSON_UNESCAPED_SLASHES);
                    $requestBody  = strtolower(hash('sha256', $jsonBody));
                    $stringToSign = strtoupper($method) . ':' . $va . ':' . $requestBody . ':' . $secret;
                    $signature    = hash_hmac('sha256', $stringToSign, $secret);
                    $timestamp    = Date('YmdHis');
                    //End Generate Signature


                    $ch = curl_init($url);

                    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'va: ' . $va,
                        'signature: ' . $signature,
                        'timestamp: ' . $timestamp
                    );

                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    curl_setopt($ch, CURLOPT_POST, count($body));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    $err = curl_error($ch);
                    $ret = curl_exec($ch);


                    curl_close($ch);


                    if ($err) {
                        echo $err;
                    } else {

                        //Responsee
                        $ret = json_decode($ret);
                        if ($ret->Status == 200) {
                            $sessionId  = $ret->Data->SessionID;
                            $url =  $ret->Data->Url;
                            header('Location:' . $url);
                        } else {

?>
                            <div style="background:#19334d; margin: auto;width: 50%;border: 3px solid #19334d;padding: 10px;">
                                <p style="color:white;" align="center"><?php print_r(ucfirst($ret->Message)); ?>
                                </p>
                            </div><?php
                                }
                                //End Response
                            }
                        } else {
                                    ?>
                    <div style="background:#19334d; margin: auto;width: 50%;border: 3px solid #19334d;padding: 10px;">
                        <p style="color:white;" align="center">Your Currency must be in IDR (Indonesian currency) for payment in iPaymu payment
                        </p>
                    </div>
                <?php

                        }


                ?>
<?php

            } catch (Exception $e) {
                // print('Error: ' . $e->getMessage());
                die("Unable to process");
            }
        }
    }
}
