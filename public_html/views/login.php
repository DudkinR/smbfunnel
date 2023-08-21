<html>
<head>
<meta charset="UTF-8">
<title><?php w('Admin: Login'); ?></title>
<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
<link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

<!-- language -->
<!-- <script src="./lang/cache.js"></script>
<script src="./assets/js/lang.js"></script> -->
<!-- /language -->

<?php echo $headers; ?>
</head>
<body class="loginbg">
<div class="container" id="veuauththsection">
<div class="d-flex justify-content-center h-100">

<div class="card">
<div class="card-body">
    <img src="assets/img/logo-text.png" class="mx-auto d-flex img-fluid" alt="CloudFunnel" width="40%" />

    <h5 class="p-3 text-center text-white" style="padding: 14px 4px; margin-bottom: 10px; font-size: 15px;"><?php echo t("Login into your account."); ?></h5>
  

  <div class="mb-3">
    <input type="email" class="form-control" name="admin_email" id="admin_email" v-bind:placeholder="t('Enter Email')" v-model="email">
   </div>

     <div class="mb-3">
    <input type="password" class="form-control" name="password" id="password" v-bind:placeholder="t('Enter Password')" v-model="userpass">
   </div>
   <label>
   <div class="mb-3 row align-items-center remember mb-1" id="checkbox">
      <input type="checkbox" name="remember" v-model="remember"> {{t('Remember Me')}}
    </div>
    </label>
	<p v-if="(err.length>0)? true:false" style="color:#FF1493;font-size:14px;text-align:center;">{{t(err)}}</p>
   <center><button class="btn theme-button btn-block loginn form-control" name="login" type="button" v-on:click="userLogin($event)">{{t('Login')}}</button>
   <p style="font-size:14px !important;margin-top:10px !important;"><a href="index.php?page=forgot_password" style="font-size:14px;margin-top:20px;">{{t('Forgot Password?')}}</a></p>
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