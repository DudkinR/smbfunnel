<?php
class Sequence
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $ip;
	var $mailing_api;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		$this->load=$arr['load'];
		$this->ip=$arr['ip'];
		$this->mailing_api="http://cloudfunnels.in/membership/api/mail";
	}
	function createSequence()
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;

		$smtpid = $_POST['smtpid'];
		$sequence_id = $_POST['sequence_id'];
		$mailsubject = $_POST['mailsubject'];
		$mailbody = $_POST['mailbody'];
		// echo $mailbody;
		$unsubsmsg = $_POST['unsubsmsg'];
		$sequencedays = $_POST['sequencedays'];
		$datetime= date("d-M-y h:ia");
		$listid = $_POST['listid'];
		$update = $_POST['updaterec'];
		$date=date("d-M-Y h:ia");
		$time=date("h:ia");
		$checkexist = $_POST['checkexist'];
		$title = $_POST['title'];

		$smtpid = $mysqli->real_escape_string($smtpid);
		$listid = $mysqli->real_escape_string($listid);
		$mailsubject = $mysqli->real_escape_string($mailsubject);
		$mailbody = $mysqli->real_escape_string($mailbody);
		$unsubsmsg = $mysqli->real_escape_string($unsubsmsg);
		$sequencedays = $mysqli->real_escape_string($sequencedays);
		$updater = $mysqli->real_escape_string($update);
		$checkexist = $mysqli->real_escape_string($checkexist);
		$title = $mysqli->real_escape_string($title);

		// echo strpos($listid,"all");
		if (strpos($listid,"all") == 1) {
			$listid = "@all@";
		}

		$sentdata = "".$mailsubject."@clickbrk@".$mailbody."@clickbrk@".$unsubsmsg."";

		if($update != "null"){
			$update = "update `".$pref."quick_sequence` set title='".$title."',listid='".$listid."',smtpid='".$smtpid."',sentdata='".$sentdata."',sequence='".$sequencedays."',date='".$date."',`sequence_id`='$sequence_id',time='".time()."',updated_on='".time()."' where id='".$updater."'";
			// echo $update;

			$res = $mysqli->query($update);
			// print_r($res);
		}
		elseif($checkexist !=""){
			$selectmax = "select max(id) from `".$pref."quick_sequence`";
			$sel = $mysqli->query($selectmax);
			// print_r($sel);
			while($resul = $sel->fetch_assoc()){
				$gettheid = $resul['max(id)'];
			$update = "update `".$pref."quick_sequence` set title='".$title."',listid='".$listid."',smtpid='".$smtpid."',`sequence_id`='$sequence_id',sentdata='".$sentdata."',sequence='".$sequencedays."',updated_on='".time()."' where id='".$gettheid."'";
			$res = $mysqli->query($update);

			}
		}
		else{

		$insert = "INSERT into `".$pref."quick_sequence` (`title`,`listid`, `smtpid`, `sentdata`, `stimezone`, `sequence`, `date`,`sequence_id`, `time`, `updated_on`) VALUES('".$title."','".$listid."','".$smtpid."','".$sentdata."','','".$sequencedays."','".$date."','$sequence_id','".time()."','".time()."')";
		// print_r($insert);
		$res = $mysqli->query($insert);
	}
		// print_r($res);

		if ($res == 1) {
			return 1;
		}
		else{
			return 0;
		}
	}
	function createNewSequence()
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table = $pref."new_sequence";
		$description = $_POST['description'];
		$title = $_POST['title'];
		$updater = $_POST['update'];

		$description = $mysqli->real_escape_string($description);
		$title = $mysqli->real_escape_string($title);
		$updater = $mysqli->real_escape_string($updater);
		$insert=0;
		if( strlen( $updater ) >0 ){
			$update = "UPDATE `$table` SET `title`='$title',`description`='$description' WHERE `id`='$updater'";
			$res = $mysqli->query($update);
			$insert = $updater;
		}
		else{
			$insert = "INSERT INTO `$table` (`title`,`description`, `created_on`) VALUES ('$title','$description','".time()."')";
			$res = $mysqli->query($insert);
			$insert = $mysqli->insert_id;
		}
		if ($res) {
			return $insert;
		}
		else{
			return 0;
		}
	}
	function createSequenceForCompose($title,$list_id,$smtp_id,$sentdata,$extra_data)
	{ 
		//this is for compose mail
		//storing delay, groupsize and exta emails in sequence

		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table=$pref."quick_sequence";

		$title=$mysqli->real_escape_string($title);
		$list_id=$mysqli->real_escape_string($list_id);
		$smtp_id=$mysqli->real_escape_string($smtp_id);
		$sentdata=$mysqli->real_escape_string($sentdata);
		$sequence_id = 0;
		$insert = "INSERT INTO `".$table."` (`title`,`listid`,`sequence_id`, `smtpid`, `sentdata`, `stimezone`, `sequence`, `date`, `time`,`updated_on`) VALUES ('".$title."','".$list_id."','".$sequence_id."','".$smtp_id."','".$sentdata."','".$extra_data."','compose','','".time()."','".time()."')";
			
		if($mysqli->query($insert))
		{
			return $mysqli->insert_id;
		}
		else
		{
			return 0;
		}
		
	}
	function composeOrScheduleSubscriptionMail($listid,$email,$name,$extra=array(),$compose=false,$compose_data=array())
	{
		//echo plugins_url('composemailfunction.php',__FILE__).'test';

		//compose_data will be sentdata,smtp

		if ($_SESSION['user_plan_type' . get_option('site_token')] == 2) {
			return 0;
		}

		if(filter_var($email,FILTER_VALIDATE_EMAIL))
		{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$listid='@'.$listid.'@';

		$compose_list_id=$listid;

		$settingtable=$pref.'quick_sequence';
		$scheduletable=$pref.'quick_subscription_mail_schedule';

		if($compose)
		{
			$compose=$mysqli->real_escape_string($compose);
			$sql1="select * from `".$settingtable."` where `id`=".$compose."";
		}
		else
		{
			$sql1="SELECT * from `".$settingtable."` where listid like '%".$listid."%' or listid like '%all%'";
		}

		// echo $sql1;
		$sql_result=$mysqli->query($sql1);
		// print_r($sql_result);
		while($row = $sql_result->fetch_assoc())
		{
		$idd = $row['id'];
     	$listid=$row['listid'];
	    $sequenceafter=$row['sequence'];
	    $timezone=$row['stimezone'];
	    $smtpid=$row['smtpid'];
 	    $sentdata=$row['sentdata'];

			$otp=substr(str_shuffle('abcdefghijklmnopqrstuvwxyzASDFGHJKLZXCVBNMQWERTYUIOP01234567890'),0,5);
			$otp .=substr(str_shuffle('1234567890'),0,5);
		$otp .=time();
		
		$sentdata=self::linksReplacer($sentdata,$idd,$otp);
		
		$member_ob=$this->load->loadMember();
		$sentdata=$member_ob->varifiedMembersipLinkReplacer($sentdata);
		if(is_array($extra))
		{
			if(isset($extra['membership']))
			{
				if(is_array($extra['membership']))
				{
				$sentdata=$member_ob->membrshipTemplatecreator($sentdata,$extra['membership']);
				}
			}
			if(isset($extra['products']))
			{
				if(is_array($extra['products']))
				{
				$sell_ob=$this->load->loadSell();
				$sentdata=$sell_ob->productTemplatecreator($sentdata,$extra['products']);

				echo $sentdata;
				}
			}
		}
		$sentdata=str_replace("{name}",$name,$sentdata);

		if(!isset($extra["first_name"]))
		{
			$name_toreplace=explode(" ", trim($name));
			$sentdata=str_replace("{first_name}",$name_toreplace[0],$sentdata);
		}

		if(!isset($extra["last_name"]))
		{
			$name_toreplace=explode(" ", trim($name));
			$last_name_to_replace=(isset($name_toreplace[1]))? $name_toreplace[1]:"";
			$sentdata=str_replace("{last_name}",$last_name_to_replace,$sentdata);
		}

		// Replace extra values of list
		$extra_arr = isset($extra[0]) ? json_decode($extra[0]) : $extra;
		foreach ($extra_arr as $key => $value) {
			if (is_array($value)) {
				continue;
			}
			$replaced_string = "{" . $key . "}";
			$name_toreplace = trim($value);
			$sentdata = str_replace($replaced_string, $name_toreplace, $sentdata);
		}

		$sentdata=str_replace("{email}",$email,$sentdata);

    $unsubscribelink="<a href='".get_option('install_url')."/index.php?page=do_unsubscribe&card=".base64_encode($email)."&token=".$otp."'>@unsubscribemsg@</a>";

		preg_match_all("/({unsubscribe})+(.(?!{unsubscribe}))*({\/unsubscribe})+/",$sentdata,$unsubarr);
    if(isset($unsubarr[0]))
		{
			if(is_array($unsubarr[0]))
			{
				for($i=0;$i<count($unsubarr[0]);$i++)
				{
				$tempunsubscribemsg=$unsubscribelink;
				$originalunsubtext=$unsubarr[0];
				$originalunsubtext=$originalunsubtext[$i];
				$unsubtextt=str_replace("{unsubscribe}","",$originalunsubtext);
				$unsubtextt=str_replace("{/unsubscribe}","",$unsubtextt);
				$tempunsubscribemsg=str_replace('@unsubscribemsg@',$unsubtextt,$tempunsubscribemsg);
				$sentdata=str_replace($originalunsubtext,$tempunsubscribemsg,$sentdata);
				}
			}
		}

		if(strpos($sentdata,'{unsubscribe}')>=0)
		{
        $temppunsubscribemsg=str_replace("@unsubscribemsg@",'unsubscribe',$unsubscribelink);
				$sentdata=str_replace('{unsubscribe}',$temppunsubscribemsg,$sentdata);
		}

		$sentdata=arrayIndexToStr($sentdata,$extra);

	    $email_subjct=explode("@clickbrk@", $sentdata);
	    $emailsubject=$email_subjct[0];
	    $email_body=$email_subjct[1];
	    $unsubscribemsg=$email_subjct[2];

			if(strpos($unsubscribemsg,"do_unsubscribe&card")<2)
			{
				$email_subjct[2]=str_replace("@unsubscribemsg@",$unsubscribemsg,$unsubscribelink);
				$unsubscribemsg=$email_subjct[2];
			//	$email_subjct[2]=str_replace('{unsubscribe}',$temppunsubscribemsg,$email_subjct[2]);
				$sentdata=implode("@clickbrk@",$email_subjct);
			}

		$trackurl=get_option('install_url');
		$trackurl .="/index.php?page=mail_track&token=@token@&card=".base64_encode($email)."&img=this.jpg";

		$trackurl=str_replace("@token@",base64_encode($otp),$trackurl);

		$trackurl="<img src='".$trackurl."'>";

		if(($sequenceafter===0|| $sequenceafter==="0") || ($sequenceafter==='compose' && $compose))
		{
			$smtpid=($compose)? $compose_data['smtp']:$smtpid;

			$email_body .= self::brbanding();

			$mailstat=self::sendMail($smtpid,$name,$email,$emailsubject,$email_body,$unsubscribemsg.$trackurl);

			$sendmailstat=0;
			if($mailstat==1)
			{
			$sendmailstat=1;
			}

	  $idd=$mysqli->real_escape_string($idd);

			if($compose)
			{
				$listid=$mysqli->real_escape_string($compose_list_id);
			}

			$listid=$mysqli->real_escape_string($listid);
			$smtpid=$mysqli->real_escape_string($smtpid);
			$sentdata=$mysqli->real_escape_string($sentdata);
			$email=$mysqli->real_escape_string($email);

			$in="INSERT INTO `".$pref."quick_subscription_mail_schedule` (seqid, listid,smtpid,status,sentdata,extraemails,sdate,stime,stimezone,stoken,date,time) VALUES ('".$idd."','".$listid."','".$smtpid."','".$sendmailstat."','".$sentdata."','".$email."','".date('d-M-Y h:ia')."','".date('h:ia')."','N/A','".$otp."','".date('d-M-Y')."','".time()."')";

			$mysqli->query($in);
			if($sequenceafter==='compose')
			{
				return $sendmailstat;
			}
		}
		elseif(is_numeric($sequenceafter))
		{
			$timezone=date_default_timezone_get();
			if(strlen($timezone)<2){$timezone='UTC';date_default_timezone_set("UTC");}
			// require_once('schedulefunc.php');
			// $ob=new wpqkmlschdlmail();
			$date=date('d-M-Y h:ia');

			$date=date('d-M-Y h:ia',strtotime($date.' +'.$sequenceafter.' days'));
			$rdate=date('d-M-Y',strtotime($date));
			$rtime=date('h:ia',strtotime($date));
			$rsip=self::getUserIP();

		    $rurl=get_option('install_url');
            $rurl .="/index.php?page=mail_send";

			self::reqToCreatorForSchedule($rurl,$otp,$timezone,$rdate,$rtime,$rsip,'1');

			$idd=$mysqli->real_escape_string($idd);
			$listid=$mysqli->real_escape_string($listid);
			$smtpid=$mysqli->real_escape_string($smtpid);
			$sentdata=$mysqli->real_escape_string($sentdata);
			$trackurl=$mysqli->real_escape_string($trackurl);
			$email=$mysqli->real_escape_string($email);

			$in="INSERT INTO `".$pref."quick_subscription_mail_schedule` (`seqid`, `listid`,`smtpid`,`status`,`sentdata`,`extraemails`,`sdate`,`stime`,`stimezone`,`stoken`,`date`,`time`) VALUES ('".$idd."','".$listid."','".$smtpid."','-1','".$sentdata.$trackurl."','".$email."','".$rdate."','".$rtime."','".$timezone."','".$otp."','".date('d-M-Y')."','0')";
			// print_r($in);

			$resultqry = $mysqli->query($in);
			// print_r($resultqry);
		}
		}
	}
	}

	function getUserIP(){
	return $this->ip;

	}

	function reqToCreatorForSchedule($rurl,$token,$timezone,$rdate,$rtime,$rsipp,$status)
     {
          $data = array(
		  'auth'=>'',
          'rburl' =>$rurl,
          'rtoken' =>$token,
          'rtimezone' =>$timezone,
		  'rdate' =>$rdate,
          'rtime' =>$rtime,
          'rpip' =>$rsipp,
		  'rstatus' =>$status
              );

			$sequence_api_ob=$this->load->loadScheduler();
			$output=$sequence_api_ob->addScheduledMails($data);

			$jsn=json_decode($output);
			return $jsn;
	 }
	function brbanding()
	{
		$pr=$this->load->isPlusUser();
		$content="";
		if(!$pr)
		{
			$install_url=get_option('install_url');

			$logo= $install_url."assets/theme-assets/assets/images/poweredby.png";

			$content ="<center><a href='".$install_url."/index.php?page=mmbr_lgout'><p><img src='".$logo."' alt='Powered By CloudFunnels' style='max-height:20px;max-width:200px'></p></a></center>";
		}
		return $content;
	} 
	function spinText($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*?)\}/x',
            array($this, 'spinTextCallBack'),
            $text
        );
	}
	function spinTextCallBack($text)
	{
		$text = $this->spinText($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
	}
	 function sendMail($smtpid,$name,$email,$emailsubject,$email_body,$unsubscribemsg,$debug=0,$ufromname="",$ufromemail="",$ureplyname="",$ureplyemail="")
	 {//send mail
		$funnel_ob=$this->load->loadFunnel();
		if($funnel_ob->do_route)
		{
			$emailsubject=str_replace($funnel_ob->routed_url,get_option('install_url'),$emailsubject);
			$email_body=str_replace($funnel_ob->routed_url,get_option('install_url'),$email_body);
			$unsubscribemsg=str_replace($funnel_ob->routed_url,get_option('install_url'),$unsubscribemsg);
		}

		if(get_option('spin_email')=='1')
		{
			$emailsubject=self::spinText($emailsubject);
			$email_body=self::spinText($email_body);
			$unsubscribemsg=self::spinText($unsubscribemsg);
		}

		 $mysqli=$this->mysqli;
		 $pref=$this->dbpref;
		 if(is_numeric($smtpid))
		 {
		    $sql = "select * from `".$pref."quick_smtp_setting` where id=".$smtpid;
			$result = $mysqli->query($sql);
			if($result->num_rows>0)
			{
			$res = $result->fetch_assoc();
			$hostname = $res['hostname'];
			$username = $res['username'];
			$password = $res['password'];
			$port = $res['port'];
			$encryption = $res['encryption'];
			$fromname = $res['fromname'];
			$fromemail = $res['fromemail'];
			if(filter_var($ufromemail,FILTER_VALIDATE_EMAIL))
			{
				$fromname = $ufromname;
				$fromemail =$ufromemail;
			}
				$replyemail = $res['replyemail'];
				$replyname = $res['replyname'];

			if(filter_var($ureplyemail, FILTER_VALIDATE_EMAIL))
			{
				$replyemail= $ureplyemail;
				$replyname= $ureplyname;	
			}

			return self::smtpMailSending($hostname,$username,$password,$encryption,$port,$fromname,$fromemail,$replyemail,$replyname,$name,$email,$emailsubject,$email_body,$unsubscribemsg,$debug);
			}
			else
			{
				return "SMTP Not Exists";
			}
		 }
		 elseif($smtpid=='api')
		 {
			 return self::apiMailSend($name,$email,$emailsubject,$email_body);
		 }
		 else
		 {
			if(self::phpMailSend($name,$email,$emailsubject,$email_body,$unsubscribemsg))
			{
				return 1;
			}
		 }

	 }
     function phpMailSend($name,$email,$emailsubject,$email_body,$unsubscribemsg)
	 {
		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// More headers
		//$headers .= 'From: '.$fromname.' <'.$fromemail.'>' . "\r\n";

		$content=$email_body.$unsubscribemsg;

		if($plugin_loader)
		{
			$content=$plugin_loader->processTheEmailContent($content);
			$emailsubject=$plugin_loader->processTheEmailSubject($emailsubject);
		}
	
		return mail($email,$emailsubject,$content,$headers);
	 }
	 function smtpMailSending($hostname,$username,$password,$encryption,$port,$fromname,$fromemail,$replyemail,$replyname,$name,$email,$emailsubject,$email_body,$unsubscribemsg,$debug=0)
	 {
		$plugin_loader=false;
		if(isset($GLOBALS['plugin_loader']))
		{
			$plugin_loader=$GLOBALS['plugin_loader'];
		}
		require_once("vendor/autoload.php");

		if(! class_exists('\PHPMailer\PHPMailer\PHPMailer'))
		{
			return;
		}

		$mail = new \PHPMailer\PHPMailer\PHPMailer();
		try {
		$mail->SMTPDebug =$debug;
		$mail->isSMTP();
		$mail->Host= $hostname;
		$mail->SMTPAuth= true;
		$mail->Username=$username;
		$mail->Password=$password;
		$mail->SMTPSecure =$encryption;
		$mail->Port= $port;
		$mail->CharSet = 'UTF-8';
		$mail->setFrom($fromemail,$fromname);
		$mail->addAddress($email,$name);
		$mail->addReplyTo("".$replyemail."", "".$replyname."");
		$mail->isHTML(true);

		$content=$email_body."<br><p><center>".$unsubscribemsg."</p></center>";

		if($plugin_loader)
		{
			$content=$plugin_loader->processTheEmailContent($content);
			$emailsubject=$plugin_loader->processTheEmailSubject($emailsubject);
		}

		$mail->Subject = $emailsubject;

		$mail->Body=$content;
		if($mail->send())
		{
			return 1;
		}
		else
		{
			return 0;
		}

		} catch (Exception $e) {
		return $mail->ErrorInfo;
		}
	 }
	 function apiMailSend($name,$email,$subject,$content)
	 {
		$api_url=$this->mailing_api;
		$token=get_option('site_token');
		$mailing_data=json_encode(array('name'=>$name,'email'=>$email,'subject'=>$subject,'content'=>$content,'token'=>$token,'url'=>get_option('install_url')));

		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$api_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,array('data'=>$mailing_data));
		$res=curl_exec($ch);
		$data=json_decode($res);
		return (isset($data->sent) && $data->sent===true)? true:false;
	 }
	 function sequenceTrack($otp,$email)
	 {
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."quick_subscription_mail_schedule";
		$otp=$mysqli->real_escape_string($otp);
		$email=$mysqli->real_escape_string($email);

		$mysqli->query("update `".$table."` set status='2' where `stoken`='".$otp."' and `extraemails`='".$email."'");
  }
	function linksReplacer($content,$sequence_id,$emailtoken)
	{
		//create detectable links
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."email_links_visits";
		//$reg="/(?<=(<a(\s)))((href=\")|(href='))+([-A-Za-z0-9+&@#\/%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/%=~_|])+(\"|')+(?=(\>|>))/";

		$reg= "/((href=\")|(href='))+([-A-Za-z0-9+&@#\/%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/%=~_|])+(\"|')/";

		preg_match_all($reg,$content,$arr);
		if(isset($arr[0]))
		{
			if(is_array($arr[0]))
			{
				$urls=$arr[0];
				for($i=0;$i<count($urls);$i++)
				{
					$mainurl=$urls[$i];
					$url=preg_replace("/(href=|\"|\')/",'',$mainurl);
					if(!filter_var($url,FILTER_VALIDATE_URL)){continue;}
					elseif(strpos($url,"&qfnldetectcard")>0)
					{continue;}
					lbl:
					$token=substr(str_shuffle("sdfghjklzxcvbnmqwertyuiopQWERTYUIOPSDFGHJKLXCVBNM1234567890"),0,5);
					$token .=time();

					$chk=$mysqli->query("select `id` from `".$table."` where `url`='".$url."' and `email_token`='".$emailtoken."' and `url_token`='".$token."' and `sequence_id`='".$sequence_id."'");

          if($chk->num_rows>0)
					{
						goto lbl;
					}

					$mysqli->query("insert into `".$table."` (`url`,`sequence_id`,`email_token`,`url_token`,`visited`,`createdon`) values('".$url."','".$sequence_id."','".$emailtoken."','".$token."','0','".time()."')");

					if (strpos($mainurl,'cf-verified') == false) {
						$mailurl='href="'.get_option('install_url')."?page=do_redirect&qfnlemlcard=".base64_encode($emailtoken)."&qfnldetectcard=".base64_encode($token).'"';
				  	}else{
					   $mailurl = $mainurl;
				  	}	
					$content=str_replace($mainurl,$mailurl,$content);
				}
			}
		}
		return $content;
	}
	function storeLinksVisits($otp,$mailcard)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."email_links_visits";

		$otp=$mysqli->real_escape_string(base64_decode($otp));
		$mailcard=$mysqli->real_escape_string(base64_decode($mailcard));
		$qry=$mysqli->query("select `id`,`url` from `".$table."` where `email_token`='".$mailcard."' and `url_token`='".$otp."'");
		if($qry->num_rows>0){
			$r=$qry->fetch_object();
			$url=$r->url;
			$mysqli->query("update `".$table."` set `visited`='1',`createdon`='".time()."' where `email_token`='".$mailcard."' and `url_token`='".$otp."' and `visited`='0'");
			echo "<script>window.location='".$url."'</script>";
		}

		

	}
}
?>
