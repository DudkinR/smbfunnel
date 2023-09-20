<?php
class CFPay_Pesapal_payment
{
    private $consumer_key;
    private $consumer_secret;
    private $api;
    var $token;
    var $params;
    var $signature_method;
    
    var $QueryPaymentStatus;
    var $QueryPaymentStatusByMerchantRef;
    var $querypaymentdetails;
    var $consumer;
    
    
    function doPayment($payment_setup,$product,$callback_url)
    {
        include_once('OAuth.php');
        $credentials=json_decode($payment_setup['credentials']);
        
        $this->api=($credentials->type=='1')? 'https://www.pesapal.com':'https://demo.pesapal.com/';

        $this->QueryPaymentStatus         =   $this->api.'/API/QueryPaymentStatus';
        $this->QueryPaymentStatusByMerchantRef  =   $this->api.'/API/QueryPaymentStatusByMerchantRef';
        $this->querypaymentdetails        =   $this->api.'/API/querypaymentdetails';

        $this->token = $this->params = NULL;
        $this->consumer_key = $credentials->consumer_key;
        $this->consumer_secret = $credentials->secret_key;
        $this->consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret);
        $this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();



      $user_data=get_requested_order();
        $data=array('name'=>'','email'=>'','phone'=>'');
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
            if(isset($user_data['data']['phone']))
            {
                $data['phone']=$user_data['data']['phone'];
            }
        }
        if(!isset($_GET['execute']))
        {
            self::initiate($product,$callback_url,$data);
        }
        else
        {
          if(isset($_GET['pesapal_merchant_reference']))
          {
            $pesapalMerchantReference = $_GET['pesapal_merchant_reference'];
          }

          if(isset($_GET['pesapal_transaction_tracking_id']))
          { 
            $pesapalTrackingId = $_GET['pesapal_transaction_tracking_id'];
          }

          $status =  self::checkStatusUsingTrackingIdandMerchantRef($pesapalTrackingId,$pesapalMerchantReference);

            if( $status )
            {
              $data = self::getTransactionDetails($pesapalTrackingId,$pesapalMerchantReference,$credentials,$product,$data);
              return $data;
              exit;
            }
        }
      }

    function initiate($product,$callback_url,$user_data)
    {
        include_once('OAuth.php');
        // $callback_url.="&success=true";
        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }

      if ( !in_array( strtolower($currency) ,array("kes","tzs","ugx")) ) {
        ?>
          <!DOCTYPE html>
          <html lang="en">
              <head>
              <meta name="viewport" content="width=device-width, initial-scale=1">
                  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                  <title>Purchase</title>
                  <!-- jQuery is used only for this example; it isn't required to use Stripe -->
                  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
                  <script src="assets/js/jquery-3.4.1.min.js"></script>
                  <script src="assets/bootstrap/js/bootstrap.min.js"></script>
                  <link rel="stylesheet" href="assets/css/style.css"> 
              </head>
              <body>
          <div class="container-fluid">
          <div class="row"> 
          <div class="col-sm-4 offset-sm-4" style="margin-top:50px;">
          <div class="card exclude-pnl">
          <div class="card-header" style="background:linear-gradient(#19334d,#19334d);">Error! </div>
          <div class="card-body">
          <div class="paymentErrors alert alert-danger" style="display:none;"></div>

          <div class="alert alert-warning">Pesapal only supports KES, TZS, UGX as currency for now. Please choose KES, TZS, UGX currrency or contact the store manager.</div>

          </div>
          </div>
          </div>
          </div>
          </div>
              </body>
          <style>
          .panel
          {
            -webkit-box-shadow: 2px 4px 9px -2px rgba(0,0,0,0.75);
          -moz-box-shadow: 2px 4px 9px -2px rgba(0,0,0,0.75);
          box-shadow: 2px 4px 9px -2px rgba(0,0,0,0.75);
          }
          </style>  
          </html>
          <?php
          exit();
        } 
    
        $email = $user_data['email'];
        $phonenumber = (!empty($user_data['phone']))?$user_data['phone']:" ";
        $name=explode(" ", $user_data['name']);
        $first_name=isset($name[0])?$name[0]:"";
        $last_name=isset($name[1])?$name[1]:"";

        $iframelink=$this->api."/api/PostPesapalDirectOrderV4";
       
       //get form details
        $amount = number_format($total, 2);//format amount to 2 decimal places

        $desc = (!empty($description))? $description: " ";
        $type = "MERCHANT"; //default value = MERCHANT
        $reference = bin2hex(random_bytes( 10 ));//unique order id of the transaction, generated by merchant

        $currency=strtoupper($currency);
        $post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
           <PesapalDirectOrderInfo 
            xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
              xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" 
              Currency=\"".$currency."\" 
              Amount=\"".$amount."\" 
              Description=\"".$desc."\" 
              Type=\"".$type."\" 
              Reference=\"".$reference."\" 
              FirstName=\"".$first_name."\" 
              LastName=\"".$last_name."\" 
              Email=\"".$email."\" 
              PhoneNumber=\"".$phonenumber."\" 
              xmlns=\"http://www.pesapal.com\" />";
      $post_xml = htmlentities($post_xml);

        //post transaction to pesapal
        $iframe_src = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, "GET", $iframelink, $this->params);
        $iframe_src->set_parameter("oauth_callback", $callback_url);
        $iframe_src->set_parameter("pesapal_request_data", $post_xml);
        $iframe_src->sign_request($this->signature_method, $this->consumer, $this->token);

        //display pesapal - iframe and pass iframe_src
        ?>
        <iframe src="<?php echo $iframe_src;?>" width="100%" height="700px"  scrolling="no" frameBorder="0">
            <p>Browser unable to load iFrame</p>
        </iframe>
        <?php
    }


    function checkStatusUsingTrackingIdandMerchantRef($pesapalTrackingId,$pesapalMerchantReference){
    include_once('OAuth.php');
    //get transaction status
      $request_status = OAuthRequest::from_consumer_and_token(
                  $this->consumer, 
                  $this->token, 
                  "GET", 
                  $this->QueryPaymentStatus, 
                  $this->params
                );
      $request_status->set_parameter("pesapal_merchant_reference", $pesapalMerchantReference);
      $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
      $request_status->sign_request($this->signature_method, $this->consumer, $this->token);
      
        
        $status = $this->curlRequest($request_status);
      
        return $status;
      }
      
      function getTransactionDetails($pesapalTrackingId,$pesapalMerchantReference,$credentials,$product,$user_data)
      {
       include_once('OAuth.php');
        $request_status = OAuthRequest::from_consumer_and_token(
                    $this->consumer, 
                    $this->token, 
                    "GET", 
                    $this->querypaymentdetails, 
                    $this->params
                  );
        $request_status->set_parameter("pesapal_merchant_reference", $pesapalMerchantReference);
        $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
        $request_status->sign_request($this->signature_method, $this->consumer, $this->token);
      
        $responseData = $this->curlRequest($request_status);
        
        $pesapalResponse = explode(",", $responseData);
        
        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }
        
        if($pesapalResponse[2]=="COMPLETED")
        {
            $arr=array(
              'payer_name'=> $user_data['name'],
              'payer_email'=> $user_data['email'],
              'payment_id'=> $pesapalResponse[3],
              'total_paid'=> $total,
              'payment_currency'=> $currency,
            );
       
          return $arr;
          
        }
        else if( $pesapalResponse[2]=='PENDING' )
        {
           $responseData1 = $this->curlRequest($request_status);
        
           $pesapalResponse1 = explode(",", $responseData1);
            if($pesapalResponse1[2]=="COMPLETED")
            {
                $arr=array(
                  'payer_name'=> $user_data['name'],
                  'payer_email'=> $user_data['email'],
                  'payment_id'=> $pesapalResponse[3],
                  'total_paid'=> $total,
                  'payment_currency'=> $currency,
                );
           
              return $arr;
              
            }
        
        }
        else{
            return 0;
        }
      }

      function curlRequest($request_status){
         include_once('OAuth.php');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_status);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){
          $proxy_tunnel_flag = (
              defined('CURL_PROXY_TUNNEL_FLAG') 
              && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE'
            ) ? false : true;
          curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
          curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
          curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
        }
        
        $response           = curl_exec($ch);
        $header_size        = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $raw_header         = substr($response, 0, $header_size - 4);
        $headerArray        = explode("\r\n\r\n", $raw_header);
        $header           = $headerArray[count($headerArray) - 1];
        
        //transaction status
        $elements = preg_split("/=/",substr($response, $header_size));
        $pesapal_response_data = $elements[1];
        
        return $pesapal_response_data;
      }
} 
?>