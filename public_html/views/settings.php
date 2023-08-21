<?php
global $cf_available_languages;
$user_type = $_SESSION['user_plan_type' . $site_token_for_dashboard];
$data = cf_enc(get_option('valid_user_data'), 'decrypt');
?>
<div class="container-fluid">
  <div class="card pb-2  br-rounded" id="hidecard1">
    <div class="row d-flex">
      <div class="col-md-3 cf-vertical-cont">
        <div class="cf-vertical-tabs py-4">
          <ul class="nav nav-tabs d-block border-0" role="tablist">


            <li class="nav-item  ">
              <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">
                <i class="fas fa-cog pr-2"></i><?php w("General Settings"); ?></a>
            </li>

            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#salessetting" role="tab">
                <i class="fas fa-cog pr-2"></i><?php w("Sales Settings"); ?></a>
            </li>

            <li class="nav-item">
              <a class="nav-link " data-bs-toggle="tab" href="#membershipsetting" role="tab">
                <i class="fas fa-cog pr-2"></i><?php w("Membership Settings"); ?></a>
            </li>

            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#fourzerofourpage" role="tab">
                <i class="fas fa-cog pr-2"></i><?php w("404 Page Setup"); ?></a>
            </li>

            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#setuperror" role="tab">
                <i class="fas fa-cog pr-2"></i><?php w("Membership Error Texts"); ?></a>
            </li>

          </ul>
        </div>
      </div>
      <div class="col-md-9">
        <div class="card-body pb-2" id="hidecard2">

          <div class="settingpage">
            <form action="" method="post" enctype="multipart/form-data">
              <div class="tab-content ">
                <div id="home" class="tab-pane fade in active show ">
                  <h3 class="theme-text  text-sm-center text-md-left"><?php w("General Settings"); ?></h3>
                  <?php if ($user_type == 2) : ?>
                    <div class="mb-3">
                      <label><?php w("Free Version"); ?></label>
                      <p class="form-control"><?php w("Current free version"); ?> <?php echo t(get_option('qfnl_current_version')); ?></p>
                    </div>
                    <div class="from-group mb-3">
                      <div class="input-group">
                        <p class="form-control" id="message-purchased"><?php w("If you have purchased elite or pro upgrade, then click here to upgrade"); ?></p>
                        <div class="input-group-append">
                          <button class="btn btn-outline-success" style="cursor:pointer" onclick="upradeUser(this)" type="button"><i class="far fa-arrow-alt-circle-up"></i> Upgrade</button>
                        </div>
                      </div>
                    </div>
                  <?php else : ?>
                    <div class="mb-3">
                      <label><?php w("Version"); ?></label>
                      <p class="form-control"><?php w("Current version"); ?> <?php echo t(get_option('qfnl_current_version')); ?></p>
                    <?php endif; ?>
                    </div>
                    <div class="mb-3">
                      <label><?php w("Installation URL"); ?></label>
                      <input type="url" class="form-control" placeholder="<?php w("Enter installation url"); ?>" name="install_url" value="<?php echo get_option('install_url'); ?>">
                    </div>
                    <div class="mb-3" data-bs-toggle="tooltip" title="<?php w("If you turn off the router mode it will not work for the funnels whic were created with router mode and vice versa"); ?>">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><input type="checkbox" name="qfnl_router_mode" onchange="routerConfirmDeletion(this)" <?php if (get_option('qfnl_router_mode') == '1') {
                                                                                                                                                  echo "checked";
                                                                                                                                                } ?>></span>
                        </div>
                        <p class="form-control"><?php w("Use Router Mode For Funnels"); ?></p>
                      </div>
                    </div>
                    <div class="mb-3">
                      <div class="input-group" data-bs-toggle='tooltip' title='<?php w("If you are turning the setting on then please make sure the protocol for the `Installation URL` is also `https` and you are allowed to use HTTPS on this site. (Please clear cache for the funnels after turning it on.)"); ?>'>
                        <div class="input-group-prepend"><span class='input-group-text'><input type="checkbox" name="force_https_funnels_pages" <?php if (get_option('force_https_funnels_pages') == '1') {
                                                                                                                                                  echo "checked";
                                                                                                                                                } ?>></span></div>
                        <p class="form-control"><?php w("Force Loading Pages with HTTPS"); ?>
                        <p>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label><?php w("Select Language"); ?></label>
                      <select name="app_language" class="form-select">
                        <?php
                        foreach ($cf_available_languages as $cf_available_languages_index => $cf_available_languages_value) {
                          $temp_lang_selected = "";
                          if (get_option('app_language') == $cf_available_languages_index) {
                            $temp_lang_selected = " selected";
                          }
                          echo "<option value='" . $cf_available_languages_index . "'" . $temp_lang_selected . ">" . $cf_available_languages_value . "</option>";
                        }
                        ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <input type="checkbox" name="disable_page_preview" value=1 <?php if (get_option('disable_page_preview')) {
                                                                                          echo "checked";
                                                                                        } ?>>
                          </span>
                        </div>
                        <p class="form-control">Disable page preview to increase performance</p>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label><?php w("Default SMTP"); ?></label>
                      <select name="default_smtp" class="form-select">
                        <option value="php" <?php if (get_option('default_smtp') == 'php') {
                                              echo "selected";
                                            } ?>><?php w("Hosting Mailer(PHP Mailer)"); ?></option>
                        <?php
                        $smtp_ob = $info['load']->loadSMTP();
                        $smtps = $smtp_ob->getSMTP('', 1);/*load all*/
                        if ($smtps) {
                          while ($r = $smtps->fetch_object()) {
                            $checked = "";
                            if ($r->id == get_option('default_smtp')) {
                              $checked = "selected";
                            }
                            echo "<option value='" . $r->id . "' " . $checked . ">" . $r->title . "</option>";
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><input type="checkbox" <?php if (get_option('spin_email')) {
                                                                                  echo "checked";
                                                                                } ?> name='spin_email'></span>
                        </div>
                        <p class="form-control"><?php w("Use spinner for emails"); ?></p>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label><?php w("CRON Command For Email Sequence"); ?></label>
                      <input type="text" class="form-control" value="wget -O - <?php $apiurl = get_option('install_url');
                                                                                $apiurl .= "/index.php?page=schedule_api_runserver";
                                                                                echo $apiurl; ?> >/dev/null 2>&1">
                    </div>
                    <div class="mb-3">
                      <label><?php w("IPN Security Key"); ?></label>
                      <input type="text" class="form-control" name="ipn_token" id="ipn_token" placeholder="<?php w("Enter Security Key"); ?>" value="<?php echo get_option('ipn_token'); ?>">
                    </div>
                </div>
                <div id="salessetting" class="tab-pane fade">
                  <h3 class="theme-text"><?php w("Notification setting for sales."); ?></h3>
                  <div class="mb-3">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><input type="checkbox" <?php if (get_option('sales_notif_email_to_admin_check')) {
                                                                                echo "checked";
                                                                              } ?> name='sales_notif_email_to_admin_check'>
                        </span>
                      </div>
                      <p class="form-control"><?php w("Send notification for sales."); ?>
                      </p>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label><?php w("Choose SMTP"); ?></label>
                    <select name="sales_notif_email_smtp" class="form-select">
                      <option value="php" <?php if (get_option('default_smtp') == 'php') {
                                            echo "selected";
                                          } ?>><?php w("Hosting Mailer(PHP Mailer)"); ?></option>
                      <?php
                      $smtp_ob = $info['load']->loadSMTP();
                      $smtps = $smtp_ob->getSMTP('', 1);/*load all*/
                      if ($smtps) {
                        while ($r = $smtps->fetch_object()) {
                          $checked = "";
                          if ($r->id == get_option("sales_notif_email_smtp")) {
                            $checked = "selected";
                          }
                          echo "<option value='" . $r->id . "' " . $checked . ">" . $r->title . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label><?php w("Choose Product"); ?></label>
                    <div class="dropdown">
                      <button type="button" class="btn border btn-block dropdown-toggle" data-bs-toggle="dropdown">
                        <?php w("Select Product"); ?>
                      </button>
                      <div id="allprooducts" class="dropdown-menu btn-block  pl-2" style="overflow-y: auto;max-height: 150px;">
                        <!-- <label>&nbsp;<input class="me-3" type="checkbox" name="sales_notif_email_product[]" value="all">all</label> -->
                        <?php

                        $products_ob = $info['load']->loadSell();
                        $products = $products_ob->getProductIdTitle();/*load all*/

                        if (is_object($products)) {
                          if ($products->num_rows > 0) {

                            $send_product_email = explode(",", get_option("sales_notif_email_products"));

                            while ($r = $products->fetch_object()) {
                              if ($send_product_email[0] != null) {
                                if (in_array($r->id, $send_product_email)) {
                                  echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="sales_notif_email_product[]" value="'  .  $r->id  . '">' .  $r->title .  ' </label></div>';
                                } else {
                                  echo ' <div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="sales_notif_email_product[]" value="' .  $r->id  . '" >' .  $r->title .  ' </label></div>';
                                }
                              } else {
                                echo ' <div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="sales_notif_email_product[]" value="' .  $r->id  . '" checked>' .  $r->title .  ' </label></div>';
                              }
                            }
                          }
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label><?php w("Add emails where to send notification (One on each line)"); ?></label>
                    <textarea rows="2" class="form-control" name="sales_notif_email_to_admin" id="sales_notif_email_to_admin" placeholder="<?php w("Add email"); ?>"><?php $sales_notif_email_admin = get_option("sales_notif_email_to_admin");
                                                                                                                                                                      $emails = explode(",", $sales_notif_email_admin);
                                                                                                                                                                      foreach ($emails as $email) {
                                                                                                                                                                        echo str_ireplace(" ", "", trim($email)) . "\r\n";
                                                                                                                                                                      } ?></textarea>
                  </div>
                  <div class="mb-3">
                    <label><?php w("Do you want OTP verification for COD"); ?>?</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><input type="checkbox" id="disable_otp_for_cod" <?php if (get_option('disable_otp_for_cod')) {
                                                                                                          echo "checked";
                                                                                                        } ?> name='disable_otp_for_cod'>
                        </span>
                      </div>
                      <p class="form-control"><?php w("Disable OTP verification FOR COD"); ?>
                      </p>
                    </div>
                  </div>
                  <div class="mb-3" id="open_cod_box" style="display:<?php if (get_option('disable_otp_for_cod')) {
                                                                        echo "none";
                                                                      } ?>">
                    <div class="mb-3">
                      <label for="cod_store_name"><?php w("Enter Store Name For COD"); ?></label>
                      <input id="cod_store_name" type="text" class="form-control" value="<?php echo stripslashes(stripslashes(get_option('cod_store_name'))); ?>" name='cod_store_name'>
                    </div>
                    <div class="mb-3">
                      <label for="cod_store_message"><?php w("Enter Message For COD"); ?></label>
                      <textarea id="cod_store_message" class="form-control" name="cod_store_message"><?php echo str_replace("\\\\r\\\\n", "", str_replace("\&quot;", "", str_replace("\\r\\n", "", htmlentities(get_option('cod_store_message'))))); ?></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="cod_store_image"><?php w("Enter Store Image For COD"); ?></label>
                      <div class="input-group mb-3">
                        <input type="text" class="form-control" name="cod_store_image" id="cod_store_image" placeholder="<?php w("Store Image"); ?>" value="<?php echo stripslashes(stripslashes(get_option('cod_store_image'))); ?>">
                        <div class="input-group-append">
                          <button class="btn btn-success" type="button" onclick="sfGetProductImage('#cod_store_image', false)"><?php w("Get Image"); ?></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <h3 class="theme-text mt-5">Cash on delivery email content setup</h3>
                  <div class="mb-3 mt-4">
                    <div class="alert alert-info">Use the variable <strong>{otp}</strong> any where to replace the OTP.</div>
                    <label>Email Title</label>
                    <input type="text" placeholder="Enter Email Title" class="form-control" name="cod_otp_email_title" value="<?php echo htmlentities(get_option('cod_otp_email_title')) ?>">
                  </div>
                  <div class="mb-3">
                    <title>Email Email Content</title>
                    <textarea id="cod_otp_email_content" name="cod_otp_email_content"><?php echo str_replace("\\\\r\\\\n", "", str_replace("\&quot;", "", str_replace("\\r\\n", "", htmlentities(get_option('cod_otp_email_content'))))); ?></textarea>
                    <?php register_tiny_editor('#cod_otp_email_content'); ?>
                  </div>
                </div>
                <div id="membershipsetting" class="tab-pane fade">
                  <h3 class="theme-text "><?php w("Membership Settings"); ?></h3>
                  <div class="mb-3">
                    <label><?php w("Valid Regular-Expression For Membership Passwords"); ?></label>
                    <input type="text" class="form-control" name="secure_password_regex" id="secure_password_regex" placeholder="<?php w("Enter Regular Expression"); ?>" value="<?php echo htmlentities(base64_decode(get_option('secure_password_regex'))); ?>">
                  </div>
                  <div class="mb-3">
                    <?php
                    $fpwdemail = get_option('members_fpwd_mail');
                    $fpwdemail = explode("@fpwdemlbrk@", $fpwdemail);
                    ?>
                    <label><?php w("Forgot Password Email Title For Members"); ?></label>
                    <input type="text" name="fpwdemltitle" class="form-control" value="<?php
                                                                                        echo  str_replace("\\", "", $fpwdemail[0]); ?>" placeholder="<?php w("Enter Title"); ?>">
                    <label><?php w("Forgot Password Email Content For Members"); ?></label>
                    <textarea name="members_fpwd_mail" class="form-control" placeholder="<?php w("Enter Message"); ?>"><?php if (isset($fpwdemail[1])) {
                                                                                                                          echo str_replace("\\", "", $fpwdemail[1]);
                                                                                                                        } ?></textarea>
                  </div>
                  <div class="mb-3">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <input type="checkbox" name="qfnl_cancel_membership_withsales" <?php if (get_option('qfnl_cancel_membership_withsales') == '1') {
                                                                                            echo "checked";
                                                                                          } ?>>
                        </span>
                      </div>
                      <p class="form-control"><?php w("Cancel membership on purchase cancelation"); ?></p>
                    </div>
                  </div>
                  <h3 class="theme-text mt-5">Free sign up email content</h3>
                  <div class="mb-3">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <input type="checkbox" name="qfnl_free_signup_email" <?php if (get_option('qfnl_free_signup_email') == '1') {
                                                                                  echo "checked";
                                                                                } ?>>
                        </span>
                      </div>
                      <p class="form-control"><?php w("Send Email On Free Signup"); ?></p>
                    </div>
                  </div>
                  <div class="mb-3 mt-4">
                    <div class="alert alert-info">Use the variable <strong>{name}</strong>, <strong>{email}</strong> and <strong>{funnel}</strong> for name, email and funnel name.</div>
                    <label>Email Title</label>
                    <input type="text" placeholder="Enter Email Title" class="form-control" name="free_singn_email_title" value="<?php echo htmlentities(get_option('free_singn_email_title')) ?>">
                  </div>
                  <div class="mb-3">
                    <title>Email Email Content</title>
                    <textarea id="free_singn_email_content" name="free_singn_email_content"><?php echo str_replace("\\\\r\\\\n", "", str_replace("\&quot;", "", str_replace("\\r\\n", "", htmlentities(get_option('free_singn_email_content'))))); ?></textarea>
                    <?php register_tiny_editor('#free_singn_email_content'); ?>
                  </div>
                </div>
                <div id="fourzerofourpage" class="tab-pane fade">
                  <h3 class="theme-text "><?php w("404 Page Setup"); ?></h3>
                  <div class="mb-3">
                    <label><?php w("Select 404 page theme"); ?></label>
                    <div class="input-group">
                      <select class="form-select" name="default_404_page_template" id="default_404_page_template">
                        <?php
                        $unwanterpagetemplate = get_option('default_404_page_template');
                        for ($i = 1; $i <= 2; $i++) {
                          $unwanterpagetemplateselected = ($unwanterpagetemplate == $i) ? "selected" : "";
                          echo "<option value='" . $i . "' " . $unwanterpagetemplateselected . ">" . t("Template \${1}", array($i)) . "</option>";
                        }
                        ?>
                      </select>
                      <div class="input-group-append" style="cursor:pointer;" onclick="window.open('<?php echo get_option('install_url') . '/' . time() . '/?loadtemplate='; ?>'+document.getElementById('default_404_page_template').value,'_blank')"><span class="input-group-text">
                          <i class="fas fa-eye" style="color:#4F5467 !important;"></i>
                        </span></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label><?php w("Add Your Go-back Page URL"); ?></label>
                    <input type="url" class="form-control" placeholder="<?php w("Enter Go-Back URL"); ?>" value="<?php echo get_option('default_404_page_url'); ?>" name="default_404_page_url">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Add Your Go-back Page Button Text"); ?></label>
                    <input type="text" class="form-control" placeholder="<?php w("Enter Go-Back Button Text"); ?>" value="<?php echo get_option('default_404_page_button_text'); ?>" name="default_404_page_button_text">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Add Your Preferred Logo To Display"); ?></label>
                    <div class="input-group">
                      <textarea class="form-control pimg404" style="resize:none;" disabled><?php if (filter_var(get_option('default_404_page_logo'), FILTER_VALIDATE_URL)) {
                                                                                              echo get_option('default_404_page_logo');
                                                                                            } else {
                                                                                              echo t("No Image Uploaded.");
                                                                                            } ?></textarea>
                      <?php if (filter_var(get_option('default_404_page_logo'), FILTER_VALIDATE_URL)) {
                        echo "<div class='input-group-append'><span class='input-group-text'><a href='" . get_option('default_404_page_logo') . "' target='_BLANK'><i class='fas fa-eye'></i></span></a></div>";
                      } ?>

                      <div class="input-group-append 404imgurl"><span class="input-group-text" style="cursor:pointer;"><i class="fas fa-arrow-circle-up"></i>&nbsp;<?php w("Upload"); ?></span></div>
                      <div class="input-group-append deleteinvalidimage"><span class="input-group-text" style="cursor:pointer;"><i class="fas fa-trash"></i>&nbsp; <?php w("Delete"); ?></span></div>
                      <input type="file" accept="image/*" name="default_404_page_logo" id="default_404_page_logo" style="display:none;">
                      <input type="hidden" name="hiddeninvalidfileurl" id="hiddeninvalidfileurl" value="<?php echo get_option('default_404_page_logo'); ?>">
                    </div>
                    <script>
                      document.getElementsByClassName("deleteinvalidimage")[0].onclick = function() {
                        if (confirmDeletion()) {
                          document.getElementById('hiddeninvalidfileurl').value = '';
                          document.getElementsByClassName("pimg404")[0].innerHTML = t("Please Save To Delete The Image Completely.");
                        }
                      };
                      document.getElementsByClassName("404imgurl")[0].onclick = function() {
                        var invalidfile_doc = document.getElementById("default_404_page_logo");
                        invalidfile_doc.click();
                        invalidfile_doc.onchange = function() {
                          document.getElementsByClassName("pimg404")[0].innerHTML = invalidfile_doc.value;
                        };
                      };
                    </script>
                  </div>
                </div>
                <div id="setuperror" class="tab-pane fade">
                  <h3 class="theme-text "><?php w("Setup Membership Error Texts"); ?></h3>
                  <div class="mb-3">
                    <label><?php w("Display Alert For Insecure Passwords In Membership"); ?></label>
                    <input type="text" class="form-control" name="not_secure_password_alert" id="not_secure_password_alert" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('not_secure_password_alert')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Authentication Error Alert For Forgot Password"); ?></label>
                    <input type="text" class="form-control" name="fpwd_auth_error" id="fpwd_auth_error" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('fpwd_auth_error')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error For Passwords That Don’t Match"); ?></label>
                    <input type="text" class="form-control" name="pwd_mismatch_err" id="pwd_mismatch_err" placeholder="<?php w("Enter Text To Display") ?>" value="<?php echo htmlentities(get_option('pwd_mismatch_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error To Display For The User, Trying To Re-Register"); ?></label>
                    <input type="text" class="form-control" name="re_register_err" id="re_register_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('re_register_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error For Invalid Email"); ?></label>
                    <input type="text" class="form-control" name="invalid_email_err" id="invalid_email_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('invalid_email_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error For Already  Available Email"); ?></label>
                    <input type="text" class="form-control" name="already_email_err" id="already_email_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('already_email_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error for unauthorized access"); ?></label>
                    <input type="text" class="form-control" name="un_auth_access_err" id="un_auth_access_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('un_auth_access_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error For Non-existing Users"); ?></label>
                    <input type="text" class="form-control" name="usr_does_not_exist_err" id="usr_does_not_exist_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('usr_does_not_exist_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Invalid Login Credentials Error"); ?></label>
                    <input type="text" class="form-control" name="invalid_login_credntials_err" id="invalid_login_credntials_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('invalid_login_credntials_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error For Unsent Emails"); ?></label>
                    <input type="text" class="form-control" name="snd_email_err" id="snd_email_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('snd_email_err')); ?>">
                  </div>
                  <div class="mb-3">
                    <label><?php w("Error For Membership Cancelation"); ?></label>
                    <input type="text" class="form-control" name="qfnl_membership_cancelation_message" id="snd_email_err" placeholder="<?php w("Enter Text To Display"); ?>" value="<?php echo htmlentities(get_option('qfnl_membership_cancelation_message')); ?>">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <input type="submit" name="save_settings" id="savesetting" class="btn theme-button btnclr float-right" style="margin-bottom:10px;" value="<?php w("Save Setting"); ?>" onsubmit="return false">
              </div>

              <br>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<script>
  function upradeUser(t) {
    var request = new ajaxRequest();
    let data1 = <?= $data ?>;
    let check = document.getElementById("message-purchased");
    check.innerHTML = 'checking and upgrading...';
    t.innerHTML = `<i class="fas fa-spinner fa-spin"></i>`;
    var email = data1.custemail;
    var ordercode = data1.license;
    var data = {
      chkforauthvalidationpucrhase: 1,
      auth_valid_user: email,
      auth_valid_order_code: ordercode,
    };
    request.postRequestCb("req.php", data, function(res) {
      res = res.trim();
      if (res == "1") {
        t.innerHTML = `<i class="far fa-arrow-alt-circle-up"></i> Upgraded`;
        check.innerHTML = "Successfully validated, redirecting you in a moment.";
        window.location.reload();
      } else {
        if (res === "0") {
          check.innerHTML = "Unable to verify you";
          window.location.reload();
        }
      }
    });
  }

  function sfGetProductImage(selector, html) {
    try {
      //here calling open media
      openMedia(function(content) {
        try {
          document.querySelectorAll(selector)[0].value = content;
        } catch (err) {
          console.log(err);
        }
      }, html);
    } catch (err) {
      console.log(err)
    }
  }
  $(document).ready(function() {
    $("#disable_otp_for_cod").on("change", function() {
      if ($(this).is(":checked")) {
        $("#open_cod_box").hide();
      } else {
        $("#open_cod_box").show();
      }
    });
  });
</script>
<script>
  function routerConfirmDeletion(doc) {
    if (!confirmDeletion()) {
      if (doc.checked) {
        doc.checked = false;
      } else {
        doc.checked = true;
      }
    }
  }
</script>
<style>
  .nav-tabs a.nav-link {
    height: 100%;
    vertical-align: middle;
    border-radius: 0px;
  }
</style>