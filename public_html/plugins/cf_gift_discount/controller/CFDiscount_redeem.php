<?php
    class CFDiscount_redeem
    {
        var $laod;
        function __construct($arr)
        {
            if(isset($arr['loader']))
            {
                $this->load=$arr['loader'];
            }
        }
        function checkGiftCard( array $data )
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref."gift_cards";
            $gift_code = $mysqli->real_escape_string( $data['gift_code'] );
            $currency = get_session('cfdisc_order_data')['payment_currency'];
            $date = $mysqli->real_escape_string( $data['currentdate'] );
            $currency=strtoupper($currency);
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if( $access=='admin' )
            {
                $row = $mysqli->query("SELECT `remaining_value`,`status`,`currency`,`id`,`expiration_type`,`expiration_date` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='giftcard'"); 
            }else{
                $row = $mysqli->query("SELECT `remaining_value`,`status`,`currency`,`id`,`expiration_type`,`expiration_date` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='giftcard' AND `user_id`=".$user_id.""); 
            }

            $response = array("status"=>0,"error"=>"err", 'type'=>'error', 'message'=>'Error');
            if( $row->num_rows > 0 )
            {
                $r = $row->fetch_object();
                if( $r->status == 0 )
                {
                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Invalid gift card' );
                    return json_encode($response);
                }
                if( $r->expiration_type =='set_expiration' )
                {
                    $sdate=$r->expiration_date;
                    if( $date > $sdate )
                    {
                        $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! The gift card code has expired!' );
                        return json_encode($response);
                    }
                }
                if( $r->remaining_value == 0 )
                {
                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! You have already redeemed the gift card!.' );
                    return json_encode($response);
                }
                if( $r->currency == $currency )
                {
                    $response = array("status"=>1,"error"=>"no",  'type'=>'redeem');
    
                }else{
                    $response = array("status"=>0,"error"=>"err", 'type'=>'currency', 'message'=>'Gift card currency should be same as product currency!');
                }
            }else{
    
                $response = array("status"=>0,"error"=>"err", 'type'=>'invalid_code', 'message'=>'Invalid gift card!');
            }

            return json_encode($response);
        }

        function checkDiscount( array $data )
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref."gift_cards";
            $gift_code = $mysqli->real_escape_string( $data['discount_code'] );
            $date = $mysqli->real_escape_string( $data['currentdate'] );
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if( $access=='admin' )
            {
                $row = $mysqli->query("SELECT `status`,`id`,`redeem_no`,`expiration_type`,`expiration_date` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='percentage'");
            }else{
                $row = $mysqli->query("SELECT `status`,`id`,`redeem_no`,`expiration_type`,`expiration_date` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='percentage' AND `user_id`=".$user_id."");
            }
            $response = array("status"=>0,"error"=>"err", 'type'=>'error', 'message'=>'Error');
            if( $row->num_rows > 0 )
            {
                $r = $row->fetch_object();
                if( $r->status == 0 )
                {
                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Invalid discount code!' );
                    return json_encode($response);
                }
                if( $r->redeem_no == 0 )
                {
                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! The discount code has expired!'  );
                    return json_encode($response);
                }
                elseif( $r->expiration_type =='set_expiration' )
                {
                    $edate=$r->expiration_date;                 
                    if( $date > $edate )
                    {
                        $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! The discount code has expired!' );
                        return json_encode($response);
                    }else{

                        $response = array("status"=>1,"error"=>"no",  'type'=>'redeem');
                        return json_encode($response);
                    }
                }
                else{
                    $response = array("status"=>1,"error"=>"no",  'type'=>'redeem');
                }
            }else{
    
                $response = array("status"=>0,"error"=>"err", 'type'=>'invalid_code', 'message'=>'Invalid discount code!');
            }
            return json_encode($response);
        }

        function addNotificationAfterGiftcardRedeem($data){
            global $mysqli;
            global $dbpref;
            global $app_variant;
            $table1=$dbpref."issue_gift";
            $table=$dbpref."gift_cards";
            $payment_id=$data['payment_data'][0]['payment_id'];
            $product_id=$data['payment_data'][0]['product_id'];
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $d = get_sales( array( 'payment_id'=>$payment_id ,'product_id'=>$product_id ));
            $sell_id =cf_enc( $d[0]['id'] );
            $payment_data=json_decode($data['payment_data'][0]['data']);
            $name=$payment_data->payer_name;
            $email=$payment_data->payer_email;
            $giftcard=get_session('cfredeem_giftcard_successfully');
            $giftcard_id = $giftcard['data']['gift_id'];
            $giftcard_balance = $giftcard['data']['for_restore'];
            $remaining_value = $giftcard['data']['remaining_value'];
            $giftcard_currency = $giftcard['data']['currency'];
            $funneld_id = $data['payment_data'][0]['funnel_id'];
            $date_created  = date( "Y-m-d H:i:s",time() );
            if( $app_variant =="shopfunnels" )
            {
                $commen = "$name<a target='_blank' href='index.php?page=sales&store_id=$funneld_id&sell_id=".$sell_id."' class='text-primary'>($email)</a> paid $giftcard_balance $giftcard_currency with this gift card on order #<a target='_blank' href='index.php?page=sales&store_id=$funneld_id&sell_id=".$sell_id."' class='text-primary'>($payment_id)</a>.";
            }else{
                $commen = "$name<a target='_blank' href='index.php?page=sales&sell_id=".$sell_id."' class='text-primary'>($email)</a> paid $giftcard_balance $giftcard_currency with this gift card on order #<a target='_blank' href='index.php?page=sales&sell_id=".$sell_id."' class='text-primary'>($payment_id)</a>.";
            }
                
            $comment=$mysqli->real_escape_string($commen);
            $mysqli->query("UPDATE `".$table."` SET `remaining_value`='".$remaining_value."' WHERE `id`='".$giftcard_id."'");
            $mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`,`order_id`, `comment`,`name`,`email`,`type`,`last_deduct_value` ,`created_at`,`user_id`) VALUES ( '".$giftcard_id."','".$payment_id."', '".$comment."', '".$name."','".$email."','giftcard','".$giftcard_balance."','".$date_created."',".$user_id.")");
            unset_session('cfredeem_giftcard_successfully');
            unset_session('cfdisc_oldallproductdetail');
            unset_session('cfdisc_order_data');
        }
        function addNotificationAfterDiscountRedeem( $data ){
            global $mysqli;
            global $dbpref;
            global $app_variant;
            $table1=$dbpref."issue_gift";
            $table=$dbpref."gift_cards";
            $payment_id=$data['payment_data'][0]['payment_id'];
            $product_id=$data['payment_data'][0]['product_id'];
            $d = get_sales( array( 'payment_id'=>$payment_id ,'product_id'=>$product_id ));
            $sell_id =cf_enc( $d[0]['id'] );
            $payment_data=json_decode($data['payment_data'][0]['data']);
            $name=$payment_data->payer_name;
            $email=$payment_data->payer_email;
            $funneld_id = $data['payment_data'][0]['funnel_id'];
            $giftcard=get_session('cfredeem_discount_successfully');
            $giftcard_id = $giftcard['data']['gift_id'];
            $giftcard_balance = $giftcard['data']['for_restore'];
            $remaining_attempt = $giftcard['data']['remaining_attempt'];
            $giftcard_currency = $giftcard['data']['currency'];
            $date_created  = date( "Y-m-d H:i:s",time() );
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            if( $app_variant =="shopfunnels" )
            {
                $commen = "$name<a target='_blank' href='index.php?page=sales&store_id=$funneld_id&sell_id=".$sell_id."' class='text-primary'>($email)</a> paid $giftcard_balance $giftcard_currency with this discount code on order #<a target='_blank' href='index.php?page=sales&store_id=$funneld_id&sell_id=".$sell_id."' class='text-primary'>($payment_id)</a>.";
            }
            else{
                $commen = "$name<a target='_blank' href='index.php?page=sales&sell_id=".$sell_id."' class='text-primary'>($email)</a> paid $giftcard_balance $giftcard_currency with this discount code on order #<a target='_blank' href='index.php?page=sales&sell_id=".$sell_id."' class='text-primary'>($payment_id)</a>.";
            }
            $comment=$mysqli->real_escape_string($commen);
            $mysqli->query("UPDATE `".$table."` SET `redeem_no`='".$remaining_attempt."' WHERE `id`='".$giftcard_id."'");
            $mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`,`order_id`, `comment`,`name`,`email`,`type`,`last_deduct_value` ,`created_at`,`user_id`) VALUES ( '".$giftcard_id."','".$payment_id."', '".$comment."', '".$name."','".$email."','discount','".$giftcard_balance."','".$date_created."',".$user_id.")");
            unset_session('cfredeem_discount_successfully');
            unset_session('cfdisc_oldallproductdetail');
            unset_session('cfdisc_order_data');
        }
        function redeemGiftCard( $gift_code,$order,$order_data )
        {

            global $mysqli;
            global $dbpref;
            global $app_variant;
            $table = $dbpref."gift_cards";
            $gift_code = $mysqli->real_escape_string(  $gift_code );
            $total_amount = $order['subtotal_price'];
            $currency =$order['payment_currency'];
            if( isset($order_data['order_session']['product_id']) &&  $order_data['order_session']['optional_products']  )
            {

                $oproducts = $order_data['order_session']['product_id'];
                if( $order_data['order_session']['optional_products'] )
                {
                    $optional_products = $this->getProductId( $order_data['order_session']['optional_products'] );
                }else{
                    $optional_products = [];
                }
            }else{
                $oproducts=false;
            }
           
            $currency=strtoupper($currency);
            $row = $mysqli->query("SELECT `remaining_value`,`apply_type`,`products`,`status`,`currency`,`id` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='giftcard'");
            $return_val=false;
            if( $row->num_rows > 0 )
            {
                $r = $row->fetch_object();
                // check currecy equal or not
                if( $r->status == 0 )
                {
                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Invalid gift card!' );
                    return false;
                }
                if( $r->currency == $currency )
                {
                    $remaining_value =  $r->remaining_value ;
                    $gift_id =  $r->id;
                    $products = json_decode( $r->products, true );
                    if(   $remaining_value > 0 )
                    {
                        if( $remaining_value == $total_amount )
                        {
                            $new_total = 0;
                            $new_remaining_value=0;
                            $for_restore = $remaining_value;
                        }
                        elseif( $remaining_value > $total_amount )
                        {
                            $remaining_value = $remaining_value-$total_amount;
                            $new_total = 0;
                            $new_remaining_value = $remaining_value;
                            $for_restore = $total_amount;
                            
                        }elseif( $remaining_value < $total_amount )
                        {
                            $new_total = $total_amount- $remaining_value;
                            $new_remaining_value = 0;  
                            $for_restore = $remaining_value;
                        }
    
                        $total = $new_total;
                        $remaining_value =$new_remaining_value;

                        if( $r->apply_type == "custom" )
                        {

                            if( count( $products ) > 0 )
                            {
                                if( $oproducts )
                                {
                                    if( in_array( $oproducts,$products ) )
                                    {
                                        $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'type'=>'success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'original_total'=>$total_amount, 'message'=>'Success','subtotal_price'=> $total,'remaining_value'=>$remaining_value,'for_restore'=> $for_restore,'gift_code' => $gift_code);
                                        $return_val= true;
                                    }elseif( array_intersect( $optional_products,$products ) )
                                    {
                                        $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'type'=>'success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'original_total'=>$total_amount, 'message'=>'Success','subtotal_price'=> $total,'remaining_value'=>$remaining_value,'for_restore'=> $for_restore,'gift_code' => $gift_code);
                                        $return_val= true;
                                    }else{

                                        $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! You cannot use this giftcard code on these product(s)!' );
                                        return [ 'status'=>false,'data'=> $response ];
                                    } 
                                }else{
                                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! You cannot use this giftcard code on these product(s)!' );
                                    return [ 'status'=>false,'data'=> $response ];
                                }  
                            }else{

                                $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'type'=>'success', 'message'=>'Success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'original_total'=>$total_amount, 'subtotal_price'=> $total,'remaining_value'=>$remaining_value,'for_restore'=> $for_restore,'gift_code' => $gift_code);
                                $return_val= true;
                            }
                        }else{

                            $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'type'=>'success', 'message'=>'Success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'original_total'=>$total_amount, 'subtotal_price'=> $total,'remaining_value'=>$remaining_value,'for_restore'=> $for_restore,'gift_code' => $gift_code);
                            $return_val= true;
                        }

                        
                    }else{
                        $response = array("status"=>0,"error"=>"err", 'type'=>'redeem', 'message'=>'You have already redeemed the gift card!');
                        $return_val=false;
                    }
    
                }else{
                    $response = array("status"=>0,"error"=>"err", 'type'=>'currency', 'message'=>'Gift card currency should be same as product currency!');
                    $return_val=false;
                }
            }else{

                $response = array("status"=>0,"error"=>"err", 'type'=>'invalid_code', 'message'=>'Invalid gift card!');
                $return_val=false;
            }
            return ['status'=>$return_val,'data'=>$response];
        }
        function redeemDiscount( $gift_code,$order,$order_data )
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref."gift_cards";
            $gift_code = $mysqli->real_escape_string(  $gift_code );
            $total_amount = $order['subtotal_price'];
            $currency =$order['payment_currency'];
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            // if app is not a shopfunels

            if( isset($order_data['order_session']['product_id']) &&  $order_data['order_session']['optional_products']  )
            {
                $oproducts = $order_data['order_session']['product_id'];
                if( $order_data['order_session']['optional_products'] )
                {
                    $optional_products = $this->getProductId( $order_data['order_session']['optional_products'] );
                }else{
                    $optional_products = [];
                }
            }else{
                $oproducts=false;
            }
            $currency=strtoupper($currency);
            if( $access=='admin' )
            {
                $row = $mysqli->query("SELECT `discount_type`,`status`,`apply_type`,`products`,`percentage`,`redeem_no`,`id` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='percentage'");
            }else{
                $row = $mysqli->query("SELECT `discount_type`,`status`,`apply_type`,`products`,`percentage`,`redeem_no`,`id` FROM `".$table."` WHERE `gift_code`='".$gift_code."' AND `discount_type`='percentage' AND `user_id`=$user_id");
            }
            $return_val=false;
            if( $row->num_rows > 0 )
            {
                $r = $row->fetch_object();
                $products = json_decode( $r->products, true );
                if( $r->status == 0 )
                {
                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Invalid discount code!' );
                    return ['status'=>false,'data'=>$response];
                }
                
                if( $r->redeem_no > 0 )
                {
                    // echo $total_amount;
                    $percentage = $r->percentage;
                    $percentage=$percentage/100;
                    $new_total = $percentage*$total_amount;
                    $net_total = $total_amount-$new_total;
                    $gift_id =  $r->id;
                    $redeem_no = $r->redeem_no-1;

                    if( $r->apply_type == "custom" )
                    {
                        if( count( $products ) > 0 )
                        {
                            if( $oproducts )
                            {
                                if( in_array( $oproducts,$products ) )
                                {
                                    $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'percentage'=>$r->percentage, 'type'=>'success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'],  'original_total'=>$total_amount, 'message'=>'Success','subtotal_price'=> $net_total,'remaining_attempt'=>$redeem_no,'for_restore'=> $new_total,'gift_code' => $gift_code);
                                    $return_val= true;
                                }elseif( array_intersect( $optional_products,$products ) )
                                {
                                    $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'percentage'=>$r->percentage, 'type'=>'success', 'original_total'=>$total_amount, 'message'=>'Success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'subtotal_price'=> $net_total,'remaining_attempt'=>$redeem_no,'for_restore'=> $new_total,'gift_code' => $gift_code);
                                    $return_val= true;
                                }else{
                                    $response = array( "status"=>0,"error"=>"err", 'type'=>'invalid', 'message'=> 'Sorry! You cannot use this discount code on these product(s)!' );
                                    return [ 'status'=>false,'data'=> $response ];
                                }   
                            }else{
                                $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'percentage'=>$r->percentage, 'type'=>'success', 'original_total'=>$total_amount, 'message'=>'Success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'subtotal_price'=> $net_total,'remaining_attempt'=>$redeem_no,'for_restore'=> $new_total,'gift_code' => $gift_code);
                                $return_val= true;
                            }
                        }else{
                            $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'percentage'=>$r->percentage, 'type'=>'success', 'original_total'=>$total_amount, 'message'=>'Success','shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'subtotal_price'=> $net_total,'remaining_attempt'=>$redeem_no,'for_restore'=> $new_total,'gift_code' => $gift_code);
                            $return_val= true;
                        }
                    }else{
                        $response = array("status"=>1,"error"=>"no", 'gift_id'=>$gift_id, 'currency'=>$currency, 'percentage'=>$r->percentage, 'type'=>'success', 'shipping_charge'=>$order['shipping_charge'],'tax_amount'=>$order['tax_amount'],'subtotal_price'=>$order['subtotal_price'], 'original_total'=>$total_amount, 'message'=>'Success','subtotal_price'=> $net_total,'remaining_attempt'=>$redeem_no,'for_restore'=> $new_total,'gift_code' => $gift_code);
                        $return_val= true;
                    }
                }else{
                    $response = array("status"=>0,"error"=>"err", 'type'=>'currency', 'message'=>'Sorry! This discount code has expired now!');
                    $return_val=false;
                }
            }else{
                $response = array("status"=>0,"error"=>"err", 'type'=>'invalid_code', 'message'=>'Invalid discount code!');
                $return_val=false;
            }
            return ['status'=>$return_val,'data'=>$response];
        }

        public function getProductId( $product_ids)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."all_products";
            $optional_products=[];
            if( count( $product_ids ) > 0 )
            {
                foreach($product_ids  as $product_id )
                {
                    $pid=$mysqli->real_escape_string( $product_id );
                    $sql = $mysqli->query( "SELECT `id` FROM `".$table."` where `productid`='$pid'" );
                    if( $sql->num_rows > 0 )
                    {
                        $data = $sql->fetch_object();
                        $optional_products[] = $data->id;
                    }
                }
            }
            return $optional_products;
        }
        public function getProductArray( $product_id,$getOnly=false)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."all_products";
            $pid=$mysqli->real_escape_string( $product_id );
            if( $getOnly ){
                if( is_array( $getOnly ) ) {
                    $in_indexes= implode(',', array_map(function($pp){ return "`".$pp."`"; }, $getOnly));

                }else{
                    $in_indexes= "`$getOnly`";
                }
                $sql = $mysqli->query( "SELECT $in_indexes FROM `".$table."` where `id`=$pid" );
            }else{
                $sql = $mysqli->query( "SELECT * FROM `".$table."` where `id`=$pid" );
            }
            if( $sql->num_rows > 0 )
            {
                $data = $sql->fetch_assoc();
                return $data;
            }
            return false;
        }
        public function IfGiftProductThenCreateGiftCard( $datas ){       
            if( $datas ){

                foreach( $datas['payment_data'] as $payment_data )
                {
                    $product_id = $payment_data['product_id'];
                    $products = $this->getProductArray( $product_id ,['p_type','currency','price']);
                    if( $products['p_type'] == "gift_card")
                    {
                        $data = [
                            'gift_code'=> bin2hex(random_bytes(10)),
                            'action'=>'savegiftcards_ajax',
                            'savegiftcards'=> 'save',
                            'is_gift_product'=> 'yes',
                            'giftcard_id'=> '',
                            'initial_value'=> $products['price'],
                            'currency'=> $products['currency'],
                            'apply_type'=> 'all',
                            'expiration_type'=> 'no_expiration',
                            'expiration_date'=> '2030-10-18',
                            'status'=> 1,
                            'discount_type'=>'giftcard',
                            'notes'=>'',
                            'member_id'=> '-1',
                        ];
                        $giftob = $this->load->load("giftcard");
                        $d = $giftob->createGiftCard( $data );
                        $da = json_decode($d,true);
                        $data['giftcard_id'] = $da['last_id'];
                        $data['name'] = $payment_data['payer_name'];
                        $data['email'] = $payment_data['payer_email'];
                        $data['type'] = "giftcard";
                        $giftob->sendCode( $data );
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }

    }