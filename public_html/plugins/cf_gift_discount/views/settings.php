<?php
global $mysqli;
global  $app_variant;
$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
$discounts=$this->load('discount');

if( $app_variant == "shopfunnels" ){
    $students="Customer";

}
elseif( $app_variant == "cloudfunnels" ){
    $students="Member";

}
elseif( $app_variant == "coursefunnels" ){
    $students="Student";

}

$data = $discounts->getSettings();
if( $data )
{
    $gift = json_decode($data['giftcard'],true);
    $discounts = json_decode($data['discount'],true);
    $dlabeltext =$discounts['label_text'];
    $dbuttontext =$discounts['button_text'];
    $dbuttoncolor =$discounts['button_color'];
    $dbuttonbcolor =$discounts['button_bcolor'];
    $dresult_bcolor =$discounts['result_bcolor'];
    $dresult_color =$discounts['result_color'];
    $derror_color =$discounts['error_color'];
    $demail_sub =$data['demail_subject'];
    $demail_content =$data['demail_content'];
    $dcustomcss = str_ireplace( "\\r\\n", "\r\n",$discounts['customCSS'] );

    $glabeltext =$gift['label_text'];
    $gbuttontext =$gift['button_text'];
    $gbuttoncolor =$gift['button_color'];
    $gbuttonbcolor =$gift['button_bcolor'];
    $gcustomcss =str_ireplace("\\r\\n","\r\n",$gift['customCSS']);
    $gresult_bcolor =$gift['result_bcolor'];
    $gresult_color =$gift['result_color'];
    $gerror_color = $gift['error_color'];
    $gemail_sub =$data['gemail_subject'];
    $gemail_content =$data['gemail_content'];
}else{
    $gemail_sub="Someone Sent {initial_value} {currency} Gift card";
    $demail_sub="Someone Sent {percentage}% Discount Code";
    $glabeltext ="Enter Gift Code";
    $gbuttontext = "Apply";
    $gbuttoncolor ="#ffffff";
    $gbuttonbcolor ="#023059";
    $gerror_color ="#ff0000";
    $gresult_bcolor ='#ceecf2';
    $gemail_content="<p>Hi {name},</p><p>Your  {initial_value} {currency} gift card is active. Keep this email or write down your gift card number.</p><p><strong>{giftcode}</strong></p><p>If you did not raise the request please write to our support team.</p><p>Thanks</p>";
    $demail_content="<p>Hi {name},</p><p>Your  {percentage} discount code is active. Keep this email or write down your discount code number.</p><p><strong>{discount}</strong></p><p>If you did not raise the request please write to our support team.</p><p>Thanks</p>";
    $gresult_color ='#777';
    $gcustomcss = "";
    $dlabeltext ="Enter Discount Code";
    $dbuttontext = "Apply";
    $dbuttoncolor ="#ffffff";
    $dbuttonbcolor ="#023059";
    $dresult_bcolor ='#ceecf2';
    $dresult_color ='#777';
    $derror_color ="#ff0000";
    $dcustomcss = "";

}


