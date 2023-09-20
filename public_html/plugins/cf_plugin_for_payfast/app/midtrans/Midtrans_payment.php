<?php
//create a namesapce
namespace Midtrans;
    class CFPay_Midtrans_payment
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
            $itemarr          =   array();
            $sheepingcharge   =   0;
            $tax              =   0;
            $totalprice       =   0;
            $productid        =   0;
            $currency         =   "IDR";
            $producttitle     =   "";
            
            $allproductdetail =  "";
            
            $order_data_array =  $_SESSION['order_form_data'.get_option('site_token')];
            
            $all_price_detail =  $product;
            
            if( is_array( $all_price_detail ) )
            {
                foreach( $all_price_detail as $all_price_detail_index=>$all_price_detail_val )
                {
                    ${$all_price_detail_index}=$all_price_detail_val;
                }
            }
            
            $allproductdetail .= "Total Price: ".number_format($totalprice,2)." ".$currency."\n";
            $allproductdetail .= "Tax: ".number_format($tax,2)." ".$currency."\n";
            $allproductdetail .= "Shipping Charge: ".number_format($sheepingcharge,2)." ".$currency;
            $allproductdetail  = str_replace("<br>","\n",$allproductdetail);
            
            $_SESSION['total_paid'.get_option('site_token')]       =  $total;
            $_SESSION['payment_currency'.get_option('site_token')] =  $currency;
            
            $price = $total;
            // $name="User";
            $firstname="User";
            $lastname="User";
            if(isset($order_data_array['data']['name']))
            {
                $name       =   explode(" ",$order_data_array['data']['name']);
                $firstname  =  (isset($name[0])) ? $name[0]:'';
                $lastname   =  (isset($name[1]))  ? $name[1]:'';
            
            }
            else if(isset($order_data_array['data']['firstname']))
            {
                $firstname  = $order_data_array['data']['firstname'];
                if(isset( $order_data_array['data']['lastname']) )
                {
                    $lastname  = " ".$order_data_array['data']['lastname'];
                }
            }
            $phone        =    (isset($order_data_array['data']['phone']))? $order_data_array['data']['phone']:'Not Specified';
            $email        =    (isset($order_data_array['data']['email']))? $order_data_array['data']['email']:'Not Specified';
            $postalcode      =    (isset($order_data_array['data']['zip']))? $order_data_array['data']['zip']:'Not Specified';
            $address   =    (isset($order_data_array['data']['address']))? $order_data_array['data']['address']:'Not Specified';
            $city         =    (isset($order_data_array['data']['city']))? $order_data_array['data']['city']:'Not Specified';
            
            $country_code =    (isset($order_data_array['data']['country_code']))? $order_data_array['data']['countrycode']:'IDN';
            
            require_once  "vendor/midtrans/midtrans-php/Midtrans.php";
            
            $midmkey  =  trim($credentials->client_id);
            $midckey  =  trim($credentials->client_secret);
            $midskey  =  trim($credentials->salt);
            
            
            
                if(! isset($_GET['execute']) )
                {
                    
                                //Set Your server key
                    Config::$serverKey  =  $midskey;
                    
                    if( $credentials->type == "1" ){
                        
                        // Uncomment for production environment
                        Config::$isProduction = true;
                    }
                    elseif( $credentials->type == "0" ){
                        // Uncomment for production environment
                        Config::$isProduction = false;
                    }
                    
                    // Uncomment to enable sanitization
                    Config::$isSanitized = true;
                    
                    // Uncomment to enable 3D-Secure
                    Config::$is3ds = true;
                
                    
                    // Required
                    $transaction_details = array(
                        'order_id'       =>   mt_rand(),
                        'gross_amount'   =>   (int)$price, // no decimal allowed for creditcard
                    );
                
                    
                    // Optional
                    $billing_address = array(
                        'first_name'    => $firstname,
                        'last_name'     => $lastname,
                        'address'       => $address,
                        'city'          => $city,
                        'postal_code'   => $postalcode,
                        'phone'         => $phone,
                        'country_code'  => $country_code
                    );
                    
                    // Optional
                    $shipping_address = array(
                        'first_name'    => $firstname,
                        'last_name'     => $lastname,
                        'address'       => $address,
                        'city'          => $city,
                        'postal_code'   => $postalcode,
                        'phone'         => $phone,
                        'country_code'  => $country_code
                    );
                    
                    // Optional
                    $customer_details = array(
                        'first_name'        =>  $firstname,
                        'last_name'         =>  $lastname,
                        'email'             =>  $email,
                        'phone'             =>  $phone,
                        'billing_address'   =>  $billing_address,
                        'shipping_address'  =>  $shipping_address
                    );
                    
                    // Fill SNAP API parameter
                    $params = array(
                        'transaction_details'   =>  $transaction_details,
                        'customer_details'      =>  $customer_details,
                    );
                    try {
                        // Get Snap Payment Page URL
                        $paymentUrl  =  Snap::createTransaction($params)->redirect_url;
                      
                        // Redirect to Snap Payment Page
                        echo "<script>window.location='".$paymentUrl."'</script>";
                    }
                    catch ( Exception $e ) {
                        echo  $e->getMessage();
                        return 0;
                    }
                }
                else
                {
                    if( isset($_GET['success']) && $_GET['success']=="true" ){
                        
                        //Set Your server key
                        Config::$serverKey  =  $midskey;
                        
                        if( $credentials->pay_type == "1" ){
                            
                            // Uncomment for production environment
                            Config::$isProduction = true;
                        }
                        elseif( $credentials->pay_type == "0" ){
                            // Uncomment for production environment
                            Config::$isProduction = false;
                        }
                    
                        //get the order_id form URL
                        $order_id=isset($_GET['order_id']) ? $_GET['order_id']: $_GET['id'];
                        
                        
                        //get status form order id
                        $notif = \Midtrans\Transaction::status($order_id);
                        $transaction   =  $notif->transaction_status;
                        $type          =  $notif->payment_type;
                        $order_id      =  $notif->order_id;
                        $fraud         =  $notif->fraud_status;
                        $status_code   =  $notif->status_code;
                        $gross_amount  =  $notif->gross_amount;
                        $fetch_signature_key  = $notif->signature_key;
                        $signature_key        = hash("sha512" ,$order_id."".$status_code."".$gross_amount."".$midskey);
                        if( !empty($status_code) && isset($status_code) && $status_code==200 )
                        {
                            //verify signature key
                            if( $fetch_signature_key === $signature_key )
                            {
                                if ($transaction == 'capture') {
                                    // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                                    if ($type == 'credit_card') {
                                        if ($fraud == 'challenge') {
                                            // TODO set payment status in merchant's database to 'Challenge by FDS'
                                            // TODO merchant should decide whether this transaction is authorized or not in MAP
                                            // echo "Transaction order_id: " . $order_id ." is challenged by FDS";
                                            // $cancel = \Midtrans\Transaction::cancel($order_id);
                                            return 0;
                                        } else {
                                            // TODO set payment status in merchant's database to 'Success'
                                            $response['payer_name']=$firstname." ".$lastname;
                                            $response['payer_email']=$email;
                                            $response['payment_id']=$order_id;
                                            $response['payment_currency']=$currency;
                                            $response['total_paid']=number_format($total,2);
                                            return $response;
                                        }
                                    }
                                } else if ($transaction == 'settlement') {
                                    // TODO set payment status in merchant's database to 'Settlement'
                                    $response['payer_name']=$firstname." ".$lastname;
                                    $response['payer_email']=$email;
                                    $response['payment_id']=$order_id;
                                    $response['payment_currency']=$currency;
                                    $response['total_paid']=number_format($total,2);
                                    return $response;
                                    
                                } else if ($transaction == 'pending') {
                                    // TODO set payment status in merchant's database to 'Pending'
                                    // $cancel = \Midtrans\Transaction::cancel($order_id);
                                    return 0;
                                    
                                } else if ($transaction == 'deny') {
                                    // TODO set payment status in merchant's database to 'Denied'
                                    // $cancel = \Midtrans\Transaction::cancel($order_id);
                                    return 0;
                                    
                                } else if ($transaction == 'expire') {
                                    // TODO set payment status in merchant's database to 'expire'
                                    // $cancel = \Midtrans\Transaction::cancel($order_id);
                                    return 0;
                                    
                                } else if ($transaction == 'cancel') {
                                    // TODO set payment status in merchant's database to 'Denied'
                                    // $cancel = \Midtrans\Transaction::cancel($order_id);
                                    return 0;
                                }
                            }else
                            {
                                return 0;
                            }
                        }elseif(!empty($status_code) && isset($status_code) && $status_code==201)
                        {
                            
                            // $cancel = \Midtrans\Transaction::cancel($order_id);
                            return 0;
                        }else
                        {
                            // $cancel = \Midtrans\Transaction::cancel($order_id);
                            return 0;
                                
                        }
                        
                    }else
                    {
                        return 0;
                    }
            
                }   
           
        }
    } 
?>