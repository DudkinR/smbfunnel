<?php
    class CFPay_Authorize_payment
    {
        function __construct()
        {

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
            //============================||=========================
          
            $itemarr=array();
            
            $sheepingcharge=0;
            $tax=0;
            $totalprice=0;
            $productid=0;
            $currency="USD";
            $producttitle="";
            
            $allproductdetail="";
            
          
            $all_price_detail=$product;
            //'itemarr','sheepingcharge','tax','totalprice','currency','total', 'allproductdetail'
            if(is_array($all_price_detail))
            {
                foreach($all_price_detail as $all_price_detail_index=>$all_price_detail_val)
                {
                    ${$all_price_detail_index}=$all_price_detail_val;
                }
            }
            
            $allproductdetail .="<hr/>Total Price: ".number_format($totalprice,2)." ".$currency."<br>";
            
            $allproductdetail .="Tax: ".number_format($tax,2)." ".$currency."<br>";
            
            $allproductdetail .="Shipping Charge: ".number_format($sheepingcharge,2)." ".$currency;
            
            
            $_SESSION['total_paid'.get_option('site_token')]=$total;
            $_SESSION['payment_currency'.get_option('site_token')]=$currency;
            
            if(isset($_GET['execute']))
            {
            require 'api/vendor/autoload.php'; 
            
            //use net\authorize\api\contract\v1 as net\authorize\api\contract\v1;
            //use net\authorize\api\controller as net\authorize\api\controller;
            
            define("AUTHORIZENET_LOG_FILE","phplog");
            
            // Common setup for API credentials  
              $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();   
              $merchantAuthentication->setName($credentials->client_id);   
              $merchantAuthentication->setTransactionKey($credentials->client_secret);
              $otp=substr(str_shuffle('qwertyuiopasdfghjklzxcvbnm789456123'),0,1);  
              $refId='ref'.$otp.time();
            
            // Create the payment data for a credit card
              $creditCard = new net\authorize\api\contract\v1\CreditCardType();
              $creditCard->setCardNumber($_POST['cardnumber']);  
              $creditCard->setExpirationDate( $_POST['year']."-".$_POST['month']);
              $paymentOne = new net\authorize\api\contract\v1\PaymentType();
              $paymentOne->setCreditCard($creditCard);
            
            // Create a transaction
              $transactionRequestType = new net\authorize\api\contract\v1\TransactionRequestType();
              $transactionRequestType->setTransactionType("authCaptureTransaction");   
              $transactionRequestType->setAmount($total);
              $transactionRequestType->setPayment($paymentOne);
              $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
              $request->setMerchantAuthentication($merchantAuthentication);
              $request->setRefId( $refId);
              $request->setTransactionRequest($transactionRequestType);
              $controller = new net\authorize\api\controller\CreateTransactionController($request);
              $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);   
            
            if ($response != null) 
            {
              $tresponse = $response->getTransactionResponse();
  
               $tresponse->getResponseCode();
              
          //  print_r($tresponse);

              if (($tresponse != null) && ($tresponse->getResponseCode()=="1"))
              {
                //echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                //echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
                 $_SESSION['authorizedotnet_payer_name'.get_option('site_token')]=$_POST['name'];
                 $_SESSION['authorizedotnet_payer_email'.get_option('site_token')]=$_POST['email'];
                $tresponse= json_encode($tresponse);
             // print_r($tresponse);

   	$paymentdata=json_decode($tresponse);
    
          // print_r($paymentdata->transId); 
           
 //print_r($paymentdata);
    return array(
      'payer_name'=>$_POST['name'],
      'payer_email'=> $_POST['email'],
      'payment_id'=> $paymentdata->transId,
      'total_paid'=> $_SESSION['total_paid'.get_option('site_token')],
      'payment_currency'=> $_SESSION['total_paid'.get_option('site_token')]
      );


               
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
            ?>
            <html>
            <head>
            <title>Credit Card Payment</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
              <script src="assets/js/jquery-3.4.1.min.js"></script>
              <script src="assets/bootstrap/js/bootstrap.min.js"></script>
              <link rel="stylesheet" href="assets/css/style.css">
              <script>
              function cardValidation() {
                var valid = true;
                var cardUser=$('#card-user').val();
                var cardEmail=$('#card-email').val();
                var cardNumber = $('#card-number').val();
                var month = $('#month').val();
                var year = $('#year').val();
                
                if (cardUser.trim() == "") {
                       valid = false;
                }
                
                if (cardEmail.trim() == "") {
                       valid = false;
                }
            
                if (cardNumber.trim() == "") {
                       valid = false;
                }
            
                if (month.trim() == "") {
                        valid = false;
                }
                if (year.trim() == "") {
                    valid = false;
                }
            
                if(valid == false) {
                    $(".paymentErrors").css('display','block');
                }
                else
                {
                     $(".paymentErrors").css('display','none');
                }
            
                return valid;
            }
              </script>
              
            </head>
            <body>
            <div class="container-fluid">
            <div class="row">  
            <div class="col-sm-4 offset-sm-4" style="margin-top:50px;">
            <div class="card exclude-pnl">
            <div class="card-header" style="background:linear-gradient(#19334d,#19334d);">Credit Card Payment</div>
            <div class="card-body">
            <div class="paymentErrors alert alert-danger" style="display:none;">Please Fill All Fields</div>
              <div class="card card-default" style="margin-bottom:10px;">
                <div class="card-header" style="font-size:15px;color:rgb(0,0,0)">
                  Total <strong><?php echo number_format(round($total),2)." (".$currency.")" ?></strong> going to be paid,  <a data-toggle="collapse" href="#collapse1" style="color:#004080;"><u>View Detail</u></a>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                  <div class="card-body"><?php echo $allproductdetail; ?></div>
                  <div class="card-footer">Total: <?php echo number_format(round($total),2)." (".$currency.")" ?></div>
                </div>
              </div>
            <form action="index.php?page=do_payment&execute=1" method="POST" id="paymentForm" onsubmit="return cardValidation()">
            <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Your Name" id="card-user">
            </div>
            <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Your Email Id" id="card-email">
            </div>
            <div class="form-group">
            <label>Card Number</label>
            <input type="text" name="cardnumber" size="20" autocomplete="off" class="form-control" placeholder="Card Number" id="card-number"/>
            </div>
            
            <div class="form-group">
            <div class="row">
            <div class="col">
            <label>Expiration (MM/YYYY)</label>
            <div class="row">
            <div class="col">
            <input type="text" name="month" placeholder="MM" size="2" class="form-control" id="month" />
            </div>
            <div class="col">
            <input type="text" name="year" placeholder="YYYY" size="4" class="form-control" id="year"/>
            </div>
            </div>
            </div>
            
            </div>
            </div>
            <br>
            <div class="form-group">
            <input type="submit" id="makePayment" class="btn form-control theme-button" value="Make Payment">
            </div>
            </form>
            </div>
            </div>
            </div>
            </div></div>
            </body>
            </html>
            <?php }
          
        }
    } 
?>