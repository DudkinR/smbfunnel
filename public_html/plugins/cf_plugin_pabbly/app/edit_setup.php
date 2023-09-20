<?php
$pabbly_webhook_data = get_option('pabbly_webhook_data') ? get_option('pabbly_webhook_data') : "";
$pabbly_create = "https://connect.pabbly.com/share-app/XRVTYQBXBWZeFAVvBFcEIwAUV1MJUwQ5Bh9URlxTUytUGQRBVhcPZQhEAGoES1AxB04AalEOBmYOGldTBVMCcQMZU0YGGQVYADNSFwZZACpdP1MkAEsFP14PBTYETAQ-AA9XcAlEBFkGC1Q2XEdTFVQNBH9WGw8vCEgAaARLUHoHSgBpUQgGJg43";
?>

<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid">Pabbly</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">  
            <div class="d-flex justify-content-end align-items-center">Setup and manage Pabbly integration requirements</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg" style="border: 1px solid #68ceec;">
                <div class="card-header bg-white border-bottom-0" style="color: #68ceec;"><?php w("Pabbly Credentials") ?></div>
                <div class="card-body">
                    <form action="" id="pabbly_form_setup" method="post" class="row bg-white p-3 d-flex justify-content-center flex-column align-items-center">
                        <input type="hidden" id="pabbly_form_ajax" value="<?=get_option('install_url')."/index.php?page=ajax"?>">
                        <div id="cfpabbly_inputs" class="text-white"></div>
                        <div class="justify-content-center d-flex mb-5">
                            <button type="button" class="mx-auto btn cfpabbly_create_inp_btn mt-2" style="background-color: #68ceec; color: #FFFFFF;"><i class="fa fa-plus" type="button"></i></button>
                        </div>
                        <div class="row">
                            <div class="col"><a class="btn btn-block" style="background-color: #68ceec; color: #FFFFFF;" href="<?= $pabbly_create ?>" target="_BLANK"><?php w("Create Pab"); ?></a></div>
                            <div class="col"><button type="submit" class="btn btn-block" style="background-color: #68ceec; color: #FFFFFF;" name="savepabblybtn" id="savepabblybtn"><?php echo t('Save pabbly'); ?></button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cfpabbly_toast_success">
    <div id="img"><i class="fas fa-check" type="button"></i></div>
    <div id="desc">Form saved successfully</div>
</div>
<div id="cfpabbly_toast_error">
    <div id="img"><i class="fas fa-exclamation" type="button"></i></div>
    <div id="desc">Something went wrong</div>
</div>

<script>
    let cfpabbly_inp_ob = new cfpabblyMangeInputs();
    <?php
    $cfpabbly_all_webook_setting = json_decode(stripcslashes($pabbly_webhook_data), true);
    if (gettype($cfpabbly_all_webook_setting) == "array" && !empty($cfpabbly_all_webook_setting)) {
        foreach ($cfpabbly_all_webook_setting as $key => $value) {
            echo "
            cfpabbly_inp_ob.createINP(`" . $value['name'] . "`, `" . $value['url'] . "`, `" . $value['status'] . "`, `" . $value['del'] . "`);
            ";
        }
    }
    ?>
    document.querySelectorAll(".cfpabbly_create_inp_btn")[0].onclick = function(eve) {
        eve.preventDefault();
        cfpabbly_inp_ob.createINP();
    };
</script>