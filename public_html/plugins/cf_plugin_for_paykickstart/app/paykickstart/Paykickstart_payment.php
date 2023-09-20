<?php
    class CFPay_Paykickstart_payment
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
            if(isset($_GET['execute']))
            {
                if($_SERVER['REQUEST_METHOD']=="POST")
                {
                    $credentials=json_decode($payment_setup['credentials']);
                    if(self::is_valid_paykickstart_ipn($_POST,$credentials->client_secret))
                    {
                        if($_POST['event']=="sales")
                        {
                        
                            $data=$_POST;     
                            $data['payer_name']=$_POST['buyer_first_name']." ".$_POST['buyer_last_name'];
                            $data['payer_email']=$_POST['buyer_email'];
                            $data['payment_id']=$_POST['transaction_id'];
                            $data['payment_currency']="USD";
                            $data['total_paid']=$_POST['amount'];

                            $_SESSION['total_paid'.get_option('site_token')]=$_POST['amount'];
                            $_SESSION['payment_currency'.get_option('site_token')]='USD';
                            $temptax=0;
                            if(is_numeric($_POST['tax_amount'])){$temptax=$_POST['tax_amount'];}
                            $_SESSION['ipn_tax'.get_option('site_token')]=$temptax; 
                            
                            $data['ipn_tax'] = $temptax;
                            return $data;
                        }
                        else
                        {
                            return 0;
                        }      
                    }
                    else
                    {return 0;}
                }
                else
                {
                    return 0;
                }
            }
        }
        function is_valid_paykickstart_ipn($data, $secret_key)
        {
            $paramStrArr = array();
            $paramStr = NULL;
            foreach($data as $key=>$value)
            {
                if($key == "verification_code") continue;
                if(!$key OR !$value) continue;
                $paramStrArr[] = (string) $value;
            }
            ksort( $paramStrArr, SORT_STRING );
            $paramStr = implode("|", $paramStrArr);
            $encKey = hash_hmac( 'sha1', $paramStr, $secret_key );
            return $encKey == $data["verification_code"] ;
        }
        

    } 
?>