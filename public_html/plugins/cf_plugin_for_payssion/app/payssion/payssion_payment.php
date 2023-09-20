<?php
class CFPay_payssion_payment
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


        $array = array();
        $sheepingcharge = 0;
        $tax = 0;
        $totalprice = 0;
        $err = "";
        $allproductdetail = "";
        $aKey = $credentials->client_API;
        $secret = $credentials->client_secret;
        $order_data_array = $_SESSION['order_form_data' . get_option('site_token')];
        $all_price_detail = $product;

        //'itemarr','sheepingcharge','tax','totalprice','currency','total', 'allproductdetail'
        if (is_array($all_price_detail)) {
            foreach ($all_price_detail as $all_price_detail_index => $all_price_detail_val) {
                ${$all_price_detail_index} = $all_price_detail_val;
            }
        }




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

        // Generate Order Code
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((float)microtime() * 1000000);
        $i = 0;
        $pass = '';
        while ($i <= 7) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }

        $msg = implode('|', array($aKey, $_GET['transaction_id'], $_GET['order_id'], $secret));
        $api_sig2 = md5($msg);

        $array = array(
            "transaction_id" => $_GET['transaction_id'],
            "api_key" => $aKey,
            "api_sig" => $api_sig2,
            "order_id" => $_GET['order_id']
        );


        if (isset($_GET['execute']) && $_GET['execute'] == 1) {
            if ($credentials->cctype == 0) {
                $detailURL = 'http://sandbox.payssion.com/api/v1/payment/details';
            } else {
                $detailURL = 'https://www.payssion.com/api/v1/payment/details';
            }


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $detailURL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $array,

            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $resp = json_decode($response);

            if ($resp->transaction->state == 'completed') {
                try {

                    return array(
                        'payer_name' => $name,
                        'payer_email' => $order_data_array['data']['email'],
                        'payment_id' =>  $pass,
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
            try {

                $msg = implode('|', array($aKey, $price, $currency, $pass, $secret));
                $api_sig = md5($msg);
                if ($credentials->cctype == 0) {
                    $url = 'https://sandbox.payssion.com/checkout/' . $aKey;
                } else {
                    $url = 'https://www.payssion.com/checkout/' . $aKey;
                }
?>
                <!DOCTYPE html>
                <html lang="en">

                <body>
                    <form name="payssion_hosted_payment" id="payssion_hosted_payment" action="<?php echo $url ?>" method="post">
                        <input type="hidden" name="api_sig" value="<?php echo $api_sig; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $pass; ?>">
                        <input type="hidden" name="payer_email" value="<?php echo $order_data_array['data']['email']; ?>">
                        <input type="hidden" name="description" value="Charge for test">
                        <input type="hidden" name="amount" value="<?php echo $price ?>">
                        <input type="hidden" name="currency" value="<?php echo $currency ?>">
                        <input type="hidden" name="return_url" value="<?php echo get_option('install_url') . "/index.php?page=do_payment&execute=1" ?>">
                        <?php if (strlen(trim($err)) > 0) {
                            echo "<p class='text-center text-danger'>" . $err . "</p>";
                        } else {
                            echo "<br>";
                        } ?>
                    </form>
                </body>

                </html>
                <script type="text/javascript">
                    function formAutoSubmit() {
                        var frm = document.getElementById("payssion_hosted_payment");
                        frm.submit();
                    }
                    window.onload = formAutoSubmit;
                </script>
<?php
            } catch (Exception $e) {
                //handle exception
                echo "Exception: " . $e->getMessage();
            }
        }
    }
}
?>