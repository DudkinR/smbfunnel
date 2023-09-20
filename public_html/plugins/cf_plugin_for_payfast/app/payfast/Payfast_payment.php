<?php

class CFPay_Payfast_payment
{
    function __construct()
    {

    }
    function doPayment($payment_setup,$product,$callback_url)
    {
        $user_data=get_requested_order();
        
        $data=array('name'=>'','firstname'=>'','lastname'=>'','email'=>'');

        $credentials=json_decode($payment_setup['credentials']);

        if(isset($user_data['data']))
        {
            if(isset($user_data['data']['firstname']))
            {
                $data['firstname']=$user_data['data']['firstname'];
            }
            if(isset($user_data['data']['lastname']))
            {
                $data['lastname']=$user_data['data']['lastname'];
            }
            if(isset($user_data['data']['name']))
            {
                $data['name']=$user_data['data']['name'];
            }
            if(isset($user_data['data']['email']))
            {
                $data['email']=$user_data['data']['email'];
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

    function generateSignature($data, $passPhrase = null) {
    // Create parameter string
        $pfOutput = '';
        foreach( $data as $key => $val ) {
        if(!empty($val)) {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }
        }
    // Remove last ampersand
    $getString = substr( $pfOutput, 0, -1 );
    if( $passPhrase !== null ) {
        $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
    }
    return md5( $getString );
    }

    function initiate($credentials,$product,$callback_url,$user_data)
    {
        if(has_session('payfast_pay_token'))
        {
            unset_session('payfast_pay_token');
        }
        foreach($product as $index=>$val)
        {
            ${$index}=$val;
        }
        $email = $user_data['email'];
        $firstname = $user_data['firstname'];
        $lastname = $user_data['lastname'];
        $notify_url=plugin_dir_url(dirname(__FILE__))."payfast/notify.php";
        $cf_token=substr(str_shuffle('ASDFGHJKZXCVBNMQWERTYU1234567890'),0,5);
        $cf_token .=time();
        $cf_token='pyfst_'.$cf_token;
        set_session('payfast_pay_token',$cf_token);
        $api_url=($credentials->type=='1')? 'https://www.payfast.co.za/eng/process':'https://sandbox.payfast.co.za/eng/process';
        $pfHost = ($credentials->type=='1')? 'www.payfast.co.za' : 'sandbox.payfast.co.za';
        // Construct variables
        $cartTotal = $total;// from $product array.
        
        set_session('currency',$currency);
        set_session('pfHost',$pfHost);
        $myvalue = $allproductdetail;
        $arr = explode(' ',trim($myvalue));
        $myitem=$arr[0];
        $data = array(
        // Merchant details
        'merchant_id' => $credentials->public_key,
        'merchant_key' => $credentials->secret_key,
        'return_url' => $callback_url, 
         //'cancel_url' => $callback_url,
        'notify_url' => $notify_url,
         //Buyer details
        'name_first' => $firstname,
        'name_last'  => $lastname,
        'email_address'=> $email,
        //Transaction details
        'm_payment_id' => $cf_token, //Unique payment ID to pass through to notify_url
        'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
        'item_name' => $myitem
        );
        $signature = self::generateSignature($data);
        $data['signature'] = $signature;
        $htmlForm = '<form name="myForm" id="myForm"  action="'.$api_url.'" method="post">';
        foreach($data as $name=> $value)
        {
            $htmlForm .= '<input name="'.$name.'" type="hidden" value="'.$value.'" />';
        }
        $htmlForm .= '<input style="display:none;" type="submit" value="Pay Now" /></form>';
        echo $htmlForm;
        echo "<script>document.forms['myForm'].submit();</script>";
    }
    
    function callBack($credentials,$product,$user_data)
    {
    $file_path=plugin_dir_path(dirname(__FILE__))."payfast/newfile.txt";
    $myfile = fopen($file_path, "r") or die("Unable to open file!");
    $query_string=fread($myfile,filesize($file_path));
    $pfParamString=$query_string;
    fclose($myfile);
    $response_pay=explode("&",$pfParamString);
    //print_r($response_pay);
    $payer_name=$response_pay[18];
    $payment_status=$response_pay[2];
    $payer_email=$response_pay[20];
    $payment_id=$response_pay[1];
    $total_paid=$response_pay[5];
    $payer_name = explode("=",$payer_name)[1];
    $payer_email = urldecode(explode("=",$payer_email)[1]);
    $payment_id = explode("=",$payment_id)[1];
    $total_paid = explode("=",$total_paid)[1];
    $payment_status = explode("=",$payment_status)[1];  
    $currency=get_session('currency');  
    $arr=array(
                'payer_name'=> $payer_name,
                'payer_email'=> $payer_email,
                'payment_id'=> $payment_id,
                'total_paid'=> $total_paid,
                'payment_currency' => $currency
            );
       $pfHost=get_session('pfHost');    
       $pfValidServer = self::pfValidServerConfirmation($pfParamString, $pfHost);
       unlink($file_path);
       if($pfValidServer && $payment_status=='COMPLETE')
       {
           return $arr;
       }
       return 0;
    }//callBack function ends here
    
// Server request to confirm details
function pfValidServerConfirmation( $pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null ) {
    // Use cURL (if available)
    if( in_array( 'curl', get_loaded_extensions(), true ) ) {
        $url = 'https://'. $pfHost .'/eng/query/validate';
        // Create default cURL object
        $ch = curl_init();
        // Set cURL options - Use curl_setopt for greater PHP compatibility
        // Base settings
        curl_setopt( $ch, CURLOPT_USERAGENT, NULL );           // Set user agent
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
        curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        
        // Standard settings
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
        if( !empty( $pfProxy ) )
            curl_setopt( $ch, CURLOPT_PROXY, $pfProxy );
    
        // Execute cURL
        $response = curl_exec( $ch );
        //print_r($response);
        curl_close( $ch );
        if ($response === 'VALID') {
            return 1;
        }
    }
    return 0;
    }//valid_server function ends here .. 
}//class ends here 
?>