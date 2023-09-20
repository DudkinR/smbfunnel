<?php
class CFPay_Paystack_payment
{
    function __construct()
    {

    }
    function doPayment($payment_setup,$product,$callback_url)
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
        if(!isset($_GET['execute']))
        {
            self::initiate($credentials,$product,$callback_url,$data);
        }
        else
        {
            return self::callBack($credentials,$product,$data);
        }
    }
    function initiate($credentials,$product,$callback_url,$user_data)
    {
        //print_r($credentials);
        if(has_session('paystack_pay_token'))
        {
            unset_session('paystack_pay_token');
        }

        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }

        $curl = curl_init();

        //print_r($user_data);
        $email = $user_data['email'];
        $amount = $total*100;  //the amount in kobo. This value is actually NGN 300
        // url to go to after payment

        $cf_token=substr(str_shuffle('ASDFGHJKZXCVBNMQWERTYU1234567890'),0,5);
        $cf_token .=time();
        set_session('paystack_pay_token',$cf_token);

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'amount'=>$amount,
            'email'=>$email,
            'callback_url' => $callback_url.'&cf_paystack_token='.$cf_token,
        ]),
        CURLOPT_HTTPHEADER => [
            "authorization: Bearer ".$credentials->secret, //replace this with your own test key
            "content-type: application/json",
            "cache-control: no-cache"
        ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err){
            // there was an error contacting the Paystack API
            //die('Curl returned error: ' . $err);
            die('Unable to pay.');
        }

        $tranx = json_decode($response, true);

        if(!$tranx['status']){
            // there was an error from the API
            //print_r('API returned error: ' . $tranx['message']);
            die('Unable to pay.');
        }

        // comment out this line if you want to redirect the user to the payment page
        //print_r($tranx);
        // redirect to page so User can pay
        // uncomment this line to allow the user redirect to the payment page
        header('Location: ' . $tranx['data']['authorization_url']);
    }
    function callBack($credentials,$product,$user_data)
    {

        if(!(has_session('paystack_pay_token') && isset($_GET['cf_paystack_token']) && get_session('paystack_pay_token')===$_GET['cf_paystack_token']))
        {
            return false;
        }
        unset_session('paystack_pay_token');

        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }

        $curl = curl_init();
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if(!$reference){
        die('No reference supplied');
        }

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$credentials->secret,
            "cache-control: no-cache"
        ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);


        if($err){
            // there was an error contacting the Paystack API
            //die('Curl returned error: ' . $err);
            return false;
        }

        $tranx = json_decode($response);

        if(!$tranx->status){
            // there was an error from the API
            //die('API returned error: ' . $tranx->message);
            return false;
        }

        if('success' == $tranx->data->status){
            // transaction was successful...
            // please check other things like whether you already gave value for this ref
            // if the email matches the customer who owns the product etc
            // Give value
            //echo "<h2>Thank you for making a purchase. Your file has bee sent your email.</h2>";
            $arr=array(
                'payer_name'=> $user_data['name'],
                'payer_email'=> $user_data['email'],
                'payment_id'=>  $_GET['reference'],
                'total_paid'=> $total,
                'payment_currency'=> $tranx->data->currency,
            );
            return $arr;
        }
        return false;
    }
} 
?>