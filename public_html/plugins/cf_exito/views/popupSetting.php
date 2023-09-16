<?php
global $mysqli;
global $dbpref;

if(isset($_GET['cfexito_form_id']))
{
  if( is_numeric( $_GET['cfexito_form_id'] ) )
  {
    $form_id = $_GET['cfexito_form_id'];
  }

  $form_id=$mysqli->real_escape_string($form_id);
  $table =$dbpref."ext_popup_form"; 
  $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `id`=".$form_id );

  $data = $returnOptions->fetch_assoc( );
  $table1=$dbpref.'ext_popup_inputs';

  $returnInputs = $mysqli->query("SELECT * FROM `".$table1."` WHERE `form_id`=".$form_id." ORDER BY `position` ASC" );

  $cfexito_header_content = $data['header_text'];
  $cfexito_form_name =  $data['form_name'];
  $cfexito_footer_content  =  $data['footer_text'];
  $form_setup  =  json_decode( $data['form_setup'] ) ;
  $cfexito_form_customcss=$data['form_css'];
  $fid = $data['id'];
  $is_global_form=$data['is_global'];

  $cfexito_delay_time = ( isset( $form_setup->cfexito_delay_time ) && is_numeric($form_setup->cfexito_delay_time) ) ? $form_setup->cfexito_delay_time : 1;

  $cfexito_header_b_color = ( !empty( $form_setup->cfexito_header_b_color) ) ? $form_setup->cfexito_header_b_color: "#2400eab";

  $cfexito_footer_b_color = ( !empty( $form_setup->cfexito_footer_b_color) )  ? $form_setup->cfexito_footer_b_color: "#333333";

  $cfexito_submit_b_color = ( !empty( $form_setup->cfexito_submit_b_color ) ) ? $form_setup->cfexito_submit_b_color : "#ff0000";

  $cfexito_submit_t_color = ( !empty( $form_setup->cfexito_submit_t_color ) ) ? $form_setup->cfexito_submit_t_color : "#ffffff";

  $cfexito_submit_text = ( !empty( $form_setup->cfexito_submit_text ) ) ? $form_setup->cfexito_submit_text: "Subscribe us";

  $cfexito_theme = ( !empty( $form_setup->cfexito_theme ) ) ? $form_setup->cfexito_theme:"cfexito_a";

  $cfexito_use_as_exit = ( !empty( $form_setup->cfexito_use_as_exit) ) ? $form_setup->cfexito_use_as_exit: "";

  $cfexito_form_animation = ( !empty( $form_setup->cfexito_form_animation) ) ? $form_setup->cfexito_form_animation : "";

  $cfexito_allow_processcf=(isset($form_setup->allow_process_in_cf) && $form_setup->allow_process_in_cf==='0')? false:true; 

  $cfexito_redirect_url= (isset($form_setup->redirect_url))? $form_setup->redirect_url:'';

  $dont_display_after_subscription=(isset($form_setup->dont_display_after_subscription) && $form_setup->dont_display_after_subscription=='1')? true:false;
}
else
{
  $is_global_form=0;
  $cfexito_delay_time=1;
  $cfexito_header_b_color ="#FFFFFF";
  $cfexito_footer_b_color = "#FFFFFF";
  $cfexito_submit_b_color ="#ff0000";
  $cfexito_submit_t_color = "#ffffff";
  $cfexito_submit_text ="Subscribe us";
  $cfexito_theme ="theme_a";
  $cfexito_use_as_exit =0;
  $cfexito_form_animation = "";
  $cfexito_allow_processcf=true; 
  $cfexito_redirect_url='';
  $dont_display_after_subscription=true;
}
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Exito Form Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
          <div class="d-flex justify-content-end align-items-center">Create, edit, manage forms</div>
      </div>
  </div>
  <?php if(isset($_GET['cfexito_form_id']) && (!(isset($is_global_form) && $is_global_form))): ?>
    <div class="alert alert-warning">
      Use the shortcode  &nbsp;<span class="text-info">  <strong style="cursor:pointer;" onclick="copyText(`[cfexito_shortcode id=<?php echo $fid; ?>]`)" data-toggle="tooltip" title="Copy to clipboard">[cfexito_shortcode id=<?php echo $fid; ?>]</strong>  </span>&nbsp; to show the popup on any funnel page.
    </div>
  <?php endif; ?>

    <form  id="cfexito_AddSetting"  autocomplete="off">
        <div class="row bg-white shadow p-0 m-0 mb-3 ">
          <div class="col-lg-6 mt-4">
            <input type="hidden" id="cfexito_ajax" name="cfexito_cfexito_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
          
              <input type="hidden" name="cfexito_form_id" value="<?php echo ((isset($fid))? $fid:0); ?>">
              <input type="hidden" name="cfexito_param" value="<?php echo ((isset($fid))? 'update_popupForm':'save_popupForm'); ?>">
            
              <h4 class="text-primary  p-2 px-4">Add Settings</h4>
              <div class="p-4 py-0 border">
                <div class="form-group py-1">
                  <label for="email">Enter Form Name (Required)  </label>
                  <input type="text" name="cfexito_form_name" required=""  id="cfexito_form_name" value="<?php echo ((isset($cfexito_form_name))? $cfexito_form_name:'') ?>" class="form-control" placeholder="Enter Form Name">
                </div>
                <div class="form-group py-1">
                  <label for="email">Submit Button Text:  </label>
                  <input type="text" name="cfexito[cfexito_submit_text]" required=""  id="cfexito_submit_text" value="<?php echo ((isset($cfexito_submit_text))? $cfexito_submit_text:''); ?>" class="form-control" placeholder="Enter Submit Button Text">
                </div>
                <div class="form-group py-1">
                   <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Popup Delay time in second</span>
                      </div>
                      <input type="number"  required="" value="<?php echo ((isset($cfexito_delay_time))? $cfexito_delay_time:''); ?>"   name="cfexito[cfexito_delay_time]" min="0" id="cfexito_delay_time" class="form-control" >
                    </div>
                </div>
              </div>
            <div class=" border" >
              <h4 class="text-primary mb-3 p-2">Create Custom Input Field & Headers</h4>
              <hr/>
              <div class="form-group p-3" >
                <div class="row">
                  <div class="col-sm-12">
                    <div id="cfexito_input_container" style="max-width:100%"></div>
                  </div>
                </div>
                <button class="btn btn-primary btn-block cfexcito_createinp_btn mt-2"><i class="fas fa-pencil-alt" type="button"></i>&nbsp;Create New</button>
              </div>
              <hr />
            </div>
          </div>
          <div class="col-lg-6 mt-4">  
            <h4 class="text-primary m-0 p-2">Add Extra Settings</h4>
            <div class="p-4 py-0 border">
              <div class="row form-group">
                  <label  class="col-sm-5 col-form-label align-self-center">Header Background Color</label>
                  <div class="col text-right">
                    <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfexito_header_b_color))? $cfexito_header_b_color:''); ?>" name="cfexito[cfexito_header_b_color]" value="">
                    </div>
              </div>
              <div class="row form-group">
                <label  class="col-sm-5 col-form-label align-self-center">Footer Background Color</label>
                <div class="col text-right">
                  <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfexito_footer_b_color))? $cfexito_footer_b_color:''); ?>" name="cfexito[cfexito_footer_b_color]" value="">
                </div>
              </div>
              <div class="row form-group">
                <label  class="col-sm-5 col-form-label align-self-center">Submit Button Background Color</label>
                <div class="col text-right">
                  <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfexito_submit_b_color))? $cfexito_submit_b_color:''); ?>" name="cfexito[cfexito_submit_b_color]" value="">
                </div>
              </div>
              <div class="row form-group">
                <label  class="col-sm-5 col-form-label align-self-center">Submit Button Text Color</label>
                <div class="col text-right">
                  <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfexito_submit_t_color))? $cfexito_submit_t_color:''); ?>" name="cfexito[cfexito_submit_t_color]" value="">
                </div>
              </div>
              <div class="form-group row">
                <label for="boxposition" class="col-sm-4 col-form-label"> Form Appearance Animation Type</label>
                <div class="col-sm-8">
                  <select class="form-control form-control-sm"  id="boxposition" name="cfexito[cfexito_form_animation]">
                      <option <?php if(isset($cfexito_form_animation) && $cfexito_form_animation=="t_to_c"){ echo "selected"; } ?>  value="t_to_c">Top To Center</option>
                      <option <?php if(isset($cfexito_form_animation) && $cfexito_form_animation=="r_to_c"){ echo "selected"; } ?> value="r_to_c">Right To Center</option>
                      <option <?php if(isset($cfexito_form_animation) && $cfexito_form_animation=="l_to_c"){ echo "selected"; } ?>  value="l_to_c">Left To Center</option>
                      <option <?php if(($cfexito_form_animation) && $cfexito_form_animation=="b_to_c"){ echo "selected"; } ?> value="b_to_c">Bottom To Center</option>
                      <option <?php if(isset($cfexito_form_animation) && $cfexito_form_animation=="c_to_c"){ echo "selected"; } ?> value="c_to_c">Center To Center</option>
                    </select> 
                  </div>
                </div> 
                  <div class="form-group row">
                  <label for="customcss" class="col-sm-4 col-form-label align-self-center">Custom CSS</label>
                    <div class="col-sm-8">
                    <textarea class="form-control form-control-sm" id="customcss" rows="4"  name="cfexito_custom_css"><?php echo ((isset($cfexito_form_customcss))? $cfexito_form_customcss:''); ?></textarea>
                    <p class="mt-0" style="font-size:12px !important;opacity:0.6;">**Use base selector name <strong>.this-form</strong><br>Example: <br><strong>.this-form input[type=text]<br>{border-radous: 5px;}<strong></p>
                    </div>
                  </div>
                <div class="form-group py-1">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <input type="checkbox" value="checked" 
                        <?php 
                        if(isset($cfexito_use_as_exit) && $cfexito_use_as_exit == "yes" )
                        {
                          echo "checked";
                        } 
                        ?> id="cfexito_use_as_exit" />
                      </div>
                    </div>
                    <input type="hidden" id="cfexito_use_as_exit_hidden"  name="cfexito[cfexito_use_as_exit]" value="<?php echo ((isset($cfexito_use_as_exit))? $cfexito_use_as_exit:''); ?>"  >
                    <p class="form-control">Use as exit popup only</p>
                  </div>
                </div>
                <div class="form-group py-1">
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><input type="checkbox" value=1 name="cfexito[dont_display_after_subscription]" <?php if($dont_display_after_subscription){echo "checked";} ?>></span></div>
                    <p class="form-control">Don't display the form after subscription</p>
                  </div>
                </div>
                <div class="form-group py-1">
                  <label class="mb-2">Display Setup</label>
                  <div class="form-group mb-0">
                    <input type="radio" id="cfexito_global_show" name="cfexito_is_global" <?php echo ((isset($is_global_form) && $is_global_form)? 'checked':''); ?> name="cfexito_popop_where_show" value=1>
                      <label for="cfexito_global_show">Display on all pages</label>
                  </div>
                  <div class="form-group mt-0">
                      <input type="radio" id="cfexito_custom_show" <?php echo ((!(isset($is_global_form) && $is_global_form))? 'checked':'') ?> name="cfexito_is_global" value=0>
                      <label for="cfexito_custom_show">Pages where the shortcode will be applied</label>
                    </div>
                </div>
                
                <div class="form-group">
                  <label class="mb-2">Manage how to process after form submission</label>
                  <label><input type="radio" name="cfexito[allow_process_in_cf]" value=1 <?php if(!(isset($cfexito_allow_processcf) && !$cfexito_allow_processcf)){ echo 'checked'; } ?>>&nbsp; Allow form submitted data to pass in main CloudFunnels process</label>
                  <label><input type="radio" name="cfexito[allow_process_in_cf]" value=0 <?php if((isset($cfexito_allow_processcf) && !$cfexito_allow_processcf)){ echo 'checked'; } ?>>&nbsp; Redirect to given URL</label>
                  <input type="url" name="cfexito[redirect_url]" class="form-control" placeholder="Enter URL" value="<?php echo( (isset($cfexito_redirect_url) && filter_var($cfexito_redirect_url, FILTER_VALIDATE_URL))? $cfexito_redirect_url:'') ?>">
                </div>

            </div>
          </div>
        </div>
        <div class="row p-0 m-0 mt-5 shadow bg-white">
          <div class="col-lg-6 p-3" style="margin-top: 10px;" >
            <h4 class="text-primary mb-3 p-2">Enter Header Content</h4>
            <textarea name="cfexito_header_content" id="cfexito_header_content"  class="form-control"> <?php echo ((isset($cfexito_header_content))? $cfexito_header_content:''); ?></textarea>
          </div>
          <div class="col-lg-6 p-3" style="margin-top: 10px;" >
            <h4 class="text-primary mb-3 p-2">Enter Footer Content</h4>
            <textarea name="cfexito_footer_content" id="cfexito_footer_content" class="form-control" > <?php echo ((isset($cfexito_footer_content))? $cfexito_footer_content:''); ?></textarea>
          </div>
        </div>
        <div class="mt-5 p-3 bg-white">
          <h4 class=" text-primary ">Choose Theme</h4>
          <hr class="mt-2 mb-5">
          <div class="row text-center text-lg-left">
            <div class="col-lg-3 col-md-4 col-6 text-center cfexito_images ">
              <label for="cfexito_a">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/a.png" alt="Theme image">
              </label>
              <input type="radio"   name="cfexito[cfexito_theme]" value="theme_a" id="cfexito_a" <?php if((isset($cfexito_theme)) && $cfexito_theme == "theme_a"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfexito_images ">
              <label for="cfexito_b">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/b.png" alt="Theme image">
              </label>
              <input type="radio"   name="cfexito[cfexito_theme]" value="theme_b" id="cfexito_b" <?php if(isset($cfexito_theme) && $cfexito_theme == "theme_b"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfexito_images ">
              <label for="cfexito_c">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/c.png" alt="Theme image">
              </label>
              <input type="radio"   name="cfexito[cfexito_theme]" value="theme_c" id="cfexito_c" <?php if(isset($cfexito_theme) && $cfexito_theme == "theme_c"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfexito_images ">
              <label for="cfexito_d">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/d.png" alt="Theme image">
              </label>
              <input type="radio"   name="cfexito[cfexito_theme]" value="theme_d" id="cfexito_d" <?php if( isset($cfexito_theme) && $cfexito_theme == "theme_d"){ echo "checked"; }  ?> >
            </div>
          </div>
        </div>
          <button type="submit" class="btn btn-primary cfexito_save_setting float-right m-3 " id="cfexito_save_setting">
          Save Changes</button>
      </form>
</div>
<script type="text/javascript">
    let cfexito_inp_ob=new CFEXItoMangeInputs();
    //to create input fieldcreateINP=function(name='',placeholder='',title='',required=1, type='text'){}
   <?php
      if( isset($returnInputs) && $returnInputs->num_rows > 0 )
      {
        while( $data_input= $returnInputs->fetch_assoc() )
        {
           echo "
              cfexito_inp_ob.createINP( '".$data_input['name']."','".$data_input['placeholder']."','".$data_input['title']."','".$data_input['required']."','".$data_input['type']."' );
            ";
        }
      }
    else{
      ?>
        cfexito_inp_ob.createINP('name',"Enter Name","Enter Name",1,"text");
        cfexito_inp_ob.createINP('email',"Enter Email","Enter Email",1,"email");
      
      <?php
    }
   ?>
    //to get all input fields data use getInputs() method
    //     let cf_excito_inps=cfexito_inp_ob.getInputs();
    // console.log( cf_excito_inps );
    //create input on cretae button click
    document.querySelectorAll(".cfexcito_createinp_btn")[0].onclick=function(eve){
    eve.preventDefault();
    cfexito_inp_ob.createINP();
    };
</script>