<?php
require plugin_dir_path(dirname(__FILE__, 1)) . "/controller/CFScarcityJeet_process.php";
?>
<!DOCTYPE html>
<html lang="en">

<body>
    <form method="POST" action="" id="cf_scarcity_jeet_form">
        <section id="section01">
            <div class="container-fluid bar_wrapper">
                <h3> CF Scarcity Jeet </h3>
                <div class="row justify-content-start">
                    <div class="col-xl-6 col-lg-12 col-md-12">
                        <div class="box_label">Choose Theme</div>
                        <div class="choose_theme_wrapper">
                            <div class="position">
                                <div class=" text-start"><label for=""> Theme :</label></div>
                                <select id="cf_scarcity_jeet_images" class="form-control w-70" name="theme">
                                    <option <?php if (isset($theme) && $theme == "theme_a") {
                                                echo "selected";
                                            } ?> value="theme_a">Theme 1</option>
                                    <option <?php if (isset($theme) && $theme == "theme_b") {
                                                echo "selected";
                                            } ?> value="theme_b">Theme 2</option>
                                </select>
                            </div>
                            <div class="position">
                                <div class="text-start"><label for="">Apply To :</label></div>

                                <select id="cf_scarcity_jeet_apply_to" class="form-control w-70" name="apply_to">
                                    <option <?php if (isset($apply_to) && $apply_to == "specificpage") {
                                                echo "selected";
                                            } ?> value="specificpage">Specific page</option>
                                    <option <?php if (isset($apply_to) && $apply_to == "shortcodeOnly") {
                                                echo "selected";
                                            } ?> value="shortcodeOnly">Shortcode only</option>
                                    <option <?php if (isset($apply_to) && $apply_to == "funnels") {
                                                echo "selected";
                                            } ?> value="funnels">Specific funnels</option>
                                </select>
                            </div>
                            <div id="specificpage_div">
                                <div class="text-start"><label for="">Enter specific page URLs (one on each line):</label></div>                                            
                                <textarea name="cfscarcity_page_url" rows="4" class="form-control"><?php if (is_array($cfscarcity_page_url) || is_object($cfscarcity_page_url)) {
                                                                                                        foreach ($cfscarcity_page_url as $pageurl) {                                                                                                            
                                                                                                            echo str_ireplace(" ", "", rtrim($pageurl)) . "\r\n";
                                                                                                        }
                                                                                                    } else {                                                                                                        
                                                                                                        echo str_ireplace(" ", "", rtrim($cfscarcity_page_url)) . "\r\n";
                                                                                                    } ?></textarea>
                                
                                <div class="notes">*Does not matter which funnel is selected on these pages the message needs to be displayed</div>
                            </div>
                            <div id="specificfunnel_div">
                                <div class="text-start"><label for="">Select Funnel :</label></div>

                                <div class="dropdown">
                                    <button type="button" class="btn border btn-block dropdown-toggle" data-bs-toggle="dropdown">Select Funnels</button>
                                    <input type="hidden" name="scarcity_funnels[]" value="f">
                                    <div id="allprooducts" class="dropdown-menu btn-block ps-2" style="overflow-y: auto;max-height: 150px;">
                                        <?php
                                        $explod_funnel = explode(",", $funnels);
                                        if (in_array("f", $explod_funnel)) {
                                            if (in_array("all", $explod_funnel)) {
                                                echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="scarcity_funnels[]" value="all">All funnels </label></div>';
                                            } else {
                                                echo ' <div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="scarcity_funnels[]" value="all">All funnels </label></div>';
                                            }
                                        } else {
                                            echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="scarcity_funnels[]" value="all">All funnels </label></div>';
                                        }
                                        $fnls = get_funnels();
                                        foreach ($fnls as $f) {
                                            if (in_array("f", $explod_funnel)) {
                                                if (in_array($f['id'], $explod_funnel)) {
                                                    echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="scarcity_funnels[]" value="'  .  $f['id']  . '">' .  $f['name'] .  ' </label></div>';
                                                } else {
                                                    echo '<div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="scarcity_funnels[]" value="'  .  $f['id']  . '">' .  $f['name'] .  ' </label></div>';
                                                }
                                            } else {
                                                echo '<div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="scarcity_funnels[]" value="'  .  $f['id']  . '">' .  $f['name'] .  ' </label></div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="shortcodeOnly_div" class="text-start"><label for="">Use shortcode : </label><span onclick="copyText(`[cf_scarcity_jeet]`)"> [cf_scarcity_jeet]</span></div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12 col-md-12">

                        <div class="box_label">Bar Appearance</div>
                        <div class="bar_appearance_top">

                            <div class="bar-Gradient">
                                <div class="w-30 text-end"><label>Bar Gradient Color :</label></div>
                                <div class="d-flex justify-content-start align-items-center flex-wrap ms-2">
                                    <div class="choose_color">
                                        <input type="color" name="Bar_Gradient1" id="cf_scarcity_jeet_Select_color1" value="<?php echo ((isset($Bar_Gradient1)) ? $Bar_Gradient1 : ''); ?>">
                                        <span>Choose Color</span>
                                    </div>

                                    <div class="choose_color">
                                        <input type="color" name="Bar_Gradient1_1" id="cf_scarcity_jeet_Select_color1_1" value="<?php echo ((isset($Bar_Gradient1_1)) ? $Bar_Gradient1_1 : ''); ?>" />
                                        <span>Choose Color</span>
                                    </div>

                                </div>
                            </div>

                            <div class="catch_line">
                                <div class="w-30 text-end me-2"><label>Catch Line :</label></div>
                                <input type="text" placeholder="Offer end soon!" class="text-example w-70" name="CatchLine" id="cf_scarcity_jeet_CatchLine" oninput="myCatchLine()" value="<?php echo ((isset($CatchLine)) ? $CatchLine : ''); ?>">
                            </div>

                            <div class="d-flex justify-content-start align-items-center flex-nowrap my-3">
                                <div class="w-30 text-end me-2">
                                    <label>Catch Line Color :</label>
                                </div>
                                <div class="choose_color">
                                    <input type="color" name="catch_line_color" placeholder="Choose Any Color" id="cf_scarcity_jeet_CatchLineColor" value="<?php echo ((isset($catch_line_color)) ? $catch_line_color : ''); ?>">
                                    <span>Choose Color</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php
                    $checkedShowActionButton = '';
                    if ($show_action_button == true) {
                        "$show_action_button ";
                        $checkedShowActionButton = "checked";
                    }
                    ?>
                    <div class="col-xl-6 col-lg-12 col-md-12">
                        <div class="box_label"> <span>Show Action Button</span>
                            <input type="checkbox" <?php echo $checkedShowActionButton; ?> name="show_action_button" id="cf_scarcity_jeet_show_action_button" class="inputCheck" value="true" onclick="myFunction()" />
                            <script>
                                function myFunction() {
                                    var checkBox = document.getElementById("cf_scarcity_jeet_show_action_button");
                                    var text = document.getElementById("text");
                                    if (checkBox.checked == true) {
                                        text.style.display = "block";
                                    } else {
                                        text.style.display = "none";
                                    }
                                }
                            </script>
                        </div>
                        <?php $textVisibility = '';
                        if ($show_action_button == true) {
                            $textVisibility = "display:block";
                        } else {
                            $textVisibility = "display:none";
                        } ?>
                        <div class="bar_appearance_middle" id="text" style="<?php echo $textVisibility; ?>">
                            <div class="d-flex justify-content-start align-items-center flex-nowrap my-3">
                                <div class="w-30 text-end me-2">
                                    <label>Background Color :</label>
                                </div>
                                <div class="choose_color">
                                    <input type="color" name="action_Background_color" id="cf_scarcity_jeet_ActionBackgroundColor" placeholder="Choose Background Color" value="<?php echo ((isset($action_Background_color)) ? $action_Background_color : ''); ?>" class="colorField jscolor">
                                    <span>Choose Color</span>
                                </div>
                            </div>

                            <div class="catch_line">
                                <div class="w-30 text-end me-2"><label>Button Text :</label></div>
                                <input type="text" class="text-example w-70" name="action_button_text" id="cf_scarcity_jeet_ActionButtonText" placeholder="Enter Button Text" value="<?php echo ((isset($action_button_text)) ? $action_button_text : ''); ?>" oninput="myButtonText()">
                            </div>

                            <div class="d-flex justify-content-start align-items-center flex-nowrap my-3">
                                <div class="w-30 text-end me-2">
                                    <label>Button Color :</label>
                                </div>
                                <div class="choose_color">
                                    <input type="color" name="action_button_text_color" id="cf_scarcity_jeet_ActionButtonTextColor" placeholder="Choose Button Text Color" value="<?php echo ((isset($action_button_text_color)) ? $action_button_text_color : ''); ?>">
                                    <span>Choose Color</span>
                                </div>
                            </div>

                            <div class="catch_line">
                                <div class="w-30 text-end me-2"><label>Link to : </label></div>
                                <input type="link" class="text-example w-70" name="button_link" id="cf_scarcity_jeet_ActionButtonLink" placeholder="Enter Button Link Here" value="<?php echo ((isset($button_link) && filter_var($button_link, FILTER_VALIDATE_URL)) ? $button_link : '') ?>" />
                            </div>
                        </div>
                    </div>
                    <?php
                    $checkedProductBoxShow = '';
                    if ($product_box_show == true) {
                        "$product_box_show";
                        $checkedProductBoxShow = "checked";
                    }
                    ?>
                    <div class="col-xl-6 col-lg-12 col-md-12">
                        <div class="box_label"> <span>Timer Configuration</span></div>

                        <div class="timer_configuration_wrapper">
                            <div class="position">
                                <div class="w-30 text-end"><label for="">Select Time Zone :</label></div>
                                <select class="form-control w-70" name="timezone" id="cf_scarcity_jeet_TimeZone">
                                    <option <?php if (isset($timezone) && $timezone == "-720") {
                                                echo "selected";
                                            } ?> value="-720">(GMT -12:00) Eniwetok, Kwajalein</option>
                                    <option <?php if (isset($timezone) && $timezone == "-660") {
                                                echo "selected";
                                            } ?> value="-660">(GMT -11:00) Midway Island, Samoa</option>
                                    <option <?php if (isset($timezone) && $timezone == "-600") {
                                                echo "selected";
                                            } ?> value="-600">(GMT -10:00) Hawaii</option>
                                    <option <?php if (isset($timezone) && $timezone == "-590") {
                                                echo "selected";
                                            } ?> value="-590">(GMT -9:30) Taiohae</option>
                                    <option <?php if (isset($timezone) && $timezone == "-540") {
                                                echo "selected";
                                            } ?> value="-540">(GMT -9:00) Alaska</option>
                                    <option <?php if (isset($timezone) && $timezone == "-480") {
                                                echo "selected";
                                            } ?> value="-480">(GMT -8:00) Time (US &amp; Canada)</option>
                                    <option <?php if (isset($timezone) && $timezone == "-420") {
                                                echo "selected";
                                            } ?> value="-420">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                    <option <?php if (isset($timezone) && $timezone == "-360") {
                                                echo "selected";
                                            } ?> value="-360">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                    <option <?php if (isset($timezone) && $timezone == "-300") {
                                                echo "selected";
                                            } ?> value="-300">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                    <option <?php if (isset($timezone) && $timezone == "-290") {
                                                echo "selected";
                                            } ?> value="-290">(GMT -4:30) Caracas</option>
                                    <option <?php if (isset($timezone) && $timezone == "-240") {
                                                echo "selected";
                                            } ?> value="-240">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                    <option <?php if (isset($timezone) && $timezone == "-230") {
                                                echo "selected";
                                            } ?> value="-230">(GMT -3:30) Newfoundland</option>
                                    <option <?php if (isset($timezone) && $timezone == "-180") {
                                                echo "selected";
                                            } ?> value="-180">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                    <option <?php if (isset($timezone) && $timezone == "-120") {
                                                echo "selected";
                                            } ?> value="-120">(GMT -2:00) Mid-Atlantic</option>
                                    <option <?php if (isset($timezone) && $timezone == "-60") {
                                                echo "selected";
                                            } ?> value="-60">(GMT -1:00) Azores, Cape Verde Islands</option>
                                    <option <?php if (isset($timezone) && $timezone == "+0") {
                                                echo "selected";
                                            } ?> value="+0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                    <option <?php if (isset($timezone) && $timezone == "+60") {
                                                echo "selected";
                                            } ?> value="+60">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
                                    <option <?php if (isset($timezone) && $timezone == "+120") {
                                                echo "selected";
                                            } ?> value="+120">(GMT +2:00) Kaliningrad, South Africa</option>
                                    <option <?php if (isset($timezone) && $timezone == "+180") {
                                                echo "selected";
                                            } ?> value="+180">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                    <option <?php if (isset($timezone) && $timezone == "+210") {
                                                echo "selected";
                                            } ?> value="+210">(GMT +3:30) Tehran</option>
                                    <option <?php if (isset($timezone) && $timezone == "+240") {
                                                echo "selected";
                                            } ?> value="+240">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                    <option <?php if (isset($timezone) && $timezone == "+270") {
                                                echo "selected";
                                            } ?> value="+270">(GMT +4:30) Kabul</option>
                                    <option <?php if (isset($timezone) && $timezone == "+300") {
                                                echo "selected";
                                            } ?> value="+300">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                    <option <?php if (isset($timezone) && $timezone == "+330") {
                                                echo "selected";
                                            } ?> value="+330">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                    <option <?php if (isset($timezone) && $timezone == "+345") {
                                                echo "selected";
                                            } ?> value="+345">(GMT +5:45) Kathmandu, Pokhara</option>
                                    <option <?php if (isset($timezone) && $timezone == "+360") {
                                                echo "selected";
                                            } ?> value="+360">(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                    <option <?php if (isset($timezone) && $timezone == "+390") {
                                                echo "selected";
                                            } ?> value="+390">(GMT +6:30) Yangon, Mandalay</option>
                                    <option <?php if (isset($timezone) && $timezone == "+420") {
                                                echo "selected";
                                            } ?> value="+420">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                    <option <?php if (isset($timezone) && $timezone == "+480") {
                                                echo "selected";
                                            } ?> value="+480">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                    <option <?php if (isset($timezone) && $timezone == "+525") {
                                                echo "selected";
                                            } ?> value="+525">(GMT +8:45) Eucla</option>
                                    <option <?php if (isset($timezone) && $timezone == "+540") {
                                                echo "selected";
                                            } ?> value="+540">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                    <option <?php if (isset($timezone) && $timezone == "+570") {
                                                echo "selected";
                                            } ?> value="+570">(GMT +9:30) Adelaide, Darwin</option>
                                    <option <?php if (isset($timezone) && $timezone == "+600") {
                                                echo "selected";
                                            } ?> value="+600">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                    <option <?php if (isset($timezone) && $timezone == "+630") {
                                                echo "selected";
                                            } ?> value="+630">(GMT +10:30) Lord Howe Island</option>
                                    <option <?php if (isset($timezone) && $timezone == "+660") {
                                                echo "selected";
                                            } ?> value="+660">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                    <option <?php if (isset($timezone) && $timezone == "+690") {
                                                echo "selected";
                                            } ?> value="+690">(GMT +11:30) Norfolk Island</option>
                                    <option <?php if (isset($timezone) && $timezone == "+720") {
                                                echo "selected";
                                            } ?> value="+720">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                                    <option <?php if (isset($timezone) && $timezone == "+765") {
                                                echo "selected";
                                            } ?> value="+765">(GMT +12:45) Chatham Islands</option>
                                    <option <?php if (isset($timezone) && $timezone == "+780") {
                                                echo "selected";
                                            } ?> value="+780">(GMT +13:00) Apia, Nukualofa</option>
                                    <option <?php if (isset($timezone) && $timezone == "+840") {
                                                echo "selected";
                                            } ?> value="+840">(GMT +14:00) Line Islands, Tokelau</option>
                                </select>
                            </div>


                            <div class="catch_line">
                                <div class="w-30 text-end me-2"><label>Time End Date/ Time :</label></div>
                                <input type="datetime-local" name="end_date" id="cf_scarcity_jeet_EndDate" value="<?php echo $end_date; ?>" class="text-example w-70">
                            </div>

                            <div class="d-flex justify-content-start align-items-center flex-nowrap my-3">
                                <div class="w-30 text-end me-2">
                                    <label>Text Color : </label>
                                </div>
                                <div class="choose_color">
                                    <input type="color" name="timer_text_color" id="cf_scarcity_jeet_TimerTextColor" placeholder="Choose Text Color" value="<?php echo ((isset($timer_text_color)) ? $timer_text_color : ''); ?>" class="colorField jscolor">
                                    <span>Choose Color</span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-xl-6 col-lg-12 col-md-12">

                        <div class="box_label"> <span>Show Image</span>
                            <input type="checkbox" <?php echo $checkedProductBoxShow; ?> name="product_box_show" id="cf_scarcity_jeet_ProductBoxShow" onclick="showFunction()" class="inputCheck" value="true">
                            <script>
                                function showFunction() {
                                    var checkBox = document.getElementById("cf_scarcity_jeet_ProductBoxShow");
                                    var text = document.getElementById("sImage");
                                    if (checkBox.checked == true) {
                                        text.style.display = "block";
                                    } else {
                                        text.style.display = "none";
                                    }
                                }
                            </script>
                        </div>
                        <?php $productVisibility = '';
                        if ($product_box_show == true) {
                            $productVisibility = "display:block";
                        } else {
                            $productVisibility = "display:none";
                        }
                        ?>
                        <div id="sImage" style="<?php echo $productVisibility; ?>" class="bar_appearance_bottom">
                            <div class="catch_line">
                                <div class="w-30 text-end me-2"><label>Image Path :</label></div>
                                <input type="text" class="text-example w-70" name="product_box_image" id="cf_scarcity_jeet_ProductBoxImage" value="<?php echo ((isset($product_box_image)) ? $product_box_image : ''); ?>" />
                                <button class="btn btn-primary" type="button" onclick="cfscarcity_Geturl('#cf_scarcity_jeet_ProductBoxImage', false)">Uplaod</button>
                            </div>

                            <div class="catch_line">
                                <div class="w-25 text-end me-2"><label>Link to :</label></div>
                                <input type="text" placeholder="#" name="product_link" value="<?php echo ((isset($product_link)) ? $product_link : ''); ?>" class="text-example w-70">
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12 col-md-12">
                        <div class="box_label effect_class">
                            <div class="position mb-0">
                                <div class="text-end"><label for="">Effect :</label></div>
                                <select class="form-control w-75" name="effect">
                                    <?php
                                    $expiry_action = array(
                                        "none" => "No Effect", "slide" => "Slide In", "fade" => "Fade In", "bounce" => "Bounce", "flash" => "Flash", "pulse" => "Pulse",
                                        "rubberBand" => "Rubber Band", "shake" => "Shake", "headShake" => "Head Shake", "swing" => "Swing", "tada" => "Tada", "wobble" => "Wobble", "jello" => "Jello",
                                        "bounceIn" => "Bounce In", "bounceInDown" => "Bounce In Down", "bounceInLeft" => "Bounce In Left", "bounceInRight" => "Bounce In Right", "bounceInUp" => "Bounce In Up",
                                        "fadeInDown" => "Fade In Down", "fadeInDownBig" => "Fade In Down Big", "fadeInLeft" => "Fade In Left", "fadeInLeftBig" => "Fade In Left Big", "fadeInRight" => "Fade In Right",
                                        "fadeInRightBig" => "Fade In Right Big", "fadeInUp" => "Fade In Up", "flip" => "Flip", "flipInX" => "Flip In X", "flipInY" => "Flip In Y", "lightSpeedIn" => "Light Speed In",
                                        "rotateIn" => "Rotate In", "rotateInDownLeft" => "Rotate In Down Left", "rotateInDownRight" => "Rotate In Down Right", "rotateInUpLeft" => "Rotate In Up Left",
                                        "rotateInUpRight" => "Rotate In Up Right", "rollIn" => "Roll In", "zoomIn" => "Zoom In", "zoomInDown" => "Zoom In Down", "zoomInLeft" => "Zoom In Left",
                                        "zoomInRight" => "Zoom In Right", "zoomInUp" => "Zoom In Up", "slideInDown" => "Slide In Down", "slideInLeft" => "Slide In Left", "slideInRight" => "Slide In Right"
                                    );
                                    foreach ($expiry_action as $key => $value) {
                                    ?>
                                        <option value="<?php echo $key; ?>" <?php if (isset($effect) && $effect == $key) {
                                                                                echo "selected";
                                                                            } ?>><?php echo $value; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <button href="#" class="save_setting" type="submit" id="cf_scarcity_jeet_btn-primary" name="Submit" value="Save Changes">Save Settings</button>
                    </div>

                </div>
            </div>
        </section>

    </form>
</body>

</html>

<?php
//here we are imporing  cf_media
cf_media();
?>