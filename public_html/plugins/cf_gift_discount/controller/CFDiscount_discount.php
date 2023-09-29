<?php
class CFDiscount_discount
{
	var $mysqli;
	var $dbpref;
	var $load;
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

		$this->ip=getIP();
	}
	/*
	Add a Discount
	*/ 
	function createDiscount( $data ){
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$giftClass = $this->load->load('giftcard'); 
		$table = $pref."gift_cards";
		$table1 = $pref."issue_gift";
		$input=[];
		$filter_data=['discount_id','savediscounts','action'];
		$user_id=$_SESSION['user' . get_option('site_token')]; 
		$access=$_SESSION['access' . get_option('site_token')]; 
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

		$input['updated_at'] = date("Y-m-d H:i:s",time());
		
		// first check if gift_code already availabe or not
		if(  $data['savediscounts'] == "save" )
        {
            $input['created_at'] = date("Y-m-d H:i:s",time());
            $discount_code = $input['gift_code'];
            $input['gift_code'] = strip_tags($input['gift_code']);
            if( empty( $data['gift_code'] ) )
            {
                return json_encode(array('status'=>0,'message'=>t('Please enter unique discount codes')));
            }
			if($access=='admin')
            $check_gift = "SELECT `id` FROM `".$table."` WHERE `gift_code`='$discount_code' AND `discount_type`='percentage'";
			else
			$check_gift = "SELECT `id` FROM `".$table."` WHERE `gift_code`='$discount_code' AND `discount_type`='percentage' AND `user_id`=$user_id";

            $check_gift_code = $mysqli->query( $check_gift );
            if( $check_gift_code->num_rows <= 0 )
            {
				
                $in_indexes= implode( ',', array_map( function( $pp ) { return "`".$pp."`"; }, array_keys( $input ) ) );
                $in_values= implode( ',', array_map( function( $val ) { return '"'.$val.'"';}, array_values( $input ) ) );
                $in=$mysqli->query("INSERT INTO `".$table."` (".$in_indexes.",`user_id`) VALUES (".$in_values.",$user_id)");
                if( $in )
                {
                    $lastid=$mysqli->insert_id;
                    $comment='You issued a discount code.';
                    $mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`,`user_id`) VALUES ('".$lastid."','".$comment."','".$date_created."',".$user_id.")");
					// if customer added in gift card then send a gift email
					if( !empty($input['member_id']) && $input['member_id'] != -1 )
					{
						$members = get_member($input['member_id']);
						$email = $members['email'];
						$name = $members['name'];
						$percentage = $input['percentage'];
						$email_data= $giftClass->replaceSubject( "USD", '10', 'discount', $name, $email, $discount_code, $percentage);
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
						
						$mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`,`user_id`) VALUES ($lastid,'".$comment1."','".$date_created."',".$user_id.")");
					}
                    return json_encode(array('status'=>1,'message'=>t('Discount added successfully'),'last_id'=>$lastid));
                }else{
                    return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
                }
            }else{
                return json_encode(array('status'=>0,'message'=>t('The discount code is already taken. Please add a unique discount code.')));
            }
        }
        else if( $data['savediscounts'] == "update" )
        {
            $discount_id = $mysqli->real_escape_string($data['discount_id']);
            $gift = $this->load->load('giftcard');
            $in_index = $gift->updateQuery( $input );
            $ins=$mysqli->query("UPDATE  `".$table."` SET ".$in_index." WHERE `id`=$discount_id")?1:-1;
            if( $ins )
            {
                $lastid=$discount_id;
                $comment='You updated the discount code.';
                $mysqli->query("INSERT INTO `".$table1."` (`giftcard_id`, `comment`, `created_at`,`user_id`) VALUES ('".$lastid."','".$comment."','".$date_created."',".$user_id.")");

                return json_encode(array('status'=>1,'message'=>t('Discount updated successfully'),'last_id'=>$lastid));
            }else{
                return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page')));
            }
        }
	}
	/*update discount*/

	/*
	Settings 
	*/
	function settings( $data )
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table = $pref."gift_cards_settings";
		$filter_data=['action','type'];
		$giftcard=[];
		$discount=[];
		$gemail_subject =   $mysqli->real_escape_string( $data['gemail_subject'] );
		$gemail_content =   $mysqli->real_escape_string( $data['gemail_content'] );
		$demail_subject =    $mysqli->real_escape_string( $data['demail_subject'] );
		$demail_content =   $mysqli->real_escape_string( $data['demail_content'] );
		$user_id=$_SESSION['user' . get_option('site_token')]; 
		$access=$_SESSION['access' . get_option('site_token')]; 
		foreach( $data['giftcard'] as $index => $dat  )
		{
            if( !in_array( $index, $filter_data ) )
			{
                $giftcard[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
			}
		}
		foreach( $data['discount'] as $index => $dat  )
		{
            if( !in_array( $index, $filter_data ) )
			{
                $discount[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
			}
		}
		if(empty(trim($data['gemail_content'])) )
		{
			$gemail_c="<p>Hi {name},</p><p>Your  {initial_value} {currency} gift card is active. Keep this email or write down your gift card number.</p><p><strong>{giftcode}</strong></p><p>If you did not raise the request please write to our support team.</p><p>Thanks</p>";
			$gemail_content=$mysqli->real_escape_string( $gemail_c );
		}
		if(empty(trim($data['demail_content'])))
		{
    		$demail_c="<p>Hi {name},</p><p>Your  {percentage} discount code is active. Keep this email or write down your discount code number.</p><p><strong>{discount}</strong></p><p>If you did not raise the request please write to our support team.</p><p>Thanks</p>";
			$demail_content = $mysqli->real_escape_string( $demail_c );
		}

		$gift = $mysqli->real_escape_string( json_encode( $giftcard ) );
		$dis = $mysqli->real_escape_string( json_encode( $discount ) );
		if($access=='admin')
		$teqt_sql="SELECT * FROM `$table`";
		else
		$teqt_sql="SELECT * FROM `$table` WHERE `user_id`=$user_id";

		$pr = $mysqli->query($teqt_sql);
		if( $pr->num_rows > 0 )
		{
			$mysqli->query("DELETE  FROM `$table`");
		}
		$mysqli->query("INSERT INTO `$table` (`giftcard`,`discount`,`gemail_subject`,`gemail_content`,`demail_subject`,`demail_content`,`user_id`)VALUE('".$gift."','".$dis."','".$gemail_subject."','".$gemail_content."','".$demail_subject."','".$demail_content."',".$user_id.")");
		if( $mysqli->affected_rows > 0 )
		{
			return json_encode(array('status'=>1,'message'=>t('Saved changes')));
		}else{
			return json_encode(array('status'=>0,'message'=>t('There is something wrong please refresh the page.')));
		}
	} 
	function getSettings( )
	{
		global $mysqli;
		$pref=$this->dbpref;
		$user_id=$_SESSION['user' . get_option('site_token')]; 
		$access=$_SESSION['access' . get_option('site_token')]; 
		$table = $pref."gift_cards_settings";
		if($access=='admin')
		$teqt_sql="SELECT * FROM `$table`";
		else
		$teqt_sql="SELECT * FROM `$table` WHERE `user_id`=$user_id";
		$row = $mysqli->query($teqt_sql;
		if( $row->num_rows > 0 )
		{
			$r = $row->fetch_assoc();
			return $r;
		}
		return false;


	}
	

}
?>
