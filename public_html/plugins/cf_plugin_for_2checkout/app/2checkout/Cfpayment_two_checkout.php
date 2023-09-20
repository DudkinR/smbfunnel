<?php
class Cfpayment_two_checkout
{
    function __construct()
    {

    }
    function loadPaymentPage($credentials,$product_detail_arr,$doo="init")
    {
        
        $data=get_requested_order();
        if(!$data){die('Unable to process the payment');}

        foreach($product_detail_arr as $index=>$val)
        {
            ${$index}=$val;
        }

        $credentials=json_decode($credentials);

        if($doo=="process")
        {
            //Assign the returned parameters to an array.
            $params = array();
            foreach ($_REQUEST as $k => $v) {
                $params[$k] = $v;
            }
  
            //Check the MD5 Hash to determine the validity of the sale.
            $passback = Twocheckout_Return::check($params, "tango", 'array');
  
          if ($passback['code'] == 'Success') 
          {
            //$id = $params['merchant_order_id'];
            $order_number = $params['order_number'];
            $invoice_id = $params['invoice_id'];

            return array(
                'payer_name'=> (isset($data['data']['name']))? $data['data']['name']:'',
                'payer_email'=> (isset($data['data']['email']))? $data['data']['email']:'',
                'payment_id'=>  $order_code,
                'total_paid'=> $total,
                'payment_currency'=> $currency
            );


          }
          else
          {
              return false;
          }
        }
        else
        {
            require_once('2checkout-php-master/lib/Twocheckout.php');
    
            $args = array(
                'sid' => $credentials->seller_id,
                'mode' => "2CO",
                'li_0_name' => 'Products',
                'li_0_price' => $total,
                'card_holder_name' => (isset($data['data']['name']))? $data['data']['name']:'',
                'email' => (isset($data['data']['email']))? $data['data']['email']:'',
                /*
                'street_address' => '123 test st',
                'city' => 'Columbus',
                'state' => 'Ohio',
                'zip' => '43123',
                'country' => 'USA',*/
                'x_receipt_link_url'=>add_query_arg(array('page'=>'do_payment','execute'=>1),get_option('install_url')),
                'demo'=>($credentials->type=='1')? true:false
            );
            Twocheckout_Charge::redirect($args);
        }
    }
    function processPyment(){

    }
    function doPayment($data,$product)
    {
        if(isset($_GET['execute']))
        {
            return self::loadPaymentPage($data['credentials'],$product,'process');
        }
        else
        {
            self::loadPaymentPage($data['credentials'],$product);
        }
    }
}

?>