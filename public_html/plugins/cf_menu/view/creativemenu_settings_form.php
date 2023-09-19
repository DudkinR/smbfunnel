<?php
global $mysqli;
global $dbpref;
global $app_variant;
$table = $dbpref . "cfmenu";

if (isset($_GET['cfmenu_id'])) {
    if (is_numeric($_GET['cfmenu_id'])) {
        $menu_id = $_GET['cfmenu_id'];
    } else {
        header('location: ' . get_option('install_url') . "/index.php?page=no-permission");
        die();
    }

    $menu_id = $mysqli->real_escape_string($menu_id);
    $result = $mysqli->query("SELECT * from `" . $table . "` WHERE id='$menu_id'");
    $data = $result->fetch_assoc();

    $cfmenu_name = $data['form_name'];
    $cfmenu_desc = $data['form_desc'];
    $cfmenu_custom_url = $data['custom_url'];
    $cfmenu_dragndrop = $data['dropndown'];
    $logo_details = json_decode($data['logo_details'], true);
    $manage_styles = json_decode($data['manage_styles'], true);
    $extra_buttons = json_decode($data['extra_buttons'], true);
    $cfmenu_choose_theme = $data['choose_theme'];
} else {
    $menu_id = 0;
    $logo_details = array(
        'cfmenu_logo_type' => 0,
        'cfmenu_logo_title' => '',
        'cfmenu_logo_click_url' => '#',
        'cfmenu_logo_color' => '#2B50FF',
        'cfmenu_logo_font_size' => 20,
        'cfmenu_logo_height' => '',
        'cfmenu_logo_width' => '',
        'cfmenu_logo_padding' => '',
        'cfmenu_logo_margin' => '',
        'cfmenu_logo_img_source' => '',
        'cfmenu_logo_img_height' => '',
        'cfmenu_logo_img_width' => '',
        'cfmenu_logo_img_padding' => '',
        'cfmenu_logo_img_margin' => '',
        'cfmenu_logo_img_border_radius' => 0
    );

    $manage_styles = array(
        'cfmenu_navbar_gradient_background_drop' => '',
        'cfmenu_navbar_gradient_background1' => '#FFFFFF',
        'cfmenu_navbar_gradient_background2' => '#FFFFFF',
        'cfmenu_navbar_gradient_background_combination_drop' => 0,
        'cfmenu_manage_nav_border_radius' => 0,
        'cfmenu_manage_nav_border_radius_drop' => 'px',
        'cfmenu_navbar_position' => 'left',
        'cfmenu_manage_nav_textcolor' => '#2B50FF',
        'cfmenu_manage_nav_hovercolor' => '#1D36AD',
        'cfmenu_manage_nav_hoverbackcolor' => '#FFFFFF',
        'cfmenu_navbar_slogan_drop' => 0,
        'cfmenu_manage_slogan_text' => '',
        'cfmenu_manage_slogan_color' => '#F0F0F0',
        'cfmenu_manage_slogan_font_size' => 16,
        'cfmenu_manage_html_icon' => '',
        'cfmenu_manage_icon_color' => '#2B50FF',
        'cfmenu_manage_icon_back' => '#FFFFFF',
        'cfmenu_manage_icon_padding' => 0,
        'cfmenu_manage_nav_items_font_size' => 18,
        'cfmenu_customCSS' => ''
    );
    $extra_buttons = array(
        'cfmenu_navbar_search_drop' => 0,
        'cfmenu_manage_search_icon' => '<i class="fas fa-search"></i>',
        'cfmenu_manage_search_text' => "",
        'cfmenu_navbar_cart_drop' => 0,
        'cfmenu_manage_cart_icon' => '<i class="fas fa-shopping-cart"></i>',
        'cfmenu_manage_cart_text' => "",
        'cfmenu_navbar_admin_drop' => 0,
        'cfmenu_manage_admin_icon' => '<i class="fas fa-user"></i>',
        'cfmenu_manage_admin_text' => "",
    );
    $cfmenu_name = "";
    $cfmenu_desc = "";
    $cfmenu_custom_url = "";
    $cfmenu_dragndrop = "";
    $cfmenu_choose_theme = "theme1";
}
$app_variant_arr = t('funnel');
if ($app_variant == 'shopfunnels') {
    $app_variant_arr = t('store');
}
$forms_ob = $this->load('form_controller');
?>
<div class="container-fluid bg-white">
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4 shadow p-3 rounded">
                <h6 class="mb-0"><?php w('CF Menu Settings'); ?></h6>
                <div><?php w('Create, edit, and manage forms'); ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php
        if ($menu_id != 0) { ?>
            <div class="col-md-12 mt-4">
                <div class="d-sm-flex align-items-center mb-4 shadow p-3 rounded alert alert-success w-100">
                <?php w('Use the shortcode'); ?> &nbsp;<span class="text-success"><strong style="cursor:pointer;" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Click to copy'); ?>" onclick="cfmenuCopyText(`[cfmenu id=<?= $menu_id ?>]`)">[cfmenu id=<?= $menu_id ?>]</strong></span> &nbsp; <?php w("to show the popup on any $app_variant_arr page"); ?>.
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <div id="preview_nav"></div>
    </div>

    <form id="cfmenu_settings_form_data" method="post">
        <input type="hidden" id="cfmenu_ajax" value="<?= get_option('install_url') . "/index.php?page=ajax"; ?>" />
        <input type="hidden" name="cfmenu_ajax_insertUrl" id="cfmenu_ajax_insertUrl" value="<?php echo get_option('install_url') . "/index.php?page=cfmenu_form_details&cfmenu_id="; ?>" />
        <input type="hidden" name="cfmenu_update_insert" value="<?= ((isset($menu_id) && $menu_id != 0) ? 'update' : 'create') ?>" />
        <input type="hidden" name="cfmenu_form_id" value="<?= $menu_id ?>" />

        <div class="row p-3">
            <div class="container-fluid" id="cfmenu_wizard">
                <ul class="nav nav-tabs nav-justified">
                    <li><a class="active nav-item nav-link" href="#cfmenu_description_step" aria-controls="cfmenu_description_step" role="tab" data-bs-toggle="tab"><?php w('Form & Description'); ?></a></li>
                    <li><a class="nav-item nav-link" href="#cfmenu_navbar_step" aria-controls="cfmenu_navbar_step" role="tab" data-bs-toggle="tab"><?php w('Create Navbar'); ?></a></li>
                    <li><a class="nav-item nav-link" href="#cfmenu_themes_step" aria-controls="cfmenu_themes_step" role="tab" data-bs-toggle="tab"><?php w('Themes'); ?></a></li>
                    <li><a class="nav-item nav-link" href="#cfmenu_logo_step" aria-controls="cfmenu_logo_step" role="tab" data-bs-toggle="tab"><?php w('Logo'); ?></a></li>
                    <li><a class="nav-item nav-link" href="#cfmenu_slogan_step" aria-controls="cfmenu_slogan_step" role="tab" data-bs-toggle="tab"><?php w('Slogan & Mobile Screen'); ?></a></li>
                    <li><a class="nav-item nav-link" href="#cfmenu_styles_step" aria-controls="cfmenu_styles_step" role="tab" data-bs-toggle="tab"><?php w('Styles'); ?></a></li>
                    <?php if($app_variant === 'shopfunnels') { ?><li><a class="nav-item nav-link" href="#cfmenu_extra_buttons_step" aria-controls="cfmenu_extra_buttons_step" role="tab" data-bs-toggle="tab"><?php w('Extra Buttons'); ?></a></li><?php } ?>
                </ul>

                <div class="tab-content d-flex justify-content-center">
                    <div class="col-md-12 col-lg-12 col-sm-12 row tab-pane show active fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_description_step">
                        <h5><?php w('Form & Description'); ?>
                            <hr class="bg-info">
                        </h5>
                        <div class="w-100 row pl-3">
                            <label class="col-form-label"><?php w('Enter form name'); ?></label>
                            <div class="col text-right">
                                <input type="text" placeholder="<?php w('Enter form name'); ?>" name="cfmenu_form_name" value="<?= $cfmenu_name ?>" class="form-control mb-4" required />
                            </div>
                        </div>
                        <div class="w-100 pl-3 row">
                            <label class="col-form-label"><?php w('Enter description'); ?></label>
                            <div class="text-right col">
                                <input type="text" placeholder="<?php w('Enter description'); ?>" name="cfmenu_form_desc" value="<?= $cfmenu_desc ?>" class="form-control mb-4" />
                            </div>
                        </div>
                    </div>

                    <!-- ================================= -->
                    <!-- ========= Navbar Setup ========== -->
                    <!-- ================================= -->
                    <div class="col-md-12 col-lg-12 col-sm-12 tab-pane fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_navbar_step">
                        <!-- ======================================= -->
                        <!-- ==== Funnel, pages and Custom URL ===== -->
                        <!-- ======================================= -->
                        <div class="row bg-white rounded p-3">
                            <div class="col-md-6 col-lg-6">
                                <h5><?php w('Manage Navbar Items'); ?>
                                    <hr class="bg-info">
                                </h5>
                                <div class="w-100">
                                    <label class="col-form-label mb-1"><?php w("Select your $app_variant_arr"); ?></label>

                                    <div class="cfmenuShowFunnelsData">
                                    <?php
                                    $get_all_funnels = get_funnels();
                                    //print_r($get_all_funnels);
                                    foreach ($get_all_funnels as $key => $value) { ?>
                                        <div class="form-check">
                                            <label class="form-check-label" for="get_funnel_id_<?= $value['funnel_id'] ?>">
                                                <input type="radio" id="get_funnel_id_<?= $value['funnel_id'] ?>" class="form-check-input" onclick="funnelpostData(this.id)" name="get_funnel_option" value="<?= $value['name'] ?>">
                                                <?= $value['name'] ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    </div>

                                    <div class="container-fluid showPagesOfFunnel"></div>
                                    <div class="container-fluid showPagesOfFunnel2"></div>
                                </div>

                                <div class="row bg-white p-3">
                                    <div class="w-100">
                                        <div id="cfmenu_inputs" class="text-white"></div>
                                    </div>
                                    <button type="button" class="mx-auto btn btn-primary cfmenu_create_inp_btn mt-2"><i class="fa fa-plus" type="button"></i></button>
                                </div>
                            </div>

                            <!-- =================================== -->
                            <!-- =========== Drag N Drop =========== -->
                            <!-- =================================== -->
                            <div class="col-md-6 col-lg-6">
                                <div class="drag-n-drop">
                                    <ul class="cfmenu_normal_ul"><?php
                                                                    if (gettype(json_decode(stripcslashes($cfmenu_dragndrop), true)) == "array") {
                                                                        for ($i = 0; $i < count(json_decode(stripcslashes($cfmenu_dragndrop), true)); $i++) {
                                                                            $forms_ob->exatractNavChildren(json_decode(stripcslashes($cfmenu_dragndrop), true), $i);
                                                                        }
                                                                    }
                                                                    ?></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================================== -->
                    <!-- ========= Select Themes ========== -->
                    <!-- ================================== -->
                    <div class="col-md-12 col-lg-12 col-sm-12 tab-pane fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_themes_step">
                        <h5><?php w('Select Theme'); ?>
                            <hr class="bg-info">
                        </h5>
                        <div class="col-12">
                            <div class="row">
                                <div class="form-control border-0 h5" style="margin-left: 10px;"><?php w('Simple Menu'); ?></div>
                                <div class="d-flex flex-wrap justify-content-center cfmenu_choose_theme">
                                    <div class="col-12 text-center my-2">
                                        <div class="image-radio" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Theme 1'); ?>">
                                            <img src="<?= plugins_url('../assets/img/theme1.png', __FILE__) ?>" alt="Theme Name" class="img-responsive" />
                                            <input type="radio" value="theme1" name="cfmenu_choose_theme" <?php if ((isset($cfmenu_choose_theme)) && $cfmenu_choose_theme == "theme1") {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                            <i class="far fa-check-circle d-none"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center my-2">
                                        <div class="image-radio" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Theme 2'); ?>">
                                            <img src="<?= plugins_url('../assets/img/theme3.png', __FILE__) ?>" alt="Theme Name" class="img-responsive" />
                                            <input type="radio" value="theme3" name="cfmenu_choose_theme" <?php if ((isset($cfmenu_choose_theme)) && $cfmenu_choose_theme == "theme3") {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                            <i class="far fa-check-circle d-none"></i>
                                        </div>
                                    </div>

                                    <div class="col-12 text-center my-2">
                                        <div class="image-radio" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Theme 3'); ?>">
                                            <img src="<?= plugins_url('../assets/img/theme2.png', __FILE__) ?>" alt="Theme Name" class="img-responsive" />
                                            <input type="radio" value="theme4" name="cfmenu_choose_theme" <?php if ((isset($cfmenu_choose_theme)) && $cfmenu_choose_theme == "theme4") {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                            <i class="far fa-check-circle d-none"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- =============================== -->
                                <!-- ========= Mega Menus ========== -->
                                <!-- =============================== -->
								<?php if($app_variant === 'shopfunnels') { ?>
                                <div class="form-control border-0 h5 mt-4" style="margin-left: 10px;"><?php w('Mega Menu'); ?></div>
                                <div class="d-flex flex-wrap justify-content-center cfmenu_choose_theme">
                                    <div class="col-12 text-center my-2">
                                    <hr>
                                        <div class="image-radio" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Theme 1'); ?>">
                                            <img src="<?= plugins_url('../assets/img/theme4.png', __FILE__) ?>" alt="Theme Name" class="img-responsive" />
                                            <input type="radio" value="theme2" name="cfmenu_choose_theme" <?php if ((isset($cfmenu_choose_theme)) && $cfmenu_choose_theme == "theme2") {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                            <i class="far fa-check-circle d-none"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center my-2">
                                        <hr>
                                        <div class="image-radio" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Theme 2'); ?>">
                                            <img src="<?= plugins_url('../assets/img/theme5.png', __FILE__) ?>" alt="Theme Name" class="img-responsive" />
                                            <input type="radio" value="theme5" name="cfmenu_choose_theme" <?php if ((isset($cfmenu_choose_theme)) && $cfmenu_choose_theme == "theme5") {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                            <i class="far fa-check-circle d-none"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center my-2">
                                        <hr>
                                        <div class="image-radio" data-bs-toggle="tooltip" data-placement="top" title="<?php w('Theme 3'); ?>">
                                            <img src="<?= plugins_url('../assets/img/theme6.png', __FILE__) ?>" alt="Theme Name" class="img-responsive" />
                                            <input type="radio" value="theme6" name="cfmenu_choose_theme" <?php if ((isset($cfmenu_choose_theme)) && $cfmenu_choose_theme == "theme6") {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                            <i class="far fa-check-circle d-none"></i>
                                        </div>
                                    </div>
                                </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- ================================= -->
                    <!-- ========= Logo Details ========== -->
                    <!-- ================================= -->
                    <div class="col-md-12 col-lg-12 col-sm-12 row tab-pane fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_logo_step">
                        <h5><?php w('Logo Details'); ?>
                            <hr class="bg-info">
                        </h5>
                        <div class="col-12 form-group">
                            <div class="row">
                                <label class="col-form-label col-sm-5"><?php w('Logo Type'); ?></label>
                                <div class="col text-right">
                                    <select class="form-control" onchange="cfmenu_togglelogo()" id="cfmenu_logo_type" name="cfmenu_logo_type">
                                        <option <?= (isset($logo_details['cfmenu_logo_type']) && $logo_details['cfmenu_logo_type'] == 0) ? 'selected' : '' ?> value="0"><?php w('No need'); ?></option>
                                        <option <?= (isset($logo_details['cfmenu_logo_type']) && $logo_details['cfmenu_logo_type'] == 1) ? 'selected' : '' ?> value="1"><?php w('Text'); ?></option>
                                        <option <?= (isset($logo_details['cfmenu_logo_type']) && $logo_details['cfmenu_logo_type'] == 2) ? 'selected' : '' ?> value="2"><?php w('Image'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <label class="col-form-label col-sm-5"><?php w('Logo Click URL'); ?></label>
                                <div class="col text-right">
                                    <input type="text" class="form-control" name="cfmenu_logo_click_url" value="<?= $logo_details['cfmenu_logo_click_url'] ?>" placeholder="<?php w('Users will go to that page when will click on it'); ?>." />
                                </div>
                            </div>
                        </div>

                        <!-- =================================== -->
                        <!-- ======== Logo Title Toggler ======= -->
                        <!-- =================================== -->
                        <div class="col-12 form-group" id="cfmenu_logo_text">
                            <div class="row">
                                <label class="col-form-label col-sm-5"><?php w('Logo Title'); ?></label>
                                <div class="col text-right">
                                    <input type="text" class="form-control" name="cfmenu_logo_title" value="<?= $logo_details['cfmenu_logo_title'] ?>" placeholder="<?php w('Enter your logo title'); ?>" />
                                </div>
                            </div>
                           
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Text Color'); ?></label>
                                <div class="col text-right">
                                    <input name="cfmenu_logo_color" value="<?= $logo_details['cfmenu_logo_color'] ?>" class="jscolor form-control">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Font Size'); ?></label>
                                <div class="col text-right">
                                    <input type="number" name="cfmenu_logo_font_size" class="form-control" value="<?= $logo_details['cfmenu_logo_font_size'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Logo Height'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_height" class="form-control" value="<?= $logo_details['cfmenu_logo_height'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Logo Width'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_width" class="form-control" value="<?= $logo_details['cfmenu_logo_width'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Logo Padding'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_padding" class="form-control" value="<?= $logo_details['cfmenu_logo_padding'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Logo Margin'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_margin" class="form-control" value="<?= $logo_details['cfmenu_logo_margin'] ?>">
                                </div>
                            </div>
                        </div>

                        <!-- =================================== -->
                        <!-- =========== Image Toggle ========== -->
                        <!-- =================================== -->
                        <div class="col-12 form-group" id="cfmenu_logo_image">
                            <div class="row">
                                <label class="col-form-label col-sm-5"><?php w('Image Source'); ?></label>
                                <div class="col text-right">
                                    <div class="input-group">
                                        <input type="url" name="cfmenu_logo_img_source" id="cfmenu_logo_img_source" value="<?= $logo_details['cfmenu_logo_img_source'] ?>" class="form-control" placeholder="<?php w('Enter your image address'); ?>...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success" onclick="cfmenuOpenMedia('#cfmenu_logo_img_source', false)"><?php w('UPLOAD'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Image Height'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_img_height" value="<?= $logo_details['cfmenu_logo_img_height'] ?>" class="form-control" value="10">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Image Width'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_img_width" class="form-control" value="<?= $logo_details['cfmenu_logo_img_width'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Image Padding'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_img_padding" class="form-control" value="<?= $logo_details['cfmenu_logo_img_padding'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Image Margin'); ?></label>
                                <div class="col text-right">
                                    <input type="text" name="cfmenu_logo_img_margin" class="form-control" value="<?= $logo_details['cfmenu_logo_img_margin'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Image Border Radius'); ?></label>
                                <div class="col text-right">
                                    <input type="number" min="0" name="cfmenu_logo_img_border_radius" class="form-control" value="<?= $logo_details['cfmenu_logo_img_border_radius'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================================================== -->
                    <!-- ========= Manage Slogan & Mobile Screen ========== -->
                    <!-- ================================================== -->
                    <div class="col-md-12 col-lg-12 col-sm-12 row tab-pane fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_slogan_step">
                        <h5><?php w('Manage Slogan & Mobile Screen Icon'); ?>
                            <hr class="bg-info">
                        </h5>
                        <div class="container-fluid row">
                            <div class="col-lg-6 form-group">
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Add Slogan'); ?></label>
                                    <div class="col text-right">
                                        <select class="form-control" onchange="cfmenu_toggleslogan()" id="cfmenu_navbar_slogan" name="cfmenu_navbar_slogan_drop">
                                            <option <?= (isset($manage_styles['cfmenu_navbar_slogan_drop']) && $manage_styles['cfmenu_navbar_slogan_drop'] == '0') ? 'selected' : '' ?> value="0"><?php w('No'); ?></option>
                                            <option <?= (isset($manage_styles['cfmenu_navbar_slogan_drop']) && $manage_styles['cfmenu_navbar_slogan_drop'] == '1') ? 'selected' : '' ?> value="1"><?php w('Yes'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="cfmenu_show_slogan mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Slogan Text'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_slogan_text" class="form-control" value="<?= $manage_styles['cfmenu_manage_slogan_text'] ?>">
                                    </div>
                                </div>
                                <div class="cfmenu_show_slogan mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Slogan Color'); ?></label>
                                    <div class="col text-right">
                                        <input name="cfmenu_manage_slogan_color" class="jscolor form-control" value="<?= $manage_styles['cfmenu_manage_slogan_color'] ?>">
                                    </div>
                                </div>
                                <div class="cfmenu_show_slogan mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Slogan Font Size'); ?></label>
                                    <div class="col text-right">
                                        <input type="number" min="0" name="cfmenu_manage_slogan_font_size" class="form-control" value="<?= $manage_styles['cfmenu_manage_slogan_font_size'] ?>">
                                    </div>
                                </div>
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Custom CSS'); ?></label>
                                    <div class="col text-right">
                                        <textarea name="cfmenu_customCSS" rows="4" class="form-control"><?= $manage_styles['cfmenu_customCSS'] ?></textarea>
                                        <div class="text-left">
                                            <p class="mt-0" style="font-size: 12px !important; opacity: 0.6; color: #5bc0de !important;">
                                                **<?php w('Use base selector name'); ?>
                                                <strong>.this-form</strong> <br><?php w('Example'); ?> : <br>
                                                <strong>.this-form input[type=text] <br>
                                                    {border-radous: 5px;}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <h5>Manage Mobile Screen Icon <hr class="bg-info"></h5> -->
                            <div class="col-lg-6 form-group">
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('HTML Icon'); ?></label>
                                    <div class="col text-right">
                                        <input name="cfmenu_manage_html_icon" type="text" class="form-control" value="<?= $manage_styles['cfmenu_manage_html_icon'] ?>">
                                    </div>
                                </div>
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Icon Text Color'); ?></label>
                                    <div class="col text-right">
                                        <input name="cfmenu_manage_icon_color" class="jscolor form-control" value="<?= $manage_styles['cfmenu_manage_icon_color'] ?>">
                                    </div>
                                </div>
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Icon Background Color'); ?></label>
                                    <div class="col text-right">
                                        <input name="cfmenu_manage_icon_back" class="jscolor form-control" value="<?= $manage_styles['cfmenu_manage_icon_back'] ?>">
                                    </div>
                                </div>
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Icon Padding'); ?></label>
                                    <div class="col text-right">
                                        <input name="cfmenu_manage_icon_padding" min="0" type="number" class="form-control" value="<?= $manage_styles['cfmenu_manage_icon_padding'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =================================== -->
                    <!-- ========= Navbar Styling ========== -->
                    <!-- =================================== -->
                    <div class="col-md-12 col-lg-12 col-sm-12 row tab-pane fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_styles_step">
                        <h5><?php w('Manage Styles'); ?>
                            <hr class="bg-info">
                        </h5>
                        <div class="col-12 form-group">
                            <div class="row">
                                <label class="col-form-label col-sm-5"><?php w('Sticky Navbar'); ?></label>
                                <div class="col text-right">
                                    <select class="form-control" name="cfmenu_navbar_sticky">
                                        <option <?= (isset($manage_styles['cfmenu_navbar_sticky']) && $manage_styles['cfmenu_navbar_sticky'] == 0) ? 'selected' : '' ?> value="0"><?php w('No'); ?></option>
                                        <option <?= (isset($manage_styles['cfmenu_navbar_sticky']) && $manage_styles['cfmenu_navbar_sticky'] == 1) ? 'selected' : '' ?> value="1"><?php w('Yes'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Navbar Background Color'); ?></label>
                                <div class="col text-right border border-primary rounded">
                                    <div class="input-group my-3">
                                        <label class="float-left my-auto mr-3 text-dark"><?php w('Gradient Background'); ?></label>
                                        <div class="input-group-append p-0 m-0 border-0 form-control">
                                            <select class="form-control" id="cfmenu_navbar_gradient_background_drop" onchange="cfmenu_togglegradient()" name="cfmenu_navbar_gradient_background_drop">
                                                <option <?= (isset($manage_styles['cfmenu_navbar_gradient_background_drop']) && $manage_styles['cfmenu_navbar_gradient_background_drop'] == 0) ? 'selected' : '' ?> value="0"><?php w('No'); ?></option>
                                                <option <?= (isset($manage_styles['cfmenu_navbar_gradient_background_drop']) && $manage_styles['cfmenu_navbar_gradient_background_drop'] == 1) ? 'selected' : '' ?> value="1"><?php w('Yes'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <input name="cfmenu_navbar_gradient_background1" class="mb-2 jscolor form-control" value="<?= $manage_styles['cfmenu_navbar_gradient_background1'] ?>">
                                    <div class="cfemnu_navbar_show_gradient_details">
                                        <input name="cfmenu_navbar_gradient_background2" class="mb-2 jscolor form-control" value="<?= $manage_styles['cfmenu_navbar_gradient_background2'] ?>">
                                        <div class="input-group mb-3">
                                            <label class="float-left my-auto mr-3 text-dark"><?php w('Gradient Combination'); ?></label>
                                            <div class="input-group-append p-0 m-0 border-0 form-control">
                                                <select class="form-control" class="cfmenu_navbar_gradient_background_combination_drop" name="cfmenu_navbar_gradient_background_combination_drop">
                                                    <option <?= (isset($manage_styles['cfmenu_navbar_gradient_background_combination_drop']) && $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] == 'to top') ? 'selected' : '' ?> value="to top"><?php w('to top'); ?></option>
                                                    <option <?= (isset($manage_styles['cfmenu_navbar_gradient_background_combination_drop']) && $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] == 'to bottom') ? 'selected' : '' ?> value="to bottom"><?php w('to bottom'); ?></option>
                                                    <option <?= (isset($manage_styles['cfmenu_navbar_gradient_background_combination_drop']) && $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] == 'to left') ? 'selected' : '' ?> value="to left"><?php w('to left'); ?></option>
                                                    <option <?= (isset($manage_styles['cfmenu_navbar_gradient_background_combination_drop']) && $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] == 'to right') ? 'selected' : '' ?> value="to right"><?php w('to right'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Navbar Border Radius'); ?></label>
                                <div class="col text-right input-group">
                                    <input name="cfmenu_manage_nav_border_radius" type="number" min="0" class="form-control" value="0" required>
                                    <div class="input-group-append">
                                        <select class="form-control" name="cfmenu_manage_nav_border_radius_drop">
                                            <option <?= (isset($manage_styles['cfmenu_manage_nav_border_radius_drop']) && $manage_styles['cfmenu_manage_nav_border_radius_drop'] == 'px') ? 'selected' : '' ?> value="px">px</option>
                                            <option <?= (isset($manage_styles['cfmenu_manage_nav_border_radius_drop']) && $manage_styles['cfmenu_manage_nav_border_radius_drop'] == '%') ? 'selected' : '' ?> value="%">%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Navbar Items Color'); ?></label>
                                <div class="col text-right">
                                    <input name="cfmenu_manage_nav_textcolor" class="jscolor form-control" value="<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Navbar Items Hover Text Color'); ?></label>
                                <div class="col text-right">
                                    <input name="cfmenu_manage_nav_hovercolor" class="jscolor form-control" value="<?= $manage_styles['cfmenu_manage_nav_hovercolor'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Navbar Items Hover Background Color'); ?></label>
                                <div class="col text-right">
                                    <input name="cfmenu_manage_nav_hoverbackcolor" class="jscolor form-control" value="<?= $manage_styles['cfmenu_manage_nav_hoverbackcolor'] ?>">
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <label class="col-form-label col-sm-5"><?php w('Font Size'); ?></label>
                                <div class="col text-right">
                                    <input name="cfmenu_manage_nav_items_font_size" required type="number" min="0" class="form-control" value="<?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =========================================== -->
                    <!-- ========= Extra Buttons for Shop ========== -->
                    <!-- =========================================== -->
					<?php if($app_variant === 'shopfunnels') { ?>
                    <div class="col-md-12 col-lg-12 col-sm-12 tab-pane fade mt-4 bg-white shadow-lg border border-info rounded p-3" role="tabpanel" id="cfmenu_extra_buttons_step">
                        <h5><?php w('Extra Buttons'); ?>
                            <hr class="bg-info">
                        </h5>
                        <div class="container-fluid row">
                            <div class="col-lg-4 form-group border border-primary p-3">
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Add Search Button'); ?></label>
                                    <div class="col text-right">
                                        <select class="form-control" onchange="cfmenu_toggleextrabutton('search')" id="cfmenu_navbar_search" name="cfmenu_navbar_search_drop">
                                            <option <?= (isset($extra_buttons['cfmenu_navbar_search_drop']) && $extra_buttons['cfmenu_navbar_search_drop'] == '0') ? 'selected' : '' ?> value="0"><?php w('No'); ?></option>
                                            <option <?= (isset($extra_buttons['cfmenu_navbar_search_drop']) && $extra_buttons['cfmenu_navbar_search_drop'] == '1') ? 'selected' : '' ?> value="1"><?php w('Yes'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="cfmenu_show_search mt-3 row" style="display:<?= ($extra_buttons['cfmenu_navbar_search_drop'] == '0') ? 'none' : 'flex' ?>">
                                    <label class="col-form-label col-sm-5"><?php w('Search Icon'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_search_icon" class="form-control" value='<?= $extra_buttons["cfmenu_manage_search_icon"] ?>'>
                                    </div>
                                </div>
                                <div class="cfmenu_show_search mt-3 row" style="display:<?= ($extra_buttons['cfmenu_navbar_search_drop'] == '0') ? 'none' : 'flex' ?>">
                                    <label class="col-form-label col-sm-5"><?php w('Search Text'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_search_text" class="form-control" value="<?= $extra_buttons['cfmenu_manage_search_text'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 form-group border border-primary p-3">
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Add Cart Button'); ?></label>
                                    <div class="col text-right">
                                        <select class="form-control" onchange="cfmenu_toggleextrabutton('cart')" id="cfmenu_navbar_cart" name="cfmenu_navbar_cart_drop">
                                            <option <?= (isset($extra_buttons['cfmenu_navbar_cart_drop']) && $extra_buttons['cfmenu_navbar_cart_drop'] == '0') ? 'selected' : '' ?> value="0"><?php w('No'); ?></option>
                                            <option <?= (isset($extra_buttons['cfmenu_navbar_cart_drop']) && $extra_buttons['cfmenu_navbar_cart_drop'] == '1') ? 'selected' : '' ?> value="1"><?php w('Yes'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="cfmenu_show_cart mt-3 row" style="display:<?= ($extra_buttons['cfmenu_navbar_cart_drop'] == '0') ? 'none' : 'flex' ?>">
                                    <label class="col-form-label col-sm-5"><?php w('Cart Icon'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_cart_icon" class="form-control" value='<?= $extra_buttons["cfmenu_manage_cart_icon"] ?>'>
                                    </div>
                                </div>
                                <div class="cfmenu_show_cart mt-3 row" style="display:<?= ($extra_buttons['cfmenu_navbar_cart_drop'] == '0') ? 'none' : 'flex' ?>">
                                    <label class="col-form-label col-sm-5"><?php w('Cart Text'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_cart_text" class="form-control" value="<?= $extra_buttons['cfmenu_manage_cart_text'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 form-group border border-primary p-3">
                                <div class="mt-3 row">
                                    <label class="col-form-label col-sm-5"><?php w('Add Admin Button'); ?></label>
                                    <div class="col text-right">
                                        <select class="form-control" onchange="cfmenu_toggleextrabutton('admin')" id="cfmenu_navbar_admin" name="cfmenu_navbar_admin_drop">
                                            <option <?= (isset($extra_buttons['cfmenu_navbar_admin_drop']) && $extra_buttons['cfmenu_navbar_admin_drop'] == '0') ? 'selected' : '' ?> value="0"><?php w('No'); ?></option>
                                            <option <?= (isset($extra_buttons['cfmenu_navbar_admin_drop']) && $extra_buttons['cfmenu_navbar_admin_drop'] == '1') ? 'selected' : '' ?> value="1"><?php w('Yes'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="cfmenu_show_admin mt-3 row" style="display:<?= ($extra_buttons['cfmenu_navbar_admin_drop'] == '0') ? 'none' : 'flex' ?>">
                                    <label class="col-form-label col-sm-5"><?php w('Admin Icon'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_admin_icon" class="form-control" value='<?= $extra_buttons["cfmenu_manage_admin_icon"] ?>'>
                                    </div>
                                </div>
                                <div class="cfmenu_show_admin mt-3 row" style="display:<?= ($extra_buttons['cfmenu_navbar_admin_drop'] == '0') ? 'none' : 'flex' ?>">
                                    <label class="col-form-label col-sm-5"><?php w('Admin Text'); ?></label>
                                    <div class="col text-right">
                                        <input type="text" name="cfmenu_manage_admin_text" class="form-control" value="<?= $extra_buttons['cfmenu_manage_admin_text'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php } ?>
                </div>

                <div class="p-3 container-fluid" style="display: flex; justify-content: space-between;">
                    <a href="index.php?page=cfmenu_allforms" class="mt-4 p-3 shadow rounded btn btn-primary h5"><?php w('Back'); ?></a>
                    <button type="submit" name="submit_form" id="cfmenu_submit_btn" class="mt-4 p-3 shadow rounded btn btn-primary h5 mr-1"><?php w('Save Settings'); ?></button>
                </div>
            </div>
        </div>


    </form>
</div>
<script type="text/javascript">
    let cfmenu_inp_ob = new cfmenuMangeInputs();
    <?php
    if (gettype(json_decode(stripcslashes($cfmenu_custom_url), true)) == "array" && !empty(json_decode(stripcslashes($cfmenu_custom_url), true))) {
        foreach (json_decode(stripcslashes($cfmenu_custom_url), true) as $key => $value) {
            echo "
            cfmenu_inp_ob.createINP(`" . $value['name'] . "`, `" . $value['inpurl'] . "`, `".$value['direct_id']."`, `".$value['extraids']."`, `" . $value['icon'] . "`, `" . $value['del'] . "`);
            ";
        }
    }
    ?>

    try {
        var cfmenuSelectOnlyLi = document.querySelector('ul.cfmenu_normal_ul').querySelectorAll('li');
        var cfmenuSelectOnlyUl = document.querySelector('ul.cfmenu_normal_ul').querySelectorAll('ul');
        for (var k = 0; k < cfmenuSelectOnlyLi.length; k++) {
            if (cfmenuSelectOnlyLi[k].innerHTML == "") cfmenuSelectOnlyLi[k].remove();
        }
        for (var k = 0; k < cfmenuSelectOnlyUl.length; k++) {
            if (cfmenuSelectOnlyUl[k].innerHTML == "") cfmenuSelectOnlyUl[k].remove();
        }
    } catch (e) {}
    document.querySelectorAll(".cfmenu_create_inp_btn")[0].onclick = function(eve) {
        eve.preventDefault();
        cfmenu_inp_ob.createINP();
    };

    $(function() {
        $('ul.cfmenu_normal_ul').sortable({
            autocreate: true,
            update: function(evt) {}
        });
    });

    function cfmenuOpenMedia(selector, html) {
        try {
            openMedia(function(content) {
                try {
                    document.querySelectorAll(selector)[0].value = content;
                } catch (err) {}
            }, html);
        } catch (err) {}
    }
</script>

<?php
cf_media();
?>