<div class="container-fluid">
<div class="row">
<div class="col-sm-6 offset-sm-3">
<div class="card shadow">
<div class="card-header theme-text bg-white border-bottom-0"><?php w("Verify Membership"); ?></div>
<div class="card-body">
<div class="alert alert-info"><strong><?php w("Hi!"); ?></strong> <?php w("Welcome to CloudFunnels. You need to verify your \${1}membership\${2}. This is a one time process. All you need to do is fill in your verification code from your CloudFunnels \${1}membership\${2}.",array("<a href='http://cloudfunnels.in/membership/members/register' target='_BLANK'>",'</a>')) ?></div>


<div class="mb-3">
<div class="input-group">
<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
<input type="email" class="form-control" id="authcustemail" placeholder="<?php w("Enter membership email"); ?>">
</div>
</div>

<div class="mb-3">
<div class="input-group">
<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-key"></i></span></div>
<input type="text" class="form-control" id="authcustordercode" placeholder="<?php w("Enter License Code"); ?>">
</div>
</div>
<p id="authvalidationerr" style="text-align:center;margin-bottom:2px;"></p>
<button class="btn theme-button form-control" onclick="authPurchaseData(this)"><?php w("Verify"); ?></button>

<p style="text-align:center;margin-top:10px;">
<?php w("Haven't register yet?"); ?><a href="http://cloudfunnels.in/membership/members/register" class="text-center new-account" target="_BLANK"> <?php w("click here"); ?></a> <?php w("or"); ?> <a href="https://teknikforce.com/support/" class="text-center new-account" target="_BLANK"><?php w("ask for help"); ?></a>
</p>
</div>
</div>
</div>
</div>
</div>