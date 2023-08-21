<?php
class Userdata
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $site_token;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		if(isset($arr['load']))
		{
			$this->load=$arr['load'];
		}
		if(get_option('site_token'))
		{$this->site_token=get_option('site_token');}
		else
		{
			$temp_sitetoken=time();
			$temp_sitetoken .=substr(str_shuffle('sdfguiqwertyuiosdfghjxcvbnm1234567890'),1,5);
			add_option('site_token',$temp_sitetoken);
			$this->site_token=$temp_sitetoken;
		}
	}
	function currentIP()
	{//get current ip
		$ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
	  return  $ipaddress;
	}
	
	function register()
	{//register user
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$username = $_POST['user-name'];
		$emailid = $_POST['user-email'];
		$password = password_hash($_POST['user-pass'],PASSWORD_DEFAULT);
		$date= time();
		$ip= self::currentIP();
		
		$my_username = $mysqli->real_escape_string($username);
		$my_emailid = $mysqli->real_escape_string($emailid);
		$my_password = $mysqli->real_escape_string($password);
		
		$verifycode=time();
		$verifycode .=substr(str_shuffle('sxdcfgvhj15238sdxcfvgbhj358awsedrft'),0,5);
		$sql="INSERT INTO `".$pref."users` (`name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_lastsignin`, `ip_created`, `date_created`, `date_signin`,`permission`,`profile_picture`) VALUES ('".$my_username."','".$my_emailid."','".$my_password."','','".$verifycode."','','','".$ip."','".$date."','','admin','assets/img/profile/qfnlladdprofile.png')";
		if(! self::getUser($my_emailid))
		{
			if($mysqli->query($sql)){
				$insertedUserId = mysqli_insert_id($mysqli);
                $sql_adm="INSERT INTO `".$pref."administrator`(`id`, `user_id`, `type`) VALUES
                (NULL,'".$insertedUserId."','user')";
                $mysqli->query($sql_adm);
				return 1;
			}
			else
				return 0;
		}
		else
		{
			return 2;
		}
		
	}
	
	function getUser($id)
	{//get user data by id or email
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table="`".$pref."users`";
		$val=$mysqli->real_escape_string($id);
		
		if(is_numeric($val))
		{
			$sql="select * from ".$table." where `id`=".$val."";
		}
		elseif(filter_var($val,FILTER_VALIDATE_EMAIL))
		{
			$sql="select * from ".$table." where `email`='".$val."'";
		}
		else
		{
			return 0;
		}
		$qry=$mysqli->query($sql);
		
		if($qry)
		{
			return $qry->fetch_object();
		}
		else
		{
			return 0;
		}
	}
	function getAllUsers($plugin=false){
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table="`".$pref."users`";
		$qry=$mysqli->query("select * from ".$table." order by id desc");
		
		if($qry)
		{
			if($qry->num_rows>0)
			{
				return $qry->fetch_object();
			}
			else{return 0;}
		}
		else{return 0;}
	}
	function pluginGetAllUsers($arg)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table="`".$pref."users`";
		$arr=array();
		$qry=$mysqli->query("select * from ".$table."".$arg);

		while($r=$qry->fetch_assoc())
		{
			unset($r['password']);
			unset($r['verified']);
			unset($r['verifycode']);
			unset($r['date_verifycodegen']);
			$r['permission']=explode(',',$r['permission']);
			if(strlen($r['profile_picture']))
			{
				$url=get_option('install_url');
				$r['profile_picture']=trim($r['profile_picture'],'/');
				$r['profile_picture']=$url.'/'.$r['profile_picture'];
			}
			array_push($arr,$r);
		}

		return $arr;
	}
	function isLoggedin($regenerate=0)
	{
		lbl:
		if(isset($_SESSION['user'.$this->site_token]))
		{
			if($regenerate==1)
			{
				$user_ob=self::getUser($_SESSION['user'.$this->site_token]);
				return self::adminLogin(base64_encode($user_ob->email),"",1);
			}
			return $_SESSION['user'.$this->site_token];
		}
		elseif(isset($_COOKIE['qfnlreuser'.$this->site_token]))
		{
		    self::adminLogin($_COOKIE['qfnlreuser'.$this->site_token],"",2);
		}
		else
		{
			return 0;
		}
	}
	function hasPermission($page=0)
	{
		//user has permission to this page
		if(!self::isLoggedin()){return 0;}
		$init=0;
		if($page===0)
		{
			if(isset($_GET['page']))
			{
				++$init;
				$page=$_GET['page'];
			}
		}
		elseif(strlen($page)>2)
		{
			++$init;
		}
		if($init>0)
		{
			$data=self::getUser($_SESSION['user'.$this->site_token]);
			if($data)
			{
			$arr=explode(',',$data->permission);
			if(in_array($_GET['page'],$arr)||in_array('admin',$arr))
			{
				return 1;
			}
			else
			{
				if(in_array($page, array("no_permission", "logout" )))
				{
					return 1;
				}
				else
				{
				return 0;
				}
			}
			}
		}
		else
		{
			return 1;
		}
	}
	function adminLogin($email,$pass,$auto=0)
	{
		//$auto 0 normal login, 1 autologin, 2cookie user 
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table="`".$pref."users`";
		
		if($auto==1)
		{
			$email=base64_decode($email);
		}
		elseif($auto==2)
		{
			$cookie_query=$mysqli->query("select `email`,`verifycode` from ".$table."");
			$got_cookie_user=false;
			if($cookie_query->num_rows >0)
			{
				while($r=$cookie_query->fetch_object())
				{
					$cookiecode=hash_hmac('sha1',$r->email,$r->verifycode);
					if(base64_encode($cookiecode)==$email)
					{
						$got_cookie_user=true;
						$email=$r->email;
						break;
					}
				}
			}
			if(!$got_cookie_user)
			{
				return 0;
			}
		}
		
		$admin_email=$mysqli->real_escape_string($email); 
        $password=$mysqli->real_escape_string($pass); 
		$sql=$mysqli->query("SELECT `id`,`password`,`verifycode`,`permission`,`name`,`profile_picture` FROM ".$table." WHERE `email`='".$admin_email."' ");
		if($sql->num_rows >0)
		{
		  $data=$sql->fetch_object();
		  $re_vrify=0;
		  lbl:
		  if(password_verify($password, $data->password)||$re_vrify==1)
		  {
			  $_SESSION['user'.$this->site_token]=$data->id;
			  $_SESSION['user_name'.$this->site_token]=$data->name;
			  $_SESSION['user_profile_picture'.$this->site_token]=$data->profile_picture;
			  $_SESSION['user_plan_type'.$this->site_token]=$this->load->isProUser();
			  
			  if(strlen($_SESSION['user_profile_picture'.$this->site_token])<2)
			  {
				  $_SESSION['user_profile_picture'.$this->site_token]="assets/img/profile/qfnlladdprofile.png";
			  }
			  $pages=explode(',',$data->permission);
			  $_SESSION['permission_page_arr'.$this->site_token]=$pages;
			  if(isset($pages[0]))
			  {
				$_SESSION['first_page'.$this->site_token]=$pages[0];
				if($pages[0]=='admin')
				{
				$_SESSION['first_page'.$this->site_token]="dashboard";
				}
			  }
			  
			  
			  if(isset($_POST['remember']) && isset($_POST['admin_login']))
			  {
				  setcookie('qfnlreuser'.$this->site_token,base64_encode( hash_hmac('sha1',$email,$data->verifycode)),time()+86400*14);
			  }
			  elseif(isset($_POST['admin_login']))
			  {
				 setcookie('qfnlreuser'.$this->site_token,'',-1); 
			  }
			  delete_option('qfnlcache_downloadable_template');
			  self::updateLastLoggedInTime($data->id);
			  return $data->id;
		  }
		  elseif($auto==1 || $auto==2)
		  {
			$_SESSION['user'.$this->site_token]=$data->id;
			++$re_vrify;
			goto lbl;
			if($auto==2)
			{
				delete_option('qfnlcache_downloadable_template');
			}
			self::updateLastLoggedInTime($data->id);
			return $data->id;  
		  }
		  else
		  {
		  return 0;
		  }
        }
        return 0;
	}
	function updateLastLoggedInTime($id)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;

		$time=time();
		$ip=self::currentIP();

		$table="`".$pref."users`";

		$id=$mysqli->real_escape_string($id);
		//$qry="update ".$table." set `date_signin`='".$time."',`ip_lastsignin`='".$ip."' where `id`=".$id."";
		$mysqli->query("update ".$table." set `date_signin`='".$time."',`ip_lastsignin`='".$ip."' where `id`=".$id."");
	}
	function forgotPassOtpGeneration($email)
	{
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
		{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table="`".$pref."users`";
		
		$qry=$mysqli->query("SELECT `id`,`name` FROM ".$table."  WHERE `email`='".$email."'");
		if($qry->num_rows>0)
		{
			$r=$qry->fetch_object();
			$_SESSION['fpwdid'.$this->site_token]=$r->id;
			$_SESSION['fpwdotp'.$this->site_token]=substr(str_shuffle('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNMM124567890'),1,10);
			
			$_SESSION['fpwdotpcreatedon'.$this->site_token]=time();
			
			$subject="Password Reset for CloudFunnels";
			
			$body="
			<p>Dear ".$r->name.",</p>
			<p>We have received a request to reset password for CloudFunnels installed in <a href='".get_option('install_url')."'>".get_option('install_url')."</a>.</p>
			<p>Please use the OTP below to reset your password</p>
			<p><strong>".$_SESSION['fpwdotp'.$this->site_token]."</strong></p>
			<p>If you have any queries or feedback you can create a support ticket at <a href='http://teknikforce.com/support'>http://teknikforce.com/support</a>.</p>
			<br>
			<p>Cheers!</p>
			<p>Teknikforce</p>
			";
			
			$sequence_ob=$this->load->loadSequence();
			
			$mail=$sequence_ob->sendMail('api',$r->name,$email,$subject,$body,"");
			
			if(!$mail)
			{
				echo "Unable to send mail";
			}
			else
			{
				return 1;
			}
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
	function fpwdOTPVerification($otp)
	{
		$time=time();
		if(isset($_SESSION['fpwdotpcreatedon'.$this->site_token]))
		{
		if(($_SESSION['fpwdotpcreatedon'.$this->site_token]-$time)<10*60)
		{
		if(isset($_SESSION['fpwdotp'.$this->site_token]))
		{
			if($_SESSION['fpwdotp'.$this->site_token]==$otp)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 2;
		}
		}
		else
		{
			return 2;
		}
		}
	}
	function saveNewPass($pass)
	{
		if(!isset($_SESSION['fpwdid'.$this->site_token]))
		{
			return 2;
		}
		else
		{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table="`".$pref."users`";
		$hash= password_hash($mysqli->real_escape_string($pass), PASSWORD_DEFAULT);

		$verifycode=time();
		$verifycode .=substr(str_shuffle('sxdcfgvhj15238sdxcfvgbhj358awsedrft'),0,5);

        $qry=$mysqli->query("UPDATE ".$table." SET  `password`='".$hash."',`verifycode`='".$verifycode."' WHERE `id`=".$_SESSION['fpwdid'.$this->site_token]."");
		if($qry)
		{
			return 1;
		}
		else
		{
			return 0;
		}
		}
	}
	function userDataIsValid($recheck=0)
	{
		global $cf_product_code;

		if(get_option('cookie_token'))
		{
			$cookie_token=(int)get_option('cookie_token');
			$current_cookie_time=time();
			if(($current_cookie_time-$cookie_token))
			{
				update_option('cookie_token',$current_cookie_time);
			}
		}
		else
		{
			update_option('cookie_token',time());
		}

		if($recheck===0)
		{
			if(get_option('is_valid_user'))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		elseif(get_option('is_valid_user')||isset($_POST['auth_valid_user']))
		{

			$email="";$license_code="";

			if(isset($_POST['auth_valid_user']) && isset($_POST['auth_valid_order_code']))
			{
				$email=trim($_POST['auth_valid_user']);
				$license_code=trim($_POST['auth_valid_order_code']);
				if(strlen($email)<1 || strlen($license_code)<1)
				{
					return "Please Enter All Required Data Properly.";
				}
			}
			elseif(get_option('valid_user_data'))
			{
				$jsn=json_decode(cf_enc(get_option('valid_user_data'),"decrypt"));
				if(isset($jsn->custemail) && isset($jsn->license))
				{
					$email=$jsn->custemail;
					$license_code=$jsn->license;
				}
			}
			$custinfo=array
			(
			"custemail"=>$email,
			'custip'=>$this->load->getInfo('ip'),
			'custdomain'=>get_option('install_url'),
			'license'=>$license_code
			);
			// $apiurl="http://cloudfunnels.in/membership/api/auth_site";
			$apiurl="https://162.0.238.76/membership/api/auth_site";

			$req_count=0;
			reqlbl:
			++$req_count;
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$apiurl);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$custinfo);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                "Host: cloudfunnels.in"
              ));
			$res=curl_exec($ch);
			curl_close($ch);
			$res=json_decode($res);
			if(isset($res->valid) && $res->valid==true)
			{
				if(isset($res->product_permissions))
				{
					$custinfo['product_permissions']=$res->product_permissions;
				}
				if(isset($_POST['auth_valid_user']))
				{
					delete_option('is_valid_user');
					delete_option('valid_user_data');	
					add_option('is_valid_user','1');
					update_option('valid_user_data',cf_enc(json_encode($custinfo)));
					if($res->product_permissions=="pro"){
						$_SESSION['user_plan_type'.$this->site_token]=1;
					}elseif($res->product_permissions=="free"){
						$_SESSION['user_plan_type'.$this->site_token]=2;
					}else{
						$_SESSION['user_plan_type'.$this->site_token]=0;
					}
				}
				if(isset($res->product_permissions))
				{
					update_option('valid_user_data',cf_enc(json_encode($custinfo)));
				}
				return 1;
			}
			else
			{
				if($req_count<2)
				{
				sleep(mt_rand(2,4));
				goto reqlbl;
				}
				delete_option('is_valid_user');
				delete_option('valid_user_data');
				return 0;
			}
		}
	}
}
