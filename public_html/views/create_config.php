<?php global $cf_available_languages; ?>
<html>
<head>
<meta charset="UTF-8">
<title> <?php w('Admin: Initialize Configuaration Files and Generate User'); ?></title>
<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
<link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
<?php echo $headers; ?>
</head>
<body class="loginbg">
<div class="container-fluid bg" id="veuauththsection">
<div class="d-flex justify-content-center h-100">
<div class="card">
<div class="card-body">
<img src="assets/img/logo-text.png" class="mx-auto d-flex img-fluid" alt="CloudFunnel" width="30%" />
  <div v-if="!language_selected">
    <h5 class="p-3 text-center text-white" style="padding-bottom:2px !important; margin-bottom: 2px !important; font-size: 15px;">{{t('Select Your Language')}}</h5>
    <form action="" method="POST">
    <div class="mb-3 mt-2">
      <select class="form-select" name="generate_translation">
          <?php
            foreach($cf_available_languages as $cf_available_languages_index=>$cf_available_languages_val)
            {
              echo "<option value='".$cf_available_languages_index."'>".$cf_available_languages_val."</option>";
            }
          ?>
      </select>
    </div>
    <?php if(!isset($_POST['generate_translation'])){ ?>
    <button type="submit" class="btn theme-button btn-block form-control" id="language_selector">Continue</button>
    <?php } ?>
    </form>
  </div>
	<!--create table-->
	<div v-if="createtable && language_selected">
    <h5 class="p-3 text-center text-white" style="padding-bottom:2px !important; margin-bottom: 2px !important; font-size: 15px;">{{t('Enter Database Credentials')}}</h5>
	<!-- <center><h4 style="padding:4px;padding-top:2px;padding-bottom:8px;margin-bottom:10px">Add Database</h4></center> -->
  <div class="mb-3" style="margin-top:2px;">
  <p style="font-size:14px !important;text-align:left !important; margin-bottom:8px !important;color:#bfbfbf !important;">{{t('Please fill in the database connection information below to get started')}}</p>
    <!-- <label for="hostname">Database Host Name</label> -->
    {{t('Database Host Name')}}
    <input type="text" class="form-control"   v-bind:placeholder="t('Enter Database Host Name')" v-model="host">
   </div>
  <div class="mb-3">
    <!-- <label for="username">Data base User Name</label> -->
    {{t('Database Username')}}
    <input type="text" class="form-control"  v-bind:placeholder="t('Enter Database User Name')" v-model="user">
  </div>
 
  <div class="mb-3">
    <!-- <label for="dbpassword">Database Password</label> -->
    {{t('Database Password')}}
    <input type="password" class="form-control"  v-bind:placeholder="t('Enter Db Password')" v-model="pass">
  </div> 
   <div class="mb-3">
    <!-- <label for="dbname">Database Name</label> -->
    {{t('Database Name')}}
    <input type="text" class="form-control"  v-bind:placeholder="t('Enter Db Name')" v-model="db">
  </div>
  <div class="mb-3">
    <!-- <label for="dbname">Port</label> -->
    {{t('Port(Enter if required)')}}
    <input type="text" class="form-control"  v-bind:placeholder="t('Enter Port Number (If required)')" v-model="port">
  </div>
  <div class="mb-3">
    <!-- <label for="prefix">Table Prefix</label> -->
    {{t('Table Prefix')}}
    <input type="text" class="form-control"  v-bind:placeholder="t('Enter Table Prefix')" v-model="prefix">
  </div>
   <span style="color:#FF1493;font-size:14px;" v-html="t(err)"></span>
   <button type="button" class="btn theme-button btn-block submitt form-control" v-on:click="createConfig($event)">{{t('Submit')}}</button>
   <p style="font-size:12px;margin-top:4px;color:#bfbfbf !important"><?php w("Don’t know how to create a database? \${1}Watch the tutorial here\${2}. Creating a database for CloudFunnels.",array('<a href="https://cloudfunnels.in/membership/members#tutorials_installation" target="_BLANK">','</a>')); ?></p>
   </div>
   <!--user register-->
   <div v-if="createuser">
   <h5 class="p-3 text-center text-white" style="padding-bottom:2px !important; margin-bottom: 2px !important; font-size: 15px;">{{t('Create an Admin User')}}</h5>
   
   <div class="mb-3" style="margin-top:2px;">
   <p style="font-size:14px !important;text-align:left !important; margin-bottom:8px !important;color:#bfbfbf !important;">{{t('Put in the details of the administrator for this site. The administrator will have full control over the site.')}}</p>
    <!-- <label for="dbpassword">Enter Name</label> -->
    {{t('Enter Name')}}
    <input type="text" class="form-control"  v-bind:placeholder="t('Enter Your Name')" v-model="username">
  </div> 
   <div class="mb-3">
    <!-- <label for="dbname">Enter Email ID</label> -->
    {{t('Enter Email ID')}}
    <input type="email" class="form-control"  v-bind:placeholder="t('Enter Email Id')" v-model="email">
  </div> 
  <div class="mb-3">
    <!-- <label for="prefix">Enter Password</label> -->
    {{t('Enter Password')}}
    <input type="password" class="form-control"  v-bind:placeholder="t('Enter Password')" v-model="userpass">
  </div>
  <div class="mb-3">
    <!-- <label for="prefix">Re-enter Password</label> -->
    {{t('Re-enter Password')}}
    <input type="password" class="form-control"  v-bind:placeholder="t('Re-Enter Password')" v-model="repass">
  </div>
  
  <span style="color:#FF1493;font-size:14px;" v-html="t(err)"></span>
   <button type="button" class="btn theme-button btn-block submitt form-control" v-on:click="createUser($event)">{{t('Register')}}</button>
  </div>
  <!-- SMTP-->
  <center>
  <h6 style="margin-bottom:0px;margin-top:10px;" v-if="current_language.length>0"><a href="https://teknikforce.com/" target="__blank">{{t('${1} @ CloudFunnels by Teknikforce',['<?php echo date('Y'); ?>'])}}</a></h6>
  <h6 style="margin-bottom:0px;margin-top:10px;" v-else><a href="https://teknikforce.com/" target="__blank"><?php echo date('Y'); ?> @ CloudFunnels by Teknikforce</a></h6>
  </center>
  <p style="margin-bottom:0px;margin-top:5px"><a href="http://teknikforce.com/support/" target="_BLANK" style="font-size:11px;">{{t('Need Support? Click here')}}</a></p>
</div></div></div></div>

<style>
.loginbg .mb-3{color:#bfbfbf !important;}
.submitt{margin-top:8px;}
</style>

<?php echo $footer; ?>
</body>
</html>