?>
<div class="container-fluid">   
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"><img src='<?=CFGIFT_DISCOUNT_PLUGIN_URL_URL?>/assets/img/f7.png' alt='Gift' />  <?php w('Gift cards and discount settings'); ?></h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center"><?php w('Settings'); ?></div>
        </div>
    </div>
    <div id="cfdisp-collectionformdiv"  class="px-4">
        <div id="cfdisp-success-class" tabindex="-1" >
        </div>
        <div id="cfdisp-error-class"  tabindex="-1">
        </div>
        <div class="cfgift_tabs">
                <ul class="cfgift_tabs-nav">
                    <li class="cfgift_tab-active"><a href="#cfgift_tab-1"><?php w('Gift Card Settings'); ?></a></li>
                    <li ><a href="#cfgift_tab-2"><?php w('Discount Settings'); ?></a></li>
                </ul>
                <div class="cfgift_tabs-stage">
                    <form action="" class="cfdisp_setting-form">
                        <div id="cfgift_tab-1" class="cfgift_tabs_navvar">
                            <input type="hidden" value="savegiftsetting_ajax" name="action" >
                                <h5><?php w('Gift Card Settings'); ?></h5>
                            <div class="mb-3">
                                <label for=""><?php w('Email Subject'); ?> </label>
                                <input type="text" name="gemail_subject" value="<?=$gemail_sub?>" placeholder="<?php w('Enter Email Subject for gift card template');?>" id="email_subject" class="form-control" >
                                <div class="text-start mt-1">
                                    <span href="javascript:void(0)" class="btn btn-info btn-sm" data-bs-toggle ="collapse" data-target="#cfdisp-demo1"><?php w('Shortcodes'); ?></span>
                                    <div id="cfdisp-demo1" class="collapse" >
                                        <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                            <strong><?php w('Shortcodes'); ?></strong>:: <br>
                                            <span class="text-info cfdisp_cursor" onclick="copyText(`{initial_value}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{initial_value}</span> <?php w('for the price value of gift card'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{currency}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{currency}</span> <?php w('for the currency symbol'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{giftcode}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{giftcode}</span> <?php w('for the gift card'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{name}</span> <?php w('for name'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{email}</span> <?php w('for email'); ?>
                                        </p>
                                        <p style="font-size: 13px !important;font-weight:500">
                                        <strong><?php w('Example'); ?></strong>:: <br>
                                            <?php w('John Doe Sent {initial_value} {currency} Gift card'); ?> <br>
                                            <?php w('John Doe Sent 100 USD Gift Card'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" >
                                <label for=""><?php w('Email Content'); ?></label>
                                <textarea name="gemail_content" id="cfdis_gmail_content" ><?php echo str_replace("\\\\r\\\\n","",str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities($gemail_content)))); ?></textarea>
                                <div class="text-start py-2">
                                    <strong class="btn btn-info btn-sm" data-bs-toggle ="collapse" data-target="#cfdisp-demo2"><?php w('Shortcodes'); ?></strong> 
                                    <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                        <div class="text-start py-3 mt-1">
                                            <div id="cfdisp-demo2" class="collapse" style="font-size: 13px !important;font-weight:500">
                                            <span class="text-info cfdisp_cursor" onclick="copyText(`{initial_value}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{initial_value}</span> <?php w('for the price value of gift card'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{currency}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{currency}</span> <?php w('for the currency symbol'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{giftcode}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{giftcode}</span> <?php w('for the gift card'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{name}</span> <?php w('for name'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{email}</span> <?php w('for email'); ?>

                                            </div>
                                        </div>
                                    </p>
                                </div>
                            </div>
                            <br>
                            <h5 class="text-dark"><?php w('Manage Gift Card shortcode'); ?></h5> <hr class="bg-primary">
                            <br>
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Input Label Text'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[label_text]" value="<?= ($glabeltext)? $glabeltext:''; ?>" class="form-control form-control-sm" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Button Text'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[button_text]" value="<?= ($gbuttontext)? $gbuttontext:''; ?>" class="form-control form-control-sm" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Button Background Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[button_bcolor]" value="<?= ($gbuttonbcolor)? $gbuttonbcolor:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Button Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[button_color]" value="<?= ($gbuttoncolor)? $gbuttoncolor:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Error Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[error_color]" value="<?= ($gerror_color)? $gerror_color:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Result Box Background Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[result_bcolor]" value="<?= ($gresult_bcolor)? $gresult_bcolor:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Result Box Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="giftcard[result_color]" value="<?= ($gresult_color)? $gresult_color:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5"><?php w('Custom CSS'); ?></label>
                                        <div class="col text-end">
                                            <textarea name="giftcard[customCSS]" id="" rows="4" class="form-control form-control-sm"><?=($gcustomcss)? $gcustomcss:''; ?></textarea>
                                            <div class="text-start">
                                                <p class="mt-0" style="font-size: 12px !important; opacity: 0.6;">
                                                    **<?php w('Use base selector name'); ?> 
                                                    <strong>.this-form</strong> <br><?php w('Example'); ?> : <br> 
                                                    <strong>.this-form input[type=text] <br>
                                                    {border-radous: 5px;}</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cfgift_tab-2" class="cfgift_tabs_navvar">    
                            <div class="mb-3">
                                <label for=""><?php w('Email Subject'); ?> </label>
                                <input type="text" value="<?=$demail_sub ?>" placeholder="<?php w('Enter Email Subject for discount template'); ?>" name="demail_subject"  id="email_subject" class="form-control" >
                                <div class="text-start mt-1">
                                    <span href="javascript:void(0)" class="btn btn-info btn-sm" data-bs-toggle ="collapse" data-target="#cfdisp-demo5"><?php w('Shortcodes'); ?></span>
                                    <div id="cfdisp-demo5" class="collapse" >
                                        <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                            <strong><?php w('Shortcodes'); ?></strong>:: <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{currency}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{currency}</span>> <?php w('for the currency symbol'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{name}</span>> <?php w('for name'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{email}</span>> <?php w('for email'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{discount_code}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{discount_code}</span>> <?php w('for email discount code'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{percentage}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{percentage}</span>> <?php w('for percentage'); ?>
                                        </p>
                                        <p style="font-size: 13px !important;font-weight:500">
                                        <strong><?php w('Example'); ?></strong>:: <br>
                                            <?php w('John Doe Sent {percentage}% Discount Code'); ?> <br>
                                            <?php w('John Doe Sent 10% Discount Code'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for=""><?php w('Email Content'); ?></label>
                                <textarea  name="demail_content" id="cfdis_dgmail_content" ><?php echo str_replace("\\\\r\\\\n","",str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities($demail_content)))); ?></textarea>
                                <div class="text-start py-2">
                                    <strong class="btn btn-info btn-sm" data-bs-toggle ="collapse" data-target="#cfdisp-demo3"><?php w('Shortcodes'); ?></strong> 
                                    <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                        <div class="text-start py-3 mt-1">
                                            <div id="cfdisp-demo3" class="collapse" style="font-size: 13px !important;font-weight:500">
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{currency}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{currency}</span> <?php w('for the currency symbol'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{percentage}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{percentage}</span> <?php w('for the discount percentage'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{discount_code}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{discount_code}</span> <?php w('for the discount code'); ?><br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{name}</span> <?php w('for name'); ?> <br>
                                                <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle ="tooltip" title="Copy to clipboard" >{email}</span> <?php w('for email'); ?>
                                            </div>
                                        </div>
                                    </p>
                                </div>
                            </div>
                            <br>
                            <h5 class="text-dark"><?php w('Manage Discount shortcode'); ?></h5> <hr class="bg-primary">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Input Label Text'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[label_text]" value="<?= ($dlabeltext)? $dlabeltext:''; ?>" class="form-control form-control-sm" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Button Text'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[button_text]" value="<?= ($dbuttontext)? $dbuttontext:''; ?>" class="form-control form-control-sm" >
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Button Background Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[button_bcolor]" value="<?= ($dbuttonbcolor)? $dbuttonbcolor:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Button Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[button_color]" value="<?= ($dbuttoncolor)? $dbuttoncolor:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Error Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[error_color]" value="<?= ($derror_color)? $derror_color:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Result Box Background Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[result_bcolor]" value="<?= ($dresult_bcolor)? $dresult_bcolor:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5 align-self-center"><?php w('Result Box Color'); ?></label>
                                        <div class="col text-end">
                                            <input name="discount[result_color]" value="<?= ($dresult_color)? $dresult_color:''; ?>" class="jscolor form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5"><?php w('Custom CSS'); ?></label>
                                        <div class="col text-end">
                                            <textarea name="discount[customCSS]" id="" rows="4" class="form-control form-control-sm"><?=($dcustomcss)? $dcustomcss:''; ?></textarea>
                                            <div class="text-start">
                                                <p class="mt-0" style="font-size: 12px !important; opacity: 0.6;">
                                                    **<?php w('Use base selector name'); ?> 
                                                    <strong>.this-form</strong> <br><?php w('Example'); ?> : <br> 
                                                    <strong>.this-form input[type=text] <br>
                                                    {border-radous: 5px;}</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="bg-primary">
                        <button class="btn btn-primary  cfdisp-save-setting"><?php w('Save'); ?></button>
                    </form>
                </div>
            </div>
    </div>
    <br><br><br><br><br>

</div>


<div id="cfdisp-sfsnackbar"><?php w('added successfully',[ucfirst(t($students))]); ?>.</div>
<input type="hidden" id="discountGiftbaseurl" value="<?=CFGIFT_DISCOUNT_PLUGIN_URL;?>/">
<input type="hidden" id="giftdiscountajaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<input type="hidden" id="giftdiscountinstall_url" value="<?=get_option('install_url')?>/">
<?php register_tiny_editor(array("#cfdis_gmail_content","#cfdis_dgmail_content")) ?>