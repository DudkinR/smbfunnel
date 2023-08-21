<?php
class SecureClass
{
	var $site_token;
	public function __construct()
	{
		if(function_exists('get_option'))
		{
		if(get_option('site_token'))
		{
		$this->site_token=get_option('site_token');
		}
		else
		{
			$this->site_token="";
		}
		}
		else
		{
			$this->site_token="";
		}
	}
	public function setToken($slug=false)
	{
		//csrf token create and validate
			$s="";
			if($slug)
			{
				$s=$slug;
			}
			$_SESSION['csrf_token'.$s.$this->site_token]=substr(str_shuffle('1234567890zxcvbnmasdfghjkqwertyuiop'),0,5);
			return $_SESSION['csrf_token'.$s.$this->site_token];
	}
	public function matchToken($token,$slug=false)
	{
		$s="";
		if($slug)
		{
			$s=$slug;
		}
		if(isset($_SESSION['csrf_token'.$s.$this->site_token]))
		{
		if($_SESSION['csrf_token'.$s.$this->site_token]==$token)
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
			return 0;
		}
	}
	public function manageRate($set=1)
	{
		//1 create 2check 0 destroy
		if(!isset($_SESSION['authrate'.$this->site_token]) && $set==1)
		{
			$_SESSION['authrate'.$this->site_token]=0;
			$_SESSION['timeauthrate'.$this->site_token]=time();
		}
		elseif($set==2)
		{
			++$_SESSION['authrate'.$this->site_token];
			$currenttime=time();
			$diff=$currenttime-$_SESSION['timeauthrate'.$this->site_token];			
			if(($_SESSION['authrate'.$this->site_token]>=10)&&($diff<120))
			{
				if($diff>=120)
				{
				self::manageRate(0);	
				}
				return 0;
			}
			else
			{
				if($diff>=120)
				{
				self::manageRate(0);	
				}
				return 1;
			}
		}
		else if($set==0)
		{
			if(isset($_SESSION['authrate'.$this->site_token]))
			{
				unset($_SESSION['authrate'.$this->site_token]);
				unset($_SESSION['timeauthrate'.$this->site_token]);
			}
		}
	}
	function isSecurePassword($str)
	{
		$regex=base64_decode(get_option('secure_password_regex'));
		if(preg_match("/".$regex."/",$str))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}

?>