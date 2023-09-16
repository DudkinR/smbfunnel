<?php
    class CFPay_Stripe_payment
    {
        private $mysqli;
        private $dbpref;
        function __construct()
        {
            global $mysqli;
            global $dbpref;
            $this->mysqli = $mysqli;
            $this->dbpref = $dbpref;
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

            $sheepingcharge=0;
            $tax=0;
            $totalprice=0;
            $currency="USD";
            $allproductdetail="";
            $all_price_detail=$product;
            
            if(is_array($all_price_detail))
            {
                foreach($all_price_detail as $all_price_detail_index=>$all_price_detail_val)
                {
                    ${$all_price_detail_index}=$all_price_detail_val;
                }
            }
            $name = $data['name'];
            $email = $data['email'];
            
            $allproductdetail .="<hr/>Total Price: ".number_format($totalprice,2)." ".$currency."<br>";
            $allproductdetail .="Tax: ".number_format($tax,2)." ".$currency."<br>";
            $allproductdetail .="Shipping Charge: ".number_format($sheepingcharge,2)." ".$currency;
            
            
            $_SESSION['total_paid'.get_option('site_token')]=$total;
            $_SESSION['payment_currency'.get_option('site_token')]=$currency;

            
            //cancel and success url
            $succesUrl = get_option('install_url')."/index.php/?page=do_payment&execute=1&status=success";
            $errorUrl  = get_option('install_url')."/index.php/?page=do_payment&execute=1&status=cancel";
            //checkout url
            $checkout_url = get_option('install_url')."/index.php/?page=do_payment&go_execute=1";
            //go back to payment page url
            $goback_url = get_option('install_url')."/index.php/?page=do_payment";

            

            $currency = strtolower($currency);
            $products = $all_price_detail['items'];
            $quantity = isset($user_data['data']['quantity'])? intval($user_data['data']['quantity']):1;
            
            require 'vendor/autoload.php';
            //This is your test secret API key.
            \Stripe\Stripe::setApiKey( $credentials->client_secret);

   
            if( isset( $_GET['status'] ) && isset($_GET['session_id']) && isset( $_GET['execute'] ) ){
               $data_s=false;
                if( $_GET['status'] == "success" )
                {
                    if( $credentials->payment_type == "payment")
                    {
                        $data_s = $this->oneTimepayment($credentials,$name,$email);
                    }
                    elseif( $credentials->payment_type == "subscription")
                    {
                        $data_s = $this->subscriptionBasedPayment($credentials,$name,$email);
                    }else{
                        $data_s=false;
                    }
                }else{
                    $data_s=false;
                }
                return $data_s;
            }
            elseif( isset( $_GET['go_execute'] )) {
                if( $credentials->payment_type == "payment")
                {
                    $product_arrs = [];
                    foreach( $products as $item )
                    {
                        if(empty($item['description']) || strlen($item['description']) <= 0 )
                        {
                            $item['description']=$item['title'];
                        }
                        $product_arrs['items'][]=[
                            'price_data' => [
                            'currency' => $currency,
                            'product_data' => [
                                'name' => $item['title'],
                                // 'description' => $item['description'],
                                'description' => $item['title'],
                            ],
                            'unit_amount' => ($item['price']*100),
                            ],
                                'quantity' => $quantity
                        ];
                    }
                    $product_arrs = $this->addTaxAndShippingCharg($currency,$all_price_detail,$product_arrs);
                    // header('Content-Type: application/json');
                    try{
                        $checkout_session = \Stripe\Checkout\Session::create([
                            'billing_address_collection'=>'required',       
                            "payment_method_types"=>["card"],    
                            'line_items' => [$product_arrs['items']],
                            'mode' => 'payment',
                            'success_url' => $succesUrl."&session_id={CHECKOUT_SESSION_ID}",
                            'cancel_url' => $errorUrl,
                            'customer_email' => $email
                        ]);
                        echo "<script>window.open('$checkout_session->url','_self')</script>";
                    }
                    catch(Exception $e)
                    {
                        echo $e->getMessage();
                        echo PHP_EOL."Please refresh the page or click on back button";

                    }

                }
    
                // For subsciption based payment
                elseif(  $credentials->payment_type == "subscription" )
                {
                    $product_arrs = [];
                    foreach( $products as $item )
                    {
                        if(empty($item['description']) || strlen($item['description']) <= 0 )
                        {
                            $item['description']=$item['title'];
                        }
                        $product_arrs['items'][]=[
                            'price'=>$item['productid'],
                            'quantity' => $quantity
                        ];
                    }
                    $product_arrs = $this->addTaxAndShippingCharg($currency,$all_price_detail,$product_arrs);
                    try{
                        $checkout_session = \Stripe\Checkout\Session::create([
                            'billing_address_collection'=>'required',       
                            "payment_method_types"=>["card"],    
                            'line_items' => [$product_arrs['items']],
                            'mode' => 'subscription',
                            'success_url' => $succesUrl."&session_id={CHECKOUT_SESSION_ID}",
                            'cancel_url' => $errorUrl,
                            'customer_email' => $email
                        ]);
                        
                        echo "<script>window.open('$checkout_session->url','_self')</script>";
                    }
                    catch(Exception $e)
                    {
                        echo $e->getMessage();
                        echo PHP_EOL."Please refresh the page or click on back button";
                    }
                }
            }else{
                echo ' <script>window.location.href="'.$checkout_url.'"</script>';
                exit;
            }
        }

        // For one time payemnt
        function oneTimepayment($credentials,$name,$email)
        {
            global $mysqli;
            $api_error="";
            $checkout_session_id = $mysqli->real_escape_string($_GET['session_id']);
            $stripe = new \Stripe\StripeClient($credentials->client_secret);
            try{
                $checkout_data = \Stripe\Checkout\Session::retrieve($checkout_session_id);
            }catch(Exception $e){
                $api_error = $e->getMessage();
            }
            if(empty($api_error) && $checkout_data && $checkout_data->payment_status=="paid" )
            {
                $customer_name = !empty($checkout_data->customer_details->name)?$checkout_data->customer_details->name:$name; 
                $customer_email = !empty($checkout_data->customer_email)?$checkout_data->customer_email:$email; 
                $response=array();
                $response['payer_name']=$customer_name;
                $response['payer_email']=$customer_email;
                $response['payment_currency']=strtoupper($checkout_data->currency);
                $response['total_paid']=($checkout_data->amount_total/100);
                $response['payment_id']=$checkout_session_id;
            }else{
                $response=0;
            }
            return $response;
        }
        // FOr subscription type payment
        function subscriptionBasedPayment($credentials,$name,$email)
        {
            global $mysqli;
            require 'vendor/autoload.php';
            if( isset( $_GET['status'] ) && $_GET['status']=="success" )
            {
                $fielddata=[];
                $fielddata['credentials']=json_encode($credentials);
                $fielddata['payment_type']='subscription';
                $api_error="";
                $checkout_session_id = $mysqli->real_escape_string($_GET['session_id']);
                $stripe = new \Stripe\StripeClient($credentials->client_secret);
                try{
                    $checkout_data = \Stripe\Checkout\Session::retrieve($checkout_session_id,[]);
                }catch(Exception $e){
                    $api_error = $e->getMessage();
                }

                 if(empty($api_error) && $checkout_data && $checkout_data->payment_status=="paid" )
                 {
                    $customer_name = !empty($checkout_data->customer_details->name)?$checkout_data->customer_details->name:$name; 
                    $customer_email = !empty($checkout_data->customer_email)?$checkout_data->customer_email:$email; 
                    $fielddata['last_payment_id']=$checkout_session_id;
                    $fielddata['last_payment_detail']=json_encode($checkout_data);
                     try{

                         $customer = $stripe->customers->retrieve($checkout_data->customer,[]);
                     }catch(Exception $e){
                         $api_error = $e->getMessage();
                     }
                     $fielddata['customer_id']=$checkout_data->customer;
                    $subscribe_id = $checkout_data->subscription;
                    $fielddata['subscription_id']=$subscribe_id;
                        // get subscriiption detials
                    $fielddata['subscription_detail']=json_encode($this->getSubscriptionDetail($stripe,$subscribe_id));
                    $sub_details = $this->getSubscriptionDuration($stripe,$subscribe_id);
                    
                    $fielddata['activated_on'] = $sub_details['data']['activated_on'];
                    $fielddata['payment_method'] = "stripe";
                    $fielddata['expires_on'] = $sub_details['data']['expires_on'];
                    $fielddata['status'] = $sub_details['data']['status'];
                    
                    $customer_name = $customer_email = ''; 
                    if(!empty($customer)){ 
                        $customer_name = !empty($customer->name)?$customer->name:$name; 
                        $customer_email = !empty($customer->email)?$customer->email:$email; 
                    }  
                    $this->addDataInSubscriptionTable($fielddata);
                    $response=array();
                    $response['payer_name']=$customer_name;
                    $response['payer_email']=$customer_email;
                    $response['payment_currency']=strtoupper($checkout_data->currency);
                    $response['total_paid']=($checkout_data->amount_total/100);
                    $response['payment_id']=$checkout_session_id;
                 }else{
                     $response=0;
                 }
            }else{
             $response=0;
            }
            return $response;
        }
        function billingForm($currency,$total,$allproductdetail,$checkout_url)
        {
            $form="";
            $install_url = get_option("install_url"); 
            ob_start();
            echo'
            <!DOCTYPE html><html><head>
                    <title>Stripe Checkout</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
                    <script src="'.$install_url.'/assets/js/jquery-3.4.1.min.js"></script>
                    <script src="'.$install_url.'/assets/bootstrap/js/bootstrap.min.js"></script>
                    <link rel="stylesheet" href="'.$install_url.'/assets/css/style.css"/>
                </head>
                <body class="bg-light">
                    <div class="container-fluid">
                        <div class="row">	
                            <div class="col-sm-4 offset-sm-4" style="margin-top:50px;">
                                <div class="card exclude-pnl">
                                    <div class="card-header" style="background:linear-gradient(#19334d,#19334d);">Process Payment</div>
                                    <div class="card-body">
                                        <div class="card card-default" style="margin-bottom:10px;">
                                            <div class="card-header bg-default" style="font-size:15px;color:rgb(0,0,0)">
                                                Total <strong>';
                                                echo $total." (".$currency.")";
                                                echo '</strong> going to be paid,  <a data-toggle="collapse" href="#collapse1" style="color:#004080;">
                                                <u>View Detail</u></a>
                                            </div>
                                            <div id="collapse1" class="panel-collapse collapse">
                                                <div class="card-body">';
                                                 echo $allproductdetail;
                                                 echo '</div>
                                                <div class="card-footer">Total:';
                                                echo $total." (".$currency.")";
                                                echo '</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-warning btn-block" id="checkout-button">Checkout</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        const btn = document.getElementById("checkout-button")
                        btn.addEventListener("click", function(e) {
                            e.preventDefault();
                            window.location.href="'.$checkout_url.'"
                        });
                    </script>
                </body>
            </html>';
            $form.=ob_get_clean();
            return $form;
        }
        function getSubscriptionDetail($stripe,$subscribe_id)
        {
            $data=  $stripe->subscriptions->retrieve(
                $subscribe_id,
                []
            );   
            return $data;
        }
        function getSubscriptionDuration($stripe, $id)
        {
            $detail= $this->getSubscriptionDetail($stripe, $id);
            return $this->readSubscriptionDuration($detail);
        }
        function readSubscriptionDuration($sub)
        {
            $stat= ['status'=> false, 'data'=> [], 'error'=> ''];
            if(isset($sub->id))
            {
                $data= array
                (
                    'expires_on'=> 0,
                    'activated_on'=> null,
                    'status'=> null,
                );
                $ac_stat= 'active';

                if(in_array($sub->status, ['canceled', 'unpaid', 'incomplete_expired', 'past_due']))
                {
                    $ac_stat= 'canceled';
                }
                else if(in_array($sub->status, ['incomplete']))
                {
                    $ac_stat= 'paused';
                }
                $data['status']= $ac_stat;
                $data['expires_on']= (int) $sub->current_period_end;
                $data['activated_on'] = date('Y-m-d H:i:s', $sub->current_period_start);
                
                $stat['status'] = true;
                $stat['data'] = $data;
            }
            else
            {
                $stat['error']= "Unsupported subscription object provided";
            }
            return $stat;
        }

        function addTaxAndShippingCharg($currency,$all_price_detail,$product_arrs)
        {
            if( isset( $all_price_detail['sheepingcharge'] ) && $all_price_detail['sheepingcharge'] > 0 )
            {
                $shprice = $all_price_detail['sheepingcharge'];
                $product_arrs['items'][]=[
                    'price_data' => [
                      'currency' => $currency,
                      'product_data' => [
                        'name' => 'Shipping Cost',
                      ],
                    'unit_amount' => ($shprice*100),
                    ],
                        'quantity' => 1
                ];
            }
            if( isset( $all_price_detail['tax'] ) && $all_price_detail['tax'] > 0 )
            {
                $taxes = $all_price_detail['tax'];
                $product_arrs['items'][]=[
                    'price_data' => [
                      'currency' => $currency,
                      'product_data' => [
                        'name' => 'Tax',
                      ],
                    'unit_amount' => ($taxes*100),
                    ],
                        'quantity' => 1
                ];
            }

            return $product_arrs;
        }
        function addDataInSubscriptionTable( $data )
        {
            global $dbpref;
            $table = $dbpref."subscriptions";
            return $this->insert( $table, $data );
        }
        function listenWebhookEvent($stripe_id,$method)
        {
            global $mysqli;
            global $dbpref;
            $method= $mysqli->real_escape_string($method);
            $id= (int)$mysqli->real_escape_string($stripe_id);
            $table=$dbpref."payment_methods";
            $qry=$mysqli->query("SELECT * FROM `$table` WHERE `method` ='$method' AND `id`=$id ");
            
            if( $qry->num_rows > 0 )
            {
                $data =  $qry->fetch_object();

                $credentials = json_decode($data->credentials);
                require 'vendor/autoload.php';
                //This is your test secret API key.
                 \Stripe\Stripe::setApiKey( $credentials->client_secret);
                $stripe = new \Stripe\StripeClient($credentials->client_secret);
                $endpoint_secret = $credentials->endpoint_secret;

                $payload = @file_get_contents('php://input');
                $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
                $event = null;
                try {
                    $event = \Stripe\Webhook::constructEvent(
                        $payload, $sig_header, $endpoint_secret
                    );
                    // for testing
                    // $event = \Stripe\Event::constructFrom(
                    //     json_decode($payload, true)
                    //   );
                } catch(\UnexpectedValueException $e) {
                    // Invalid payload
                    $res['error']= "Invalid payload";
                    $err= true;
                } 
                catch(\Stripe\Exception\SignatureVerificationException $e) {
                    // Invalid signature
                    $res['error']= "Invalid Signature";
                    $err= true;
                }
                $type= "";
                if(!$err)
                {
                    $subscriptionSchedule= false;
                    // Handle the event
                    switch ($event->type) 
                    {
                        case 'customer.deleted':
                            {
                                $type= 'customer_deleted';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        case 'customer.updated':
                            {
                                $type= 'customer_updated';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        case 'customer.subscription.created':
                            {
                                $type= 'subscription_updated';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        case 'customer.subscription.updated':
                            {
                                $type= 'subscription_updated';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        case 'customer.subscription.pending_update_applied':
                            {
                                $type= 'subscription_updated';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        case 'customer.subscription.pending_update_expired':
                            {
                                $type= 'subscription_updated';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        case 'customer.subscription.deleted':
                            {
                                $type= 'subscription_deleted';
                                $subscriptionSchedule = $event->data->object;
                                break;
                            }
                        default:
                            {
                                //echo 'Received unknown event 
                                $type ='unknown';
                                $subscriptionSchedule= false;
                                $res['error']= "Received unknown event type";
                                break;
                            }
                    }

                    if($subscriptionSchedule)
                    {
                        $sub_data= $subscriptionSchedule;
                        $data= array
                        (
                            'type'=> false,
                            'action'=> false,
                            'subscription'=> 0, 
                            'customer'=> 0, 
                            'data'=> $sub_data,
                        );
                        $data= (object) $data;
                        if(strpos($type, 'subscription_')===0)
                        {
                            $data->subscription= $sub_data->id;
                            $data->type= 'subscription';
                            $data->action= str_replace('subscription_', '', $type);
                        }
                        else if(strpos($type, 'customer_')===0)
                        {
                            $data->customer= $sub_data->id;
                            $data->type= 'customer';
                            $data->action= str_replace('customer_', '', $type);
                        }
                        $this->addorupdateSalesAndMembership( $data, $type,$stripe );
                    }
                }                
            }
        }

        function addorupdateSalesAndMembership( $data, $type,$stripe)
        {

            $type= false;
            $action= false;
            $id= 0;
            
            if(in_array($data->type, ['subscription', 'customer']))
            {
                $type= $data->type;
                $action= $data->action;
                $id= $data->$type;
            }
            if($type && $action && $id)
            {
                $this->refreshSubscription($type, $action, $id,$stripe);
            }

        }
        function refreshSubscription($type, $action, $id,$stripe)
        {
            $subs = $this->getSubscriptions($id,$type.'_id');
            if($subs)
            {
                while($sub = $subs->fetch_object())
                {

                    $session_id = $sub->last_payment_id;

                    if( $action == "deleted" )
                    {
                        $update_data=['status'=>"canceled"];   
                        $this->update('subscriptions',$update_data,[ $type.'_id'=>$id ]);
                        $this->deleteorupdateMemberAndSales($session_id,'deleted');
                        echo json_encode(['status'=>true,'status_code'=>200,'message'=>'Deleted Customer Details']);
    
                    }
                    else if( $action == "updated"  )
                    {
                        $stripe_sub= $this->getSubscriptionDuration( $stripe, $sub->subscription_id );

                        if($stripe_sub['status'] )
                        // if( true )
                        {

                            $sub_data= $stripe_sub['data'];
                            // $sub_data= [

                            // ];
                            // $sub_data['status']= 'canceled';
                            // $sub_data['expires_on']= '1644664870';
                            // $sub_data['activated_on'] = date('Y-m-d H:i:s', '1641986470');
                            $this->deleteorupdateMemberAndSales($session_id,$sub_data['status']);

                            $update_data=
                            [
                                'status'=> $sub_data['status'],
                                'expires_on'=> $sub_data['expires_on'],
                                'activated_on'=> $sub_data['activated_on']
                            ];   
                            $this->update('subscriptions',$update_data,[ $type.'_id'=>$id ]);
                            echo json_encode(['status'=>true,'status_code'=>200,'message'=>'Updated Subscription Details']);
                        }
                    }
                }
            }
        }
        function insert( $table, $data )
        {
            $field = implode(", ", array_map(function($key){  global $mysqli; $ke = $mysqli->real_escape_string($key); return "`$ke`"; },array_keys($data)));
            $value = implode(", ", array_map(function($val){ global $mysqli; $va=  $mysqli->real_escape_string($val);  return "'$va'"; },array_values( $data ) ) );
            $qry   = "INSERT INTO `$table` ($field) VALUES ($value)";
            $run   = $this->mysqli->query($qry);
            if( $run && !$this->mysqli->error )
            {
                return $this->mysqli->insert_id;

            }else{
                return false;
            }
        }
        function update( $table, $data, $ids)
        {
            $table=$this->dbpref.$table;
            $update_field='';
            $where_field='';
            try{

                if(is_array($data) && is_array($ids))
                {

                    $arr_key = array_keys($data);
                    $id_key = array_keys($ids);
        
                    for( $i=0; $i<count($arr_key); $i++ )
                    {
                        if( $i==count($arr_key)-1 )
                        {
                            $update_field.="`".$arr_key[$i]."`='".$data[$arr_key[$i]]."' ";
                        }else{
                            $update_field.="`".$arr_key[$i]."`='".$data[$arr_key[$i]]."', ";
                        }
                    }
                    for( $i=0; $i<count($ids); $i++ )
                    {
                        if( $i==count($id_key)-1 )
                        {
                            $where_field.="`".$id_key[$i]."`='".$ids[$id_key[$i]]."' ";
                        }else{
                            $where_field.="`".$id_key[$i]."`='".$ids[$id_key[$i]]."', ";
                        }
                    }
                    $qry = "UPDATE  `".$table."` SET ".$update_field." WHERE $where_field";
                    $run  = $this->mysqli->query($qry)?1:0;
    
                    if( $run && !$this->mysqli->error )
                    {
                        return true;
                    }
                    return false;
                }else{
                    throw new Exception('The second and third parameters must be the associative arrays');
                }
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }
        function delete( $table,$ids )
        {
            $table=$this->dbpref.$table;
            try{
                if(is_array($ids))
                {
                    $where_field='';
                    $id_key = array_keys($ids);
                    for( $i=0; $i<count($id_key); $i++ )
                    {
                        if( $i==count($id_key)-1 )
                        {
                            $where_field.="`".$id_key[$i]."`='".$ids[$id_key[$i]]."' ";
                        }else{
                            $where_field.="`".$id_key[$i]."`='".$ids[$id_key[$i]]."', ";
                        }
                    }

                    $qry = "DELETE FROM  `$table`  WHERE $where_field";
                    $run  = $this->mysqli->query($qry)?1:0;
                    if( $run && !$this->mysqli->error )
                    {
                        return true;
                    }
                    return false;
                }else{
                    throw new Exception('The second parameter must be an associative array');
                }
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }
        
        function getSubscriptions($id,$id_type )
        {
            //$id_type id||subscription_id||customer_id||last_payment_id;
            
            if(array_search($id_type, ['id', 'subscription_id', 'customer_id', 'last_payment_id'])===false)
            {
                throw new \Exception("'id_type' should be 'id' or 'subscription_id' or 'customer_id' or 'user' '".$id_type."' provided");
            }
            $table = $this->dbpref.'subscriptions';
            $sql = "SELECT * FROM `$table` WHERE `$id_type`='$id' ORDER BY `id` DESC";
            $run = $this->mysqli->query($sql);
            if( $run->num_rows > 0 )
            {
                return $run;
            }
            return false;
        }
        function deleteorupdateMemberAndSales($session_id,$action){
            $table = $this->dbpref."all_sales";
            $qry = "SELECT `membership`,`id` FROM `$table` WHERE `payment_id`='$session_id'";
            $run = $this->mysqli->query($qry);
            if( $run->num_rows )
            {

                while( $data =  $run->fetch_object() )
                {
                    $member = $data->membership;
                    if( $action == "deleted"   )
                    {    
                        $update_data=['valid'=>0];   
                        $this->update('all_sales',$update_data,[ 'payment_id'=>$session_id ]);
                        if(!empty($member))
                        {
                            $this->update('quick_member',$update_data,[ 'id'=>$member ]);
                        }
                    }
                    elseif(  $action == "canceled"  )
                    {    
                        $update_data=['valid'=>0];   
                        $this->update('all_sales',$update_data,[ 'payment_id'=>$session_id ]);
                        if(!empty($member))
                        {
                            $this->update('quick_member',$update_data,[ 'id'=>$member ]);
                        }
                    }
                    else
                    {    
                        $update_data=['valid'=>0];   
                        $this->update('all_sales',$update_data,[ 'payment_id'=>$session_id ]);
                        if(!empty($member))
                        {
                            $this->update('quick_member',$update_data,[ 'id'=>$member ]);
                        }
                    }
                }          
            }
        }

        function getStripe($method,$stripe_id)
        {
            global $mysqli;
            global $dbpref;
            $method= $mysqli->real_escape_string($method);
            $id= (int)$mysqli->real_escape_string($stripe_id);
            $table=$dbpref."payment_methods";
            $qry=$mysqli->query("SELECT `credentials` FROM `$table` WHERE `method` ='$method' AND `id`=$id ");
            $data = $qry->fetch_object();
            return json_encode($data->credentials);

        }
        function get_product_title($product_id,$get='title')
        {
            global $mysqli;
            global $dbpref;
            $id= (int)$mysqli->real_escape_string($product_id);
            $table=$dbpref."all_products";
            $qry=$mysqli->query("SELECT `$get` FROM `$table` WHERE `id`=$id");
            if( $qry->num_rows > 0)
            {
                $data = $qry->fetch_object();
                return $data->title;
            }
            return false;

        }
        function get_sales( $email,$funnel_id )
        {
            global $mysqli;
            global $dbpref;
            $email= $mysqli->real_escape_string($email);
            $funnel_id= (int)$mysqli->real_escape_string($funnel_id);
            $table=$this->dbpref."quick_member";
            $all_sales=$dbpref."all_sales";
            $subscriptions=$dbpref."subscriptions";
    
            $get_all = $mysqli->query("select `id` from `".$table."` where `email`='".$email."' and `funnelid`='".$funnel_id."'");
            if( $get_all->num_rows > 0 )
            {
                $arr=[];
                while( $ids = $get_all->fetch_object() )
                {
                    $sql = "SELECT s.`expires_on`,s.`status`,sl.`purchase_email`,sl.`purchase_name`,sl.`productid`,sl.`payment_id` FROM  `$subscriptions` as s INNER JOIN `$all_sales` as sl ON s.`last_payment_id`=sl.`payment_id` WHERE `paymentmethod` LIKE '%stripe%' AND `membership`='$ids->id'";
                    $qry = $mysqli->query( $sql );
                    if( $qry->num_rows > 0)
                    {
                        while($data = $qry->fetch_assoc())
                        {
                            $arr[]=$data;

                        }
                    }
                }
                return $arr;
            }
            return false;
        }
        function customer_portal($rsession_id,$stripe_id,$method)
        {
            global $mysqli;
            global $dbpref;
            $session_id= $mysqli->real_escape_string($rsession_id);
            $method= $mysqli->real_escape_string($method);
            $id= (int)$mysqli->real_escape_string($stripe_id);
            $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            $rsession_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $url;
            $return_url= $mysqli->real_escape_string($rsession_url);
            $table=$dbpref."payment_methods";
            $qry=$mysqli->query("SELECT `credentials` FROM `$table` WHERE `method` ='$method' AND `id`=$id ");
            if( $qry->num_rows > 0 )
            {
                $data =  $qry->fetch_object();
                $credentials = json_decode($data->credentials);
                $this->oepnCustomerPortal($credentials,$session_id,$return_url);
                
            }
        }
        function oepnCustomerPortal($credentials,$session_id,$return_url)
        {

            require 'vendor/autoload.php';
            //This is your test secret API key.
                \Stripe\Stripe::setApiKey( $credentials->client_secret);
                header('Content-Type: application/json');


            try {
            $checkout_session = \Stripe\Checkout\Session::retrieve( $session_id );

            // Authenticate your user.
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $checkout_session->customer,
                'return_url' => $return_url,
            ]);
            header("HTTP/1.1 303 See Other");
            header("Location: " . $session->url);
            } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            echo "Please go back";
            }

        }
        
        
    }
?>