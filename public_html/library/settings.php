<?php
class Settings
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $base_dir;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref']; 
		$this->load=$arr['load'];
		$this->base_dir=$arr['base_dir'];
	}

    function saveSettings()
    {
		foreach($_POST as $index=>$post)
		{
			if($index=="secure_password_regex")
			{
				continue;
			}
			$_POST[$index]=$post;
		}
		$mysqli=$this->mysqli;
		$install_url=rtrim($_POST['install_url'],'/');
		$default_smtp=$_POST['default_smtp'];
		$fpwd_message=$mysqli->real_escape_string($_POST['fpwdemltitle'])."@fpwdemlbrk@";
		$fpwd_message .=$mysqli->real_escape_string($_POST['members_fpwd_mail']);
		$ipntoken=$_POST['ipn_token'];
		$membership_password_regex=base64_encode($_POST['secure_password_regex']);
		$not_secure_password_alert=$mysqli->real_escape_string($_POST['not_secure_password_alert']);
		$fpwd_auth_error=$mysqli->real_escape_string($_POST['fpwd_auth_error']);
		$pwd_mismatch_err=$mysqli->real_escape_string($_POST['pwd_mismatch_err']);
		
		$re_register_err=$mysqli->real_escape_string($_POST['re_register_err']);
		$invalid_email_err=$mysqli->real_escape_string($_POST['invalid_email_err']);
		$already_email_err=$mysqli->real_escape_string($_POST['already_email_err']);
		$un_auth_access_err=$mysqli->real_escape_string($_POST['un_auth_access_err']);
		$usr_does_not_exist_err=$mysqli->real_escape_string($_POST['usr_does_not_exist_err']);
		$invalid_login_credntials_err=$mysqli->real_escape_string($_POST['invalid_login_credntials_err']);
		$snd_email_err=$mysqli->real_escape_string($_POST['snd_email_err']);
		$qfnl_membership_cancelation_message=$mysqli->real_escape_string($_POST['qfnl_membership_cancelation_message']);
		$app_language=$mysqli->real_escape_string($_POST['app_language']);

		$disable_page_preview=(isset($_POST['disable_page_preview']))? 1:0;
		
		$disable_otp_for_cod=(isset($_POST['disable_otp_for_cod']))? 1:0;
		$cod_store_name = $mysqli->real_escape_string($_POST['cod_store_name']);
		$cod_store_image = $mysqli->real_escape_string($_POST['cod_store_image']);
		$cod_store_message = $mysqli->real_escape_string($_POST['cod_store_message']);

		$spin_email=0;
		if(isset($_POST['spin_email']))
		{
			$spin_email=1;
		}

		$qfnl_cancel_membership_withsales=0;
		if(isset($_POST['qfnl_cancel_membership_withsales']))
		{
			$qfnl_cancel_membership_withsales=1;
		}
		$qfnl_free_signup_email=0;
		if(isset($_POST['qfnl_free_signup_email']))
		{
			$qfnl_free_signup_email=1;
		}
		
		if(isset($_POST['qfnl_router_mode']))
		{
			modifyHtaccess("create",$this->base_dir);
			$qfnl_router_mode=1;
		}else{
			modifyHtaccess("delete",$this->base_dir);
			$qfnl_router_mode=0;
		}
		
		$default_404_page_template=$mysqli->real_escape_string($_POST['default_404_page_template']);
		$default_404_page_url=$mysqli->real_escape_string($_POST['default_404_page_url']);
		$default_404_page_button_text=$mysqli->real_escape_string($_POST['default_404_page_button_text']);

		$default_404_page_logo_url=$mysqli->real_escape_string($_POST['hiddeninvalidfileurl']);

		if(isset($_FILES['default_404_page_logo']) && (strlen($_FILES['default_404_page_logo']['name'])>0))
		{
			//print_r($_FILES['default_404_page_logo']);
			$invalidlogo=$_FILES['default_404_page_logo'];
			$invalidlogo_tempname=$invalidlogo['tmp_name'];
			$invalidlogo_name_arr=explode(".",$invalidlogo['name']);
			$invalid_extension_name=$invalidlogo_name_arr[count($invalidlogo_name_arr)-1];


			$dirtomoveuplodaedimage = $this->base_dir."/assets/img/404-logo.".$invalid_extension_name;

			$urlfor404page=get_option('install_url')."/assets/img/404-logo.".$invalid_extension_name;

			move_uploaded_file($invalidlogo_tempname,$dirtomoveuplodaedimage);

			$default_404_page_logo_url=$urlfor404page;
		}

		$force_https_funnels_pages=0;
		if(isset($_POST['force_https_funnels_pages']))
		{
			$force_https_funnels_pages=1;
		}


		$sales_notif_email_to_admin_check=(isset($_POST['sales_notif_email_to_admin_check']))? 1:0;
		
		$sales_notif_email_to_admin=$mysqli->real_escape_string($_POST['sales_notif_email_to_admin']);
		$sales_notif_email_to_admin= explode("\\r\\n", trim($sales_notif_email_to_admin));
		$sales_notif_email_to_admin = implode(",", $sales_notif_email_to_admin);
		$sales_notif_email_products =(isset($_POST['sales_notif_email_product']))? implode(',', $_POST['sales_notif_email_product']):"";
		$sales_notif_email_smtp=$mysqli->real_escape_string($_POST['sales_notif_email_smtp']); 
		$sales_notif_email_products=$mysqli->real_escape_string($sales_notif_email_products);

		$cod_otp_email_title= $mysqli->real_escape_string($_POST['cod_otp_email_title']);
		$free_singn_email_title= $mysqli->real_escape_string($_POST['free_singn_email_title']);

		$cod_otp_email_content= $mysqli->real_escape_string($_POST['cod_otp_email_content']);
		$free_singn_email_content= $mysqli->real_escape_string($_POST['free_singn_email_content']);
		
		
		update_option('install_url',$install_url);
		update_option('default_smtp',$default_smtp);
		update_option('members_fpwd_mail',$fpwd_message);
		update_option('ipn_token',$ipntoken);
		update_option('secure_password_regex',$membership_password_regex);
		update_option('not_secure_password_alert',$not_secure_password_alert);
		update_option('fpwd_auth_error',$fpwd_auth_error);
		update_option('pwd_mismatch_err',$pwd_mismatch_err);
		update_option('re_register_err',$re_register_err);
		update_option('invalid_email_err',$invalid_email_err);
		update_option('already_email_err',$already_email_err);
		update_option('un_auth_access_err',$un_auth_access_err);
		update_option('usr_does_not_exist_err',$usr_does_not_exist_err);
		update_option('invalid_login_credntials_err',$invalid_login_credntials_err);
		update_option('snd_email_err',$snd_email_err);
		update_option('qfnl_membership_cancelation_message',$qfnl_membership_cancelation_message);
		update_option('qfnl_cancel_membership_withsales',$qfnl_cancel_membership_withsales);
		update_option('qfnl_free_signup_email',$qfnl_free_signup_email);
		update_option('qfnl_router_mode',$qfnl_router_mode);
		update_option('default_404_page_template',$default_404_page_template);
		update_option('default_404_page_url',$default_404_page_url);
		update_option('default_404_page_button_text',$default_404_page_button_text);
		update_option('default_404_page_logo',$default_404_page_logo_url);
		update_option('spin_email',$spin_email);
		update_option('force_https_funnels_pages',$force_https_funnels_pages);
		update_option('app_language',$app_language);

		update_option('disable_page_preview',$disable_page_preview);
		update_option('disable_otp_for_cod',$disable_otp_for_cod);

		update_option('cod_store_message',$cod_store_message);
		update_option('cod_store_name',$cod_store_name);
		update_option('cod_store_image',$cod_store_image);

		registerTranslation($app_language);

		update_option('qfnl_setup_token',time());


		update_option('sales_notif_email_to_admin_check',$sales_notif_email_to_admin_check);
		update_option('sales_notif_email_to_admin',$sales_notif_email_to_admin);
		update_option('sales_notif_email_smtp',$sales_notif_email_smtp);
		update_option('sales_notif_email_products',$sales_notif_email_products);

		update_option('cod_otp_email_title', $cod_otp_email_title);
		update_option('cod_otp_email_content', $cod_otp_email_content);
		update_option('free_singn_email_content', $free_singn_email_content);
		update_option('free_singn_email_title', $free_singn_email_title);
    }
}
?>