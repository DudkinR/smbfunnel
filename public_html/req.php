<?php
session_start();
set_time_limit(0);
$current_base_dir=str_replace("\\","/",__DIR__);
require_once($current_base_dir."/library/esc_html.php");

if(isset($_GET["cfhttp"]))
{
	foreach($_GET as $cfhttp_data_index=>$cfhttp_data_val)
	{
		$_GET[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
		$_REQUEST[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
	}
}
if(isset($_POST["cfhttp"]))
{
	foreach($_POST as $cfhttp_data_index=>$cfhttp_data_val)
	{
		$_POST[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
		$_REQUEST[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
	}
}

require($current_base_dir."/gcp/gcp.php");

require_once($current_base_dir."/library/library.php");
$load=new Library();
$cf_product_code="mailenginepro";
if(is_file($GLOBALS["config_file"]))
{

require_once($GLOBALS["config_file"]);
require_once($current_base_dir."/library/options.php");


$load->setInfo('mysqli',$mysqli);
$load->setInfo('dbpref',$dbpref);
$load->setInfo('base_dir',str_replace("\\","/",__DIR__));
$userobforcheck=$load->loadUser();
}
$csrf_msg="Session timeout please refresh page and try again";

$security=$load->secure();


if(function_exists('get_option'))
{
$main_load=$load;
require_once($current_base_dir."/library/plugin_options.php");
}

//-------------config file generation-------------
if(isset($_POST['createconfig']))
{
//config ajax
if((isset($_POST['token'])&& $security->matchToken($_POST['token']))|| isset($importer_connected))
{
if(!is_file($GLOBALS["config_file"]))
{
$host=$_POST['host'];
$user=$_POST['user'];
$pass=$_POST['pass'];
$pref=$_POST['pref'];
$port=$_POST['port'];
$db=$_POST['dbname'];

require_once($current_base_dir.'/assets/install/table.php');
if(is_numeric($port))
{
$con=new mysqli($host,$user,$pass,$db,$port);
}
else
{
$con=new mysqli($host,$user,$pass,$db);
}
if(mysqli_connect_errno()>0){die('Unable to connect db');}

//echo createTable($con,$pref);

if(createTable($con,$pref)===1)
{
$fp=fopen($GLOBALS["config_file"],'w');

if(strlen($port)>0 && is_numeric($port))
{
$port=",".$port;
}
else
{
$port="";
}
//for normal hosting
$str="<?php 
		    \$dbpref='".$pref."';
			\$mysqli= new mysqli('".$host."','".$user."','".$pass."','".$db."'".$port.");
			if(mysqli_connect_errno()>0)
			{
				echo 'Unable to connect db';
				die();
			}
			require_once('library/options.php');
		 ?>";

fwrite($fp,$str);
fclose($fp);
echo 1;
}
else
{
echo "Unable to create tables.";
}
}
}
else
{
echo $csrf_msg;
}
}
//---------csrf match------------------
if(isset($_POST['checkcsrf']))
{
//csrf create request
if($_POST['checkcsrf']=='create')
{
$token_data= $security->setToken();
echo $token_data;
}
elseif($_POST['checkcsrf']=='match')
{
if($security->matchToken($_POST['token'])){echo 1;}else{echo 0;}
}
}
//------------------create user----------------------
if(isset($_POST['createuser']))
{
if((isset($_POST['token']) && $security->matchToken($_POST['token']))|| isset($importer_connected))
{
$userob=$load->loadUser();
$register=$userob->register();
if($register==1)
{
//add ipn token in option
$default_smtp_to_store='php';
add_option('default_smtp',$default_smtp_to_store);
add_option('spin_email','1');
add_option('members_fpwd_mail','Reset Password@fpwdemlbrk@Hi, Your One Time Password Reset Link Is <a
    href="{link}">{link}</a>');


if(!get_option('ipn_token'))
{
$ipntoken=time();
$ipntoken .=substr(str_shuffle('1235467890qwertyuiopASDFGHJKLzxcvbnm'),0,5);
add_option('ipn_token',$ipntoken);
}
//add warrior plus secrets in option
if(!get_option('site_token'))
{
$sitetoken=time();
// $sitetoken .=str_replace(".","",$_SERVER['http_host']);
$sitetoken .=substr(str_shuffle('1235467890qwertyuiopASDFGHJKLzxcvbnm'),0,5);
add_option('site_token',$sitetoken);
}
if(!get_option('cookie_token'))
{
add_option('cookie_token',time());
}
//add installation url or base url in option
if(!get_option('install_url'))
{

$protocol=$load->getProtocol();
$installurl=$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$installurl=substr($installurl,0,(strpos($installurl,"req.php")-1));
add_option('install_url',$installurl);
}
if(!get_option('secure_password_regex'))
{
add_option('secure_password_regex',base64_encode('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$'));
}
if(!get_option('not_secure_password_alert'))
{
add_option("not_secure_password_alert","Please insert password with a minimum length of eight and combination of upper
and lowercase characters, numbers and special characters");
}
if(!get_option('fpwd_auth_error'))
{
add_option('fpwd_auth_error',"Unable to Authorize, Please {link}Try Again{/link}");
}
//pwd_mismatch_err
if(!get_option('pwd_mismatch_err'))
{
add_option('pwd_mismatch_err','Pasword Did Not Match');
}

if(!get_option('re_register_err'))
{
add_option('re_register_err','You are already an user');
}

if(!get_option('invalid_email_err'))
{
add_option('invalid_email_err','Invalid Email Entered');
}
if(!get_option('already_email_err'))
{
add_option('already_email_err','A user with this email id already available');
}

if(!get_option('un_auth_access_err'))
{
add_option('un_auth_access_err','Unable To Authorize The URL, Please Try Again');
}

if(!get_option('usr_does_not_exist_err'))
{
add_option('usr_does_not_exist_err','You are not an user, Please register');
}

if(!get_option('invalid_login_credntials_err'))
{
add_option('invalid_login_credntials_err','Invalid Credentials Provided');
}
if(!get_option('snd_email_err'))
{
add_option('snd_email_err','Unable To Send The Email Please Contact Admin');
}
if(!get_option('qfnl_current_version'))
{
global $current_app_version;
add_option('qfnl_current_version',$current_app_version);
}
if(!get_option('qfnl_cancel_membership_withsales'))
{
add_option('qfnl_cancel_membership_withsales','1');
}
if(!get_option('qfnl_membership_cancelation_message'))
{
add_option('qfnl_membership_cancelation_message','Your access was canceled, contact admin to activate again.');
}
if(!get_option('qfnl_max_records_per_page'))
{
add_option('qfnl_max_records_per_page','10');
}
if(!get_option('qfnl_router_mode'))
{
add_option('qfnl_router_mode','1');
modifyHtaccess("create",__DIR__);
}
if(!get_option('default_404_page_template'))
{
add_option('default_404_page_template','1');
}
if(!get_option('default_404_page_url'))
{
add_option('default_404_page_url',get_option('install_url')."/cf-admin");
}
if(!get_option('default_404_page_button_text'))
{
add_option('default_404_page_button_text','Go To Home');
}
if(!get_option('default_404_page_logo'))
{
add_option('default_404_page_logo',get_option('install_url')."/assets/img/404-logo.png");
}
if(!get_option('zapier_token'))
{
add_option('zapier_token',str_shuffle(get_option('site_token')));
}
if(!get_option('qfnl_max_countable_rows'))
{
add_option('qfnl_max_countable_rows',0);
}
if(!get_option('temp_filename_template'))
{
add_option('temp_filename_template','temp');
}
if(!get_option('force_https_funnels_pages'))
{
add_option('force_https_funnels_pages','0');
}
if(!get_option('app_language'))
{
add_option('app_language',$_POST['app_language']);
}
if(!get_option('cod_store_message'))
{add_option('cod_store_message', 'You need to verify your email address for purchasing the listed products');}
if(!get_option('cod_store_name'))
{add_option('cod_store_name', 'Cash On Delivery');}
if(!get_option('cod_otp_email_title'))
{add_option('cod_otp_email_title', 'OTP for product confirmation');}
if(!get_option('cod_otp_email_content'))
{add_option('cod_otp_email_content', '<p>Hello,</p>
<p>Please enter the below OTP code to complete Verification.</p>
<p><strong>{otp}</strong></p>
<p>This code is valid for the next 10 minutes.</p>
<p>If you did not raise the request please write to our support team.</p>');}
if(!get_option('free_singn_email_title'))
{add_option('free_singn_email_title', 'Member registration email');}
if(!get_option('free_singn_email_content'))
{add_option('free_singn_email_content', '<p>Hi,</p><p>I hope you’re having a great week.</p> 
<p>We have one registration to your {funnel} funnel</p>
<p>Name:  {name}</p>
<p>Email: {email}</p>
<p>Thanks</p>');}
echo 1;
}
elseif($register==2)
{
echo "Already an user";
}
else
{
echo "Unable to register";
}
}
else{echo $csrf_msg;}
}
//----------admin login-------------
if(isset($_POST['admin_login']))
{
$security->manageRate(1);
if($security->manageRate(2))
{
if($security->matchToken($_POST['token']))
{
$userob=$load->loadUser();
$login=$userob->adminLogin($_POST['email'],$_POST['pass']);
if($login){
$security->manageRate(0);

$temp_site_token=get_option('site_token');

if(!isset($_SESSION['last_visited_page'.$temp_site_token]))
{
$_SESSION['last_visited_page'.$temp_site_token]=$_SESSION['first_page'.$temp_site_token];
}
$arr=array('status'=>1,'redirect'=>$_SESSION['last_visited_page'.$temp_site_token]);
echo json_encode($arr);
}
else
{echo 0;}
}
else
{
echo "Please try again after refreshing the page";
}
}
else
{
echo "Please try again after refreshing the page";
}
}
//----Forgot password-----
if(isset($_POST['admin_forgot_password']))
{
if(!$security->matchToken($_POST['token']))
{
die ('Something wrong, Please refresh the page and try again');
}
$user_ob=$load->loadUser();

if(isset($_POST['email']))
{
$mail=$user_ob->forgotPassOtpGeneration($_POST['email']);
if($mail==1)
{
$_SESSION['fpwd_step_done'.get_option('site_token')]=1;
echo 1;
}
elseif($mail===0)
{
echo "Invalid Email or User Doesn't Exist";
}
else
{
echo $mail;
}
}
elseif(isset($_POST['otp']))
{
if(isset($_SESSION['fpwd_step_done'.get_option('site_token')])){
if($_SESSION['fpwd_step_done'.get_option('site_token')]!=1){die("Unauthorized Access, please try
again.");}}else{die("Unauthorized Access, try again.");}
$otp=$user_ob->fpwdOTPVerification($_POST['otp']);
if($otp==1){$_SESSION['fpwd_step_done'.get_option('site_token')]=2;echo 1;}
elseif($otp==2){echo "Unauthorized attempt, Please Try Again";}
elseif($otp==0){echo "OTP did not match";}
}
elseif(isset($_POST['pass']))
{
if(isset($_SESSION['fpwd_step_done'.get_option('site_token')])){if($_SESSION['fpwd_step_done'.get_option('site_token')]!=2){die("Unauthorized
Access, please try again.");}}else{die("Unauthorized Access, try again.");}
$addpass=$user_ob->saveNewPass($_POST['pass']);
if($addpass==1){unset($_SESSION['fpwd_step_done'.get_option('site_token')]);echo 1;}
elseif($addpass==2||$addpass==0){echo "Please Refresh The Page and Try Again.";}
}
}

//@@@----From Here For Logged In Users----@@@

//------------create funnel---------------------
if(isset($_POST['createfunnel']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$create=$funnel->createFunnel($_POST['funnel_url'],$_POST['funnel_name'],$_POST['funnel_type'],$_POST['modify_index']);
echo $create;
}
//------------rename funnel---------------------
if(isset($_POST['renamefunnels']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$create=$funnel->renameFunnels($_POST);
echo $create;
}
//----------get current funnel ab detail----------------
if(isset($_POST['currentfunnelabdetail']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$data=$funnel->getPageFunnel($_POST['funnel_id'],$_POST['type'],$_POST['label']);

if(is_object($data))
{
$funneldata=$funnel->getFunnel($_POST['funnel_id']);
$member=$load->loadMember();
if($member->isVerifiedMembershipPage($data->id))
{
$data->verified_membership_page=1;
}
else
{
$data->verified_membership_page=0;
}
$data->primarysmtp=$funneldata->primarysmtp;
echo json_encode($data);
}
else
{
echo 0;
}
}
//-------save funnel template data data
if(isset($_POST['savetemplate']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();

$_POST['html']=str_replace('
<link rel="stylesheet" href="'.get_option('install_url').'/assets/fontawesome/css/all.css" />',"",$_POST['html']);
$_POST['html']=str_replace('
<link rel="stylesheet" href="'.get_option('install_url').'/assets/fontawesome/css/all.css">',"",$_POST['html']);

//$_POST['html']=$funnel->createQuoteForCSSUrls($_POST['html']);

$arr=array('html'=>$_POST['html'],'css'=>$_POST['css'],'js'=>$_POST['js'],'fontlink'=>$_POST['fontlink']);
echo $funnel->saveEditorData($_POST['funnel_id'],$_POST['type'],$_POST['lbl'],$_POST['category'],$arr,$_POST['folder'],$_POST['folder']);
$funnel->updatePageFunnelSettings($_POST['funnel_id'],$_POST['lbl'],json_encode(array('page_folder'=>$_POST['folder'])),1);
}
//Save editor block
if(isset($_POST['editor_block']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}

$funnel=$load->loadFunnel();
if(isset($_POST['get']))
{
$mini_blocks= array();
$blocks= $funnel->getBlocks();
echo json_encode($blocks);
}
else if(isset($_POST['set']))
{
$funnel->addTemplateBlock($_POST['block']);
echo 1;
}
else if(isset($_POST['delete']))
{
$funnel->delTemplateBlock($_POST['block_id']);
echo 1;
}
}
//----------take screenshot
if(isset($_POST['take_funnel_screenshot']))
{
//print_r($_POST);
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$funnel->saveEditorData($_POST['funnel'],$_POST['abtype'],$_POST['lavel'],$_POST['category'],array(),$_POST['page'],2);
}
//---------change label
if(isset($_POST['chnglbl']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$lbl_chng_stat= 0;
if($_POST['lbls']=='html')
{
$lbl_chng_stat= $funnel->changeLabel($_POST['funnel_id'],array(),$_POST['lblhtml']);
}
else
{
$lbls=json_decode($_POST['lbls']);
$lbl_chng_stat= $funnel->changeLabel($_POST['funnel_id'],$lbls,$_POST['lblhtml']);
}
echo $lbl_chng_stat;
}
//-------delete label
if(isset($_POST['dellbl']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$funnel->delLabel($_POST['funnelid'],$_POST['label']);
}
if(isset($_POST['copylbl']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$funnel->copyLabel($_POST['funnelid'],$_POST['label']);
}
//----------save funnel setting
if(isset($_POST['update_funnel_setting']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$u=$funnel->updatePageFunnelSettings($_POST['funnel_id'],$_POST['label'],$_POST['data']);
echo $u;
die();
}
//-------save image at destination grapes js

if(isset($_POST['imgstore']) && isset($_FILES['files']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$img_data=$funnel->uploadAssets($_FILES['files'],$_POST['upload_location'],$_POST['img_base_url'],'image');
echo json_encode($img_data);
}
//-----------templates load--------------
if(isset($_POST['load_templates']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();
$search="";
if(isset($_POST['search_template']))
{
$search=$_POST['search_template'];
}
echo $funnel->showTemplates($_POST['type'],$_POST['abtype'],$search);
}
//------------save template---------------
if(isset($_POST['save_template']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}

$token=time();
update_option('temp_filename_template','temp'.$token);

$funnel=$load->loadFunnel();
$funnel->installTemplate($_POST['template_id']);
echo
$funnel->installTemplate($_POST['template_id'],"save",array('funnel_id'=>$_POST['funnel_id'],'type'=>$_POST['ab_type'],'lavel'=>$_POST['lavel'],'category'=>$_POST['category'],'page'=>$_POST['page']));

$token=time();
update_option('temp_filename_template','temp'.$token);
}
//-------------load template Image-----------------


if(isset($_GET['templatedata_load']))
{
if(get_option('google_screenshot')!==null)
{
if(get_option('google_screenshot')==$_GET['gscrennshotid'])
{
if(isset($_GET['load']))
{
$funnelob=$load->loadFunnel();
$content=$funnelob->readContent($_GET['fid'],$_GET['lbl'],$_GET['abtype']);
$html="";$css="";
if(strlen($content['html'])>0)
{
$html=$content['html'];
}
if($content['css'])
{
$css=$content['css'];
}
$content=$html.$css;
if(strlen($content)>0)
{
echo "<html>

<head></head>

<body>".$html."<style>
    ".$css."
    </style>
</body>

</html>";
}
else
{
echo 0;
}
}
}
}

}
//get template data
if(isset($_POST['loadalltemplatedata']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnelob=$load->loadFunnel();
$content=$funnelob->readContent($_POST['fid'],$_POST['lbl'],$_POST['abtype']);
$editor_html='
<link rel="stylesheet" href="'.get_option('install_url').'/assets/fontawesome/css/all.css" />'.$content['html'];

$content['css']=str_replace("-ms-","",$content['css']);

echo
json_encode(array('html'=>$content['html'],'css'=>$content['css'],'js'=>$content['js'],'editor_html'=>$editor_html));
}
if(isset($_GET['loadalltemplatedata_get']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnelob=$load->loadFunnel();
$content=$funnelob->readContent($_GET['fid'],$_GET['lbl'],$_GET['abtype']);
$editor_fs='
<link rel="stylesheet" href="'.get_option('install_url').'/assets/fontawesome/css/all.css" />';

$content['css']=str_replace("-ms-","",$content['css']);

$bootstrap=$load->loadBootstrap();
$timer_script=$funnelob->addCoundownTimerScript('',true);
//echo json_encode(array('html'=>$content['html'],'css'=>$content['css'],'js'=>$content['js'],'editor_html'=>$editor_html));
echo "<html>

<head>
    ".$bootstrap."
    <script cfdefaultscript='1'>
    ".$content['js']."
    </script>
    ".$timer_script."
    <style>
    ".$content['css']."
    </style>
    ".$editor_fs."
</head>

<body>
    ".$content['html']."
</body>

</html>";
}


//search result for members
if(isset($_POST['searchmember']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
}
if(isset($_POST['searchsales']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
}
//update member data
if(isset($_POST["updatememberdata"]))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$member=$load->loadMember();
$exf=$_POST;
unset($exf["name"]);unset($exf["email"]);unset($exf["password"]);unset($exf["updatememberdata"]);unset($exf["funnelid"]);unset($exf["userid"]);
echo
$member->createMember($_POST['funnelid'],0,$_POST['name'],$_POST['email'],$_POST['password'],$exf,"",0,$_POST['userid']);
}
//Create list
if (isset($_POST['createlist'])) {
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$lists = $load->createlist();
$createlist = $lists->saveList();
if ($createlist != 0) {
echo $createlist;
}
else{
echo 0;
}
}
//create update product
if(isset($_POST['createsaveproduct']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$product=$load->loadSell();
echo
$product->createProduct($_POST['productid'],$_POST['title'],$_POST['description'],$_POST['download_url'],$_POST['url'],$_POST['image'],$_POST['price'],$_POST['currency'],$_POST['sheeping'],$_POST['subproducts'],$_POST['opproducts'],$_POST['tax'],$_POST['doupdate']);
}
//create sequence
if (isset($_POST['sequence'])) {
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$sequence = $load->loadSequence();
$saveSequence = $sequence->createSequence();
if ($saveSequence == 1) {
echo 1;
}
else{
echo 0;
}
}
//create sequence
if (isset($_POST['new_sequence'])) {
	if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
	$sequence = $load->loadSequence();
	echo $sequence->createNewSequence();
	die();
	}
//image upload
if(isset($_POST['tinymceimgupload']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
if (isset($_FILES)) {

$imageFolder = "assets/img/mails/";
reset ($_FILES);
$temp = current($_FILES);
if (is_uploaded_file($temp['tmp_name'])){

if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
header("HTTP/1.1 400 Invalid file name.");
return;
}

if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
header("HTTP/1.1 400 Invalid extension.");
return;
}
$temp['name']=str_replace(" ","_",$temp['name']);
$newimagenameandextension=time().$temp['name'];
$filetowrite = $imageFolder .$newimagenameandextension;
move_uploaded_file($temp['tmp_name'], $filetowrite);

$url=get_option('install_url');
$url .="/".$filetowrite;

echo json_encode(array('location' => $url));
} else {
header("HTTP/1.1 500 Server Error");
}
}
}
if (isset($_POST['payment'])) {
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$payment = $load->loadPayment();
$savePayment = $payment->savePaymentData();
if ($savePayment == 1) {
echo 1;
}
else{
echo 0;
}
}
if(isset($_POST['viewpurchasedetail']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$sellob=$load->loadSell();
$data=$sellob->getSale($_POST['viewpurchasedetail']);
if(!is_object($data))
{
echo 0;
}
else
{
$paymenttitle_ob=$sellob->getPaymentMethodDetail($data->paymentmethod);
$paymenttitle="";
if(is_object($paymenttitle_ob))
{
$paymenttitle=$paymenttitle_ob->title;
}

if(strpos(trim($data->shippingdetail),"{")==0)
{
//\&quot;

$data->shippingdetail=str_replace("\&quot;","'",$data->shippingdetail);

$selqectedshippingdetail=json_decode($data->shippingdetail);

$err=json_last_error();

if(isset($selqectedshippingdetail->optional_products))
{
unset($selqectedshippingdetail->optional_products);
}
$data->shippingdetail=json_encode($selqectedshippingdetail);
}
$cod_data="{}";
if($data->cod_data)
{
$cod_data=$data->cod_data;
unset($cod_data->id);
unset($cod_data->sell_id);

$cod_data->updated_on=date('d-M-y h:ia');
if(!$cod_data->status)
{
$cod_data->signed_by='N/A';
$cod_data->last_ip='N/A';
$cod_data->updated_on='N/A';
}
else
{
$user_ob=$load->loadUser();
$user=$user_ob->getUser($cod_data->signed_by);
if($user)
{
$cod_data->signed_by="<a href='index.php?page=createmultiuser&id=".$user->id."' target='_BLANK'>".$user->name."</a>";
}
else
{
$cod_data->signed_by="N/A";
}
}
$cod_data=json_encode($cod_data);
}
echo $data->shippingdetail."@sbreak@"."<a href='index.php?page=payment_methods&payid=".$data->paymentmethod."'
    target='_BLANK'>".$paymenttitle."</a>@sbreak@".$data->paymentdata."@sbreak@"."<button data-bs-toggle='collapse'
    data-bs-target='#viewdetailedpayment' class='btn btn-info btn-block' style='border:0px;'><span
        style='float:left;'><strong>Total Paid: ".$data->total_paid."</strong></span><span style='float:right;'><i
            class='fas fa-info-circle'></i> View Detail</span></button>
<div id='viewdetailedpayment' class='collapse'>".$data->step_payments."</div>@sbreak@".$cod_data;
}
}

if(isset($_POST['viewlistexfdata']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$list_ob=$load->createlist();
echo $list_ob->showExtraData($_POST['viewlistexfdata']);
}
if(isset($_POST['ajxsavetolist']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$list_ob=$load->createlist();
$data=(array)json_decode($_POST['ajxsavetolist']);
$exf=array();
if(strlen($data[2])>2)
{
$exf=(array)json_decode($data[2]);
}

$list_ob->addToList($_POST['listid'],$data[0],$data[1],$exf);
echo 1;
}
if(isset($_POST['qmlrtestsmtp']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$sequence_ob=$load->loadSequence();

echo
$sequence_ob->sendMail($_POST['smtpid'],$_POST['toname'],$_POST['toemail'],$_POST['emailsubject'],$_POST['emailbody'],'',$_POST['debug']);
}
//auto update
if(isset($_POST['checkforqfnl_update']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
if(!isset($_SESSION['qfnl_install_later'.get_option('site_token')]))
{
$do=$_POST['checkforqfnl_update'];
$autoupdater=$load->loadAutoUpdater();
if($do=="check")
{
echo $autoupdater->checkForUpdate();
}
elseif($do=="download")
{
echo
$autoupdater->checkForUpdate(base64_decode($_POST['checkforqfnl_update_url']),$_POST['checkforqfnl_update_version']);
}
elseif($do=="install")
{
echo $autoupdater->doUpdate($_POST['checkforqfnl_update_version']);
}
elseif($do=="install_dependency")
{
global $current_app_version;
echo $autoupdater->installDependecies($current_app_version);
}
elseif($do=="install_later")
{
$_SESSION['qfnl_install_later'.get_option('site_token')]=1;
echo 1;
}
}
}
//integrations
if(isset($_POST['saveintegration']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$int_ob=$load->loadIntegrations();
// if($_POST['saveintegration']>0)
// {
// echo
// $int_ob->storeIntegrations($_POST['title'],$_POST['type'],$_POST['data'],$_POST['position'],"update",$_POST['saveintegration']);
// }
// else
// {
// echo $int_ob->storeIntegrations($_POST['title'],$_POST['type'],$_POST['data'],$_POST['position']);
// }
}
if(isset($_POST['gateintegration']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$int_ob=$load->loadIntegrations();
echo json_encode($int_ob->getData($_POST['gateintegration']));
}
//is product shipped
if(isset($_POST['product_shipping_status']) || isset($_POST['product_valid_status']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$sales_ob=$load->loadSell();

if(isset($_POST['product_shipping_status']))
{
echo $sales_ob->shippedOrNot($_POST['product_shipping_status']);
}
else
{
echo $sales_ob->cancelorConfirmSalesAndMembership($_POST['product_valid_status']);
}
}
if(isset($_POST['chkforauthvalidationpucrhase']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
/*$checktype=0;
if(isset($_POST['auth_valid_user']) && isset($_POST['auth_valid_order_code']))
{
$checktype=1;
}*/
$user_ob=$load->loadUser();
//echo $checktype;
echo $user_ob->userDataIsValid(1);
}
if(isset($_POST['qfnl_current_page_maxdata']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$current_rows=(int)get_option('qfnl_max_countable_rows');
$req_rows=(int)$_POST['qfnl_current_page_maxdata'];
if($req_rows>$current_rows)
{
update_option('qfnl_max_countable_rows',$req_rows);
}
}
if(isset($_POST['qfnlgdprcookieconsent']))
{
if($_POST['type']=='1')
{
setcookie("qfnlcookieicreated".$_POST['qfnlgdprcookieconsent'],"1",time()+(3600*24*365),'/');
}
$gdpr_ob=$load->loadGdpr();
$gdpr_ob->storeCookieConsent($_POST['type'],$_POST['qfnlgdprcookieconsent']);
}
if(isset($_POST["qfnl_clone_site"]))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel=$load->loadFunnel();

ob_start();
$funnel->installTemplate($_POST["qfnl_clone_site"],"save",array('funnel_id'=>$_POST['funnel_id'],'type'=>$_POST['ab_type'],'lavel'=>$_POST['lavel'],'category'=>$_POST['category'],'page'=>$_POST['page']));

$res=ob_get_clean();

echo ($res=='1')? 1:"Unable To Download Site";

$token=time();
update_option('temp_filename_template','temp'.$token);
}
if(isset($_POST['qfnl_arrenge_cloner']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}

$do=trim($_POST['qfnl_arrenge_cloner']);

$cloner=$load->cloneURL();
if($do=="init"||$do=="init_content")
{
$pre_page_content="";

if($do==="init_content")
{
$pre_page_content=$_POST['remote_content'];
}

$stat=$cloner->init($_POST['qfnl_clone_target_url'],$do,$pre_page_content);
$cloner->sessionSite("set",$cloner->jsn_dir);
echo ($stat)? ceil(count($cloner->jsn_dir["temp_images"])/10):0;
}
elseif($do=="download_images")
{
//$cloner->img_upload_path="";
$stat=$cloner->init($_POST['qfnl_clone_target_url'],$do);
$cloner->sessionSite("set",$cloner->jsn_dir);
echo ($stat===false)? 0:$stat;
}

}
if(isset($_POST['compose_cf_mail']) && isset($_POST['type']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$type=$_POST['type'];
$composer=$load->loadMailComposer();
if($type=='init')
{
$data=$composer->init($_POST['title'],$_POST['smtps'],$_POST['lists'],$_POST['custom_emails'],$_POST['sentdata'],$_POST['extra_setup']);
if($data)
{
echo json_encode($data);
}
else
{
echo 0;
}

}
elseif($type=='compose')
{
echo $composer->compose($_POST['compose_data'],$_POST['compose_token']);
}
}
if(isset($_POST['clone_funnel_get_map']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$ob=$load->loadFunnelCloner();
$map=$ob->request($_POST['clone_funnel_get_map']);
if(!$map)
{
$map=0;
}


$temp_arr=json_decode($map);
if(is_array($temp_arr) && count($temp_arr)>0)
{
$funnel_ob=$load->loadFunnel();
$new_category=$temp_arr[0]->funnel_type;
$funnel_ob->initiateFunnelCloner($_POST['current_funnel'],$new_category);
}

echo $map;
}
if(isset($_POST['upload_zipped_template']) && isset($_FILES['template_zip']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$funnel_ob=$load->loadFunnel();
$file=$_FILES['template_zip'];
$data=$funnel_ob->uploadTeamplateZipAndGetURL($file['tmp_name']);

$stat=array("status"=>false,'data'=>'Unknown');

if(filter_var($data,FILTER_VALIDATE_URL))
{
$stat['status']=true;
$stat['data']=$data;
}
else
{
$stat['data']=$data;
}

echo json_encode($stat);
}
//plugins management starts here
if(isset($_POST['manage_plugins']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
//$plugin_loader
//load plugins
$plugin_loader=$GLOBALS['plugin_loader'];
if(isset($_POST['load']))
{
$plugins=$plugin_loader->getPlugins($_POST['load']);
echo json_encode($plugins);
}
//perform activation or deactivation
if(isset($_POST['process_activation']))
{
echo $plugin_loader->processActivation($_POST['plugin_id'],$_POST['process_activation']);
}
//upload zipped plugin
if(isset($_POST['upload_plugin']) && isset($_FILES['plugin_file']))
{
$uploaded_file=$_FILES['plugin_file'];
$temp_dir=$current_base_dir."/public-assets/temp_plugins";
if(cf_dir_exists($temp_dir))
{
cf_rmdir($temp_dir);
}
mkdir($temp_dir);
$plugin_file=$temp_dir."/".$uploaded_file['name'];
move_uploaded_file($uploaded_file['tmp_name'],$plugin_file);
echo $plugin_loader->uploadPlugin($plugin_file);
cf_rmdir($temp_dir);
}
//update plugins
if(isset($_POST['update_plugin']))
{
echo $plugin_loader->remotePluginInstall($_POST['update_plugin'],true,$_POST['plugin_id']);
}
//upload remote plugin
if(isset($_POST['upload_remote_plugin']))
{
/* *Filter* */
$ins_plugin=base64_decode($_POST['upload_remote_plugin']);
$verified_remote_plugin=false;
if(true|| filter_var($ins_plugin,FILTER_VALIDATE_URL))
{
$ins_url_chk=parse_url($ins_plugin);
if(true|| isset($ins_url_chk['host']) && ($ins_url_chk['host']==='cloudfunnels.in' ||
strpos($ins_url_chk['host'],'.cloudfunnels.in') !==false))
{
$verified_remote_plugin=true;
echo $plugin_loader->remotePluginInstall($ins_plugin);
die();
}
}
if(!$verified_remote_plugin){
echo "Could not verify the plugin";
}

}
if(isset($_POST['del_plugin']))
{
echo $plugin_loader->deletePlugin($_POST['del_plugin']);
}
if(isset($_POST['plugin_update_check']))
{
$data=$plugin_loader->checkForUpdate($_POST['plugin_update_check'],$_POST['get_in_detail']);
if(is_array($data))
{
echo json_encode($data);
}
}
}
//--plugin management ends here

//--Media control

//--Media control ends here
if(isset($_POST['manage_media']))
{
if(!$userobforcheck->isLoggedin()){die ('@not-logged-in@');}
$media_ob=$load->loadMedia();
if(isset($_POST['init']))
{
echo json_encode($media_ob->doInitInFrontend());
}
if(isset($_POST['upload']))
{
echo $media_ob->uploadAsset($_FILES['file']);
}
if(isset($_POST['get_assets']))
{
$data= $media_ob->getAssets($_POST['get_assets'], $_POST['page']);
echo json_encode($data);
}
if(isset($_POST['del_asset']))
{
$media_ob->deleteAsset($_POST['del_asset']);
}
if(isset($_POST['save_file_data']))
{
echo $media_ob->updateFileBasicData($_POST['file'], $_POST['title'], $_POST['description']);
}
}
?>