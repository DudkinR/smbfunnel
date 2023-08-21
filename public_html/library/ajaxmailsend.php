<?php
function sendSequencedMail($info,$data_arr)
{
if (isset($data_arr['rwqmlr'])) 
{
	$load=$info['load'];
	$mysqli=$info['mysqli'];
	$dbpref=$info['dbpref'];

	foreach($data_arr as $data_arr_index=>$data_arr_val)
	{
		$data_arr[$data_arr_index]=$mysqli->real_escape_string($data_arr_val);
	}

	$stoken = $data_arr['rtoken'];
	//print_r($stoken);
	//$pathInPieces = explode(DIRECTORY_SEPARATOR , __FILE__);
	$object = $load->loadSequence();
	$sequence_api_ob=$load->loadScheduler();

	// print_r($object);
	$select = "SELECT * FROM `".$dbpref."quick_subscription_mail_schedule` WHERE stoken='".$stoken."'";
	$query = $mysqli->query($select);
	// print_r($query);
	while($row = $query->fetch_assoc())
	{
		// print_r($row);
		$idd = $row['id'];
        $listid=$row['listid'];
	    $timezone=$row['stimezone'];
	    $smtpid=$row['smtpid'];
	    $email = $row['extraemails'];
 	    $sentdata=$row['sentdata'];
 	    $name = "";
	    $email_subjct=explode("@clickbrk@", $sentdata);
	    $emailsubject=$email_subjct[0];
	    $email_body=$email_subjct[1];
		$unsubscribemsg=$email_subjct[2];
		
		$email_body .=$object->brbanding();

			$mailstat=$object->sendMail($smtpid,$name,$email,$emailsubject,$email_body,$unsubscribemsg);
            $sendmailstat=0;
			if($mailstat==1)
			{
			$sendmailstat=1;
			}

			$update = "update `".$dbpref."quick_subscription_mail_schedule` set status='".$sendmailstat."',`time`='".time()."' where id=".$idd."";
			$res = $mysqli->query($update);

			$sequence_api_ob->remScheduledMail($data_arr['rtoken'],$data_arr['rurl']);
		}
}
}
?>
