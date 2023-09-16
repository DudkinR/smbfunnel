<?php
global $mysqli;
global $dbpref;

if(isset($_GET['cf_popup_id'])) {
    if( is_numeric( $_GET['cf_popup_id'] ) ) {
        $form_id = $_GET['cf_popup_id'];
    }

    else {
        header('location: '.get_option('install_url')."/index.php?page=no-permission");
        die();
    }

    $form_id=$mysqli->real_escape_string($form_id);

    $getShortCode = $mysqli -> real_escape_string($_GET['cf_popup_id']);
    $form_table = $dbpref."respo_popup_form";
    $form_extra_settings = $dbpref."respo_extra_settings";

    $result = $mysqli->query("SELECT * from $form_table INNER JOIN $form_extra_settings on $form_table.formId=$form_extra_settings.formId WHERE $form_table.formId='$form_id'");
    $data = $result->fetch_assoc( );

    $table1=$dbpref.'respo_popup_inputs';
    $returnInputs = $mysqli->query("SELECT * FROM `".$table1."` WHERE `form_id`=".$form_id." ORDER BY `position` ASC" );

    $cfrespo_header_content = $data['header_text'];
    $form_name =  $data['form_name'];
    $cfrespo_footer_content  =  $data['footer_text'];
    $customcss=$data['custom_css'];
    $form_width = ( !empty( $data['form_width']) ) ? $data['form_width']: 400;
    $fid = $data['formId'];

    $popup_delay_time = ( isset( $data['delay_value'] ) && is_numeric($data['delay_value']) ) ? $data['delay_value'] : 1;

    $formBackCol = ( !empty( $data['formBackCol']) ) ? $data['formBackCol']: "#FFFFFF";
    $headerBackCol = ( !empty( $data['headBackCol']) ) ? $data['headBackCol']: "#2400eab";
    $headerPadding = ( !empty( $data['headerPadding']) ) ? $data['headerPadding']: 0;
    $headerMargin = ( !empty( $data['headerMargin']) ) ? $data['headerMargin']: 0;
    
    $footerBackCol = ( !empty( $data['footBackCol']) )  ? $data['footBackCol']: "#333333";
    $footerPadding = ( !empty( $data['footerPadding']) ) ? $data['footerPadding']: 0;
    $footerMargin = ( !empty( $data['footerMargin']) ) ? $data['footerMargin']: 0;

    $submitBackCol = ( !empty( $data['submitBackCol'] ) ) ? $data['submitBackCol'] : "#ff0000";

    $submitBtnCol = ( !empty( $data['submitBtnCol'] ) ) ? $data['submitBtnCol'] : "#ffffff";
    $errorTxtCol = ( !empty( $data['errorTxtCol'] ) ) ? $data['errorTxtCol'] : "#ffffff";

    $submit_btn_text = ( !empty( $data['submitBtnText'] ) ) ? $data['submitBtnText']: "Subscribe me";
    $button_align = ( !empty( $data['button_align']) ) ? $data['button_align'] : "";

    $cfrespo_theme = ( !empty( $data['theme_id'] ) ) ? $data['theme_id']:"Theme1";
    $is_global_form = $data['display_setup'];

    $cfrespo_use_as_exit = (isset($data['use_as_exit']) && $data['use_as_exit']=='1')? true:false;
    $cfrespo_use_as_delay = (isset($data['use_as_delay']) && $data['use_as_delay']=='1')? true:false;
    $on_btn_click = (isset($data['on_btn_click']) && $data['on_btn_click']=='1')? true:false;

    $formAnimation = ( !empty( $data['form_appear']) ) ? $data['form_appear'] : "";

    $cfrespo_allow_processcf=(isset($data['allow_process_in_cf']) && $data['allow_process_in_cf']==='0')? false:true; 

    $cfrespo_redirect_url= (isset($data['redirect_url']))? $data['redirect_url']:'';

    $don_show=(isset($data['don_show']) && $data['don_show']=='1')? true:false;
}
else{
    $is_global_form=1;
    $popup_delay_time=1;
    $formBackCol ="#FFFFFF";
    $headerBackCol ="#FFFFFF";
    $headerPadding =0;
    $headerMargin =0;
    $footerBackCol = "#FFFFFF";
    $errorTxtCol = "#FF0000";
    $footerPadding =0;
    $footerMargin =0;
    $submitBackCol ="#FF0000";
    $submitBtnCol = "#FFFFFF";
    $submit_btn_text ="Subscribe us";
    $cfrespo_theme ="Theme1";
    $cfrespo_use_as_exit =0;
    $cfrespo_use_as_delay =1;
    $formAnimation = "";
    $button_align = "";
    $form_width = 400;
    $cfrespo_allow_processcf=true; 
    $cfrespo_redirect_url='';
    $don_show=true;
    $on_btn_click=false;
}

