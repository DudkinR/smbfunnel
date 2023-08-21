<?php
class Gdprcontrol
{
	var $mysqli;
	var $dbpref;
	var $load;
	public $ip;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref']; 
		$this->load=$arr['load'];
		$this->ip=$arr['ip'];
	}
  
function saveDefaultTexts()
{
	$default_texts=array(
	'cookie'=>'Important<br>This site uses cookies which may contain tracking information about visitors. By continuing to browse this site you agree to our use of cookies.',
	'data_access'=>'<p>Hello,</p><p>Please refer to your request to report all the data that we have about you.</p><p>Enclosed please find all the data that we have on you. </p><p>If you need any more information, please let us know.</p><p>Thanks</p>',
	'data_rectification'=>'<p>Hello,</p><p>Please refer to your data rectification request with us.</p><p>We are pleased to inform you that action was taken on your request and your data has been rectified.</p><p>Please let us know if you need any other help.</p><p>Thanks</p>');
	if(isset($_POST['usedefaultcookietext']))
	{
		update_option('cookie_message',$default_texts['cookie']);
	}
	if(isset($_POST['defdatxt']))
	{
		update_option('gdpr_defalt_dataacsmsg',$default_texts['data_access']);
	}
	if(isset($_POST['defdrrtxt']))
	{
		update_option('gdpr_defalt_datrectsmsg',$default_texts['data_rectification']);
	}
}

