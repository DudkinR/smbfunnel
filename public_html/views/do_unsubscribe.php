<?php 
 $mysqli = $info['mysqli'];
 $pref = $info['dbpref'];
$emailid = base64_decode($_GET['card']);
$stoken = $_GET['token'];
$status = "";
$alerttext = "";

if (isset($_POST['wpqmlr-unsubs'])) {
$select = "select * from `".$pref."quick_subscription_mail_schedule` where stoken='".$stoken."' and extraemails='".$emailid."'";

$qry = $mysqli->query($select);
if ($qry->num_rows == 1) {
	$update = "update `".$pref."quick_subscription_mail_schedule` set status=3 where extraemails='".$emailid."' and stoken='".$stoken."'";
	$up=$mysqli->query($update);
	
	if ($up == 1) {
		$check = "select * from `".$pref."quick_subscription_mail_schedule` where stoken='".$stoken."' and extraemails='".$emailid."' and status=3";
		$getdata = $mysqli->query($check);
		
		while ($row = $getdata->fetch_assoc()) {
			// print_r($row['listid']);
			if ($row['listid'] == "@all@") {
				
				$gettalllist = "select * from `".$pref."quick_list_records`";
				$result = $mysqli->query($gettalllist);

				$listids = "";
				while ($listid = $result->fetch_assoc()) {
					$listids .= $listid['id']."@";
				}
				// echo $listids."<br>";
				$listat=rtrim(implode(',',explode('@',$listids)),',');
			  
				$delete = "DELETE FROM `".$pref."quick_email_lists` WHERE `email`='$emailid' AND listid in(".$listat.")";
				$delete1 = "DELETE FROM `".$pref."quick_subscription_mail_schedule` WHERE  `extraemails`='$emailid'";
			    $query = $mysqli->query($delete);
			    $query1 = $mysqli->query($delete1);
			    if ($query == 1 || $query1==1 ) {
			    	$status = "You've been unsubscribed and you will not receive any more emails from us";
			    }

			}
			else{

			$listat=rtrim(implode(',',explode('@',"0".$row['listid'])),',');
			$listids = $row['listid'];

			$delete = "DELETE FROM `".$pref."quick_email_lists` WHERE `email`='$emailid' AND listid in(".$listat.")";
			$delete1 = "DELETE FROM `".$pref."quick_subscription_mail_schedule` WHERE `extraemails`='$emailid'";
			$query = $mysqli->query($delete);
			$query1 = $mysqli->query($delete1);
			if ($query == 1 || $query1==1 ) {
				$status = "You've been unsubscribed and you will not receive any more emails from us";
			}
		
		}
		}
	}
}
}
?>

<div class="container-fluid bg">

<br><br><br><br><br><br><br><br><br>	
<div class="row">
	<div class="col-sm-4"></div>
<div class="col-sm-4">
  <div class="card pnl visual-pnl">
  <div class="card-header bg-warning"><center style="padding: 13px;color: white;">Unsubscribe Yourself</center></div>
    <div class="card-body">
    <div id="unsubstext" style="padding: 20px;">
    <?php
    if (strlen($status) > 1) {
    	echo $alerttext = "";
    }
    else {
    	echo $alerttext = '<div class="wpqkmlr-alerttext">
  <strong>Alert!</strong> Stop receiving emails and unsubscribe from following lists.
</div>
<br>
<div class="wpqkmlr-wall">
                <form class="wpqkmlr-form" action="" method="post">
                <b><center></center></b>
                <br>
            <center><input type="submit" class="wpqkmlr-button" name="wpqmlr-unsubs" value="Unsubscribe" onclick="wqunsubsrequest()">
            <button type="button" class="wpqmlr-cancel" onclick="wqcancelrequest()">Cancel</button></center>
            </div>
    	</div>
    	
    </form>';
    }
    ?>
    <center><h4 style="color: green;font-weight: bold;"><?php echo $status; ?></h4></center>
</div>
    </div>
    <div class="col-sm-4"></div>
</div>
</div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>
<style type="text/css">
	.wpqkmlr-alerttext {
    border: 1px solid #e89f30;
    border-radius: 5px;
    padding: 13px 5px;
    color: white;
    background-color: #f57979;
}
.card-body{
	background-color: white;
}
.wpqkmlr-wall {
    margin-top: -19px;
    padding: 5px 0px 20px 0px;
    background-color: white;
}
.wpqkmlr-form {
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
}
.wpqkmlr-button, .wpqmlr-cancel {
    border: none;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    color: black;
    border-radius: 10px;
    padding: 10px;
}
.wpqkmlr-button{
	background-color: #dda21e;
}
.bg{
    background-repeat: no-repeat;
}
.pnl>.card-header {
    background: linear-gradient(#004080,#004080);
	font-size: 20px !important;
}
</style>
<script type="text/javascript">
	function wqcancelrequest(){
		window.open('','_parent','');
        window.close();
	}
	function wqunsubsrequest(){

	}
</script>