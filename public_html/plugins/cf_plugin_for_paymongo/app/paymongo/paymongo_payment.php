<?php

class CFPay_paymongo_payment
{
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
            return self::callBack($product,$data);
        }
    }
    function initiate($credentials,$product,$callback_url,$user_data)
    {               
        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }
        $ccid=trim($credentials->public_key);
        $ccs=$credentials->secret_key;
        $email = $user_data['email'];
        $name=$user_data['name'];
        $success_url=get_option('install_url')."/index.php?page=do_payment&execute=1&success=1";
        $failed_url=get_option('install_url')."/index.php?page=do_payment&execute=1&failed=1";        
        $body=[
            'data'=>[
                'attributes'=>[
                    'amount'=>ceil($total*100),
                    'redirect'=>[
                        'success'=>$success_url,
                        'failed'=>$failed_url
                    ],
                    'type'=>'gcash',
                    'currency'=>'PHP',
                    'billing'=>[
                        'name'=>$name,
                        'email'=>$email,
                    ]
                ]
            ]
        ];
        $body_o = json_encode((object)$body);

        $header= [
            "accept: application/json",
            "authorization: Basic ".base64_encode($ccs.":".$ccid),
            "content-type: application/json"
        ];
         try {  
                    

        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/sources",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body_o,
            CURLOPT_HTTPHEADER =>$header,
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            $response = json_decode($response);
        
            $checkout_url=$response->data->attributes->redirect->checkout_url;
            $_SESSION['paymonog_id']=$response->data->id;
            header('location:'.$checkout_url.'');
            die();
        }
        }
        catch (Exception $e) {
            print('Error: ' . $e->getMessage());
            return 0;
        }



    }   

    function callBack($product,$user_data)
    {
        foreach( $product as $index => $val )
        {
            ${$index}=$val;
        }
        $id=$_SESSION['paymonog_id'];
        if( isset( $_GET['success'] ) )
        {
            $arr=array(
                'payer_name'=> $user_data['name'],
                'payer_email'=> $user_data['email'],
                'payment_id'=> $id,
                'total_paid'=>($total),
                'payment_currency'=>'PHP',
                
              );
              return $arr;
        }
        else{
          return false;
        }  
    }
}
?>