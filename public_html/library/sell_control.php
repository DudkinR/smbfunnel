<?php
class Sellcontrol
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $ip;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		$this->load=$arr['load'];
		$this->ip=$arr['ip'];
	}
	function createProduct($product,$title,$description,$download_url,$url,$p_image,$price,$currency,$sheeping,$subproducts,$opproducts,$tax,$doupdate=0)
	{
		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}
		//create product 0 for insert 1 for update
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$product=$mysqli->real_escape_string($product);
		$title=$mysqli->real_escape_string($title);
		$description=$mysqli->real_escape_string($description);
		$download_url= $mysqli->real_escape_string($download_url);
		$p_image= $mysqli->real_escape_string($p_image);
		$url=$mysqli->real_escape_string($url);
		$price=$mysqli->real_escape_string($price);
		$currency=$mysqli->real_escape_string($currency);
		$sheeping=$mysqli->real_escape_string($sheeping);
		$subproducts=$mysqli->real_escape_string($subproducts);
		$opproducts=$mysqli->real_escape_string($opproducts);
		$tax=$mysqli->real_escape_string($tax);


		$data_to_provide_inplugins=array(
			'product_id'=>$product,
			'title'=>$title,
			'description'=>$description,
			'download_url'=>$download_url,
			'p_image'=>$p_image,
			'url'=>$url,
			'price'=>$price,
			'currency'=>$currency,
			'shipping_charge'=>$sheeping,
			'sub_products'=>explode('@brk@',trim($subproducts,'@brk@')),
			'optional_products'=>explode('@brk@',trim($opproducts,'@brk@')),
			'tax'=>$tax
		);

		if(strlen($product)<1)
		{
			return "Please Use Unique Product Id to continue.";
		}
		if(!is_numeric($price))
		{
			return "please Enter Product Price";
		}

		if($doupdate==0)
		{
		$chk=$mysqli->query("select `id` from `".$table."` where `productid`='".$product."'");
        if($chk->num_rows>0)
        return "Product Already Exists";
		$in=$mysqli->query("insert into `".$table."` (`productid`,`title`,`url`,`download_url`,`image`,`description`,`price`,`currency`,`shipping`,`subproducts`,`opproducts`,`tax`,`createdon`) values ('".$product."','".$title."','".$url."','".$download_url."','".$p_image."','".$description."','".$price."','".$currency."','".$sheeping."','".$subproducts."','".$opproducts."','".$tax."','".time()."')");

			if($in){
				$data_to_provide_inplugins['id']=$mysqli->insert_id;

				if(get_option("sales_notif_email_products"))
				{
					$sales_notif_email_products=explode(',', get_option( "sales_notif_email_products"));
					if(!array_search($data_to_provide_inplugins['id'], $sales_notif_email_products))
					{
						array_push($sales_notif_email_products, $data_to_provide_inplugins['id']);
					}
					update_option("sales_notif_email_products", implode(',', $sales_notif_email_products));
				}

				$plugin_loader->processProduct($data_to_provide_inplugins,'add');
			}

		}
		elseif(is_numeric($doupdate))
		{
		$chk=$mysqli->query("select `id` from `".$table."` where `productid`='".$product."' and id not in(".$doupdate.")");
        if($chk->num_rows>0)
        return "Can not use same Product Id Multiple Times";

		$u=$mysqli->query("update `".$table."` set `productid`='".$product."',`title`='".$title."',`url`='".$url."',`download_url`='".$download_url."',`image`='".$p_image."',`description`='".$description."',`price`='".$price."',`currency`='".$currency."',`shipping`='".$sheeping."',`subproducts`='".$subproducts."',`opproducts`='".$opproducts."',`tax`='".$tax."' where `id`='".$doupdate."'");

		if($u)
		{
			$data_to_provide_inplugins['id']=$doupdate;
			$plugin_loader->processProduct($data_to_provide_inplugins,'update');
		}

		}
		return 1;
	}
	function deleteProduct($id)
	{
		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}

		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$id=$mysqli->real_escape_string($id);
		$del=$mysqli->query("delete from `".$table."` where `id`='".$id."'");
		if($del)
		{
			$plugin_loader->processProduct($id,'delete');
		}
	}
	function createPaymentMethod($title,$method,$tax,$credentials,$doupdate=0)
	{
		//create product 0 for insert 1 for update
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."payment_methods";
		$title=$mysqli->real_escape_string($title);
		$method=$mysqli->real_escape_string($method);
		$tax=$mysqli->real_escape_string($tax);
		$credentials=$mysqli->real_escape_string($credentials);
		if($doupdate==0)
		{
		$in=$mysqli->query("insert into `".$table."` (`title`,`method`,`tax`,`credentials`,`creadedon`) values ('".$title."','".$method."','".$tax."','".$credentials."','".date('d-M-Y h:ia')."')");
		}
		elseif(is_numeric($doupdate))
		{
		$u=$mysqli->query("update `".$table."` set `title`='".$title."',`method`='".$method."',`tax`='".$tax."',`credentials`='".$credentials."',`creadedon`='".date('d-M-Y h:ia')."'");
		}
		return 1;
	}
	function getProductForView($last=0)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";

		$totalrecord=$mysqli->query("select count(`id`) as countid from `".$table."`");
		$totalrecord=$totalrecord->fetch_object();
		$totalrecord=$totalrecord->countid;

		$condition=1;
		$date_between=dateBetween('createdon');
		if(strlen($date_between[0])>1)
		{
			$condition=$date_between[0];
		}
		$salescountquery="(select count(`id`) from `".$pref."all_sales` where `productid`=`a`.id) as `sales_count`";
		if(isset($_POST['onpage_search']) && strlen($_POST['onpage_search'])>0)
		{
			$search=$mysqli->real_escape_string($_POST['onpage_search']);
			$qry="select `a`.*,".$salescountquery." from `".$table."` as `a`  where `productid` like '%".$search."%' or `title` like '%".$search."%' or `download_url` like '%".$search."%' or `url` like '%".$search."%' or `description` like '%".$search."%' order by `id` desc";
		}
		else
		{
		$order_by="`a`.id desc";
		if(isset($_GET['arrange_records_order']))
		{
			$order_by=base64_decode($_GET['arrange_records_order']);
		}
		if($last==0)
		{
			$qry="select `a`.*,".$salescountquery." from `".$table."` as `a` where ".$condition." order by ".$order_by." limit ".get_option('qfnl_max_records_per_page')."";
		}
		else
		{
			$limitstart=($last*get_option('qfnl_max_records_per_page'))-get_option('qfnl_max_records_per_page');
			$limitend=get_option('qfnl_max_records_per_page');

			$qry="select `a`.*,".$salescountquery." from `".$table."` as `a` where ".$condition." order by ".$order_by." limit ".$limitstart.",".$limitend."";
		}
		}
	//	echo $qry;
		$qry=$mysqli->query($qry);
		if($qry->num_rows>0)
		{
			return array('data'=>$qry,'total'=>$totalrecord);
		}
		else
		{
			return 0;
		}
	}
	function getNubmerOfTimesTheProductUsed($product_id)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."quick_pagefunnel";
		$qry=$mysqli->query("select count(distinct(`id`)) as `countid` from `".$table."` where `product`='".$product_id."'");
		$count=0;
		if($r=$qry->fetch_object())
		{
			$count=$r->countid;
		}
		return $count;
	}
	function pluginGetProducts($str)
	{
		//get all products for the plugin
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";

		$qry_str="select * from `".$table."`".$str;
		$arr=array();
		$qry=$mysqli->query($qry_str);
		while($r=$qry->fetch_assoc())
		{
			$r['sub_products']=explode('@brk@',trim($r['subproducts'],'@brk@'));
			unset($r['subproducts']);

			$r['optional_products']=explode('@brk@',trim($r['opproducts'],'@brk@'));
			unset($r['opproducts']);

			$r['product_id']=$r['productid'];
			unset($r['productid']);

			$r['shipping_charge']=$r['shipping'];
			unset($r['shipping']);

			array_push($arr,$r);
		}
		return $arr;
	}
	function getProduct($id,$idtype=0)
	{
		//for product id $idtype=1
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$id=$mysqli->real_escape_string($id);


		if($idtype==0)
		{
			$search="`id`=".$id;
		}
		else
		{
			$search="`productid`='".$id."'";
		}
		$qry=$mysqli->query("select * from `".$table."` where ".$search."");

		if(isset($qry->num_rows))
		{
		if($qry->num_rows)
		{
			return $qry->fetch_object();
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
    function getProductIdTitle()
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$qry=$mysqli->query("select `id`,`productid`,`title` from `".$table."` order by id desc");
		if($qry->num_rows>0)
		{
			return $qry;
		}
		else
		{
			return 0;
		}
	}
	function countProductSales($id="all")
	{
		//count product sales
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_sales";
		$id=$mysqli->real_escape_string($id);

		if(is_numeric($id))
		{
			$id=" where `productid`='".$id."'";
		}
		else
		{
			$id="";
		}

		$qry=$mysqli->query("select count(`id`) as countid from `".$table."`".$id."");
		if($qry)
		{
			if($r=$qry->fetch_object())
			{
				return $r->countid;
			}
		}
			return 0;
	}
	function getPaymentMethodDetail($id)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."payment_methods";

		if(is_numeric($id))
		{
			$qry=$mysqli->query("select * from `".$table."` where id='".$id."'");
			if($qry->num_rows>0)
			{
				return $qry->fetch_object();
			}
			else
			{
				return 0;
			}
		}
		else if($id==='cod')
		{
			$arr=array(
				'id'=>'cod',
				'title'=>'Cash On Delivery',
				'method'=>'COD',
				'tax'=>0,
				'credentials'=>json_encode(array()),
				'createdon'=>''
			);
			$credentials=(object) $arr;
			return $credentials;
		}
		else if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
			$id= str_replace('_ipn_', '_', $id);
			if(isset($plugin_loader->payment_methods_callbacks[$id]))
			{
				$arr=array(
					'id'=>$id,
					'title'=>$plugin_loader->payment_methods_callbacks[$id]['credentials']['title'],
					'method'=>$plugin_loader->payment_methods_callbacks[$id]['credentials']['method'],
					'tax'=>$plugin_loader->payment_methods_callbacks[$id]['credentials']['tax'],
					'credentials'=>json_encode($plugin_loader->payment_methods_callbacks[$id]['credentials']),
					'createdon'=>''
				);
				$credentials=(object)$arr;
				return $credentials;
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
	function getPaymentMethods()
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."payment_methods";

		$qry=$mysqli->query("select * from `".$table."` order by id desc");
		if($qry->num_rows>0)
		{
			return $qry;
		}
		else
		{
			return 0;
		}
	}
	function createSaleNotification($data, $id)
	{
		$sales_notif_email_check = get_option("sales_notif_email_to_admin_check" );
		if( $sales_notif_email_check==1 )
		{
			$senddata="<p>Hello,</p>";
			$senddata.="<p>I Hope you’re having a great week. <strong>".$data['payer_name']."</strong> has purchased your product <strong>".$data['product_title']."</strong>.</p><p> Sharing all the details with you of <strong>".$data['payer_name']."</strong>.</p>";
			
			$this_sale_link=get_option('install_url');
			$this_sale_link.='/index.php?page=sales&sell_id=';
			$this_sale_link=$this_sale_link.cf_enc($id);

			$senddata.="<p><strong>Payment Id</strong>: ".$data['payment_id']."</p>";
			$senddata.="<p><strong>Payment URL</strong>: <a href='".$this_sale_link."'>".$this_sale_link."</a></p>";
			
			
			foreach ($data['shipping_data'] as $customer_detail_index=> $customer_detail  ) {
				
				if($customer_detail_index=='optional_products'){continue;}
				if(is_array($customer_detail) || is_object($customer_detail))
				{
					$customer_detail=json_encode($customer_detail);
				}
				$senddata.="<div>".ucfirst(trim($customer_detail_index)).": ".$customer_detail."</div>";
				
			}

			$senddata .="<p>Regards</p><p>CloudFunnels</p>";

			$sequence_ob= $this->load->loadSequence();
			$smtpid= get_option("sales_notif_email_smtp" );
			$sales_notif_email_admin= get_option("sales_notif_email_to_admin" );
			$emails = explode(",", $sales_notif_email_admin);


			$products = explode(",", get_option( "sales_notif_email_products"));
			
			if( in_array($data['product_id'], $products) )
			{
				foreach ($emails as $email) {
					if(filter_var($email, FILTER_VALIDATE_EMAIL))
					{
						$sequence_ob->sendMail($smtpid,"",$email,"(CloudFunnels) ".$data['product_title']." Buying Notification",$senddata,"");
					}
				}
			}
		}
	}
	function createCOD($sell_id, $buyer_email)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."qfnl_cod";
		$date=date('Y-m-d H:i:s');
		$ip= getIP();

		$mysqli->query("insert into `".$table."` (`sell_id`, `status`, `buyer_email`, `signed_by`, `last_ip`, `added_on`, `updated_on`) values (".$sell_id.", 0, '".$buyer_email."', '0', '".$ip."', '".$date."', '".$date."')");
	}
	function getCODStat($sell_id)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."qfnl_cod";
		$sell_id=$mysqli->real_escape_string($sell_id);

		$qry=$mysqli->query("select * from `".$table."` where `sell_id`=".$sell_id);
		if($qry->num_rows>0)
		{
			return $qry->fetch_object();
		}
		else
		{
			return false;
		}
	}
	function updateCODStat($sell_id,$shipped=0,$paid=0)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$sales_table=$pref."all_sales";
		$table=$pref."qfnl_cod";
	}
	function storeSells($data)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_sales";

        $shippingdata=array();
		foreach($data as $index=>$val)
		{
			if($index=="shipping_data")
			{
				if(is_array($val))
				{
					foreach($val as $shippingindex=>$shippingdataval)
					{
						if(is_array($shippingdataval))
						{
							foreach($shippingdataval as $shippingdataval_index=>$shippingdataval_val)
							{
								$shippingdataval[$shippingdataval_index]=htmlentities($mysqli->real_escape_string($shippingdataval_val));
							}
							$shippingdata[$shippingindex]=json_encode($shippingdataval);
						}
						else
						{
						$shippingdata[$shippingindex]=htmlentities($mysqli->real_escape_string($shippingdataval));
						}
					}
				}
			}
			elseif(!in_array($index,array("data","membership",'step_sales')))
			{
				$data[$index]=$mysqli->real_escape_string($val);
			}

		}
		if(isset($_GET['qfnl_is_ipn']))
		{
			if(is_array($shippingdata))
			{
				$tempname="";$tempemail="";
				if(isset($shippingdata['name'])){$tempname=$shippingdata['name'];unset($shippingdata['name']);}
				if(isset($shippingdata['email'])){$tempemail=$shippingdata['email'];unset($shippingdata['email']);}
				$shippingdata=array_merge(array('name'=>$tempname,'email'=>$tempemail),$shippingdata);
			}
		}
		$shippingdata=$mysqli->real_escape_string(json_encode($shippingdata));
		$data['data']=$mysqli->real_escape_string($data['data']);

		$membershipdata="";
		if(isset($GLOBALS['sales_membershiparray']))
		{
			$membershipdata=implode(",",$GLOBALS['sales_membershiparray']);
		}
		$mysqli->query("INSERT INTO `".$pref."all_sales` (`productid`, `paymentmethod`, `membership`, `payment_id`, `shippingdetail`,`shipped`, `funnelid`, `pageid`, `paymentdata`, `parent`, `purchase_name`, `purchase_email`,`valid`,`exf`,`total_paid`,`step_payments`,`addedon`) VALUES ('".$data['product_id']."','".$data['payment_method']."','".$membershipdata."','".$data['payment_id']."','".$shippingdata."','0','".$data['funnel_id']."','".$data['page_id']."','".$data['payment_data']."','".$data['parent_id']."','".$data['payer_name']."','".$data['payer_email']."','1','".$data['data']."','".$_SESSION['total_paid'.get_option('site_token')]." (".$_SESSION['payment_currency'.get_option('site_token')].")','".$data['step_sales']."','".time()."')");
		$sell_id=$mysqli->insert_id;
		if($data['payment_method']==='cod')
		{
			self::createCOD($sell_id, $data['payer_email']);
		}
		self::createSaleNotification($data, $sell_id);
	}

	function getSale($id,$through_plugin=false,$modifiers=array())
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_sales";
		$cod_table=$pref.'qfnl_cod';



		$id=$mysqli->real_escape_string($id);

		if($through_plugin ===false)
		{
			$qry=$mysqli->query("select * from `".$table."` where `id`=".$id."");
			if($qry->num_rows)
			{
				$r=$qry->fetch_object();
				$r->cod_data=self::getCODStat($id);
				return $r;
			}
			return 0;
		}
		else
		{
			$arr=array();
			$qry_str="select * from `".$table."`".$through_plugin;
			$qry=$mysqli->query($qry_str);
			while($r=$qry->fetch_assoc())
			{
				//$modifiers
				$temp_sell_id=$r['id'];
				foreach($r as $index=>$val)
				{
					if(isset($modifiers[$index]) && $index !==$modifiers[$index])
					{
						$r[$modifiers[$index]]=$val;
						unset($r[$index]);
					}

					if($index=='shippingdetail')
					{
						$r['shipping_detail']=$r['shippingdetail'];
						unset($r['shippingdetail']);
					}

					if($index=='pageid')
					{
						$r['page_id']=$r['pageid'];
						unset($r['pageid']);
					}

					if($index=='paymentdata')
					{
						$r['payment_data']=$r['paymentdata'];
						unset($r['paymentdata']);
					}

					if($index=='addedon')
					{
						$r['added_on']=$r['addedon'];
						unset($r['addedon']);
					}

					if($index==='membership')
					{
						$r['membership_id']=trim($r['membership_id']);
						$r['membership_id']=(strlen($r['membership_id'])<1)? (array()):(explode(',',trim($r['membership_id'],',')));
					}
				}
				$r['cod_data']=self::getCODStat($temp_sell_id);
				array_push($arr,$r);
			}
			return $arr;
		}
	}

	function stepSalesCreator($credentials,$items)
	{
		//step sales creator
		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}

		$credentials=json_decode($credentials);
		$itemarr=array();

		$sheepingcharge=0;
		$tax=0;
		$totalprice=0;
		$productid=0;
		$currency="USD";
		$producttitle="";

		$allproductdetail="";
		for($i=0;$i<count($items);$i++)
		{
		if($i==0)
		{
		$currency=$items[$i]['currency'];
		$producttitle=$items[$i]['title'];
		$productid=$items[$i]['productid'];
		}
		$allproductdetail .=$items[$i]['title']." (Price: ".number_format($items[$i]['price'],2)." ".$currency.")<br>";
		$sheepingcharge= (float)$sheepingcharge+ (float)$items[$i]['shipping'];
		$tax= (float)$tax+ (float)$items[$i]['tax'];
	
		$totalprice= (float)$totalprice+ (float)$items[$i]['price'];
		}
		
		if(isset($credentials->tax))
		{
			$credentials->tax= (float) $credentials->tax;
			$tax=$tax+($totalprice *($credentials->tax/100));
		}

		$allproductdetail .="<hr/>Total Price: ".number_format($totalprice,2)." ".$currency."<br>";

		$allproductdetail .="Tax: ".number_format($tax,2)." ".$currency."<br>";

		$allproductdetail .="Sheeping Charge: ".number_format($sheepingcharge,2)." ".$currency;

		return $allproductdetail;
	}

	function doPayment($funnelid,$folder,$pageid,$abtype,$productid,$paymentmethod_id,$membership,$lists,$optionalproducts=array(),$confirm_url='',$cancel_url='',$userdata='')
	{
		//echo "test";

		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}

		$productsarr=array();
		$product=self::getProduct($productid);
		if(!is_object($product)){return "No Product Selected";}
		array_push($productsarr,(array)$product);
		$extraproducts=explode("@brk@",$product->subproducts);

		if(!is_array($optionalproducts))
		{
		$optionalproducts=array($optionalproducts);
		}
		for($i=0;$i<count($optionalproducts);$i++)
		{
			$temp_optionalproductsob=self::getProduct($optionalproducts[$i],1);
			if(is_object($temp_optionalproductsob))
			{
				$optionalproducts[$i]=$temp_optionalproductsob->id;
			}
			else
			{
				unset($optionalproducts[$i]);
			}
		}

		$products=array_unique(array_merge($extraproducts,$optionalproducts));

		foreach($products as $productt)
		{
			if($productt==$productid || (!is_numeric($productt))){continue;}
			$productdata=self::getProduct($productt);
			if(!is_object($productdata)){continue;}
			array_push($productsarr,(array)$productdata);
		}

		$payment_data=self::getPaymentMethodDetail($paymentmethod_id);
		if(!is_object($payment_data)){return "Invalid Payment Method";}
		$method=$payment_data->method;

		$membershiparr=array();
		$tempmembershiparr=explode(',',$membership);
		$membershiptext="";

		for($i=0;$i<count($tempmembershiparr);$i++)
		{
			if(is_numeric($tempmembershiparr[$i]))
			{
			array_push($membershiparr,$tempmembershiparr[$i]);
			}
		}
		if(count($membershiparr)>0)
		{
			$membershiptext=implode("@",$membershiparr);
			$membershiptext="@".$membershiptext."@";
		}

		if($method=='COD')
		{
			//COD
			if ($product->price < 1) {
				$cod_otp = substr(str_shuffle('1234567890XCVBNMASDFGHJQWERTYUIOP@#$%^&*()+xcvbnm,ertyuiwertyui'), 0, 8);
				$_POST['verify_otp'] = 1;
				$_POST['codCustOTP'] = $cod_otp;
				$cod_order_data_arr = $_SESSION['order_form_data' . get_option('site_token')];
				$_GET['execute'] = 1;
				$_SESSION['total_paid'.get_option('site_token')] = 0;
				$email = $cod_order_data_arr['data']['email'];

				$name = "";
				if (isset($cod_order_data_arr['data']['name'])) {
					$name = $cod_order_data_arr['data']['name'];
				} else if (isset($cod_order_data_arr['data']['firstname'])) {
					$name = $cod_order_data_arr['data']['firstname'];
					if (isset($cod_order_data_arr['data']['lastname'])) {
						$name .= " " . $cod_order_data_arr['data']['lastname'];
					}
				}

				$all_price_detail = self::getProductsPriceDependingOnMethod($payment_data->credentials, $productsarr);
				if (is_array($all_price_detail)) {
					foreach ($all_price_detail as $all_price_detail_index => $all_price_detail_val) {
						${$all_price_detail_index} = $all_price_detail_val;
					}
				}

				$arr = array(
					'payer_name' => $name,
					'payer_email' => $email,
					'payment_id' =>  'cf_cod_' . $cod_otp . '_' . time(),
					'total_paid' => $total,
					'payment_currency' => $currency,
				);
				$done = json_encode($arr);
			} else {
				require_once(__DIR__ . "/payment_apis/cod/function.php");
				$done = addToCOD($this, $payment_data->credentials, $productsarr, $product->description);
			}
			if($done !==0 && (isset($_GET['execute'])))
			{
				$paymentdata=json_decode($done);

				$payer_name=$paymentdata->payer_name;
				$payer_email=$paymentdata->payer_email;
				$payment_id=$paymentdata->payment_id;
			}
		}
		elseif($plugin_loader)
		{
			//process through plugin
			$product_detail_for_plugin=self::getProductsPriceDependingOnMethod(json_decode($payment_data->credentials),$productsarr);
			$done=$plugin_loader->processPaymentMethod($paymentmethod_id,$product_detail_for_plugin,$product->description);

			if(!$done){$done=0;}

			if($done !==0 && (isset($_GET['execute'])))
			{
				$paymentdata=(object)$done;
				$done=json_encode($done);

				if(!isset($paymentdata->payer_name))
				{
					throw new Exception("Missing index `payer_name` in payment confirmation data");
				}
				else if(!isset($paymentdata->payer_email))
				{
					throw new Exception("Missing index `payer_email` in payment confirmation data");
				}
				else if(!isset($paymentdata->payment_id))
				{
					throw new Exception("Missing index `payment_id` in payment confirmation data");
				}
				else if(!isset($paymentdata->total_paid))
				{
					throw new Exception("Missing index `total_paid` in payment confirmation data");
				}
				else if(!isset($paymentdata->payment_currency))
				{
					throw new Exception("Missing index `payment_currency` in payment confirmation data");
				}
				else if(!isset($paymentdata->ipn_tax))
				{
					$_SESSION['ipn_tax'.get_option('site_token')]=0;
				}

				$payer_name=$paymentdata->payer_name;
				$payer_email=$paymentdata->payer_email;
				$payment_id=$paymentdata->payment_id;


				$_SESSION['total_paid'.get_option('site_token')]=$paymentdata->total_paid;
				$_SESSION['payment_currency'.get_option('site_token')]=$paymentdata->payment_currency;
			}
		}

		if($done !==0 && (isset($_GET['execute'])))
		{
			if(strpos($method,"_ipn")<1)
			{
				$step_paymentdata=self::stepSalesCreator($payment_data->credentials,$productsarr);
			}
			else
			{
				$step_paymentdata="";
			}

			$arr['payer_name']=$payer_name;
		    $arr['payer_email']=$payer_email;
		    $arr['payment_id']=$payment_id;
			$arr['total_paid']=$_SESSION['total_paid'.get_option('site_token')];
			

			if(!isset($userdata['name']))
			{
				$userdata['name']=$arr['payer_name'];
			}
			elseif(strlen($userdata['name'])<1)
			{
				$userdata['name']=$arr['payer_name'];
			}

			if(!isset($userdata['email']))
			{
				$userdata['email']=$arr['payer_email'];
			}
			elseif(strlen($userdata['email'])<1)
			{
				$userdata['email']=$arr['payer_email'];
			}

			//add to funnels optin storing function
			$productlistarr=array();
			for($i=0;$i<count($productsarr);$i++)
			{
				if(!is_numeric($productsarr[$i]['id'])){continue;}
				array_push($productlistarr,$productsarr[$i]['id']);
			}

			$leadsdataarr=array_merge($userdata,$arr);
			$leadsdataarr['product_ids']=$productlistarr;

			$funnel_ob=$this->load->loadFunnel();
			$funnel_ob->leadsStoreFromSavedFunnels($funnelid,$folder,$abtype,$leadsdataarr,0);
			$_SESSION['current_payment_cofirmation'.get_option('site_token')]=$leadsdataarr;
			//---------------------------------

			$data=array();
			$data['payment_method']=$paymentmethod_id;$data['payment_id']=$arr['payment_id'];$data['funnel_id']=$funnelid;$data['page_id']=$pageid;$data['payment_data']=$done;$data['payer_name']=$arr['payer_name'];$data['payer_email']=$payer_email;$data['data']=$done;
			$data['shipping_data']=$userdata;
			$data['membership']=$membershiptext;
			
			$all_data_to_store=array();
			for($i=0;$i<count($productsarr);$i++)
			{
			$data['product_id']=$productsarr[$i]['id'];
			$data['product_title']=$productsarr[$i]['title'];
			$data['product_download_url']=$productsarr[$i]['download_url'];
			$data['product_image']=$productsarr[$i]['image'];
			$data['product_url']=$productsarr[$i]['url'];

			$productstempidarrsingle=$productsarr[$i];
			if($productid !=$productstempidarrsingle['id'])
			{
				$data['parent_id']=$productid;
			}
            else
			{$data['parent_id']=0;}
			$data['step_sales']=$step_paymentdata;

			$forplugin_data=$data;
			$forplugin_data['membership']=array();
			if(isset($GLOBALS['sales_membershiparray']))
			{
				$forplugin_data['membership']=$GLOBALS['sales_membershiparray'];
			}
			
			array_push($all_data_to_store, $forplugin_data);
			self::storeSells($data);
			}
			$plugin_loader->triggerSales($all_data_to_store,true);
		    echo "<script>window.location='".$confirm_url."';</script>";
		}
		elseif(isset($_GET['execute']))
		{
			if(isset($_SESSION['current_payment_cofirmation'.get_option('site_token')]))
			{
				unset($_SESSION['current_payment_cofirmation'.get_option('site_token')]);
			}
			$plugin_loader->triggerSales(array(),false);
		    echo "<script>window.location='".$cancel_url."';</script>";
		}
	}
	function checkOutDetailcreate($paymentmethod_id,$productid,$optionalproducts)
	{
		
		//counting products
		$main_products=array();
		$all_products=array($productid);
		$productsarr=array();
		$product=self::getProduct($productid);

		if(!is_object($product)){return die("No Product Selected");}
		array_push($productsarr,(array)$product);

		array_push($main_products,$product->productid);

		$extraproducts=explode("@brk@",$product->subproducts);

		if(!is_array($optionalproducts))
		{
			$optionalproducts=array($optionalproducts);
		}

		for($i=0;$i<count($optionalproducts);$i++)
		{
			$temp_optionalproductsob=self::getProduct($optionalproducts[$i],1);
			if(is_object($temp_optionalproductsob))
			{
				if(array_search($temp_optionalproductsob->id,$extraproducts)===false)
				{
					$optionalproducts[$i]=$temp_optionalproductsob->id;
				}
				else
				{
					unset($optionalproducts[$i]);
				}
			}
			else
			{
				unset($optionalproducts[$i]);
			}
		}
		$products=$extraproducts;
		//extra products
		foreach($products as $productt)
		{
			if($productt==$productid || (!is_numeric($productt))){continue;}
			$productdata=self::getProduct($productt);
			if(!is_object($productdata)){continue;}
			array_push($productsarr,(array)$productdata);
			array_push($main_products,$productdata->productid);
			array_push($all_products,$productt);
		}

		$products=$optionalproducts;
		//additional
		foreach($products as $productt)
		{
			if($productt==$productid || (!is_numeric($productt))){continue;}
			$productdata=self::getProduct($productt);
			if(!is_object($productdata)){continue;}
			array_push($productsarr,(array)$productdata);
			array_push($all_products,$productt);
		}
		$items=$productsarr;
		$all_products=array_unique($all_products);

		$payment_data=self::getPaymentMethodDetail($paymentmethod_id);
		if(!is_object($payment_data)){die("Invalid Payment Method");}
		//$method=$payment_data->method;
		//$credentials=json_decode($method);
		$data=self::getProductsPriceDependingOnMethod($payment_data,$items,1);
		if(is_array($data))
		{
			$data['all_products']=$all_products;
			$data['main_products']=$main_products;
		}
		return $data;
	}
	function getProductsPriceDependingOnMethod($credentials,$items,$checkout_page=0)
	{
		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}

		$itemarr=array();
		$sheepingcharge=0;
		$tax=0;
		$totalprice=0;

		$currency="USD";

		$allproductdetail="";
		for($i=0;$i<count($items);$i++)
		{
			if($i==0)
			{
				$currency=$items[$i]['currency'];
			}
			$allproductdetail .=$items[$i]['title']." (Price: ".number_format($items[$i]['price'],2)." ".$currency.")<br>";
			$sheepingcharge= (float)$sheepingcharge+ (float)$items[$i]['shipping'];
			$tax= (float)$tax+ (float)$items[$i]['tax'];
	
			$totalprice= (float)$totalprice+ (float)$items[$i]['price'];
		}
		
		if(isset($credentials->tax) && is_numeric($credentials->tax))
		{
			$credentials->tax= (float)$credentials->tax;
			$tax=$tax+($totalprice *($credentials->tax/100));
		}

		$total= $totalprice+$tax+$sheepingcharge;

		$data= array();
		if($checkout_page)
		{
			$data= array(
				'shipping_charge'=>$sheepingcharge,
				'tax_amount'=>$tax,
				'subtotal_price'=> $totalprice,
				'total_price'=>$total,
				'payment_currency'=>$currency,
			);
		}
		else
		{
			$data= compact('itemarr','sheepingcharge','tax','totalprice','currency','total','allproductdetail','items');
		}

		$order_session= get_requested_order();
		$data= $plugin_loader->processFilter('the_checkout_data', $data, array('payment_method'=> $credentials, 'checkout_page'=>$checkout_page, 'order_session'=>$order_session));
		return $data;
	}
	function productTemplatecreator($html, $productids = array())
	{
		//$html= $this->allProductsTeamlateCreate($html, $productids);


		$pregtestfordom = "/(cf-loop(?=((=['\"](products|courses))+)))+/";
		if (preg_match($pregtestfordom, $html, $arr)) {
			$html = @cfLoopCreator('products', $html);
		}
		$data = array();
		if (is_array($productids)) {
			for ($i = 0; $i < count($productids); $i++) {
				$product = self::getProduct($productids[$i]);
				if (is_object($product)) {
					array_push($data, (array)$product);
				}
			}
		}
		preg_match('/({(products|courses)})+/', $html, $arr);

		if (is_array($arr)) {
			$tempRplc = function () use (&$start, &$end, &$html, &$data) {
				if (!$start || !$end) {
					return;
				}
				$end = $end + 10;
				$find = substr($html, $start, ($end - $start + 1));

				$str = "";

				for ($j = 0; $j < count($data); $j++) {
					$str .= self::tempProducthipReplacerCb($find, $data[$j]);
				}
				$html = str_replace($find, $str, $html);
			};
			for ($i = 0; $i < count($arr); $i++) {
				$start = strpos($html, "{products}");
				$end = strpos($html, "{/products}");
				$tempRplc();
				$start = strpos($html, "{courses}");
				$end = strpos($html, "{/courses}");
				$tempRplc();
			}
		}

		if (isset($data[0])) {
			$html = self::tempProducthipReplacerCb($html, $data[0]);
		}

		return $html;
	}
	function tempProducthipReplacerCb($str, $datas)
	{
		// print_r($str );
		foreach ($datas as $index => $data) {
			if ($index == "id") {
				continue;
			}
			if ($index == "productid") {
				$index = "id";
			}
			if (!is_array($data)) {
				$data = $data!=''?$data:'';
				$str = str_replace("{product." . $index . "}", $data, $str);
				$str = str_replace("%7Bproduct." . $index . "%7D", $data, $str);
				$str = str_replace("{course." . $index . "}", $data, $str);
				$str = str_replace("%7Bcourse." . $index . "%7D", $data, $str);
				$str = str_replace("{course.date}", date("d-M-Y", time()), $str);
			}
		}
		$str = str_replace("{products}", "", $str);
		$str = str_replace("{/products}", "", $str);

		$str = str_replace("{courses}", "", $str);
		$str = str_replace("{/courses}", "", $str);

		return $str;
	}

	function visualOptisForSales($funnel_id='all',$pagecount=0,$search="",$limit=10, $type="all")
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_sales";
		$cod_table=$pref.'qfnl_cod';

		$cod_type="";
		$cod_type_where="";

		if($type=='cod')
		{
			$cod_type=" `id` in (select `sell_id` from `".$cod_table."`)";
		}
		else if($type=='cod_shipped')
		{
			$cod_type=" `id` in (select `sell_id` from `".$cod_table."`) and `shipped`='1'";
		}
		else if($type=='cod_pending')
		{
			$cod_type=" `id` in (select `sell_id` from `".$cod_table."`) and `shipped`='0'";
		}
		else if($type=='non_cod')
		{
			$cod_type=" `id` not in (select `sell_id` from `".$cod_table."`)";
		}
		else if($type=='non_cod_shipped')
		{
			$cod_type=" `id` not in (select `sell_id` from `".$cod_table."`) and `shipped`='1'";
		}
		else if($type=='non_cod_pending')
		{
			$cod_type=" `id` not in (select `sell_id` from `".$cod_table."`) and `shipped`='0'";
		}
		else if($type=='all_shipped')
		{
			$cod_type=" `shipped`='1'";
		}
		else if($type=='all_pending')
		{
			$cod_type=" `shipped`='0'";
		}
		if(strlen($cod_type)>1)
		{
			$cod_type_where= " where".$cod_type;
			$cod_type= " and".$cod_type;
		}

		$tempdatebetween_arr=dateBetween('addedon');
		$datebetween=$tempdatebetween_arr[1];
		$datebetween_all=$tempdatebetween_arr[0];
		if(strlen($datebetween_all)>1)
		{
			$datebetween_all=" where".$datebetween_all;
		}
		$datebetween .=$cod_type;
		if(isset($_GET['sell_id']))
		{
			$provided_sell_id= $mysqli->real_escape_string(cf_enc($_GET['sell_id'],'decrypt'));
			$datebetween .=" and `id`=".$provided_sell_id;
		} 

		$countsql="select count(`id`) as totaloptins from `".$table."` where 1".$datebetween;
		$baseurl="";
		$extrafields=0;
		$total=0;

		$total_qry=$mysqli->query($countsql);
		if($total_qry)
		{
			if($res=$total_qry->fetch_object())
			{
				$total=$res->totaloptins;
			}
		}

		if($pagecount==0)
		{
		$sql="select * from `".$table."` where `parent`='0'".$datebetween." order by id desc limit ".$limit."";
		}
		else
		{
		$pagecount=($pagecount*10)-10;

		$sql="select * from `".$table."` where `parent`='0'".$datebetween." order by `id` desc limit ".$pagecount.", ".$limit."";
		}

	//	echo $sql;

		if(is_numeric($funnel_id))
		{
		$countsql="select count(`id`) as totaloptins from `".$table."` where productid='".$funnel_id."'".$datebetween;
		 $total_qry=$mysqli->query($countsql);
		if($total_qry)
		{
			if($res=$total_qry->fetch_object())
			{
				$total=$res->totaloptins;
			}
		}
		if($pagecount==0)
		{
		$sql="select * from `".$table."` where productid='".$funnel_id."'".$datebetween." order by `id` desc limit ".$limit."";
		}
		else
		{
		$pagecount=($pagecount*10)-10;
		$sql="select * from `".$table."` where productid='".$funnel_id."'".$datebetween." order by `id` desc limit ".$pagecount.", ".$limit."";
		}
        }

		if(strlen($search)>0)
		{
		$search=$mysqli->real_escape_string($search);
		if(is_numeric($funnel_id))
		{
			$sql="select * from `".$table."` where productid='".$funnel_id."' and (paymentmethod like '%".$search."%' or payment_id like '%".$search."%' or shippingdetail like '%".$search."%' or purchase_name like '%".$search."%' or purchase_email like '%".$search."%')".$cod_type." order by id desc";
		}
		else
		{
			$sql="select * from `".$table."` where (paymentmethod like '%".$search."%' or payment_id like '%".$search."%' or shippingdetail like '%".$search."%' or purchase_name like '%".$search."%' or purchase_email like '%".$search."%')".$cod_type." order by id desc";
		}
		}
		
		$qry=$mysqli->query($sql);

		return array('sales'=>$qry,'total'=>$total,'extracols'=>$extrafields);
	}

	function deleteSale($id,$by='id')
	{
		//delete optin
		//$by may be id or funnelid or pageid
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$id=$mysqli->real_escape_string($id);
		if(is_numeric($id))
		{
			if($by !='id')
			{
				$by="'".$id."'";
			}
		}
        $table=$pref."all_sales";
		$mysqli->query("delete from `".$table."` where `".$by."`=".$id."");
	}
	function shippedOrNot($id)
	{
		//delete optin
		//$by may be id or funnelid or pageid
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_sales";
		$cod_table=$pref.'qfnl_cod';

		$id=$mysqli->real_escape_string($id);
		$qry=$mysqli->query("select `shipped` from `".$table."` where `id`=".$id."");
		$done=0;
		if($qry->num_rows)
		{
			$r=$qry->fetch_object();
			$shipped=1;
			if($r->shipped=='1')
			{$shipped=0;}

			if($mysqli->query("update `".$table."` set `shipped`='".$shipped."' where id='".$id."'"))
			{
				++$done;
				$date=date('Y-m-d H:i:s');
				$signed_by=$_SESSION['user'.get_option('site_token')];
				$ip=getIP();

				$mysqli->query("update ".$cod_table." set `status`=".$shipped.",`signed_by`=".$signed_by.", `last_ip`='".$ip."' where `sell_id`=".$id."");
			}
		}
		return $done;
	}

