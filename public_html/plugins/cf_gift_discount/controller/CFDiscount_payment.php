<?php
class CFDiscount_payment
{

	function __construct($arr)
	{
		if(isset($arr['load']))
		{
			$this->load=$arr['loader'];
		}
	}

	function registerPaymentMethod(){
		register_payment_method('temp',
		array(
			'title'=>'temp',
			'method'=>'temp',
			'tax'=>"0",
			'credentials'=>''
		),
		function($data, $product, $call_back_url){

			//here add your payment methods to process
			if($data['method']=='temp')
			{
				$order_data_array=$_SESSION['order_form_data'.get_option('site_token')];
				$name="Name is not available";
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
				else if(isset($order_data_array['data']['lastname']))
				{

						$name =$order_data_array['data']['lastname'];
				}else{
					$name="Name is not available";
				}
				
				$email = (isset($order_data_array['data']['email']))? $order_data_array['data']['email']:'';
				$all_price_detail=$product;
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
				
				
				$allproductdetail .="Total Price: ".number_format($totalprice,2)." ".$currency."\n";
				$allproductdetail .="Tax: ".number_format($tax,2)." ".$currency."\n";
				$allproductdetail .="Shipping Charge: ".number_format($sheepingcharge,2)." ".$currency;

				if( has_session('cfredeem_giftcard_successfully') )
				{
					$giftcard=get_session('cfredeem_giftcard_successfully');
					$giftcard_balance = $giftcard['data']['for_restore'];
					$giftcard_code = $giftcard['data']['gift_code'];
					$allproductdetail .="Gift Card ($giftcard_code): ".number_format($giftcard_balance,2)." ".$currency;
				}elseif( has_session('cfredeem_discount_successfully') )
				{
					$giftcard=get_session('cfredeem_discount_successfully');
					$giftcard_balance = $giftcard['data']['for_restore'];
					$giftcard_code = $giftcard['data']['gift_code'];
					$allproductdetail .="Discount (".$giftcard['data']['gift_code'].") (".$giftcard['data']['percentage']."% worth of ".$giftcard['data']['for_restore']." ".$giftcard['data']['currency'].") ";
				}

				
				$allproductdetail=str_replace("<br>","\n",$allproductdetail);
				
				$_SESSION['total_paid'.get_option('site_token')]=$total;
				$_SESSION['payment_currency'.get_option('site_token')]=$currency;

				$payment_id="custom_".bin2hex(random_bytes(10));
				unset($_SESSION['custom_payment_method'.get_option('site_token')]);
				if(isset($_GET['execute'] ) && $_GET['execute']==1 )
				{
					$ret = array(
						'payer_name'=> $name,
						'payer_email'=> $email,
						'payment_id'=> $payment_id,
						'total_paid'=> $_SESSION['total_paid'.get_option('site_token')],
						'payment_currency'=>$_SESSION['payment_currency'.get_option('site_token')] 
					);
					return $ret;
				}else{
					$order_data_array=$_SESSION['order_form_data'.get_option('site_token')];
					$redirect_url=get_option('install_url')."/index.php?page=do_payment&execute=1";
					echo "<script>window.location=`".$redirect_url."`</script>";
					exit();	
				}
			}
			else
			{
			 return false;
			}
		}
		);
	}
	
}
?>
