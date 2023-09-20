<?php
function addToCcavenue($sell_ob,$credentials,$items,$description="")
{
include('Crypto.php');
$credentials=json_decode($credentials);
$itemarr=array();
$sheepingcharge=0;
$tax=0;
$totalprice=0;
$productid=0;
$currency="INR";
$producttitle="";

$allproductdetail="";

$order_data_array=$_SESSION['order_form_data'.get_option('site_token')];
/*
for($i=0;$i<count($items);$i++)
{
	if($i==0)
	{
	$currency=$items[$i]['currency'];
	$producttitle=$items[$i]['title']." .";
	$productid=$items[$i]['productid'];
	}
	$allproductdetail .=$items[$i]['title'];
	$sheepingcharge=$sheepingcharge+$items[$i]['shipping'];
	$tax=$tax+$items[$i]['tax'];
	
	$totalprice=$totalprice+$items[$i]['price'];
}
*/
$all_price_detail=$sell_ob->getProductsPriceDependingOnMethod($credentials,$items);
//'itemarr','sheepingcharge','tax','totalprice','currency','total', 'allproductdetail'
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

$ccav_path_req=($credentials->pay_type=='1')?"https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction":"https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
//echo "ccavenue required path".$ccav_path_req;

if(isset($_GET['execute']))
{
$encResponse=$_POST["encResp"];//This is the response by the CCAvenue Server
	$rcvdString=decrypt($encResponse,$workingkey);//Crypto Decryption used as per the specified working key.
	$order_status="";
	$decryptValues=explode('&', $rcvdString);
	$dataSize=sizeof($decryptValues);
	//echo "<center>";

	for($i = 0; $i < $dataSize; $i++) 
	{
		$information=explode('=',$decryptValues[$i]);
		
		if($i==3)	$order_status=$information[1];
		
		if($i==1)	$tracking_id=$information[1];
		
	}

	if($order_status==="Success")
	{
	    
		//echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
		
        $paymenyResponse['id']=$tracking_id;
        $paymenyResponse['payer_name']=$name;
        $paymenyResponse['payer_email']=$email;
        $paymentMessage = json_encode($paymenyResponse);
        return $paymentMessage;
		
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
?>