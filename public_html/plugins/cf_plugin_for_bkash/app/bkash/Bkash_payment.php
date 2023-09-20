<?php
class CFPay_Bkash_payment
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
        if(has_session('bkash_pay_token'))
        {
            unset_session('bkash_pay_token');
        }
        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }   
        $email = $user_data['email'];
        $cf_token=substr(str_shuffle('ASDFGHJKZXCVBNMQWERTYU1234567890'),0,5);
        $cf_token .=time();
        $cf_token='bksh_'.$cf_token;
        set_session('bkash_pay_token',$cf_token);
        $api_url=($credentials->type=='1')? 'https://checkout.bka.sh/v1.2.0-beta/checkout/payment/create':'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/create';
        $amount = $total;
        //bkash curl 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $query = array(
            "amount" => $amount,
            "currency" => $currency,
            "intent" => "sale",
            "merchantInvoiceNumber" => $cf_token
        );
        $data_string = json_encode($query);
        //print_r($data_string);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: '.$credentials->public_key;
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-App-Key: '.$credentials->secret_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        print_r($response);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            die('Unable to process payment');
        }
        curl_close($ch);
        $transaction = json_decode($response);
        print_r($transaction);
        if(!(isset($transaction->paymentID))){
            //print_r('API returned error: ' . $transaction->message);
            die('Unable to process the payment');
        }
        header('Location: ' . $callback_url);
    }
    function callBack($credentials,$product,$user_data)
    {   /*
        if(!(has_session('bkash_pay_token') && isset($_GET['cf_bkash_token']) && get_session('bkash_pay_token')===$_GET['cf_bkash_token']))
        {
            return false;
        }
        unset_session('bkash_pay_token'); 
*/
        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }
        $api_url=($credentials->type=='1')? 'https://checkout.bka.sh/v1.2.0-beta/checkout/payment/execute/': 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/execute/';
        $api_url.=$payment_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: '.$credentials->public_key;
        $headers[] = 'X-App-Key: '.$credentials->secret_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        //print_r($result);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $resp = json_decode($result, true);
        if($resp)
        {
        $payment_id=$resp['paymentID'];
            $paymentStatus = $resp['transactionStatus'];
            $chargeAmount = $resp['amount'];
            $chargeCurrency = $resp['currency'];
              $arr=array(
                'payer_name'=> $user_data['name'],
                'payer_email'=> $user_data['email'],
                'payment_id'=> $payment_id,
                'total_paid'=> $total,
                'payment_currency'=> $chargeCurrency,
            );
            return $arr;
        }    
        return false;
    }//function call back ends here.
}//class ends here
?> 