function saveSettings()
{
	$cookie_message =$_POST['cookie_message'];
	$cookie_textcolr = $_POST['cookie_textcolr'];
	$cookie_backgroundcolor =$_POST['cookie_backgroundcolor'];
	$cookie_buttontext     = $_POST['cookie_buttontext'];
    $cookie_position         =$_POST['cookie_position'];
    $cookie_style          = $_POST['cookie_style'];
    $cookie_border        = $_POST['cookie_border'];
    $dontaccept_cookie_text =$_POST['dontaccept_cookie_text'];
	$cookie_font_size    =$_POST['cookie_font_size'];

	$gdpr_defalt_datrectsmsg=$_POST['gdpr_defalt_datrectsmsg'];
	$gdpr_defalt_dataacsmsg=$_POST['gdpr_defalt_dataacsmsg'];
	$gdpr_dataacess_Sub=$_POST['gdpr_dataacess_Sub'];
	$gdpr_data_rectifi_sub=$_POST['gdpr_data_rectifi_sub'];

	$sendgdprnotificationmail='0';
	if(isset($_POST['getgdprdatanotice']))
	{
		$sendgdprnotificationmail='1';
	}

	$gdprnotificationemail=$_POST['gdprdataacccesrequestemail'];

		update_option('cookie_message',$cookie_message);
		update_option('cookie_textcolr',$cookie_textcolr);
		update_option('cookie_backgroundcolor',$cookie_backgroundcolor);
		update_option('cookie_buttontext',$cookie_buttontext);
		update_option('cookie_font_size',$cookie_font_size);
		update_option('dontaccept_cookie_text',$dontaccept_cookie_text);
		update_option('cookie_position',$cookie_position);
        update_option('cookie_style',$cookie_style);
		update_option('cookie_border',$cookie_border);
		update_option('gdpr_data_notice',$sendgdprnotificationmail);
		update_option('gdpr_notice_email',$gdprnotificationemail);

		update_option('gdpr_defalt_dataacsmsg',$gdpr_defalt_dataacsmsg);
		update_option('gdpr_defalt_datrectsmsg',$gdpr_defalt_datrectsmsg);
		update_option('gdpr_dataacess_Sub',$gdpr_dataacess_Sub);
		update_option('gdpr_data_rectifi_sub',$gdpr_data_rectifi_sub);

        if(filter_var($_POST['cookie_redirectoionurl'],FILTER_VALIDATE_URL))
		{
		update_option('cookie_redirectoionurl',$_POST['cookie_redirectoionurl']);
		}
		else
		{
			update_option('cookie_redirectoionurl',"");		
		}

		if(isset($_POST['send_email_with_gdpr_confirmation']))
		{
			update_option('send_email_with_gdpr_confirmation','1');
		}
		else
		{
			update_option('send_email_with_gdpr_confirmation','0');
		}

}
function gdprJsScript($identifier)
{
	$install_url=get_option('install_url');
	$requrl=$install_url."/req.php";
	return 'document.onreadystatechange=function()
	{
		if(this.readyState=="complete")
		{
		try
		{
		var idf="'.$identifier.'";
		var cookies=document.cookie;
		if(cookies.indexOf("qfnlcookieicreated"+idf+"=")<0)
		{
			var srvr=new XMLHttpRequest();
			srvr.onreadystatechange=function()
			{
				if(this.readyState==4 && this.status==200)
				{
				setTimeout(function(){
				var doc=document.createElement("span");
				document.body.appendChild(doc);
				doc.innerHTML=srvr.responseText;},500);
				}
			};
			srvr.open("GET","'.get_option('install_url').'?page=api_request&cf_load_cookie="+idf,true);
			srvr.send();
		}
		}catch(err){}
		}
	};
	function qfnlGdprAcceptCookie(type)
	{
			document.getElementsByClassName("qfnlcookiebox")[0].style.display="none";
			var idf="'.$identifier.'";
			var srvr=new XMLHttpRequest();
			srvr.onreadystatechange=function(){
			if(this.readyState==4 && this.status==200)
			{
				if(type !==1)
				{
					window.location=document.getElementById("qfnlgdprredirecturl").value;
				}
			}
			};
			srvr.open("POST","'.$requrl.'",true);
			srvr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			srvr.send("qfnlgdprcookieconsent="+idf+"&type="+type);
	}';
}
function showCookieScript($show,$identifier)
{
	//<script src="'.get_option('install_url').'?page=api_request&cf_cookie='.$identifier.'"></script>
	if(!isset($_COOKIE["qfnlcookieicreated".$identifier]))
	{
	if($show)
	{
		return '<script src="'.get_option('install_url').'?page=api_request&cf_cookie='.$identifier.'"></script>';
	}
	}
}
function displayCookie($show,$identifier)
{
//display cookie notice

$notice="";
$install_url=get_option('install_url');
$requrl=$install_url."/req.php";

if(!isset($_COOKIE["qfnlcookieicreated".$identifier]))
{
if($show)
{	
			$redirection_url=(get_option('cookie_redirectoionurl'))? get_option('cookie_redirectoionurl'): "";

			$dontaccept_cookie_text=(get_option('dontaccept_cookie_text'))?  get_option('dontaccept_cookie_text'):'';

			$dontacceptbutton="";
			if(filter_var($redirection_url,FILTER_VALIDATE_URL))
			{
				$dontacceptbutton='<button class="qfnlcookiedontaccept" onclick="qfnlGdprAcceptCookie(0)">'.$dontaccept_cookie_text.'</button>';
			}
			$cookie_position=(get_option('cookie_position')&& strlen(get_option('cookie_position'))>5)? get_option('cookie_position'): 'bottom:0px;right:0px;';
			$cookiemessage=(get_option('cookie_message'))? get_option('cookie_message'):'';

			$cookie_border=(get_option('cookie_border') && strlen(get_option('cookie_border'))>0)?get_option('cookie_border'):'10px';

			$cookie_background=(get_option('cookie_backgroundcolor'))? get_option('cookie_backgroundcolor'):'white';

			$cookie_textcolor=(get_option('cookie_textcolr'))? get_option('cookie_textcolr'):"";

			$cookie_font_size=(get_option('cookie_font_size'))? get_option('cookie_font_size'): '10px';

			$cookie_style=(get_option('cookie_style'))? get_option('cookie_style'):"";

			$acceptbutton_text=(get_option('cookie_buttontext'))? get_option('cookie_buttontext'): "Accept";

			$notice='
			<div class="qfnlcookiebox">
			<div class="qfnlcookiecontnt">
			'. $cookiemessage.'
			</div>
			<input type="hidden" id="qfnlgdprredirecturl" value="'.$redirection_url.'">
			<div class="qfnlcookiecontrol">
			<button class="qfnlcookieaccept" onclick="qfnlGdprAcceptCookie(1)">'.$acceptbutton_text.'</button>
			'.$dontacceptbutton.'
			</div>
			</div>
			<!--@qfnl-gdpr-style@-->
			<style>
			.qfnlcookiebox
			{
			max-width:35%;
			padding:10px;
			padding-left:5px;
			padding-right:5px;
			border-radius:8px 8px 6px 6px;
			'.$cookie_position.'
			position:fixed;
			box-shadow: -1px 1px 4px 0px rgba(0,0,0,1);
			margin:'.$cookie_border.';
			background-color:#'.$cookie_background.';
			z-index:99999;
			}
			.qfnlcookiebox .qfnlcookiecontnt
			{
				color:#'.$cookie_textcolor.';
				font-size:'.$cookie_font_size.';
			}
			@media(max-width: 800px)
			{
			  .qfnlcookiebox
			  {
			  max-width:100%;
			  width:100%;
			  padding-left:8px;padding-right:8px;
			  border-radius:0px;margin:0px;
			  left:0px;
			  }
			}
			.qfnlcookiebox .qfnlcookiecontrol
			{
				margin-top:15px;
			}
			.qfnlcookiebox .qfnlcookiecontrol button{
				float:right;
				color:white;
				background-color: #004d00;
				margin-left:5px;
				border:0px;
				border-radius:5px;
				padding:6px;
				cursor:pointer;
			}
			.qfnlcookiebox .qfnlcookiecontrol .qfnlcookiedontaccept
			{
				background-color:#ff80b3;
			}
			'.$cookie_style.'
			</style>
			<!--@/qfnl-gdpr-style@-->
			';

	
}
}
return $notice;

}
function storeCookieConsent($type,$identifier)
{
	$mysqli=$this->mysqli;
	$dbpref=$this->dbpref;
	$table=$dbpref."gdpr_datas";
	$identifier=explode("_",$identifier);
	$chk=$mysqli->query("select `id` from `".$table."` where `ip`='".$this->ip."' and `action`='".$type."' and `funnel`='".$identifier[0]."'");
	if($chk->num_rows<1)
	{
	$mysqli->query("insert into `".$table."` (`ip`,`action`,`funnel`,`label`,`type`,`data`,`date`) values('".$this->ip."','".$type."','".$identifier[0]."','".$identifier[1]."','cookie','','".date('d-M-Y h:ia')."')");
	}
}

