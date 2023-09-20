<?php
    class CFPay_Ccavenue_payment
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
           
            include('Crypto.php');
            $credentials=json_decode($payment_setup['credentials']);
            $itemarr=array();
            $sheepingcharge=0;
            $tax=0;
            $totalprice=0;
            $productid=0;
            $currency="INR";
            $producttitle="";

            $allproductdetail="";

            $order_data_array=$_SESSION['order_form_data'.get_option('site_token')];

            $all_price_detail=$product;
            if(is_array($all_price_detail))
            {
                foreach($all_price_detail as $all_price_detail_index=>$all_price_detail_val)
                {
                    ${$all_price_detail_index}=$all_price_detail_val;
                }
            }

            $allproductdetail .="Total Price: ".number_format($totalprice,2)." ".$currency."\n";
            $allproductdetail .="Tax: ".number_format($tax,2)." ".$currency."\n";
            $allproductdetail .="Shipping Charge: ".number_format($sheepingcharge,2)." ".$currency;
            $allproductdetail=str_replace("<br>","\n",$allproductdetail);

            $_SESSION['total_paid'.get_option('site_token')]=$total;
            $_SESSION['payment_currency'.get_option('site_token')]=$currency;

            $price = $total;
            $name="User";
            if(isset($order_data_array['data']['name']))
            {
                $name=$order_data_array['data']['name'];
            }
            else if(isset($order_data_array['data']['firstname']))
            {
                $name=$order_data_array['data']['firstname'];
                if(isset($order_data_array['data']['lastname']))
                {
                    $name .=" ".$order_data_array['data']['lastname'];
                }
            }
            $phone = (isset($order_data_array['data']['phone']))? $order_data_array['data']['phone']:'';
            $email = (isset($order_data_array['data']['email']))? $order_data_array['data']['email']:'';

            $ccid=trim($credentials->client_id);
            $ccs=trim($credentials->client_secret);
            $workingkey=trim($credentials->salt);
            $ccav_path_req=($credentials->type=='1')?"https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction":"https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
            
            if(isset($_GET['execute']))
            {
                
                $encResponse=$_POST["encResp"];//This is the response by the CCAvenue Server
                $rcvdString=decrypt($encResponse,$workingkey);//Crypto Decryption used as per the specified working key.
                $order_status="";
                $decryptValues=explode('&', $rcvdString);
                $dataSize=sizeof($decryptValues);

                for($i = 0; $i < $dataSize; $i++) 
                {
                    $information=explode('=',$decryptValues[$i]);
                    
                    if($i==3)	$order_status=$information[1];
                    
                    if($i==1)	$tracking_id=$information[1];
                    
                }

                if($order_status==="Success")
                {
                    
                    //echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
                    
                    $response['payment_id']=$tracking_id;
                    $response['payer_name']=$name;
                    $response['payer_email']=$email;
                    $response['payment_currency']=$currency;
                    $response['total_paid']=number_format($total,2);
                    $_GET['page']='do_payment';
		            $_GET['execute']=0;
                    return $response;

                }
                else if($order_status==="Aborted")
                {
                    //echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
                    return 0;
                
                }
                else if($order_status==="Failure")
                {
                    //echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
                    return 0;
                }
                else
                {
                    //echo "<br>Security Error. Illegal access detected";
                    return 0;
                }
                //echo "</center>";
            }
            else
            {
            $redirect_url=get_option('install_url')."/index.php?page=do_payment_execute";

            $randomid = mt_rand(100000,999999); 
            $order_id="ORD".$randomid;
            $merchant_data='tid=&merchant_id='.$ccid.'&amount='.$price.'&currency='.$currency.'&order_id='.$order_id.'&billing_name='.$name.'&billing_tel='.$phone.'&billing_email='.$email.'&execute=1&promo_code=&redirect_url='.$redirect_url;


            $encrypted_data=encrypt($merchant_data,$workingkey);// Method for encrypting the data.
                ?>
                <form method="post" name="redirect" action="<?php echo $ccav_path_req ?>">
            <?php
            echo "<input type='hidden' name='encRequest' value=\"$encrypted_data\">";
            echo "<input type='hidden' name='access_code' value=\"$ccs\">";
            ?>
            </form>
            <script language='javascript'>document.redirect.submit();</script>
            <?php
            } 
           
        }
    } 
?>