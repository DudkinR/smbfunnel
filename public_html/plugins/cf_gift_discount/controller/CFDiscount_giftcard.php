<?php
class CFDiscount_giftcard
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $students;
	var $ip;
	function __construct($arr)
	{
		global $mysqli;
		global $dbpref;
		
		$this->mysqli=$mysqli;
		$this->dbpref=$dbpref;
		if(isset($arr['loader']))
		{
		$this->load=$arr['loader'];
		}
		$this->students=$arr['students'];
		
		$this->ip=getIP();
	}

	// get radom string
	function random_strings($length_of_string=5)
	{
	
		// String of all alphanumeric character
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		// Shuffle the $str_result and returns substring
		// of specified length
		return substr( str_shuffle($str_result), 0, $length_of_string);
	}

	/*
	Add a new memeber through gift-cards
	*/ 
	function addMemberThroughGiftCard( $data )
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."quick_member";
		$table1 = $pref."issue_gift";

		$code = $mysqli->real_escape_string($data['country_code']);
		$name = $mysqli->real_escape_string($data['customer_name']);
		$email = $mysqli->real_escape_string($data['customer_email']);
		// $phone = $mysqli->real_escape_string($data['customer_phoneno']);
		$password=password_hash(ucfirst($name)."@1234",PASSWORD_DEFAULT);
		// $phoneno=$code.$phone;
		$date_created = date("Y-m-d H:i:s",time());
		if( $data['savecustomer'] == "save" )
		{
			$check_email = "SELECT `id` FROM `".$table."` WHERE `email`='".$email."'";
			$check = $mysqli->query( $check_email );
			if( $mysqli->affected_rows <= 0 )
			{
				$sql="INSERT INTO `".$table."` (`funnelid`, `pageid`, `name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_created`, `ip_lastsignin`, `date_created`, `date_lastsignin`, `valid`, `exf`) 
				VALUES (1,1,'".$name."','".$email."','".$password."','custom','custom','custom','".$this->ip."','".$this->ip."','".$date_created."','".$date_created."','1','custom')";
				if( $mysqli->query($sql) )
				{
					return json_encode(array('status'=>1,'message'=>t('${1} added successfully',[ucfirst(t($this->students))]),'last_id'=>$mysqli->insert_id,'name'=>$name,'email'=>$email));
				}else{
					return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
				}

			}else{
				return json_encode(array('status'=>0,'message'=>t('${1} already available. Please use different unique email.',[ucfirst(t($this->students))])));
			}

		}else if( $data['savecustomer'] == "update" )
		{
			$id=$mysqli->real_escape_string( $data['member_id'] );
			$giftcard_id=$mysqli->real_escape_string( $data['giftcard_id'] );
			$sql1 = "SELECT `email` FROM `".$table."` WHERE `id`=$id";

			$check = $mysqli->query( $sql1 );
			if( $check->num_rows > 0 )
			{
				$result = $check->fetch_assoc();
				$gemail = $result['email'];
				if( $gemail == $email )
				{
					$sql="UPDATE `".$table."` SET `name`='".$name."',`email`='".$email."' WHERE `id`=$id";
					if( $mysqli->query($sql) )
					{
						return json_encode(array('status'=>1,'message'=>t('${1} update successfully',[ucfirst(t($this->students))]),'last_id'=>$id,'name'=>$name,'email'=>$email));
					}else{
						return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
					}
				}else{
					$check_qry = "SELECT `id` FROM `".$table."` WHERE `email`='".$email."'";
					$check_email=$mysqli->query($check_qry);
					if( $check_email->num_rows <= 0 )
					{
						$sql="UPDATE `".$table."` SET `name`='".$name."',`email`='".$email."' WHERE `id`=$id";
						if( $mysqli->query($sql) )
						{
							return json_encode(array('status'=>1,'message'=>t('${1} updated successfully',[ucfirst(t($this->students))]),'last_id'=>$id,'name'=>$name,'email'=>$email));
						}else{
							return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
						}
		
					}else{
						return json_encode(array('status'=>0,'message'=>t('${1} already available. Please use different unique email.',[ucfirst(t($this->students))])));
					}
				}
			}else{
				return json_encode(array('status'=>0,'message'=>t('Invalid members.Please refresh the page.')));
			}
			return json_encode(array('status'=>2,'message'=>t('Invalid members.Please refresh the page.')));

		}

	}

	/*
	Add a gift-cards
	*/ 
	function createGiftCard( $data )
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table = $pref."gift_cards";
		$table1 = $pref."issue_gift";
		$input=[];
		$filter_data=['giftcard_id','savegiftcards','action'];

		foreach( $data as $index => $dat  )
		{
			if( !in_array( $index, $filter_data ) )
			{
				if( $index == 'products' ){continue;}
				else{
					$input[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
				}
			}
		}
		$productss=[];
		if( isset( $data['products'] ) &&  count($data['products']) > 0  )
		{
			if( count( $data['products'] ) > 0  )
			{
				foreach( $data['products'] as $product )
				{
					if( !empty( $product ) )
					{
						$pro = $mysqli->real_escape_string( $product );
						$productss[]=$pro;
					}
				}
			}else{
				return json_encode(array('status'=>0,'message'=>t('Please select products')));
			}
		}
		
		$input['products'] = $mysqli->real_escape_string( json_encode( $productss ) );
		if( $input['expiration_type']=="no_expiration" )
		{
			$input['expiration_date'] = date("2030-01-01");
		}else{
			$input['expiration_date'] = date("Y-m-d",strtotime($input['expiration_date']));
		}

		$date_created = date("Y-m-d H:i:s",time());
		// save the gift data
		$giftcode = $input['gift_code'];
		$input['gift_code'] = strip_tags($input['gift_code']);
		
		$input['member_id'] = (int)$input['member_id'];
		$input['created_at'] = date("Y-m-d H:i:s",time());
		$input['remaining_value'] = $input['initial_value'];
		$input['updated_at'] = date("Y-m-d H:i:s",time());
		
		// first check if gift_code already availabe or not
		$check_gift = "SELECT `id` FROM `".$table."` WHERE `gift_code`='$giftcode' AND `discount_type`='giftcard'";
		$check_gift_code = $mysqli->query( $check_gift );
		if( $check_gift_code->num_rows <= 0 )
		{
			$in_indexes= implode( ',', array_map( function( $pp ) { return "`".$pp."`"; }, array_keys( $input ) ) );
			$in_values= implode( ',', array_map( function( $val ) { return '"'.$val.'"';}, array_values( $input ) ) );
			$in=$mysqli->query("INSERT INTO `".$table."` (".$in_indexes.") VALUES (".$in_values.")");
			if( $in )
			{
				$lastid=$mysqli->insert_id;
				$comment='You issued a new gift card.';
				// insert row in timeline table
				$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`) VALUES ('".$lastid."','".$comment."','".$date_created."')");
				
				// if customer added in gift card then send a gift email
				if( !empty($input['member_id']) && $input['member_id'] != -1 )
				{
					$members = get_member($input['member_id']);
					$email = $members['email'];
					$name = $members['name'];
					$inivalue = $input['initial_value'];
					$currency = $input['currency'];
					$email_data= $this->replaceSubject($currency, $inivalue,'giftcard',$name,$email,$giftcode,'10');
					$edata=[
						"",
						"name"=>$name,
						"email"=>$email,
						"subject"=>$email_data['subject'],
						"body"=>$email_data['body']
					];
					// send gift card email
					cf_mail($edata);
					
					$comment1="You sent an email message containing the gift card to $email.";
					// insert row in timeline table
					$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`) VALUES ($lastid,'".$comment1."','".$date_created."')");
				}
				return json_encode(array('status'=>1,'message'=>t('gift added successfully'),'last_id'=>$lastid));
			}else{
				return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
			}
		}else{
			return json_encode(array('status'=>0,'message'=>t('The gift card code is already taken. Please add a unique gift code.')));
		}
	}

	function updateGiftCard( $data )
	{
		//create product 0 for insert 1 for update
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table = $pref."gift_cards";
		$table1 = $pref."issue_gift";
		$input=[];
		$giftcard_id = $mysqli->real_escape_string($data['giftcard_id']);
		$type = $mysqli->real_escape_string($data['type']);
		$getgift = $mysqli->query("SELECT * FROM `".$table."` WHERE `id`='".$giftcard_id."'");
		if( $getgift->num_rows > 0 )
		{
			$gifts = $getgift->fetch_assoc();
			$filter_data=['giftcard_id','savegiftcards','action'];
			foreach( $data as $index => $dat  )
			{
				if( !in_array( $index, $filter_data ) )
				{
					if( $index == 'products' ){continue;}
					else{
						$input[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
					}
				}
			}
			$productss=[];
			if( isset( $data['products'] ) &&  count($data['products']) > 0  )
			{
				if( count( $data['products'] ) > 0  )
				{
					foreach( $data['products'] as $product )
					{
						if( !empty( $product ) )
						{
							$pro = $mysqli->real_escape_string( $product );
							$productss[]=$pro;
						}
					}
				}else{
					return json_encode(array('status'=>0,'message'=>t('Please select products')));
				}
			}
			
			$input['products'] = $mysqli->real_escape_string( json_encode( $productss ) );
			$inp = array_merge($gifts, $input);
			if( $inp['expiration_type']=="no_expiration" )
			{
				$inp['expiration_date'] = date("2030-01-01");
			}else{
				$inp['expiration_date'] = date("Y-m-d",strtotime( $inp['expiration_date'] ) );
			}
			$date_created  = date( "Y-m-d H:i:s",time() );
			if( isset($data['expiration_type'])  )
			{
				$never=$gifts['expiration_type'];
				$newnever=$data['expiration_type'];
				$old1 = strtotime( $gifts['expiration_date'] );
				$new1 = strtotime( $data['expiration_date'] );
				if( $never=='no_expiration' )
				{
					$change='never';
				}else{
					$change=date('d M, Y',$old1);
				}
				if( $newnever=='no_expiration' )
				{
					$new='never';
				}else{
					$new=date("d M, Y", $new1 );
				}
				
				if( $never != $newnever )
				{
					if($type=="giftcard")
					{
						$comment = "You changed the gift card expiration date from $change to $new.";

					}else{
						$comment = "You changed the discount expiration date from $change to $new.";
					}
					$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`) VALUES ( '".$giftcard_id."','".$comment."','".$date_created."')");
				}else if( $newnever == "set_expiration"  && $old1 != $new1 ){
					if($type=="giftcard")
					{
						$comment = "You changed the gift card expiration date from $change to $new.";
					}else{
						$comment = "You changed the gift card expiration date from $change to $new.";
					}
					$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`) VALUES ( '".$giftcard_id."','".$comment."','".$date_created."')");
				}
			}
			$inp['updated_at'] = date( "Y-m-d H:i:s",time() );
			unset($inp['created_at']);
			unset($inp['id']);
			unset($inp['currency']);
			unset($inp['gift_code']);
			unset($inp['type']);
			if($type=="giftcard")
			{
				$comment = "You updated the gift card.";
			}else{
				$comment = "You changed the discount";
			}
			$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`) VALUES ( '".$giftcard_id."','".$comment."','".$date_created."')");
			$in_index = $this->updateQuery( $inp );
			$ins=$mysqli->query("UPDATE  `".$table."` SET ".$in_index." WHERE `id`=$giftcard_id")?1:-1;
			if($ins){

				return json_encode(array('status'=>1,'message'=>t('gift update successfully'),'last_id'=>$giftcard_id));
			}else{
				return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
			}
		}


	}
	/*
	Add a gift-cards
	*/ 
	function sendCode( $data )
	{

		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table1 = $pref."issue_gift";
		$giftcard_id = $mysqli->real_escape_string($data['giftcard_id']);
		$name = $mysqli->real_escape_string($data['name']);
		$type = $mysqli->real_escape_string($data['type']);
		$email = $mysqli->real_escape_string($data['email']);
		$data=$this->getOneGiftCard($giftcard_id,array('gift_code','percentage','initial_value','currency','expiration_type','expiration_date'));
		$inivalue = $data['initial_value'];
		$date_created  = date( "Y-m-d H:i:s",time() );
		$currency=$data['currency'];
		$giftcode=$data['gift_code'];
		$percentage=$data['percentage'];
		$email_data= $this->replaceSubject($currency,$inivalue,$type,$name,$email,$giftcode,$percentage);
		$edata=[
			"",
			"name"=>$name,
			"email"=>$email,
			"subject"=>$email_data['subject'],
			"body"=>$email_data['body']
		];
		$s=cf_mail($edata);
		$comment1="You sent an Email message containing the gift card to $email.";
		$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`) VALUES ('".$giftcard_id."','".$comment1."','".$date_created."')");
		if( $mysqli->affected_rows > 0 )
		{
			return json_encode(array('status'=>1,'message'=>t('Message has been sent successfully')));
		}else{
			return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
		}


	}
	function debug($data, $type="")
	{
		echo "Data for ".$type;
		echo "<pre>";
		print_r($data);
		echo "<pre>";
	}

	function createGiftCardProduct( $data )
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
		$input=[];


		// data not provided in product form
		$data_not_provided_inform= array(
			'url'=> "",
			'price'=> 0,
			'shipping'=> 0,
			'subproducts'=>json_encode([]),
			'opproducts'=>json_encode([]),
			'tax'=>0,
			'createdon'=>time(),
			'show_currency_symbol'=>0,
			'shipping'=> 0,
			'track_quantity'=> 1,
			'has_variant'=> 1,
		);
		$data['continue_on_outof_stock']=1;
		$data['physical_product']=0;
		$data['track_quantity']=1;
		$data_provided_in_form=['productid','title','description','is_active','p_type','tags','currency','funnelid','def_product_page','continue_on_outof_stock','physical_product','track_quantity','currency_symbol'];
		
		// escape the coming data
		foreach( $data as $index => $dat  )
		{
			if( in_array( $index, $data_provided_in_form ) )
			{
				$input[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
			}
		}
		if( empty( $input['productid'] ) )
		{
			return json_encode(array('status'=>0,'type'=>'productid','message'=>t('Please enter product Id.')));
		}
		if( empty( $input['title']) )
		{
			return json_encode(array('status'=>0,'type'=>'title','message'=>t('Please enter title.')));
		}
		
		$medias=[];
		if( isset( $data['media'] ) && isset( $data['type'] ) && ( count($data['type']) == count($data['media'] ) ) )
		{
			$i=0;
			foreach( $data['media'] as $media )
			{
				if( !empty( $media ) )
				{
					$med = $media;
					$med_type = $mysqli->real_escape_string( $data['type'][$i] );
					$new_media = ['media'=>stripslashes($med),'type'=>$med_type];
					$medias[]=$new_media;

				}
				$i++;
			}
		}
		$collections=[];
		if( isset( $data['collections'] ) &&  count($data['collections']) > 0  )
		{
			foreach( $data['collections'] as $collection )
			{
				if( !empty( $collection ) )
				{
					$coll = $mysqli->real_escape_string( $collection );
					$collections[]=$coll;
				}
			}
		}

		$input['media'] = $mysqli->real_escape_string( stripslashes(json_encode( $medias ) ) );
		$input['collections'] = $mysqli->real_escape_string( json_encode($collections) );

		$inp = array_merge($input,$data_not_provided_inform);
		$arr_ob = (object)$inp;
		$data_to_provide_inplugins= array(

			'product_id'=> $arr_ob->productid,
			'title'=> $arr_ob->title,
			'description'=>$arr_ob->description,
			'url'=> $arr_ob->url,
			'price'=> $arr_ob->price,
			'currency'=> $arr_ob->currency,
			'shipping_charge'=> $arr_ob->shipping,
			'sub_products'=>[],
			'optional_products'=>[],
			'tax'=>$arr_ob->tax,
		);

		$data_to_provide_inplugins= array_merge($data_to_provide_inplugins, $inp);
		if( $data['savegiftcardsproduct'] == 'save' )
		{
			// check if product id already available or not
			$sql="SELECT `id` FROM `".$table."` WHERE `productid`='".$input['productid']."'";
			$result = $mysqli->query( $sql );
			if( $result->num_rows > 0 )
			{
				return json_encode(array('status'=>0,'type'=>'productid','message'=>t('Product id is already available. Please use different unique product Id.')));
			}
			$denom=[];
			$combinations=[];
			if( is_array( $data['denominations'] ) )
			{
				$i=0;
				
				foreach( $data['denominations'] as $dkey =>  $denomination)
				{
					if( !empty( trim($denomination) ) )
					{
						$deno = $mysqli->real_escape_string($denomination);
						$denom['Denomination'][]=$deno;
						$combinations['headers'] = ['Denomination'];
						$combinations['items'][$dkey]=['Denomination'=>$denomination];

					}
					$i++;
				}
			}else{
				$combinations['headers'] = [];
				$combinations['items']   =[]; 
			}

			$inp['price']=$denom['Denomination'][0];
			$inp['variants']=$mysqli->real_escape_string( json_encode( (object)$denom ));
			$inp['combinations']=$mysqli->real_escape_string(json_encode( (object)$combinations ));
			$in_indexes= implode(',', array_map(function($pp){ return "`".$pp."`"; }, array_keys($inp)));
			$in_values= implode(',', array_map(function($val){ return '"'.$val.'"';}, array_values($inp)));
			$in=$mysqli->query("INSERT INTO `".$table."` (".$in_indexes.") VALUES (".$in_values.")");
			if($in){

				$id= $mysqli->insert_id;
				$this->checkAndAddProductInCollection( $id );
				$data_to_provide_inplugins['id']= $mysqli->insert_id;

				if( get_option("sales_notif_email_products") )
				{
					$sales_notif_email_products=explode(',', get_option( "sales_notif_email_products"));
					if(!array_search($data_to_provide_inplugins['id'], $sales_notif_email_products))
					{
						array_push($sales_notif_email_products, $data_to_provide_inplugins['id']);
					}
					update_option("sales_notif_email_products", implode(',', $sales_notif_email_products));
				}
				$plugin_loader->processProduct($data_to_provide_inplugins,'add');
				if( count( $data['denominations'] ) > 0 )
				{
					$this->addVariantDenominations( $data['denominations'], $id,$inp['funnelid'],$inp['is_active'],$inp['def_product_page'],$inp['tags'],$inp['collections'] ,'save',$inp['currency'],$inp['currency_symbol'] );
					return json_encode(array('status'=>1,'type'=>'success','message'=>t('Gift Product added successfully.'),'last_id'=>$id));
				}


			}else{
				return json_encode(array('status'=>0,'type'=>'error','message'=>t('Error! Gift Product not added successfully.')));
			}
		}
		else if( $data['savegiftcardsproduct'] == 'update' )
		{
			$productid = $mysqli->real_escape_string($data['giftcardprd_id']);
			$denom=[];
			$combinations=[];

			/**************************************
			
				denomination ==  variants
			
			*****************************************/ 
			
			if( is_array( $data['denominations']['title'] ) )
			{
				$titles = $data['denominations']['title'];
				$variant_ids = $data['denominations']['variant_id'];
				foreach( $titles as $dkey =>  $denomination)
				{
					if( !empty( trim($denomination) ) )
					{
						$deno = $mysqli->real_escape_string($denomination);
						$denom['Denomination'][]=$deno;
						$combinations['headers'] = ['Denomination'];
						$combinations['items'][ $mysqli->real_escape_string($variant_ids[$dkey]) ]=['Denomination'=>$denomination];
					}
				}
			}else{
				$combinations['headers'] = [];
				$combinations['items']   =[]; 
			}
			$inp['price']=$denom['Denomination'][0];

			$inp['variants']=$mysqli->real_escape_string( json_encode( (object)$denom ));
			$inp['combinations']=$mysqli->real_escape_string(json_encode( (object)$combinations ));
			
			$inps=$inp;
			unset($inps['productid']);
			unset($inps['has_variant']);
			$in_index = $this->updateQuery($inps);
			$ins=$mysqli->query("UPDATE  `".$table."` SET ".$in_index." WHERE `id`='".$productid."'")?1:-1;
			if($ins){

				$id= $productid;
				$this->checkAndAddProductInCollection( $id );
				$data_to_provide_inplugins['id']= $productid;
				if( isset( $data['denominations'] ) )
				{
					$this->addVariantDenominations( $data['denominations'], $id,$inp['funnelid'],$inp['is_active'],$inp['def_product_page'],$inp['tags'],$inp['collections'],"update",$inp['currency'],$inp['currency_symbol'] );
				}
				return json_encode(array('status'=>1,'type'=>'success','message'=>t('Gift Product updated successfully.'),'last_id'=>$id));

			}else{
				return json_encode(array('status'=>1,'type'=>'success','message'=>t('Error! Gift Product not updated successfully.')));
			}
		}
	}
	function checkAndAddProductInCollection( $productid)
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table1 = $pref."all_products";
		$table = $pref."product_collections";
		$prd = $mysqli->query("SELECT `collections` FROM `$table1` WHERE `id`=$productid ");
		
		if( $prd->num_rows )
		{
			$re = $prd->fetch_object();
			$collects = json_decode( $re->collections );
			$collect_prd=[];
			foreach( $collects as $collect )
			{
				$run = $mysqli->query( "SELECT `id`,`products` FROM `$table` WHERE `id`=$collect LIMIT 1" );
				if( $run->num_rows > 0 )
				{
					$results = $run->fetch_object();
					$products = json_decode( $results->products );
					if( is_array( $products ) )
					{
						if( in_array( $productid, $products )===false )
						{
							array_push( $products, "$productid" );
							$collect_prd =  $products;
						}else{
							array_push($collect_prd,"$productid"  );
						}
					}else{
						array_push($collect_prd,"$productid"  );
					}
					$prd = json_encode( $collect_prd);
					$run = $mysqli->query( "UPDATE  `$table` SET `products`='$prd' WHERE `id`=$collect LIMIT 1" );
					return true;
				}
			}
		}

	}
	public function addVariantDenominations( $denominations, $pid,$fid,$active,$def_product_page,$tags,$collections, $action="save",$currency="USD",$currency_symbol="$" )
	{
		//create product 0 for insert 1 for update
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$data_not_provided_inform= array(
			'url'=> "",
			'is_active'=> $active,
			'parent_product'=>$pid,
			'funnelid'=>$fid,
			'is_variant'=>1,
			'p_type'=>'gift_card',
			'shipping'=> 0,
			'subproducts'=>json_encode([]),
			'opproducts'=>json_encode([]),
			'tax'=>0,
			'continue_on_outof_stock'=>1,
			'tags'=>$tags,
			'createdon'=>time(),
			'show_currency_symbol'=>0,
			'track_quantity'=> 1,
			'def_product_page'=> $def_product_page,
			'currency'=> $currency,
			'currency_symbol'=> $currency_symbol,
			'collections'=> $collections,
		);
		$in=true;

		if( $action=="save" )
		{
			if( is_array( $denominations ) )
			{
				foreach( $denominations as $dkey => $denomination)
				{
					if( !empty( trim($denomination) ) )
					{
						$denom=[];
						$combinations=[];
						$combinations['headers'] = [];
						$combinations['items']   =[]; 
						$combinations['sku']   =''; 
						$proived_field['variants']=$mysqli->real_escape_string(json_encode( (object)$denom ));
						$proived_field['combinations']=$mysqli->real_escape_string(json_encode( (object)$combinations  ));
						$deno = $mysqli->real_escape_string($denomination);
						$key = $mysqli->real_escape_string($dkey);
						$proived_field['title']="";
						$proived_field['description']="";
						$proived_field['price']=$deno;
						$proived_field['productid']=$key;
						$proived_field['media']=json_encode([]);
						$inp=array_merge($data_not_provided_inform,$proived_field);
						$in_indexes = implode(',', array_map(function($pp){ return "`".$pp."`"; }, array_keys($inp)));
						$in_values  = implode(',', array_map(function($val){ return '"'.$val.'"';}, array_values($inp)));
						$in = $mysqli->query("INSERT INTO `".$table."` (".$in_indexes.") VALUES (".$in_values.")");
						if( $mysqli->affected_rows > 0 )
						{
							$id = $mysqli->insert_id;
							$this->addVarient( $id, $deno, $pid,"add"  );
							$in = true;
						}else{
							$in=false;
						}
					}
				}
			}
		}else{
			$inputs=[];
			
			if(is_array($denominations['title']))
			{
				if( isset( $denominations['delete'] ) )
				{
					foreach ($denominations['delete'] as $delete)
					{
						$idd=$mysqli->real_escape_string($delete);
						$mysqli->query("DELETE FROM `".$table."` WHERE `id`=$idd");

					}

				}
				$i=0;
				$variant_ids = $denominations['variant_id'];
				$titles = $denominations['title'];
				$prices = $denominations['price'];
				$skus = $denominations['sku'];
				$mtypes = $denominations['variant_mtype'];
				$medias = $denominations['variant_media'];
				$proived_field=[];
				$proived_field['variants']=json_encode((object)[]);
				$combinations=[];
				
				foreach( $titles as $title )
				{
					$deno = $mysqli->real_escape_string($title);
					$combinations['headers'] = ['Denomination'];
					$combinations['items'][]=['Denomination'=>$deno];
				}

				$proived_field['combinations']=$mysqli->real_escape_string(json_encode( (object)$combinations ));
				foreach( $titles as $title )
				{
					$proived_field['title'] = $mysqli->real_escape_string( $title );
					$proived_field['price'] = $mysqli->real_escape_string( $prices[$i] );
					$proived_field['sku'] = $mysqli->real_escape_string( $skus[$i] );
					$proived_field['productid'] = $mysqli->real_escape_string( $variant_ids[$i] );
					if( isset($medias[$i]) && !empty($medias[$i]) && isset($mtypes[$i] ) && !empty($mtypes[$i] )  )
					{
						$med_type = $mysqli->real_escape_string( $mtypes[$i] );
						$med = $mysqli->real_escape_string( $medias[$i] );
						$new_media = ['media'=>stripslashes( $med ),'type'=>$med_type];
						$mediass[]=$new_media;
					}else{
						$mediass=[];
					}
					$proived_field['media'] = $mysqli->real_escape_string( stripslashes(json_encode( $mediass ) ) );
					$inputs[]=$proived_field;
					$i++;
				}
				foreach($inputs as $input )
				{
					$inp=$input;
					unset($inp['productid']);
					$in_index = $this->updateQuery($inp);
					$ins=$mysqli->query("UPDATE  `$table` SET ".$in_index." WHERE `productid`='".$input['productid']."'")?1:-1;
					if( $ins  ){
						$this->addVarient( $input['productid'], ['title'=>$inp['title'],'price'=>$inp['price']], $pid,"update"  );
						$in=true;
					}else{
						$in=false;
					}
				}
			}
		}
		return $in;
	}
	function addVarient( $id, $deno, $pid ,$type="add")
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$product_variants=$pref."product_variants";
		if($type=="add")
		{
			$mysqli->query("INSERT INTO `$product_variants` (`product`, `variant_name`, `variant_val`, `parent_product_id`) VALUES ('$id','$deno','$deno',$pid)");
		}else{
			$n=$deno['title'];
			$v=$deno['price'];
			$ids = $this->getVarientId( $id );
			if($ids)
			{
				$mysqli->query("UPDATE  `$product_variants` SET `variant_name`='$n', `variant_val`='$v' WHERE `product`='$ids' ");
			}
		}
		return true;
	}
	function getVarientId($vid)
	{
		$product_variants=$this->dbpref."all_products";
		$sql=$this->mysqli->query("SELECT `id` FROM `$product_variants` WHERE `productid`='$vid'");
		if( $sql->num_rows > 0 )
		{
			$d = $sql->fetch_object();
			return $d->id;
		}
		return false;
	}
	function updateQuery( Array $arr ){
		$str='';
		$arr_key = array_keys($arr);
		for( $i=0; $i<count($arr_key); $i++ )
		{
			if( $i==count($arr_key)-1 )
			{
				$str.="`".$arr_key[$i]."`='".$arr[$arr_key[$i]]."' ";
			}else{
				$str.="`".$arr_key[$i]."`='".$arr[$arr_key[$i]]."', ";
			}
		}
		return $str;
	}
	function getGiftCardProduct( $id=0 )
	{
		//create product 0 for insert 1 for update
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$arr=[];
		if($id)
		{
			$idd=$mysqli->real_escape_string( $id );
			$qry=$mysqli->query("SELECT * FROM `".$table."`  WHERE `id`=$idd");
			if($qry->num_rows>0)
			{
				while($data = $qry->fetch_assoc() )
				{
					$arr=$data;
				}   
			}
		}
		return $arr;
	}

	// get product variants
	function getGiftCardProductVariant( $id=false )
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$idd=$mysqli->real_escape_string( $id );
		$qry=$mysqli->query("SELECT `id`,`productid`,`title`,`price`,`sku`,`parent_product`,`media` FROM `".$table."`  WHERE `parent_product` = $idd");
		$arr=[];
		if( $qry->num_rows > 0 )
		{
			while( $data = $qry->fetch_assoc() )
			{
				$arr[]=$data;
			}   
		}
		return $arr;
	}
	function getOneGiftCard( $id=false,$get='' )
	{
		//create product 0 for insert 1 for update
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table = $pref."gift_cards";
		$id = $mysqli->real_escape_string($id);
		if( is_array( $get ) )
		{
			$in_values= implode(',', array_map(function($val){ return "`$val`";}, array_values($get)));
		}else{
			$in_values="*";
		}
		
		$sql="SELECT ".$in_values." FROM `".$table."` WHERE `id`=$id";
		$re = $mysqli->query( $sql );
		if( $re->num_rows >0 )
		{
			$data=$re->fetch_assoc();
			return $data;
		}else{
			return false;
		}
	}

	function getCollection($store_id)
    {
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$sid = $this->mysqli->real_escape_string($store_id);
		$table=$pref."product_collections";
		$qry=$mysqli->query("SELECT `id`, `title` FROM `".$table."` WHERE `funnel_id`=$sid ORDER BY `id` DESC");
		$arr=[];
		if($qry->num_rows>0)
		{
			while($data = $qry->fetch_assoc() )
			{
			$arr[]=$data;
			}   
		}
		return $arr;

    }
	function getGiftCards($page=1, $id=1, $type="giftcard" )
    {
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."gift_cards";
		$page=$mysqli->real_escape_string($page);
		$id = $mysqli->real_escape_string($id);
		$max_limit=(int)get_option('qfnl_max_records_per_page');
		if(!$max_limit)
		{$max_limit=$mysqli->real_escape_string($max_limit);}

		$arr=array();
		$limit="";

		if($max_limit !==false && is_numeric($max_limit) && is_numeric($page))
		{
			$page=($page*$max_limit)-$max_limit;
			$limit =" limit ".$page.','.$max_limit;
		}
		/////////////////////////////
		$search="";
		if(isset($_POST['onpage_search']))
		{

			$search=trim($mysqli->real_escape_string($_POST['onpage_search']));
			$search=str_replace('_','[_]',$search);
			$search=str_replace('%','[%]',$search);
			// filter data with collection title and collection type
			$search=" AND `gift_code` LIKE '%".$search."%' OR `expiration_type` LIKE '%".$search."%' OR `initial_value` LIKE '%".$search."%'";
		}
		$order_by="`id` DESC";
		if(isset($_GET['arrange_records_order']))
		{
			$order_by=base64_decode($_GET['arrange_records_order']);
		}
	  
		$date_between=dateBetween('created_at',null,true);

		if(strlen($date_between[0])>0)
		{
			$search .=$date_between[1];
		}
		if($id)
		{
			$search.=" AND `id`=".$id;

		}
		//////////////////////////////
		$qry=$mysqli->query("SELECT * FROM `".$table."` WHERE `discount_type`='".$type."' AND 1 ".$search." ORDER BY ".$order_by.$limit);
		$arr=[];
		if($qry->num_rows>0)
		{
			while($data = $qry->fetch_assoc() )
			{
			$arr[]=$data;
			}   
		}
		return $arr;

    }
	function getGiftCardsCount( $type="giftcard" )
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table= $pref."gift_cards";
		$qry = $mysqli->query("SELECT COUNT(*) as `total` FROM `".$table."` WHERE `discount_type`='".$type."'");
		if($qry->num_rows > 0 ){
			$r=$qry->fetch_object();
			return $r->total;
		}
	}

	function getallStoreProducts()
	{
        $mysqli = $this->mysqli;
		$pref   = $this->dbpref;
		$table  = $pref."all_products";
		//////////////////////////////
		$qry=$mysqli->query("SELECT `id`, `productid`, `parent_product`, `is_variant`, `title`, `combinations` FROM `".$table."` WHERE `parent_product`=0");
		$arr=[];
		if($qry->num_rows>0)
		{
			while($data = $qry->fetch_object() )
			{
				$variants = $this->getVariants('`id`, `productid`, `parent_product`, `is_variant`, `title`, `combinations`', $data->id,json_decode($data->combinations));
				$arr[]=$data;
				if( count( $variants ) > 0 )
				{
					foreach($variants as $variant)
					{
						$arr[]=$variant;
					}
				}
			}
		}
		return $arr;
	}

	function getVariants($get,$pid,$combinations)
	{
		$mysqli = $this->mysqli;
		$pref   = $this->dbpref;
		$table  = $pref."all_products";
		

		$qry=$mysqli->query("SELECT $get FROM `".$table."` WHERE  `parent_product`=$pid");
		$arr=[];
		if( $qry->num_rows > 0 )
		{
			$i=0;
			while($data = $qry->fetch_assoc() )
			{
				$v = $this->getProductTitle($pid,$data['productid']);
				$data['title']=$v['title'];
				$data['v']= $v['variants'];
				$arr[]=(object)$data;
			}
		}
		return $arr;
	}
	function getProductTitle($pid, $variant_id=false)
	{
		$mysqli= $this->mysqli;
		$table= $this->dbpref."all_products";
		$qry = $mysqli->query("SELECT `title`,`combinations` FROM `".$table."` WHERE `id`=$pid LIMIT 1");
		if( $qry->num_rows > 0 ){
			$r=$qry->fetch_object();
			$combinations =json_decode($r->combinations,true);
			if( !empty( $combinations['items'][$variant_id] ) )
			{
				$variants = $combinations['items'][$variant_id];
				if(is_array($variants))
				{
					$variant = implode("/",$variants);
					return array('title'=>$r->title,'variants'=>$variant);
				}
			}else{
				return array('title'=>'','variants'=>'');
			}

		}
	}
	// delete a collection
	function deleteCollection($id)
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table= $pref."gift_cards";
		$id= $mysqli->real_escape_string($id);
		$id= (int) $id;
		$mysqli->query("delete from `".$table."` where `id`='".$id."'")?1:-1;
		
	}
	// delete the Products
	function deleteGiftProducts($id)
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table= $pref."all_products";
		$product_variants= $pref."product_variants";
		$id= $mysqli->real_escape_string($id);
		$id= (int) $id;
		$mysqli->query("delete from `".$table."` where `id`='".$id."'");
		$mysqli->query("delete from `".$table."` where `parent_product`='".$id."'");
		$mysqli->query("delete from `".$product_variants."` where `parent_product_id`='".$id."'");
		
	}
	//get the gifts card product
	function getAllGiftProducts($total_setup= false, $page=1, $store_id, $id=1 )
    {
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$page=$mysqli->real_escape_string($page);
		$id = $mysqli->real_escape_string($id);
		$fid = $mysqli->real_escape_string($store_id);
		$max_limit=(int)get_option('qfnl_max_records_per_page');
		if(!$max_limit)
		{$max_limit=$mysqli->real_escape_string($max_limit);}

		$limit="";

		if($max_limit !==false && is_numeric($max_limit) && is_numeric($page))
		{
			$page=($page*$max_limit)-$max_limit;
			$limit =" limit ".$page.','.$max_limit;
		}
		/////////////////////////////
		$search="";
		if(isset($_POST['onpage_search']))
		{

			$search=trim($mysqli->real_escape_string($_POST['onpage_search']));
			$search=str_replace('_','[_]',$search);
			$search=str_replace('%','[%]',$search);
			// filter data with collection title and collection type
			$search="AND `title` LIKE '%".$search."%' OR `productid` LIKE '%".$search."%'";
		}
		$order_by="`id` DESC";
		if(isset($_GET['arrange_records_order']))
		{
			$order_by=base64_decode($_GET['arrange_records_order']);
		}
	  
		$date_between=dateBetween('createdon',null,false);

		if(strlen($date_between[0])>0)
		{
			$search .=$date_between[1];
		}
		if($id)
		{
			$search.=" AND `id`=".$id;

		}
		//////////////////////////////
		$qry=$mysqli->query("SELECT * FROM `".$table."`  WHERE `funnelid`=$fid AND `p_type`='gift_card' AND `is_variant`='0' AND `parent_product`=0 AND 1 ".$search." ORDER BY ".$order_by.$limit);
		// echo "SELECT * FROM `".$table."` WHERE `funnelid`=$fid AND  `p_type`='gift_card' AND `is_variant`='0' AND `parent_product`=0 AND 1 ".$search." ORDER BY ".$order_by.$limit;
		
		if($qry->num_rows>0)
		{
			return $qry;
		}else{
			return 0; 
		}
    }

	// get the gift card timeline
	function getIssueTimeline($id )
    {
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."issue_gift";
		$id = $mysqli->real_escape_string( $id );
		$qry=$mysqli->query("SELECT * FROM `".$table."` WHERE `giftcard_id`=$id ORDER BY `id` DESC");
		if( $qry->num_rows > 0 )
		{
			return $qry;
		}else{
			return 0; 
		}
    }
	//get gravatar image
	function getGravatarImage( $email = null,$size=40 ){
		$email = $email;
		$default = 'mp';
		$size = $size;
		$grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
		return $grav_url;
  
	}
	// get sales of releted gift card product
	function getSales( $id)
	{

		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_sales";
		$id = $mysqli->real_escape_string($id);
		$sql="SELECT COUNT(*) as 'total_count' FROM `".$table."` WHERE `productid`='".$id."' AND valid='1'";
		$qry=$mysqli->query($sql);
		$sales=0;
		if( $qry->num_rows > 0 )
		{
			$result = $qry->fetch_object();
			$sales_count = $result->total_count;
			$sales = $sales_count+$sales;
		}
		$v_sales = self::getVariantsSales( $id );
		$sales=$sales+$v_sales;
		return $sales;


	}
	function getVariantsSales( $pid )
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."all_products";
		$table1=$pref."all_sales";
		$sql = "SELECT `id`,`productid` FROM `$table` WHERE `parent_product`=$pid AND `is_variant`='1'";
		$row = $mysqli->query($sql);
		$sales = 0;
		if( $row->num_rows > 0 )
		{
			while( $data = $row->fetch_object() )
			{
				$pid = $data->id;
				$salescountquery=$mysqli->query("SELECT count(`id`) as `sales_count` from `$table1` WHERE `productid`='$pid' and `valid`='1'");
				if( $salescountquery->num_rows > 0 )
				{
					$r = $salescountquery->fetch_object();
					$sales_count = $r->sales_count;
					$sales = $sales_count+$sales;
				}
				
			}
		}
		return $sales;

	}
	function getGiftProdctCount( )
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table= $pref."all_products";
		$qry = $mysqli->query("SELECT COUNT(*) as `total` FROM `".$table."`  WHERE `p_type`='gift_card' AND `is_variant`='0' AND `parent_product`=0");
		if($qry->num_rows > 0 ){
			$r=$qry->fetch_object();
			return $r->total;
		}
	}
	/* get currency symboles */
	function get_currency_symbol($cc = 'USD')
	{
		$cc = strtoupper($cc);
		$currency = array(
		"USD" => "$" , //U.S. Dollar
		"AUD" => "$" , //Australian Dollar
		"BRL" => "R$" , //Brazilian Real
		"CAD" => "C$" , //Canadian Dollar
		"CZK" => "Kč" , //Czech Koruna
		"DKK" => "kr" , //Danish Krone
		"EUR" => "€" , //Euro
		"HKD" => "&#36" , //Hong Kong Dollar
		"HUF" => "Ft" , //Hungarian Forint
		"ILS" => "₪" , //Israeli New Sheqel
		"INR" => "₹", //Indian Rupee
		"JPY" => "¥" , //Japanese Yen 
		"MYR" => "RM" , //Malaysian Ringgit 
		"MXN" => "&#36" , //Mexican Peso
		"NOK" => "kr" , //Norwegian Krone
		"NZD" => "&#36" , //New Zealand Dollar
		"PHP" => "₱" , //Philippine Peso
		"PLN" => "zł" ,//Polish Zloty
		"GBP" => "£" , //Pound Sterling
		"SEK" => "kr" , //Swedish Krona
		"CHF" => "Fr" , //Swiss Franc
		"TWD" => "$" , //Taiwan New Dollar 
		"THB" => "฿" , //Thai Baht
		"TRY" => "₺" //Turkish Lira
		);
		if(array_key_exists($cc, $currency)){
			return $currency[$cc];
		}else{
			return "$";
		}
	}
	function replaceSubject($currency="USD",$prices=0,$type="giftcard",$name="",$email="",$giftcode="",$percentage="")
	{
		global $mysqli;
		$pref=$this->dbpref;
		$subject="";
		$table = $pref."gift_cards_settings";
		if($type=="giftcard")
		{
			$row = $mysqli->query("SELECT `gemail_subject`,`gemail_content` FROM `$table`");
			if( $row->num_rows > 0 )
			{
				$r = $row->fetch_assoc();
				$subject = $r['gemail_subject'];
				$content = $r['gemail_content'];
				return $this->replaceshortcode( $currency, $prices, $subject, $content,$percentage,$name,$email,$giftcode);
			}else{
				return [ 'subject'=>'','body'=>'' ];
			}

		}else{
			$row = $mysqli->query("SELECT `demail_subject`,`demail_content` FROM `$table`");
			if( $row->num_rows > 0 )
			{
				$r = $row->fetch_assoc();
				$subject = $r['demail_subject'];
				$content = $r['demail_content'];
				return $this->replaceshortcode( $currency, $prices, $subject, $content,$percentage,$name,$email,$giftcode);
			}else{
				return [ 'subject'=>'','body'=>'' ];
			}
		}
	
	}
	function replaceshortcode($currency="USD",$prices='',$subject='',$content='',$percentage='',$name='',$email='',$giftcode='')
	{
		$sub="";
		$prices = sprintf("%1\$.2f",$prices);
		$cont="";
		$shortcodes=['{initial_value}','{name}','{email}','{percentage}','{currency}','{giftcode}','{discount}'];
		$shortcodes_v=['{initial_value}'=>$prices,'{name}'=>$name,'{email}'=>$email,'{discount}'=>$giftcode,'{percentage}'=>$percentage,"%",'{currency}'=>$currency,'{giftcode}'=>$giftcode];

		foreach($shortcodes as $shortcode)
		{
			$sub =$subject;
			if( stristr( $sub,$shortcode ) )
			{
				$sub=str_ireplace( $shortcode, $shortcodes_v[$shortcode] , $sub );
			}
			$subject=$sub;
			$cont =$content;
			if( stristr( $cont,$shortcode ) )
			{
				$cont=str_ireplace( $shortcode, $shortcodes_v[$shortcode] , $cont );
			}
			$content=$cont;
		}
		return [ 'subject'=>$sub,'body'=>$content ];
	}
	// delete gift card prodcut only
	function deleteGiftProduct($id)
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table= $pref."all_products";
		$id= $mysqli->real_escape_string($id);
		$id= (int) $id;
		$mysqli->query("delete from `".$table."` where `id`='".$id."'")?1:-1;
	}
	// delete gift card only
	function deleteGiftCard($id)
	{
		$mysqli= $this->mysqli;
		$pref= $this->dbpref;
		$table= $pref."gift_cards";
		$id= $mysqli->real_escape_string($id);
		$id= (int) $id;
		$mysqli->query("delete from `".$table."` where `id`='".$id."'")?1:-1;
	}
	function getAllFunnels($funnel_id=false)
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref."quick_funnels";

      if($funnel_id)
      {
        $id = $mysqli->real_escape_string($funnel_id);
        $qry=$mysqli->query("select `id`,`name` from `$table` WHERE  `id`=$id  ORDER BY `id` DESC");
      }else{
        $qry=$mysqli->query("select `id`,`name` from `$table` ORDER BY `id` DESC");
      }
      $arr=array();
      if($qry->num_rows>0)
      {
        while($data = $qry->fetch_assoc() )
        {
          $arr[]=$data;
        }   
      }
      return $arr;

    }
	function text_to_avatar($txt){
		$colors=  ['#003366', '#005580', '#049560', '#e68a00', '#e62e00', '#e6005c', '#660066', '#800040', '#990099', '#008000', '#73264d'];
		shuffle($colors);
		$txt= strtoupper(trim($txt));
		$txt= preg_replace('/(\s){2,}/', ' ', $txt);
		$avatar= "";
		if(strlen($txt)>0) {
		$arr= array_slice(explode(" ", $txt), 0, 2);
		foreach($arr as $word){
			$avatar .= substr($word, 0, 1);
		}
		$color= $colors[mt_rand(0, 10)];
		$av_len= count($arr);
		$avatar = "<div style='background-color: ".$color."' data-dropdown-store-avatar='true'><span>".$avatar."</span></div>";
  
		}
		return $avatar;
	  }
}
?>
