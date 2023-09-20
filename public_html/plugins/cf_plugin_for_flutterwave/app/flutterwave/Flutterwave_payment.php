<?php
class CFPay_Flutterwave_payment
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
        if(has_session('flutterwave_pay_token'))
        {
            unset_session('flutterwave_pay_token');
        }

        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }

        $curl = curl_init();

        //print_r($user_data);
        $email = $user_data['email'];
        //$amount = $total;

        $cf_token=substr(str_shuffle('ASDFGHJKZXCVBNMQWERTYU1234567890'),0,5);
        $cf_token .=time();
        $cf_token='fwv_'.$cf_token;
        set_session('flutterwave_pay_token',$cf_token);

        $api_url=($credentials->type=='1')? 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay':'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/hosted/pay';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
              'payment_method'=>'card',
              'amount'=>$total,
              'customer_email'=>$email,
              'currency'=>$currency,
              'txref'=>$cf_token,
              'PBFPubKey'=>$credentials->public_key,
              'redirect_url'=> plugins_url('process.php', __FILE__).'?cf_flutterwave_token='.$cf_token,
            ]),
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "cache-control: no-cache"
              ],
            ));
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            if($err){
              // there was an error contacting the rave API
              //die('Curl returned error: ' . $err);
              die('Unable to process payment');
            }

            $transaction = json_decode($response);

            if(!((isset($transaction->data) && $transaction->data) && (isset($transaction->data->link) && $transaction->data->link)) ){
                // there was an error from the API
                //print_r('API returned error: ' . $transaction->message);
                die('Unable to process the payment');
            }
            // redirect to page so User can pay
            //print_r($transaction);
            echo "<script>window.location=`".$transaction->data->link."`;</script>";
            die();
    }
    function callBack($credentials,$product,$user_data)
    {
        if(!(has_session('flutterwave_pay_token') && isset($_GET['cf_flutterwave_token']) && get_session('flutterwave_pay_token')===$_GET['cf_flutterwave_token']))
        {
            return false;
        }
        unset_session('flutterwave_pay_token');

        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }


        if (isset($_GET['txref'])) {
            $ref = $_GET['txref'];
            $amount = $total; //Get the correct amount of your product
    
            $query = array(
                "SECKEY" => $credentials->secret_key,
                "txref" => $ref
            );
    
            $data_string = json_encode($query);

            $api_url=($credentials->type=='1')? 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify': 'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify';
                    
            $ch = curl_init($api_url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
            $response = curl_exec($ch);
    
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
    
            curl_close($ch);
    
            $resp = json_decode($response, true);
    
              $paymentStatus = $resp['data']['status'];
            $chargeResponsecode = $resp['data']['chargecode'];
            $chargeAmount = $resp['data']['amount'];
            $chargeCurrency = $resp['data']['currency'];
    
            if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)  && ($chargeCurrency == $currency)) {
              // transaction was successful...
                // please check other things like whether you already gave value for this ref
              // if the email matches the customer who owns the product etc
              //Give Value and return to Success page
              //   var_dump($resp);

              $arr=array(
                'payer_name'=> $user_data['name'],
                'payer_email'=> $user_data['email'],
                'payment_id'=> $ref,
                'total_paid'=> $total,
                'payment_currency'=> $currency,
            );
            return $arr;
            }
        }

        return false;
    }
} 
?>