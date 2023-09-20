<?php
global $mysqli;
global $dbpref;
global  $app_variant;
$app_variant = isset($app_variant) ? $app_variant : "shopfunnels";
$email_ob = $this->load('form_controller');
$all_data = $email_ob->mail_template();
// echo '<pre>';
// print_r($all_data[0]->template_name);
// exit;
$emailer_id = $_GET['email'];

if ($app_variant == "shopfunnels") {
    $students = "Customer";
} elseif ($app_variant == "cloudfunnels") {
    $students = "Member";
} elseif ($app_variant == "coursefunnels") {
    $students = "Student";
}
$sender_array = get_smtps();

$email_sub = "Your order - {orderid} is successfully placed";
$email_content = "<p>Hi {name},</p><p>We have received your order - <strong>{orderid}</strong>, We will place your order in sort time.</p><p>Your Order of - {Products} amount of the order is {amount}.</p><p>Thanks</p>";


?>
<input type="hidden" value="<?php echo $emailer_id; ?>" id="emailer_id" />
<input type="hidden" id="cfshipping_ajax" value="<?= get_option('install_url') . "/index.php?page=ajax"; ?>" />
<div class="container-fluid">    
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"> <?php w('Send Email'); ?></h4>
        </div>

    </div>
    <div id="cfdisp-collectionformdiv" class="px-4">
        <div id="cfdisp-success-class" tabindex="-1">
        </div>
        <div id="cfdisp-error-class" tabindex="-1">
        </div>
        <div class="email_tabs">
            <div class="email_tabs-stage">
                <div id="cfemail_tab-1" class="email_tabs_navvar">
                    <input type="hidden" value="saveemailsetting_ajax" name="action">
                    <h5><?php w('Email Message'); ?></h5>
                    <div class="mb-3">
                        <label for=""><?php w('Email Templates'); ?> </label>
                        <select name="email-templates" id="email-templates" class="form-control" onchange="templates()">
                            <?php foreach ($all_data as $data) {
                                echo '<option value="' . $data->id . '">' . $data->template_name . '</option>';
                            }   ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for=""><?php w('Select Sender'); ?> </label>
                        <select name="sender" id="sender" class="form-control">
                            <?php foreach ($sender_array as $value) {
                                echo '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for=""><?php w('Email Subject'); ?> </label>
                        <input type="text" name="email_subject" value="<?= $email_sub ?>" placeholder="<?php w('Enter Email Subject for order template'); ?>" id="email_subject" class="form-control">
                        <div class="text-start mt-1">
                            <span href="javascript:void(0)" class="btn btn-info btn-sm" data-bs-toggle="collapse" data-target="#cfdisp-demo1"><?php w('Shortcodes'); ?></span>
                            <div id="cfdisp-demo1" class="collapse">
                                <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                    <strong><?php w('Shortcodes'); ?></strong>:: <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{shipping_method}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{shipping_method}</span> <?php w('for the which method applied for shipping'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{tracking_number}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{tracking_number}</span> <?php w('for the tracking number'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{carrier_service}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{carrier_service}</span> <?php w('for the carrier service'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{carrier_url}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{carrier_url}</span> <?php w('for the carrier URL'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{amount}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{amount}</span> <?php w('for the amount of order'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{currency}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{currency}</span> <?php w('for the currency of order'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{products}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{products}</span> <?php w('for the ordered producs'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{orderid}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{orderid}</span> <?php w('for the order'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{name}</span> <?php w('for name'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{email}</span> <?php w('for email'); ?>
                                </p>
                                <p style="font-size: 13px !important;font-weight:500">
                                    <strong><?php w('Example'); ?></strong>:: <br>
                                    <?php w('Steav your order - {orderid} of amount {amount} is placed'); ?> <br>
                                    <?php w('Steav your order - r1ctr5454s of amount $520.00 is placed'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for=""><?php w('Email Content'); ?></label>
                        <textarea name="email_content" id="email_content"><?php echo str_replace("\\\\r\\\\n", "", str_replace("\&quot;", "", str_replace("\\r\\n", "", htmlentities($email_content)))); ?></textarea>
                        <div class="text-start py-2">
                            <strong class="btn btn-info btn-sm" data-bs-toggle="collapse" data-target="#cfdisp-demo2"><?php w('Shortcodes'); ?></strong>
                            <p class="" style="font-size: 13px !important; opacity: 0.8;">
                            <div class="text-start py-3 mt-1">
                                <div id="cfdisp-demo2" class="collapse" style="font-size: 13px !important;font-weight:500">

                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{shipping_method}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{shipping_method}</span> <?php w('for the which method applied for shipping'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{tracking_number}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{tracking_number}</span> <?php w('for the tracking number'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{carrier_service}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{carrier_service}</span> <?php w('for the carrier service'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{carrier_url}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{carrier_url}</span> <?php w('for the carrier URL'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{amount}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{amount}</span> <?php w('for the amount of order'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{currency}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{currency}</span> <?php w('for the currency of order'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{products}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{products}</span> <?php w('for the ordered producs'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{orderid}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{orderid}</span> <?php w('for the order'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{name}</span> <?php w('for name'); ?> <br>
                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{email}</span> <?php w('for email'); ?>

                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="loading-image" id="loading-image"></div>
                <hr class="bg-primary">
                <button class="btn btn-primary  send_mail"><?php w('Send Mail'); ?></button>
               
            </div>
        </div>
    </div>
    <br><br><br><br><br>

</div>
<?php register_tiny_editor(array("#email_content")) ?>
<script>
    var email_subject = '';
    var email_content = '';

    function templates() {
        let value = $("#email-templates").val();
        $.ajax({
            url: '<?php echo get_option("install_url") . "/index.php?page=ajax" ?>',
            type: "POST",
            data: {
                action: 'edit_template',
                id: value
            },
            success: function(data) {
                data = jQuery.parseJSON(data);
                $("#email_subject").val(data['subject']);
                tinymce.get('email_content').setContent(data['content']);
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>