$theme3_css = plugins_url('../themes/assets/css/Theme3.css', __FILE__);
$main_css = plugins_url('../themes/assets/css/Theme2.css', __FILE__);
echo "<link rel='stylesheet' href='$theme3_css'>";
echo "<link rel='stylesheet' href='$main_css'>";
?>

<div class="container-fluid bg-white px-lg-4 cfrespo_popup_settings">
    <div class="row">
        <div class="col-md-12 mt-lg-4 mt-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4 shadow p-3 rounded">
                <h6 class="mb-0 text-success">Popup Form Settings</h6>
                <div class=" text-info">Create, edit, manage forms</div>
            </div>
        </div>

        <div class="col-12">
            <?php if(isset($form_id)): ?>
                <div class="alert alert-info w-100">
                    Use the shortcode  &nbsp;<span class="text-success">  <strong style="cursor:pointer;" onclick="copyText(`[cf_popup id=<?php echo $fid; ?>]`)" data-toggle="tooltip" title="Copy to clipboard">[cf_popup id=<?php echo $form_id; ?>]</strong>  </span>&nbsp; to show the popup on any funnel page.
                </div>
            <?php endif; ?>

            <?php if(isset($form_id) && ((isset($on_btn_click) && $on_btn_click==1))): ?>
                <div class="alert alert-success w-100">
                    Use the shortcode  &nbsp;<span class="text-success">  <strong style="cursor:pointer;" onclick="copyText(`#cf_popup_btn_<?php echo $fid; ?>`)" data-toggle="tooltip" title="Copy to clipboard">#cf_popup_btn_<?php echo $form_id; ?></strong>  </span>&nbsp; to show the popup on clicking the button.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container-fluid mt-5 px-lg-4">
    <form id="testForm" method="post" class="w-100" autocomplete="off">
        <input type="hidden" id="cfrespo_ajax" name="cfrespo_cfrespo_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
        <input type="hidden" id="cfrespo_ajax_insertUrl" value="<?php echo  get_option('install_url')."/index.php?page=cf_new_form&cf_popup_id="; ?>" />
        <input type="hidden" name="form_id" value="<?php echo ((isset($fid))? $fid:0); ?>">
        <input type="hidden" name="cfrespo_param" id="cfrespo_param" value="<?php echo ((isset($fid))? 'update':'insert'); ?>">

        <div class="row">

            <!-- =================================== -->
            <!-- ======= MAIN FORM SETTINGS ======== -->
            <!-- =================================== -->
            <div class="col-sm-12 col-md-6">
                <div class="w-100">
                    <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded" style="height: 400px !important">
                        <h5 class="text-dark">Add Setting</h5> <hr class="bg-primary">
                        <div class="container-fluid">
                            <div class="form-group row">
                                <label class="col-form-label">Enter Form Name(Required)</label>
                                <input type="text" name="form_name" value="<?php echo ((isset($form_name))? $form_name:'') ?>" class="form-control" placeholder="Enter Form Name" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label">Submit Button Text</label>
                                <input type="text" name="submit_btn_text" id="" value="<?php echo ((isset($submit_btn_text))? $submit_btn_text:''); ?>" class="form-control" placeholder="Enter Submit Button Text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================================== -->
            <!-- ======= CREATE CUSTOM INPUTS ======= -->
            <!-- ==================================== -->
            <div class="col-sm-12 col-md-6">
                <div class="w-100">
                    <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded" style="min-height: 400px !important">
                        <h5 class="text-dark">Create Custom Input Field & Headers</h5> <hr class="bg-primary">
                        <div class="container-fluid">
                            <div id="cfrespo_input_container" style="max-width:100%"></div>
                        </div>
                        <button type="button" class="btn btn-primary w-100 cfrespo_createinp_btn mt-2"><i class="fas fa-pencil-alt" type="button"></i> Create New</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ==================================== -->
            <!-- ======== FORM EXTRA SETTING ======== -->
            <!-- ==================================== -->
            <div class="col-sm-12 col-md-6">
                <div class="w-100">
                    <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded"  style="min-height: 700px !important">
                        <h5 class="text-dark">Manage Style</h5> <hr class="bg-primary">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Form Width</label>
                            <div class="col text-right">
                                <input type="number" min="100" name="cfrespo_form_width" id="cfrespo_form_width" value="<?php echo ((isset($form_width))? $form_width:''); ?>" class="form-control form-control-sm" placeholder="Enter Form Width" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Form Background Color</label>
                            <div class="col text-right">
                                <input name="formBackCol" value="<?php echo ((isset($formBackCol))? $formBackCol:''); ?>" class="jscolor form-control form-control-sm" value="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Header Background Color</label>
                            <div class="col text-right">
                                <input name="headerBackCol" value="<?php echo ((isset($headerBackCol))? $headerBackCol:''); ?>" class="jscolor form-control form-control-sm" value="">
                            </div>
                        </div>
                        
                        <div class="input-group mb-3 row" style="font-size: 14px !important;">
                            <label class="col-form-label col-sm-5 text-left">Header's  Padding</label>
                            <input name="headerPadding" type="number" class="form-control ml-4" min="0" placeholder="Enter header's padding" value="<?php echo ((isset($headerPadding))? $headerPadding:''); ?>">
                        </div>

                        <div class="input-group mb-3 row" style="font-size: 14px !important;">
                            <label class="col-form-label col-sm-5 text-left">Header's  Margin</label>
                            <input name="headerMargin" type="number" class="form-control ml-4" min="0" placeholder="Enter header's margin" value="<?php echo ((isset($headerMargin))? $headerMargin:''); ?>">
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Footer Background Color</label>
                            <div class="col text-right">
                                <input name="footerBackCol" value="<?php echo ((isset($footerBackCol))? $footerBackCol:''); ?>" class="jscolor form-control form-control-sm" value="">
                            </div>
                        </div>
                        
                        <div class="input-group mb-3 row" style="font-size: 14px !important;">
                            <label class="col-form-label col-sm-5 text-left">Footer's  Padding</label>
                            <input name="footerPadding" type="number" class="form-control ml-4" min="0" placeholder="Enter footer's padding" value="<?php echo ((isset($footerPadding))? $footerPadding:''); ?>">
                        </div>

                        <div class="input-group mb-3 row" style="font-size: 14px !important;">
                            <label class="col-form-label col-sm-5 text-left">Footer's  Margin</label>
                            <input name="footerMargin" type="number" class="form-control ml-4" min="0" placeholder="Enter footer's margin" value="<?php echo ((isset($footerMargin))? $footerMargin:''); ?>">
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Submit Button Background Color</label>
                            <div class="col text-right">
                                <input name="submitBackCol" value="<?php echo ((isset($submitBackCol))? $submitBackCol:''); ?>" class="jscolor form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Submit Button Text Color</label>
                            <div class="col text-right">
                                <input name="submitBtnCol" value="<?php echo ((isset($submitBtnCol))? $submitBtnCol:''); ?>" class="jscolor form-control form-control-sm" value="">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Error Text Color</label>
                            <div class="col text-right">
                                <input name="errorTxtCol" value="<?php echo ((isset($errorTxtCol))? $errorTxtCol:''); ?>" class="jscolor form-control form-control-sm" value="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5 align-self-center">Submit Button Appearance</label>
                            <div class="col text-right">
                                <select name="cfrespo_button_align" id="" class="form-control ">
                                    <option <?php if(isset($button_align) && $button_align=="center"){ echo "selected"; } ?> value="center">Center</option>
                                    <option <?php if(isset($button_align) && $button_align=="right"){ echo "selected"; } ?> value="right">Right</option>
                                    <option <?php if(isset($button_align) && $button_align=="left"){ echo "selected"; } ?> value="left">Left</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5">Custom CSS</label>
                            <div class="col text-right">
                                <textarea name="customCSS" id="" rows="4" class="form-control form-control-sm"><?php echo ((isset($customcss))? $customcss:''); ?></textarea>
                                <div class="text-left">
                                    <p class="mt-0" style="font-size: 12px !important; opacity: 0.6;">
                                        **Use base selector name 
                                        <strong>.this-form</strong> <br>Example : <br> 
                                        <strong>.this-form input[type=text] <br>
                                        {border-radous: 5px;}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="w-100">
                <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded"  style="min-height: 905px !important">
                        <h5 class="text-dark">Settings</h5> <hr class="bg-primary">

                        <div class="form-group row">
                            <div class="container">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">Form Appearance</span>
                                    </div>
                                    <select name="formAnimation" id="" class="form-control ">
                                        <option <?php if(isset($formAnimation) && $formAnimation=="t_t_c"){ echo "selected"; } ?> value="t_t_c">Top To Center</option>
                                        <option <?php if(isset($formAnimation) && $formAnimation=="r_t_c"){ echo "selected"; } ?> value="r_t_c">Right To Center</option>
                                        <option <?php if(isset($formAnimation) && $formAnimation=="l_t_c"){ echo "selected"; } ?> value="l_t_c">Left To Center</option>
                                        <option <?php if(isset($formAnimation) && $formAnimation=="b_t_c"){ echo "selected"; } ?> value="b_t_c">Bottom To Center</option>
                                        <option <?php if(isset($formAnimation) && $formAnimation=="c_t_c"){ echo "selected"; } ?> value="c_t_c">Center To Center</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="container">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" id="cfresp_use_as_delay" value="1" name="use_as_delay" <?=($cfrespo_use_as_delay)? "checked":""  ?> />
                                        </div>
                                    </div>
                                    <p class="form-control">Use as delayed popup</p>
                                </div>
                                <div class="form-group mx-auto " id="cfresp-delay-time" style="max-width: 314px;display:<?=($cfrespo_use_as_delay)? "block":"none"?>">
                                    <div class="input-group mb-3">
                                        <input type="number" name="popup_delay_time" id="" value="<?php echo ((isset($popup_delay_time))? $popup_delay_time:''); ?>" min="0" class="form-control" required>
                                        <div class="input-group-append align-items-left">
                                            <span class="input-group-text">Popup delay time in second(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="container">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" value="1" name="use_as_exit" <?=($cfrespo_use_as_exit)? "checked":""  ?> />
                                        </div>
                                    </div>
                                    <p class="form-control">Use as exit popup</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="container">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" value="1" name="on_btn_click" <?php if($on_btn_click){echo "checked";} ?>>
                                        </div>
                                    </div>
                                    <p class="form-control">Show the popup on the button click</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="container">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" value="1" name="don_show" <?php if($don_show){echo "checked";} ?>>
                                        </div>
                                    </div>
                                    <p class="form-control">Don't display the form after subscription</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-5">Display Setup</label>
                            <div class="col text-right">
                                <div class="form-group mb-0 text-left">
                                    <label><input type="radio" name="display_setup" <?php echo ((!(isset($is_global_form) && $is_global_form))? 'checked':'') ?> value="0"> Pages where the shortcode will be applied</label>
                                </div>
                                <div class="form-group mb-0 text-left">
                                    <label><input type="radio" name="display_setup" <?php echo ((isset($is_global_form) && $is_global_form)? 'checked':''); ?> value="1"> Display On All Pages</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-sm-5">Manage how to process after form submission</label>
                            <div class="col text-right">
                                <div class="form-group mb-0 text-left text-dark">
                                    <label><input type="radio" name="allow_process_in_cf" id="" value="1" <?php if(!(isset($cfrespo_allow_processcf) && !$cfrespo_allow_processcf)){ echo 'checked'; } ?>> Allow form submitted data to pass in main CloudFunnels process</label>
                                    <label><input type="radio" name="allow_process_in_cf" id="" value="0" <?php if((isset($cfrespo_allow_processcf) && !$cfrespo_allow_processcf)){ echo 'checked'; } ?>>&nbsp; Redirect to given URL </label>
                                    <input type="url" name="redirect_url" class="form-control" placeholder="Enter URL" value="<?php echo( (isset($cfrespo_redirect_url) && filter_var($cfrespo_redirect_url, FILTER_VALIDATE_URL))? $cfrespo_redirect_url:'') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- =================================== -->
        <!-- ======= Header Footer ======= -->
        <!-- =================================== -->
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="w-100">
                    <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded">
                        <h5 class="text-dark">Enter Header Content</h5> <hr class="bg-primary">
                        <textarea id="header_text" class="form-control"> <?php echo ((isset($cfrespo_header_content))? $cfrespo_header_content:''); ?> </textarea>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="w-100">
                    <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded">
                        <h5 class="text-dark">Enter Footer Content</h5> <hr class="bg-primary">
                        <textarea id="footer_text" class="form-control"> <?php echo ((isset($cfrespo_footer_content))? $cfrespo_footer_content:''); ?> </textarea>
                    </div>
                </div>
            </div>
        </div>
        <!-- =================================== -->
        <!-- ======= CHOOSE THEME OPTION ======= -->
        <!-- =================================== -->
        <div class="row">
            <div class="col-12">
                <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded">
                    <h5 class="text-dark">Choose Theme</h5> <hr class="bg-primary">

                    <div class="row text-center text-lg-left d-flex justify-content-center">
                        <div class="col-lg-3 col-md-4 col-6 text-center cfrespo_theme_choose">
                            <label for="theme1" class="theme_choose_card">
                                <img src="<?php echo plugins_url('../assets/img/theme-1.png', __FILE__); ?>" alt="Theme 1" class="img-fluid img-thumbnail"><br>
                                <input type="radio" name="select_theme" id="theme1" value="Theme1" checked <?php if((isset($cfrespo_theme)) && $cfrespo_theme == "Theme1"){ echo "checked"; } ?>>
                            </label>    
                        </div>

                        <div class="col-lg-3 col-md-4 col-6 text-center cfrespo_theme_choose">
                            <label for="theme2" class="theme_choose_card">
                                <img src="<?php echo plugins_url('../assets/img/theme-2.png', __FILE__); ?>" alt="Theme 2" class="img-fluid img-thumbnail"><br>
                                <input type="radio" name="select_theme" id="theme2" value="Theme2" <?php if((isset($cfrespo_theme)) && $cfrespo_theme == "Theme2"){ echo "checked"; } ?>>
                            </label>    
                        </div>

                        <div class="col-lg-3 col-md-4 col-6 text-center cfrespo_theme_choose" style="border-radius:30px;">
                            <label for="theme3" class="theme_choose_card">
                                <img src="<?php echo plugins_url('../assets/img/theme-3.png', __FILE__); ?>" alt="Theme 3" class="img-fluid img-thumbnail"><br>
                                <input type="radio" name="select_theme" id="theme3" value="Theme3" <?php if((isset($cfrespo_theme)) && $cfrespo_theme == "Theme3"){ echo "checked"; } ?>>
                            </label>    
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="button" id="preview" class="btn cfrespo_preview_setting btn-warning cfrespo_save_setting mb-3" value="Preview">
        <button type="submit" class="btn btn-primary cfrespo_save_setting float-right mb-3" ><?php echo ((isset($fid))? 'UPDATE':'CREATE'); ?></button>
    </form>
</div>

<div class="container" id="shortcodePreview"></div>

<script type="text/javascript">
    let cfrespo_inp_ob = new CFRESpoMangeInputs();

    <?php
        if( isset($returnInputs) && $returnInputs->num_rows > 0 ) {
            while( $data_input= $returnInputs->fetch_assoc() ) {                
                echo "
                  cfrespo_inp_ob.createINP( '".$data_input['name']."','".$data_input['placeholder']."','".$data_input['title']."','".$data_input['required']."','".$data_input['type']."' );
                ";
            }
        } else {
    ?>
            cfrespo_inp_ob.createINP('name', "Enter Name", "Enter Name", 1, "text");
            cfrespo_inp_ob.createINP('email', "Enter Email", "Enter Email", 1, "email");

    <?php
        }
    ?>
    document.querySelectorAll(".cfrespo_createinp_btn")[0].onclick = function(eve) {
        eve.preventDefault();
        cfrespo_inp_ob.createINP();
    };

</script>

<?php register_tiny_editor(array("#header_text", "#footer_text"));