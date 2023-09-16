<?php
    class CFPay_JVZoo_payment
    {
        function __construct()
        {

        }
        function doPayment($payment_setup, $product, $callback_url)
        {
            $user_data=get_requested_order();
            $data=array('name'=>'','email'=>'');

            $credentials=json_decode($payment_setup['credentials']);

            if(isset($user_data['data']))
            {
                if(isset($user_data['data']['name']))
                {
                    $data['name']=$user_data['data']['name'];
                }
                if(isset($user_data['data']['email']))
                {
                    $data['email']=$user_data['data']['email'];
                }
            }
            //============================||=========================


            if(isset($_GET['execute']))
            {
                if(!isset($_POST['ctransaction']))
                die('unathorized access.');

                if(self::jvzipnVerification($credentials) == 1)
                {
                        //register sale
                        if($_POST['ctransaction'] == 'SALE')
                        {
                            $data=$_POST;
                            $data['payer_name']= $_POST['ccustname'];
                            $data['payer_email']= $_POST['ccustemail'];
                            $data['payment_id']= $_POST['ctransreceipt'];
                            $data['total_paid']= $_POST['ctransamount']/100;
                            $data['payment_currency']= "USD";
                            $data['ipn_tax']=0;
                            return $data;
                        }
                }
                else
                {
                    return 0;
                }
            }
        }
        function jvzipnVerification($credentials)
        {
            $secretKey = $credentials->client_secret;
            $pop = "";
            $ipnFields = array();
            foreach ($_POST AS $key => $value) {
                if ($key == "cverify") {
                    continue;
                }
                $ipnFields[] = $key;
            }
            sort($ipnFields);
            foreach ($ipnFields as $field) {
                // if Magic Quotes are enabled $_POST[$field] will need to be
                // un-escaped before being appended to $pop
                $pop = $pop . $_POST[$field] . "|";
            }
            $pop = $pop . $secretKey;
            $calcedVerify = sha1(mb_convert_encoding($pop, "UTF-8"));
            $calcedVerify = strtoupper(substr($calcedVerify,0,8));
            return $calcedVerify == $_POST["cverify"];
        }
    } 
?>