function exportToCSV($productid=0)
{
	$mysqli=$this->mysqli;
	$pref=$this->dbpref;
	$table=$pref."all_sales";

  if($productid !==0)
	{
	$productid=$mysqli->real_escape_string($productid);
	}

	if($productid>0)
	{
		$sql="select * from `".$table."` where `productid`='".$productid."' order by `id` desc";
	}
	else
	{
		$sql="select * from `".$table."` order by `id` desc";
	}

	$qry=$mysqli->query($sql);

	$csv_fields=array();
	$csv_fields[] = '#';
	$csv_fields[] = 'Product';
	$csv_fields[] = 'Purchase Id';
	$csv_fields[] = 'Payer Name';
	$csv_fields[] = 'Payer Email';
	$csv_fields[] = 'Payment Method';
	$csv_fields[] = 'Shipping Detail';
	$csv_fields[] = 'Shipped';
	$csv_fields[] = 'Parent Product';
	$csv_fields[] = 'Date';

	$output_filename = 'sales.csv';
$output_handle = @fopen( 'php://output', 'w' );

header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
header( 'Content-Description: File Transfer' );
header( 'Content-type: text/csv' );
header( 'Content-Disposition: attachment; filename=' . $output_filename );
header( 'Expires: 0' );
header( 'Pragma: public' );

fputcsv( $output_handle, $csv_fields );

  if($qry->num_rows>0)
	{
		$count=0;
		while($r=$qry->fetch_assoc())
		{
			++$count;
			$outputrray=array($count);
			$product=self::getProduct($r['productid']);
			array_push($outputrray,"(#".$product->productid.")".$product->title);
			array_push($outputrray,$r['payment_id']);
			array_push($outputrray,$r['purchase_name']);
			array_push($outputrray,$r['purchase_email']);
			$paymentmethod_link=get_option('install_url');
			$paymentmethod_link .="/index.php?page=payment_methods&payid=".$r['payment_id'];
			array_push($outputrray,$paymentmethod_link);

			$tempshippingdetail=(array)json_decode($r['shippingdetail']);
			$shippingdetail="";
			foreach($tempshippingdetail as $index=>$data)
			{
				$shippingdetail=$index.": ".$data."\n";
			}
			array_push($outputrray,$shippingdetail);
			$r['shipped']=($r['shipped']=='1')? 'Yes':'No';
			array_push($outputrray,$r['shipped']);
			$parent="N/A";
			if($r['parent'] !='0')
			{
				$parentproduct_ob=self::getSale($r['parent']);
				if($parentproduct_ob)
				{
				$parentproduct_ob=self::getProduct($parentproduct_ob->productid);
				if($parentproduct_ob)
				{
				$parent="(#".$parentproduct_ob->productid.") ".$parentproduct_ob->title;
				}
				}
			}
			array_push($outputrray,$parent);
			array_push($outputrray,date('d-M-Y h:ia',$r['addedon']));
			fputcsv( $output_handle,$outputrray);
		}
	}
fclose( $output_handle );
die();

}
function cancelorConfirmSalesAndMembership($id)
{
	$mysqli=$this->mysqli;
	$pref=$this->dbpref;
	$id=$mysqli->real_escape_string($id);
	$sales_table=$pref."all_sales";
	$members_table=$pref."quick_member";
	$domembership_cancel=get_option('qfnl_cancel_membership_withsales');
	$table=$pref."all_sales";

	$sales=$mysqli->query("select `membership`,`valid` from `".$sales_table."` where `id`=".$id."");

	if($r=$sales->fetch_object())
	{
	$valid=($r->valid=='1')? '0':'1';

	$mysqli->query("update `".$sales_table."` set valid='".$valid."' where `id`=".$id."");
	if($domembership_cancel=='1')
	{
			$verifycode=time();
			$verifycode .=substr(str_shuffle('asdfghjklqwertyuiopzxcvbnm1234567890'),0,5);
		
			$mysqli->query("update `".$members_table."` set valid='".$valid."',`verifycode`='".$verifycode."' where `id` in(".$r->membership.")");
	}
	}

	return 1;
}
}
?>
