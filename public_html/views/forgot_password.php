<html>
<head>
<meta charset="UTF-8">
<title><?php w('Admin: Forgot Password'); ?></title>
<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
<link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
<?php echo $headers; ?></head>
<body class="loginbg">
<div class="container" id="veuauththsection">
<div class="d-flex justify-content-center h-100">
<div class="card">
<div class="card-body">

  <img src="assets/img/logo-text.png" class="mx-auto d-flex img-fluid" alt="CloudFunnel" width="40%" />
  <center><h5 style="padding:4px;padding-top:14px;padding-bottom:14px;margin-bottom:10px;font-size:15px;">
  <span v-if="fpwdstep==0">{{t('Forgot Password? Please enter Your Email')}}</span>
  <span v-if="fpwdstep==1">{{t('We have sent you an OTP, please enter')}}</span>
  <span v-if="fpwdstep==2">{{t('Add New Password')}}</span>
  </h5></center>
 
 <div class="alert alert-success" v-if="fpwdstep==3"><strong>{{t('Your Password Updated Successfully. Redirecting to login...')}}</strong></div>

  <div class="mb-3" v-if="fpwdstep==0">
    <input type="email" class="form-control" name="admin_email" id="admin_email" v-bind:placeholder="t('Please Enter Your Email')" v-model="email">
   </div>
   
   <div class="mb-3" v-if="fpwdstep==1">
    <input type="text" class="form-control" name="admin_otp" id="admin_otp" v-bind:placeholder="t('Enter OTP')" v-model="fpwdotp">
   </div>
   
   <span v-if="fpwdstep==2">
   <div class="mb-3">
   <input type="password" class="form-control" name="password" id="password" v-bind:placeholder="t('Enter password')" v-model="userpass">
   </div>
   <div class="mb-3">
    <input type="password" class="form-control" name="password" id="repassword" v-bind:placeholder="t('Re-enter password')" v-model="repass">
   </div>
   </span>
 
	<p v-if="(err.length>0)? true:false" style="color:#FF1493;font-size:14px;text-align:center;">{{t(err)}}</p>
   <center><button class="btn theme-button btn-block loginn" name="login" type="button" v-on:click="forgotPassword($event)">{{t('Continue')}}</button>
   <p style="font-size:14px !important;margin-top:10px !important;"><a href="index.php?page=login">{{t('Login Now')}}</a></p>
   <br><br>
  <h6 style="margin-bottom:0px;margin-top:10px;"><a href="https://teknikforce.com/" target="__blank">{{t('${1} @ CloudFunnels by Teknikforce',['<?php echo date('Y'); ?>'])}}</a></h6>
  <p style="margin-bottom:0px;margin-top:5px;"><a href="http://teknikforce.com/support/" target="_BLANK" style="font-size:11px;">{{t('Need Support? Click here')}}</a></p>
   </center>
 </div>
 </div>

</div>
</div>
<?php echo $footer; ?>
</body>
</html>