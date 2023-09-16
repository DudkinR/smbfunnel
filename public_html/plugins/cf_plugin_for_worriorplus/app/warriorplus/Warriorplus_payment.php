<?php
    class CFPay_Warriorplus_payment
    {
        function __construct()
        {

        }
        function doPayment($payment_setup, $product, $callback_url)
        {
            $user_data=get_requested_order();
            $data=array('name'=>'','email'=>'');

            $credentials=json_decode($payment_setup['credentials']);
//print_r($credentials);
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
                if($_SERVER['REQUEST_METHOD'] !=="POST"){return 0;}
                if(isset($_POST['WP_ITEM_NAME']))
                {
                // echo   $credentials=json_decode($credentials);
                    if($_POST['WP_ACTION']=='sale')
                    {
                    if((strlen(trim($credentials->client_secret))<1)|| (isset($_POST['WP_SECURITYKEY']) && $_POST['WP_SECURITYKEY']==$credentials->client_secret))
                    {
                    $data=$_POST;
            
                   $data['payment_id']=$_POST['WP_SALEID'];
                    $data['payer_email']=$_POST['WP_BUYER_EMAIL'];
                    $data['payer_name']=$_POST['WP_BUYER_NAME'];
                    $data['total_paid']= $_POST['WP_SALE_AMOUNT'];
                    $data['payment_currency']= $_POST['WP_SALE_CURRENCY'];
                    $data['ipn_tax']=0;
                    return $data;
                   
                    }
                    else
                    {
                        return 0;
                    }
                    }
                    else
                    {
                        return 0;
                    }
                }
                else
                {
                    return 0;
                }
            }
        }
        
    } 
?>