function getData($type="cookie",$limitstart=0)
{
$mysqli=$this->mysqli;
$pref=$this->dbpref;
$table=$pref."gdpr_datas";
if($limitstart>0)
{
$limitstart=($limitstart*10)-10;
}
$qry=$mysqli->query("select * from `".$table."` where `type` like '%".$type."%' order by `id` desc limit ".$limitstart.",10");
if($qry->num_rows<1)
{
	$qry=0;
}
$total_query=$mysqli->query("select count(`id`) as `countid` from `".$table."` where `type` like '%".$type."%'");
$total=0;
if($r=$total_query->fetch_object())
{
	$total=$r->countid;
}
return array('query'=>$qry,'total'=>$total);
}
function storeDataRequests($name,$email,$comment='',$type='data_access')
{
	if(filter_var($email,FILTER_VALIDATE_EMAIL))
	{
	$mysqli=$this->mysqli;
	$dbpref=$this->dbpref;
	$table=$dbpref."gdpr_datas";

	$name=htmlentities($mysqli->real_escape_string($name));
	$email=htmlentities($mysqli->real_escape_string($email));
	$comment=htmlentities($mysqli->real_escape_string($comment));
	$type=htmlentities($mysqli->real_escape_string($type));

	$loginemail="";
	$status="0";
	$page_url=get_option('install_url');
	$page_url .="/index.php?page=data_requests";
	if(isset($_SESSION['qfnl_gdpr_member'.get_option('site_token')]))
	{
		$loginemail=$_SESSION['qfnl_gdpr_member'.get_option('site_token')]['email'];
		$status=2;
		if($loginemail==$email)
		{
			$status=1;
		}
	}

	$data=json_encode(array('name'=>$name,'email'=>$email,'data'=>$comment,'login_status'=>$status,'login_email'=>$loginemail));

	$refference_funnel_id="";
	$refference_label="";
	if(isset($_SESSION['current_funnel_label'.get_option('site_token')])&& isset($_GET['gdpr_token']))
	{
		$refference_funnel_id=$_SESSION['current_funnel_label'.get_option('site_token')][0];
		$refference_label=$_SESSION['current_funnel_label'.get_option('site_token')][1];

		$pagequery=$mysqli->query("select `filename`,`".$dbpref."quick_funnels`.baseurl as `baseurl` from `".$dbpref."quick_pagefunnel` inner join `".$dbpref."quick_funnels` on `".$dbpref."quick_pagefunnel`.funnelid=`".$dbpref."quick_funnels`.id  where `".$dbpref."quick_pagefunnel`.funnelid='".$refference_funnel_id."' and `".$dbpref."quick_pagefunnel`.level='".$refference_label."'");
		if($pagequery)
		{
			if($pageqry_ob=$pagequery->fetch_object())
			{
				$page_url=$pageqry_ob->baseurl.'/'.$pageqry_ob->filename;
			}
		}
	}
	$in=$mysqli->query("insert into `".$table."` (`ip`,`action`,`funnel`,`label`,`type`,`data`,`date`) values('".$this->ip."','0','".$refference_funnel_id."','".$refference_label."','".$type."','".$data."','".date('d-M-Y h:ia')."')");

	if($in)
	{
		$notification_email=get_option('gdpr_notice_email');

		if(get_option('gdpr_data_notice')=='1' &&(filter_var($notification_email,FILTER_VALIDATE_EMAIL)))
		{
			$temp_type=ucwords(str_replace("_"," ",$type));
			if($type=="data_others")
			{
				$temp_type="Others";
			}
			$status="N/A";
			if($status==1)
			{
				$status="Loggedin";
			}
			elseif($status==2)
			{
				$status="Loggedin From Different Email Id (".$loginemail.")";
			}
			if(filter_var($page_url,FILTER_VALIDATE_URL))
			{
				$page_url="<label>Request URL:</label> <p><a href='".$page_url."' target='_BLANK'>".$page_url."</a></p><br>";
			}

			$email_body="<h4>Request Received From ".$name." (Type: ".$temp_type.")</h4><br><label>Email Id:</label><p>".$email."</p><br><label>IP:</label><p>".$this->ip."</p><br><label>Status: </label><p>".$status."</p>".$page_url."<label>Description: </label><p>".$comment."</p>";
			$sequence_ob=$this->load->loadSequence();
			$sequence_ob->sendMail(get_option('default_smtp'),"",$notification_email,"CloudFunnels: Data Modification or Access Request",$email_body,"",0,$name,$email);
		}
	return 1;
	}else{return "Server Issue, Unable To Take The Request.";}
	}
	else
	{
		return "Invalid Email Entered";
	}
}
function deleteRecord($id)
{
	$mysqli=$this->mysqli;
	$dbpref=$this->dbpref;
	$table=$dbpref."gdpr_datas";
	$id=$mysqli->real_escape_string($id);
	$mysqli->query("delete from `".$table."` where `id`='".$id."'");
}
function sendmail($id)
{
//echo $id;
    $mysqli=$this->mysqli;
	$dbpref=$this->dbpref;
	$table=$dbpref."gdpr_datas";
	$sequence_ob=$this->load->loadSequence();
	$id=$mysqli->real_escape_string($id);


	$total_query=$mysqli->query("select * from `".$table."` where  `id`='".$id."'");
	if($r=$total_query->fetch_object())
	{
		$total=$r->data;
		$data=json_decode($total);
		$senddata=self::downloadGdprData($id,0);
	 	$subject="";$body="";
		if($r->type=='data_access') {
		$subject=get_option('gdpr_dataacess_Sub');
 		$body=get_option('gdpr_defalt_dataacsmsg');
		}
		elseif($r->type=='data_rectification') {
		$subject=get_option('gdpr_data_rectifi_sub');
		$body=get_option('gdpr_defalt_datrectsmsg');
		}
		else
		{
		//$subject='Your requested data';
		//$body='your data'.$senddata;
		}
		$subject=str_replace("{gdpr_data}",$senddata,$subject);
		$body=str_replace("{gdpr_data}",$senddata,$body);
		if(strlen($subject)>0 && strlen($body)>0)
		{
			 $mail=$sequence_ob->sendMail(get_option('default_smtp'),$data->name,$data->email,$subject,$body,"");
			 return $mail;
		}
	}
return 1;

	}
