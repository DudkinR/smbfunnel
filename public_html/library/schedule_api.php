<?php
class QmlrSchedular
{
	//Equivalent API
    public $mysqli;
    public $table;
	public $timezone;
	public $pref;
	private $load;
	
function __construct($arr)
{
  $this->mysqli=$arr['mysqli'];
  $this->pref=$arr['dbpref']; 
  $this->timezone='UTC';
  $this->load=$arr['load'];
  date_default_timezone_set($this->timezone);	
  $this->table=$this->pref."quick_api_schedular_table";
}

function reqControl()
{//request controller
	if(isset($_GET['runserver']))
	{
		self::runScheduledMails();
		self::sendEmail();
	}	
}
function sendEmail()
{
	$table = $this->pref."quick_member";

	$qfnl_free_signup_email = get_option("qfnl_free_signup_email" );
	if( $qfnl_free_signup_email==1 )
	{
		$title = get_option('free_singn_email_title');
		$body  = get_option('free_singn_email_content');
		$body = str_ireplace('\\\r\\\n','',$body);
		$body = str_ireplace('\r\n','',$body);
		$body = str_ireplace('\n','',$body);
		$smtpid= get_option("sales_notif_email_smtp" );
		$sales_notif_email_admin= get_option("sales_notif_email_to_admin" );
		$aemails = explode(",", $sales_notif_email_admin);
		
		$sequence_ob= $this->load->loadSequence();
		$smtpid= get_option("default_smtp");
		$members = $this->mysqli->query("SELECT `name`,`email`,`id`,`funnelid` FROM `$table` WHERE `mailed`='0'");
		if( $members->num_rows > 0)
		{
			while( $data = $members->fetch_object())
			{
				$bodys='';
				$titles='';
				$name = $data->name;
				$email = $data->email;
				$id = $data->id;
				$funnel_data = get_funnel($data->funnelid);
				$funnel_name = !empty($funnel_data['name'])?$funnel_data['name']:'';
				
				if( !empty( $email ) )
				{
					if( !empty($name) )
					{
						$titles = str_ireplace("{name}",$name,$title);
						$bodys = str_ireplace("{name}",$name,$body);
					}
					if( !empty($email) )
					{
						$bodys = str_ireplace("{email}",$email,$bodys);
						$titles = str_ireplace("{email}",$email,$titles);
					}
					$bodys = str_ireplace("{funnel}",$funnel_name,$bodys);
					$titles = str_ireplace("{funnel}",$funnel_name,$titles);
					foreach ($aemails as $aemail) {
						$sequence_ob->sendMail($smtpid,'',$aemail,$titles,$bodys,"");
					}
				}
				$this->mysqli->query("UPDATE `$table` SET `mailed`='1' WHERE `id`=$id");
			}
		}
	}
}
function remScheduledMail($token,$url)
{
	if(!filter_var($url,FILTER_VALIDATE_URL)){return 0;}
	$mysqli=$this->mysqli;
	$token=$mysqli->real_escape_string($token);
	$url=$mysqli->real_escape_string($url);
	
	$del="delete from `".$this->table."` where cburl='".$url."' and stoken='".$token."'";
	
	$mysqli->query($del);
}

function conVertDateTime($date,$time,$zone)
{
	//date-time conversion
	// print_r( DateTimeZone::listIdentifiers() );
	$currentdateob= new DateTime($date.' '.$time, new DateTimeZone($zone));
    $converted=$currentdateob->setTimezone(new DateTimeZone($this->timezone));
    $convertedtime=$converted->format('Y-m-d H:i');
	
	$date=date('Y-m-d',strtotime($convertedtime));
	$time=date('H:i',strtotime($convertedtime));
	
	return array('date'=>$date,'time'=>$time,'time_sec'=>strtotime($date." ".$time));
}

function addScheduledMails($add_arr)
         {
			 //get request for mail scheduling
			 if(!filter_var($add_arr['rburl'],FILTER_VALIDATE_URL))
			 {
				 return 0;
			 }

			 $mysqli=$this->mysqli;
			 foreach($add_arr as $add_arr_index=>$add_arr_val)
			 {
				 $add_arr[$add_arr_index]=$mysqli->real_escape_string($add_arr_val);
			 }

             $cburl= $add_arr['rburl'];
			 $token= $add_arr['rtoken'];
			 $stimezone= $add_arr['rtimezone'];
			 $sdate= $add_arr['rdate'];
			 $stime= $add_arr['rtime'];
			 $sip= $add_arr['rpip'];
			 $status= $add_arr['rstatus'];
			 $getdate=self::conVertDateTime($sdate,$stime,$stimezone);
			 $date=date('d-M-Y');
			 $time=date('h:ia');
			 $ip = getIP();
			 
			 $sel=$mysqli->query("select id from `".$this->table."` where cburl='".$cburl."' and stoken='".$token."'");
			 $numrows = $sel->num_rows;
			 // print_r($numrows);
			 
			 if($numrows == 1)
			 {
			 	$fetcharr = $sel->fetch_assoc();
				$in="update `".$this->table."` set cburl='".$cburl."',stoken='".$token."',stime='".$getdate['time_sec']."',status='".$status."',ip='".$ip."',date='".$date."',time='".$time."' where id='".$fetcharr['id']."'";
				$id=$fetcharr['id'];
				$updateed=1;
			 }
			 else
			 {
				$in="INSERT INTO `".$this->table."` (cburl,stoken,stime,status,ip,date,time) VALUES ('".$cburl."','".$token."','".$getdate['time_sec']."','".$status."','".$sip."','".$date."','".$time."')";
				//print_r($in);
			 }
			 $done=$mysqli->query($in);
			 //print_r($done);
			 
			 if($done || isset($updateed))
			 {
				 $jsn=array('created'=>true);
				 return json_encode($jsn);
			 }
			 else
			 {
				 $jsn=array('created'=>false);
				 return json_encode($jsn);
			 }
			 
         }
function runScheduledMails()
         { 
			$mysqli=$this->mysqli;
			$date=date('Y-m-d');
			$time=date('H:i');
			$status='1';
			
			$sel=$mysqli->query("select id,cburl,stoken from `".$this->table."` where stime<".time()." or stime=".time()."");
			
			while($r = $sel->fetch_assoc())
			{
			$data = array('rwqmlr'=>1,'rtoken'=>$r['stoken'],'rurl'=>$r['cburl'],'requrl'=>$_SERVER['HTTP_HOST'],'apiurl'=> '');
			// print_r($data);
			$this->load->loadCbMailer($data);
			}
             
         }
}
?>