<?php 
class Payment
{
	var $mysqli;   
	var $dbpref;  
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
	}
	function savePaymentData()
	{     
     	$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$clientid = (isset($_POST['clientid']))? $_POST['clientid']:'';
		$clientsecret = (isset($_POST['clientsecret']))? $_POST['clientsecret']:'';
		$date= time();
		$title = (isset($_POST['title']))? $_POST['title']:'';
		$paymenttype = $_POST['paymenttype'];
		$tax = $_POST['tax'];
		$payid = $_POST['payid'];
		
		$clientid = $mysqli->real_escape_string($clientid);
		$clientsecret = $mysqli->real_escape_string($clientsecret);
		$title = $mysqli->real_escape_string($title);
		$paymenttype = $mysqli->real_escape_string($paymenttype);
		$tax = $mysqli->real_escape_string($tax);
		$payid = $mysqli->real_escape_string($payid);
		$salt=(isset($_POST['salt']))? $_POST['salt']:'';
		$salt=$mysqli->real_escape_string($salt);
		$pay_type=(isset($_POST['pay_type']))? $_POST['pay_type']:'';
		$pay_type=$mysqli->real_escape_string($pay_type);

		$jsonarr = array('client_id'=>$clientid,'client_secret'=>$clientsecret,'tax'=>$tax,'salt'=>$salt,'pay_type'=>$pay_type);

		$jsonencode = json_encode($jsonarr);


		if ($payid != "") 
			{
			$sql = "UPDATE `".$pref."payment_methods` set title='".$title."',`tax`='".$tax."',credentials='".$jsonencode."' where id='".$payid."'";
			}
		else
		{
		$sql="INSERT INTO `".$pref."payment_methods` (`title`, `method`, `tax`,`credentials`, `createdon`) VALUES ('".$title."','".$paymenttype."','".$tax."','".$jsonencode."','".$date."')";
		}

		$result = $mysqli->query($sql);
		if ($result == 1) {
			return 1;
		}
		else{
			return 0;
		}

}
}
?>