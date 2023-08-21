<?php
$temp_name="";
$temp_email="";

if(isset($_GET['gdpr_token']))
{
    if(isset($_SESSION['qfnl_membership_'.get_option('site_token').'_'.$_GET['gdpr_token']]))
    {
        $_SESSION['qfnl_gdpr_member'.get_option('site_token')]=$_SESSION['qfnl_membership_'.get_option('site_token').'_'.$_GET['gdpr_token']];
        $temp_name=$_SESSION['qfnl_gdpr_member'.get_option('site_token')]['name'];
        $temp_email=$_SESSION['qfnl_gdpr_member'.get_option('site_token')]['email'];
    }
}
elseif(isset($_SESSION['qfnl_gdpr_member'.get_option('site_token')]))
{
    $temp_name=$_SESSION['qfnl_gdpr_member'.get_option('site_token')]['name'];
    $temp_email=$_SESSION['qfnl_gdpr_member'.get_option('site_token')]['email'];
}

?>
<html>
<head>
<title>GDPR Data Access/Request Form</title>
<?php echo $header; ?>
</head>
<body>
<div class="container-fluid">
<div class="row">
<div class="col-sm-4 offset-sm-4">
<br></br>
<div class="card pnl visual-pnl">
<div class="card-header">Request For Data</div>
<div class="card-body">
<form action="" method="post">
<div class="mb-3">
<label>Name</label>
<input type="text" class="form-control" name="name" placeholder="Enter Your Name" value="<?php echo $temp_name; ?>" required>
<label>Email</label>
<input type="text" class="form-control" name="email" placeholder="Enter Your Email Id" value="<?php echo $temp_email;?>" required>
<label>Type Of Request</label>
<select class="form-select" name="data_type">
<option value="data_access">Data Access</option>
<option value="data_rectification">Data Rectification</option>
<option value="data_others">Others</option>
</select>
<label>Describe your request</label>
<textarea class="form-control" name="description" placeholder="Describe about your request here" required></textarea>
<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
<center><strong><p id="err"><?php if($err==1){echo "<font color='green'>Your Request Submitted Successfully</font>";}elseif(!is_numeric($err)){echo "<font color='#e6005c'>".$err."</font>";} ?></p></strong></center>
<button type="submit" class="btn btn-info btn-block" name="submitgdprrequest">Submit Your Request</button>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
<style>
input[type=text],input[type=email],select
{
margin-bottom:10px;
}

body
{
background:linear-gradient(#42275a,#734b6d);
}
</style>
</body>
</html>