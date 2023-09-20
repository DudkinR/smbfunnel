<?php
    
    require plugin_dir_path( __FILE__ ). 'vendor/fedapay/fedapay-php/init.php';

class CFPay_fedapay_payment
{
    function __construct()
    {

    }
    function doPayment($payment_setup,$product,$callback_url)
    {
        $user_data=get_requested_order();
        $data=array('name'=>'','email'=>'','phone'=>'');
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
            if(isset($user_data['data']['phone']))
            {
                $data['phone']=$user_data['data']['phone'];
            }
        }
        if(!isset($_GET['execute']))
        {
            self::initiate($credentials,$product,$callback_url,$data);
        }
        else
        {
            return self::callBack($credentials,$product,$data);
        }
    }
    function initiate($credentials,$product,$callback_url,$user_data)
    {
        

        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }

  
        $email = $user_data['email'];
        $phonenumber = "";
        $name=explode(" ", $user_data['name']);
        $first_name=isset($name[0])?$name[0]:"";
        $last_name=isset($name[1])?$name[1]:"";

        \FedaPay\FedaPay::setApiKey(trim($credentials->secret_key));
        if( $credentials->type=='1' )
        { 
          \FedaPay\FedaPay::setEnvironment('live');  
          
        }
        else{
       
          \FedaPay\FedaPay::setEnvironment('sandbox');  
          
        }
        $currency=trim($currency);
        //die($currency);
        // echo strtolower(trim($currency))."!="."xof";
      
        if (strtolower(trim($currency)) != "xof" ) {
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

          <div class="alert alert-warning">fedapay only supports XOF as currency for now. Please select XOF currrency or contact the store manager.</div>

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
        try{
          
          if( strlen(trim($first_name)) <= 0 )
          {
            throw new Exception("Missing first_name paramter");
          }
          else if( strlen(trim($last_name)) <= 0 )
          {
            throw new Exception("Missing last_name paramter");
          }
          else if( strlen(trim($email)) <= 0 )
          {
            throw new Exception("Missing email paramter");            
          }else{
            $redirect_url=get_option('install_url')."/index.php?page=do_payment&execute=1";
            $arr = array(
              "description" =>(!empty($description) )? $description: "Fedapay Payment" ,
              "amount" =>ceil($total),
              "currency" => ["iso" => $currency],
              "callback_url" =>$redirect_url,
              "customer" => [
              "firstname" => $first_name,
              "lastname" => $last_name,
              "email" => $email,  
              ]
              );
              $transaction= \FedaPay\Transaction::create($arr);
          }

        
          $token = $transaction->generateToken();
          return header('Location: ' . $token->url);
        }
        catch(Exception $e)
        {
          echo "Unable to load payment page ".$e->getMessage();
          exit();
        }

    }
    function callBack($credentials,$product,$user_data)
    {
      foreach($product as $index=>$val)
      {
        ${$index}=$val;
      }
      global $mysqli;
      $id=$mysqli->real_escape_string(trim($_GET['id']));
      \FedaPay\FedaPay::setApiKey(trim($credentials->secret_key));
      
      if( $credentials->type=='1' )
      { 
        \FedaPay\FedaPay::setEnvironment('live'); 
      }
      else{
        \FedaPay\FedaPay::setEnvironment('sandbox');  
      }
      
      $transaction = \FedaPay\Transaction::retrieve($id);

      if ($transaction->status == "approved") {
        
          $arr=array(
              'payer_name'=> $user_data['name'],
              'payer_email'=> $user_data['email'],
              'payment_id'=> $id,
              'total_paid'=>ceil($total),
              'payment_currency'=> $currency,
              
            );
            return $arr;
      }
      else{
        return false;
      }
    }
} 
?>