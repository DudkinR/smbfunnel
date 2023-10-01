<?php
    class CFPay_Thrivecart_payment
    {
        function __construct()
        {

        }
        function doPayment($payment_setup, $product, $callback_url)
        {
            $user_data=get_requested_order();
            $data=array('name'=>'','email'=>'');


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
            $credentials=json_decode($payment_setup['credentials']);
            //client_secret
            if(isset($_POST['event']) && $_POST['event']=='order.success')
            {
                $secret=trim($credentials->client_secret);
                if((strlen($secret)<1)|| (isset($_POST['thrivecart_secret']) && $secret==$_POST['thrivecart_secret'] ))
                {
                    $data=$_POST;
                    $customer=(is_object($_POST['customer']))? $_POST['customer']:json_decode($_POST['customer']);
                    $order=(is_object($_POST['order']))? $_POST['order']:json_decode($_POST['order']);
                    $data['payment_id']=$_POST['order_id'];
                    $data['payer_email']=$customer->email;
                    $data['payer_name']=$customer->name;
                    $data['payment_currency']=$_POST['currency'];
                    $data['total_paid']=$order->total;
                    $data['ipn_tax']=$order->tax;
                    $_SESSION['total_paid'.get_option('site_token')]=$order->total;
                    $_SESSION['payment_currency'.get_option('site_token')]=$_POST['currency'];
                    $_SESSION['ipn_tax'.get_option('site_token')]=$order->tax;

                    return $data;
                }
            }
            return 0;
        }      
    } 
?>