function processDataRequests($do,$id)
{
	$mysqli=$this->mysqli;
	$dbpref=$this->dbpref;
	$table=$dbpref."gdpr_datas";
	$id=$mysqli->real_escape_string($id);
	//send_email_with_gdpr_confirmation
	if($do=='1' && get_option('send_email_with_gdpr_confirmation')=='1' && self::sendmail($id))
	{
	$mysqli->query("update `".$table."` set `action`='".$do."' where `id`=".$id."");
	return 1;
	}
	elseif(get_option('send_email_with_gdpr_confirmation')=='0' || $do=='0')
	{
	$mysqli->query("update `".$table."` set `action`='".$do."' where `id`=".$id."");
	return 1;	
	}
	else
	{
		return 0;
	}
}
function downloadGdprData($id,$download=0)
{
	$mysqli=$this->mysqli;
	$dbpref=$this->dbpref;
	$table=$dbpref."gdpr_datas";
	$optin_table=$dbpref."quick_optins";
	$membership_table=$dbpref."quick_member";
	$id=$mysqli->real_escape_string($id);
	$qry=$mysqli->query("select * from `".$table."` where `id`=".$id."");
	$data=array('optin_data'=>array(),'membership_data'=>array());

	$funnel_query_optin="";
	$funnel_query_membership="";

	if($qry->num_rows>0)
	{
		if($r=$qry->fetch_object())
		{
			$userdata=json_decode($r->data);
			$type=0;
			if(is_numeric($r->funnel))
			{
				$email=$userdata->email;

				//if(filter_var())
				if((filter_var($userdata->login_email,FILTER_VALIDATE_EMAIL) && $userdata->login_status=='1')|| (filter_var($userdata->email,FILTER_VALIDATE_EMAIL) && $userdata->login_status=='2'))
				{

						$type=1;
				}
				
				$funnel_query_optin=" and `funnelid`='".$r->funnel."'";
				$funnel_query_membership=" and `funnelid`='".$r->funnel."'";
			}
			if(filter_var($userdata->email,FILTER_VALIDATE_EMAIL) && $type===0)
			{
				$email=$userdata->email;
				$type=2;
			}
			
			if($type>0)
			{
				if($type==1)
				{
					//membership
					$membership_ob=$mysqli->query("select `name`,`email`,`ip_created` as `registration_ip`,`ip_lastsignin` as `last_loggedin_ip`,`exf` as `others` from `".$membership_table."` where `email`='".$email."'".$funnel_query_membership);

					while($r3=$membership_ob->fetch_assoc())
					{
						array_push($data['membership_data'],$r3);
					}
				}
				if($type==1||$type==2)
				{
					//optin
					$optin_ob=$mysqli->query("select `name`,`email`,`ipaddr` as `ip`,`extras` as `others` from `".$optin_table."` where `email`='".$email."'".$funnel_query_optin);

					while($r2=$optin_ob->fetch_assoc())
					{
						array_push($data['optin_data'],$r2);
					}
				}
			}
			
		}
	if($download==1)
	{
		if(isset($email) && filter_var($email,FILTER_VALIDATE_EMAIL))
		{
			return "<h5><strong>Email: ".$email."</strong></h5>".json_encode($data);
		}
	}
	return json_encode($data);	
	}
	else
	{
		return 0;
	}
}
   }
   ?>