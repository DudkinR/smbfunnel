<?php
global $mysqli;
global $dbpref;
global  $app_variant;
$app_variant = isset($app_variant) ? $app_variant : "shopfunnels";
$mail_ob = $this->load('form_controller');

if ($app_variant == "shopfunnels") {
    $students = "Customer";
} elseif ($app_variant == "cloudfunnels") {
    $students = "Member";
} elseif ($app_variant == "coursefunnels") {
    $students = "Student";
}
$total = 0;
$table2 = $dbpref . 'mail_templates';
$get_record_query = $mysqli->query("SELECT `id` FROM `" . $mysqli->real_escape_string($table2) . "`");
if (mysqli_num_rows($get_record_query) > 0) {
    $total = mysqli_num_rows($get_record_query);
}

$email_sub = "Your order - {orderid} is successfully placed";
$email_content = "<p>Hi {name},</p><p>We have received your order - <strong>{orderid}</strong>, We will place your order in sort time.</p><p>Your Order of - {Products} amount of the order is {amount}.</p><p>Thanks</p>";

?>
<input type="hidden" id="cfshipping_ajax" value="<?= get_option('install_url') . "/index.php?page=ajax"; ?>" />
<div class="container-fluid" id="template_table">
    <div class="row page-titles mb-4">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"> <?php w('Email Templates'); ?></h4>
        </div>

        <div>
            <button class="btn theme-button new_save_option" id="add_template"><i class="fas fa-plus"></i> Add Email Template</button>
        </div>
    </div>

    <div class="card pb-2  br-rounded" id="hidecard1">
        <div class="sforiginal p-2">

            <div class="row">
                <!-- <div class="col-lg-2 col-md-12 mb-3">
                        <?php echo createSearchBoxBydate(); ?>
                    </div> -->
                <div class="col-lg-4 col-md-12">
                    <?php echo showRecordCountSelection(); ?>
                </div>
                <div class="mb-3 col-lg-4 col-md-12">
                    <div class="input-group input-group-sm mb-3 float-end">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search With Name, Cost, Product Code') ?>" onkeyup="searchOrder(this.value)">
                    </div>
                </div>

            </div>

        </div>
        <div class="card-body p-0" id="hidecard2">
            <div class="row membercontainer">
                <div class="col-sm-12">
                    <?php
                    ?>
                    <div id="crdcontainer">
                        <div id="container_singledata_table">
                            <div class="table-responsive sforiginal">
                                <table class="table table-striped" id="option-table">
                                    <thead>
                                        <tr class="salerow">
                                            <th>#</th>
                                            <th><?php w('Template Name'); ?></th>
                                            <th><?php w('Subject'); ?></th>
                                            <th><?php w('Action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="keywordsearchresult">
                                        <!-- keyword search -->
                                        <?php
                                        $page_count = 0;
                                        if (isset($_GET['page_count'])) {
                                            $page_count = (int)$_GET['page_count'];
                                        }

                                        $all_data = $mail_ob->mail_template($page_count);
                                        $last_id = 0;
                                        $count = ($page_count < 1) ? 1 : $page_count;
                                        $records_to_show = get_option('qfnl_max_records_per_page');
                                        $records_to_show = (int) $records_to_show;
                                        $count = ($count * $records_to_show) - $records_to_show;

                                        ++$count;
                                        foreach ($all_data as $all_data2) { ?>
                                            <tr>
                                                <td> <?php echo $count; ?></td>
                                                <td><?php echo ucwords($all_data2->template_name); ?></td>
                                                <td> <?php echo $all_data2->subject; ?></td>
                                                <td>
                                                    <table class="actionedittable">
                                                        <tbody>
                                                            <tr>
                                                                <td><button idvalue="495" isvalid="1" class="btn unstyled-button edit_option" data-id="<?php echo $all_data2->id; ?>" data-original-title="Edit Option"><i class="fa fa-pencil-alt"></i></button></td>
                                                                <td><button type="button" class="btn unstyled-button" shipped="0" idvalue="495" data-bs-toggle="tooltip" title="" onclick="delete_mail(<?php echo $all_data2->id; ?>)" data-original-title="Delete Option"><i class="fa fa-trash text-danger"></i></button></td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        <?php
                                            $count++;
                                        } ?>
                                        <tr>
                                            <td colspan="12" class="total-data">
                                                <center> Total : <?= $total; ?></center>
                                            </td>
                                        </tr>
                                        <!-- /keyword search -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-4">
                                <div class="col-sm-6 mt-2">
                                    <?php
                                    $next_page_url = "index.php?page=message_templates&page_count";
                                    $page_count = ($page_count < 2) ? 0 : $page_count;
                                    echo createPager($total, $next_page_url, $page_count);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" id="template_add">
    <div class="row page-titles mb-4">
        <div class="col-md-11 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"> <?php w('Email Template'); ?></h4>
        </div>
        <div><span id="gobacktable" class="gobacktable" onClick="window.location.reload();" style="float: right; color: rgb(31, 87, 202); font-size: 16px; margin-bottom: 10px; cursor: pointer;"><i class="fas fa-arrow-alt-circle-left"></i> Go&nbsp;back</span><br></div>
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
                        <label for=""><?php w('Template Name'); ?> </label>
                        <input type="text" name="email_name" placeholder="<?php w('Enter Email Template Name'); ?>" id="email_name" class="form-control">
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
                <hr class="bg-primary">
                <button class="btn btn-primary" id="save_template"><?php w('Save Template'); ?></button>

            </div>
        </div>
    </div>
    <br><br><br><br><br>

</div>
<?php register_tiny_editor(array("#email_content")) ?>
<script>
    function searchOrder(search) {
        var ob = new OnPageSearch(search, "#keywordsearchresult");
        ob.url = window.location.href;
        ob.search();
    }
    $(document).ready(function() {
        let id = '';
        $("#template_add").hide();
        $(document).on("click", "#add_template", function() {
            $("#template_table").hide();
            $("#template_add").show();
        });
        $(document).on("click", ".edit_option", function() {
            $("#template_table").hide();
            $("#template_add").show();
            id = $(this).data('id');
            $.ajax({
                url: '<?php echo get_option("install_url") . "/index.php?page=ajax" ?>',
                type: "POST",
                data: {
                    action: 'edit_template',
                    id: id
                },
                success: function(data) {
                    data = jQuery.parseJSON(data);
                    $("#email_name").val(data['template_name']);
                    $("#email_subject").val(data['subject']);
                    tinymce.get('email_content').setContent(data['content']);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
        $(document).on("click", "#save_template", function() {
            let error = 0;
            var name = $("#email_name").val();
            var subject = $("#email_subject").val();
            tinymce.triggerSave();
            var content = $("#email_content").val();
            if (name == "") {
                alert("Please enter template name");
                error = 1;
            }
            if (subject == "") {
                alert("Please enter email subject");
                error = 1;
            }
            if (content == "") {
                alert("Please enter email content");
                error = 1;
            }
            if (error == 0) {
                $.ajax({
                    url: '<?php echo get_option("install_url") . "/index.php?page=ajax" ?>',
                    type: "POST",
                    data: {
                        action: 'save_template',
                        id: id,
                        name: name,
                        subject: subject,
                        content: content
                    },
                    success: function(data) {
                        if (data == '200') {
                            location.reload();
                        } else if (data == '201') {
                            alert('Template name already exits, Please enter unique name');
                        } else {
                            alert('Something wrong');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    });

    function delete_mail(id) {
        var url = $("#cfshipping_ajax").val();
        Swal.fire({
            title: t("Are you sure?"),
            text: t("You won't be able to revert this!"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: t("Yes, delete it!"),
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        action: "delete_template",
                        id: id,
                    },
                    success: function(data) {
                        if (data == "200") {
                            location.reload();
                        } else {
                            alert('Something wrong');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    },
                });
            }
        });
    }
</script>