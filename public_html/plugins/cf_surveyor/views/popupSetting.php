<?php
global $mysqli;
global $dbpref;

if(isset($_GET['cfquizo_quiz_id']))
{
  if( is_numeric( $_GET['cfquizo_quiz_id'] ) )
  {
    $quiz_id = $_GET['cfquizo_quiz_id'];
  }

  $quiz_id=$mysqli->real_escape_string($quiz_id);
  $table =$dbpref."cfquiz_popup"; 
  $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `id`=".$quiz_id );
  $data = $returnOptions->fetch_assoc( );
  $table1=$dbpref.'cfquiz_popup_inputs';

  $returnInputs = $mysqli->query("SELECT * FROM `".$table1."` WHERE `quiz_id`=".$quiz_id." ORDER BY `position` ASC" );

  $cfquizo_header_content = $data['header_text'];
  $cfquizo_quiz_name =  $data['quiz_name'];
  $cfquizo_footer_content  =  $data['footer_text'];
  $quiz_setup  =  json_decode( $data['quiz_setup'] );
  $cfquizo_quiz_customcss=$data['quiz_css'];
  $fid = $data['id'];
  $is_global_quiz=$data['is_global'];

  $cfquizo_delay_time = ( isset( $quiz_setup->cfquizo_delay_time ) && is_numeric($quiz_setup->cfquizo_delay_time) ) ? $quiz_setup->cfquizo_delay_time : 1;

  $cfquizo_header_b_color = ( !empty( $quiz_setup->cfquizo_header_b_color) ) ? $quiz_setup->cfquizo_header_b_color: "#2400eab";

  $cfquizo_footer_b_color = ( !empty( $quiz_setup->cfquizo_footer_b_color) )  ? $quiz_setup->cfquizo_footer_b_color: "#333333";

  $cfquizo_submit_b_color = ( !empty( $quiz_setup->cfquizo_submit_b_color ) ) ? $quiz_setup->cfquizo_submit_b_color : "#ff0000";

  $cfquizo_submit_t_color = ( !empty( $quiz_setup->cfquizo_submit_t_color ) ) ? $quiz_setup->cfquizo_submit_t_color : "#ffffff";

  $cfquizo_submit_text = ( !empty( $quiz_setup->cfquizo_submit_text ) ) ? $quiz_setup->cfquizo_submit_text: "Submit";

  $cfquizo_is_popup = ( !empty( $quiz_setup->cfquizo_is_popup ) ) ? $quiz_setup->cfquizo_is_popup: '0';

  $cfquizo_theme = ( !empty( $quiz_setup->cfquizo_theme ) ) ? $quiz_setup->cfquizo_theme:"cfquizo_a";

  $cfquizo_bckground = ( !empty( $quiz_setup->cfquizo_bckground ) ) ? $quiz_setup->cfquizo_bckground:"no_bckground";

  $custom_bg_url_holder = ( !empty( $quiz_setup->custom_bg_url_holder ) ) ? $quiz_setup->custom_bg_url_holder:"";
  
  $cfquizo_quiz_animation = ( !empty( $quiz_setup->cfquizo_quiz_animation) ) ? $quiz_setup->cfquizo_quiz_animation : "";

  $cfquizo_allow_processcf=(isset($quiz_setup->allow_process_in_cf) && $quiz_setup->allow_process_in_cf==='0')? false:true; 

  $cfquizo_redirect_url= (isset($quiz_setup->redirect_url))? $quiz_setup->redirect_url:'';

  $dont_display_after_subscription=(isset($quiz_setup->dont_display_after_subscription) && $quiz_setup->dont_display_after_subscription=='1')? true:false;
}
else
{
  $is_global_quiz=0;
  $cfquizo_delay_time=1;
  $cfquizo_header_b_color ="#FFFFFF";
  $cfquizo_footer_b_color = "#FFFFFF";
  $cfquizo_submit_b_color ="#E3FFF0";
  $cfquizo_submit_t_color = "#000000";
  $cfquizo_submit_text ="Submit";
  $cfquizo_is_popup=0;
  $cfquizo_theme ="theme_a";
  $cfquizo_bckground ="no_bckground";
  $custom_bg_url_holder="";
  $cfquizo_quiz_animation = "";
  $cfquizo_allow_processcf=true; 
  $cfquizo_redirect_url='';
  $dont_display_after_subscription=true;
}
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Surveyor Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
          <div class="d-flex justify-content-end align-items-center">Create, Edit, Manage Survey</div>
      </div>
  </div>
  <?php if(isset($_GET['cfquizo_quiz_id']) && (!(isset($is_global_quiz) && $is_global_quiz))): ?>
    <div class="alert alert-warning">
      Use the shortcode  &nbsp;<span class="text-info">  <strong style="cursor:pointer;" onclick="copyText(`[cfquizo_shortcode id=<?php echo $fid; ?>]`)" data-bs-toggle="tooltip" title="Copy to clipboard">[cfquizo_shortcode id=<?php echo $fid; ?>]</strong>  </span>&nbsp; to show the popup on any funnel page.
    </div>
  <?php endif; ?>

    <form  id="cfquizo_AddSetting"  autocomplete="off">
        <div class="row bg-white shadow p-0 m-0 mb-3 ">
          <div class="col-lg-6 mt-4">
            <input type="hidden" id="cfquizo_ajax" name="cfquizo_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
          
              <input type="hidden" name="cfquizo_quiz_id" value="<?php echo ((isset($fid))? $fid:0); ?>">
              <input type="hidden" name="cfquizo_param" value="<?php echo ((isset($fid))? 'update_popupquiz':'save_popupquiz'); ?>">
            
              <h4 class="text-primary  p-2 px-4">Add Settings</h4>
              <div class="p-4 py-0 border">
                <div class="mb-3 py-1">
                  <label for="email">Enter Survey Name (Required)  </label>
                  <input type="text" name="cfquizo_quiz_name" required=""  id="cfquizo_quiz_name" value="<?php echo ((isset($cfquizo_quiz_name))? $cfquizo_quiz_name:'') ?>" class="form-control" placeholder="Enter Survey Name">
                </div>

                <div class="mb-3 py-1">
                  <label class="mb-2">Display Survey as</label>
                  
                  <div class="mb-3 mt-0">
                      <input type="radio" id="cfquizo_normal_show"  <?php echo (!(isset($cfquizo_is_popup) && $cfquizo_is_popup)? 'checked':''); ?>  name="cfquizo[cfquizo_is_popup]" value=0 onclick="functi()">
                      <label for="cfquizo_normal_show">As a Normal Page Content</label>
                  </div>
                  <div class="mb-3 mb-0">
                      <input type="radio" id="cfquizo_popup_show"  <?php echo ((isset($cfquizo_is_popup) && $cfquizo_is_popup)? 'checked':''); ?>  name="cfquizo[cfquizo_is_popup]"   value=1 onclick='functi()'>
                      <label for="cfquizo_popup_show">As a Popup</label>
                  </div>
                </div>
<script type="text/javascript">
  window.onload = function() {
  functi();
};
   function functi()
   {
if($('#cfquizo_normal_show').is(':checked')) { 
$('#delay').hide();
$('#animation').hide();
$('#quiz_subsc').hide();
$('#custom_css').hide();
}
if($('#cfquizo_popup_show').is(':checked')) { 
$('#delay').show();
$('#animation').show();
$('#quiz_subsc').show();
$('#custom_css').show();
}
}

</script>
                <div class="mb-3 py-1">
                  <label for="email">Submit Button Text:  </label>
                  <input type="text" name="cfquizo[cfquizo_submit_text]" required=""  id="cfquizo_submit_text" value="<?php echo ((isset($cfquizo_submit_text))? $cfquizo_submit_text:''); ?>" class="form-control" placeholder="Enter Submit Button Text">
                </div>
                <div class="mb-3 py-1" id="delay">
                   <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Popup Delay time in second</span>
                      </div>
                      <input type="number"  required="" value="<?php echo ((isset($cfquizo_delay_time))? $cfquizo_delay_time:''); ?>"   name="cfquizo[cfquizo_delay_time]" min="0" id="cfquizo_delay_time" class="form-control" >
                    </div>
                </div>
              </div>
            <div class=" border" >
              <h4 class="text-primary mb-3 p-2">Create Custom Input Field & Headers</h4>
              <hr/>
              <div class="mb-3 p-3" >
                <div class="row">
                  <div class="col-sm-12">
                    <div id="cfquizo_input_container" style="max-width:100%"></div>
                  </div>
                </div>
                <button class="btn btn-primary btn-block cfexcito_createinp_btn mt-2"><i class="fas fa-pencil-alt" type="button"></i>&nbsp;Create New</button>
              </div>
              <hr />
            </div>
          </div>
          <div class="col-lg-6 mt-4">  
            <h4 class="text-primary m-0 p-2">Add Extra Settings</h4>
            <div class="p-4 py-0 border mt-2 ">
              <div class="row mb-3 mt-2">
                  <label  class="col-sm-5 col-quiz-label align-self-center">Header Background Color</label>
                  <div class="col text-end">
                    <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfquizo_header_b_color))? $cfquizo_header_b_color:''); ?>" name="cfquizo[cfquizo_header_b_color]" value="">
                    </div>
              </div>
              <div class="row mb-3">
                <label  class="col-sm-5 col-quiz-label align-self-center">Footer Background Color</label>
                <div class="col text-end">
                  <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfquizo_footer_b_color))? $cfquizo_footer_b_color:''); ?>" name="cfquizo[cfquizo_footer_b_color]" value="">
                </div>
              </div>
              <div class="row mb-3">
                <label  class="col-sm-5 col-quiz-label align-self-center">Submit Button Background Color</label>
                <div class="col text-end">
                  <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfquizo_submit_b_color))? $cfquizo_submit_b_color:''); ?>" name="cfquizo[cfquizo_submit_b_color]" value="">
                </div>
              </div>
              <div class="row mb-3">
                <label  class="col-sm-5 col-quiz-label align-self-center">Submit Button Text Color</label>
                <div class="col text-end">
                  <input class="jscolor form-control form-control-sm" value="<?php echo ((isset($cfquizo_submit_t_color))? $cfquizo_submit_t_color:''); ?>" name="cfquizo[cfquizo_submit_t_color]" value="">
                </div>
              </div>
              <div class="mb-3 row" id="animation">
                <label for="boxposition" class="col-sm-4 col-quiz-label"> Survey Appearance Animation Type (in Popup Only)</label>
                <div class="col-sm-8">
                  <select class="form-control form-control-sm"  id="boxposition" name="cfquizo[cfquizo_quiz_animation]">
                      <option <?php if(isset($cfquizo_quiz_animation) && $cfquizo_quiz_animation=="t_to_c"){ echo "selected"; } ?>  value="t_to_c">Top To Center</option>
                      <option <?php if(isset($cfquizo_quiz_animation) && $cfquizo_quiz_animation=="r_to_c"){ echo "selected"; } ?> value="r_to_c">Right To Center</option>
                      <option <?php if(isset($cfquizo_quiz_animation) && $cfquizo_quiz_animation=="l_to_c"){ echo "selected"; } ?>  value="l_to_c">Left To Center</option>
                      <option <?php if(($cfquizo_quiz_animation) && $cfquizo_quiz_animation=="b_to_c"){ echo "selected"; } ?> value="b_to_c">Bottom To Center</option>
                      <option <?php if(isset($cfquizo_quiz_animation) && $cfquizo_quiz_animation=="c_to_c"){ echo "selected"; } ?> value="c_to_c">Center To Center</option>
                    </select> 
                  </div>
                </div> 
                  <div class="mb-3 row" id="custom_css">
                  <label for="customcss" class="col-sm-4 col-quiz-label align-self-center">Custom CSS</label>
                    <div class="col-sm-8">
                    <textarea class="form-control form-control-sm" id="customcss" rows="4"  name="cfquizo_custom_css"><?php echo ((isset($cfquizo_quiz_customcss))? $cfquizo_quiz_customcss:''); ?></textarea>
                    <p class="mt-0" style="font-size:12px !important;opacity:0.6;">**Use base selector name <strong>.this-quiz</strong><br>Example: <br><strong>.this-quiz input[type=text]<br>{border-radius: 5px;}</strong></p>
                    </div>
                  </div>
            
                <div class="mb-3 py-1" id="quiz_subsc">
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><input type="checkbox" value=1 name="cfquizo[dont_display_after_subscription]" <?php if($dont_display_after_subscription){echo "checked";} ?>></span></div>
                    <p class="form-control">Don't display the survey after subscription (in Popup Only)</p>
                  </div>
                </div>
                <div class="mb-3 py-1" style="display: none;">
                  <label class="mb-2">Display Setup</label>
                  <div class="mb-3 mb-0">
                    <input type="radio" id="cfquizo_global_show" name="cfquizo_is_global" <?php echo ((isset($is_global_quiz) && $is_global_quiz)? 'checked':''); ?> name="cfquizo_popop_where_show" value=1>
                      <label for="cfquizo_global_show">Display on all pages</label>
                  </div>
                  <div class="mb-3 mt-0">
                      <input type="radio" id="cfquizo_custom_show" <?php echo ((!(isset($is_global_quiz) && $is_global_quiz))? 'checked':''); ?> name="cfquizo_is_global" value=0>
                      <label for="cfquizo_custom_show">Pages where the shortcode will be applied</label>
                    </div>
                </div>
                
                <div class="mb-3">
                  <label class="mb-2">Manage how to process after survey submission</label>
                  <label><input type="radio" name="cfquizo[allow_process_in_cf]" value=1 <?php if(!(isset($cfquizo_allow_processcf) && !$cfquizo_allow_processcf)){ echo 'checked'; } ?>>&nbsp; Allow Survey submitted data to pass in main CloudFunnels process(next funnel page will be displayed)</label>
                  <label><input type="radio" name="cfquizo[allow_process_in_cf]" value=0 <?php if((isset($cfquizo_allow_processcf) && !$cfquizo_allow_processcf)){ echo 'checked'; } ?>>&nbsp; Redirect to given URL</label>
                  <input type="url" name="cfquizo[redirect_url]" class="form-control" placeholder="Enter URL" value="<?php echo( (isset($cfquizo_redirect_url) && filter_var($cfquizo_redirect_url, FILTER_VALIDATE_URL))? $cfquizo_redirect_url:'') ?>">
                </div>

                <div class="mb-3 p-3" >
                  <div class="row">
                    <div class="col-sm-12">
                      <div id="" style="max-width:100%"></div>
                    </div>
                  </div>
                  <?php if(isset($quiz_id))
                  {
                    ?>
                  <a href="<?php echo  get_option('install_url'); ?>/index.php?page=cfquiz_add_questions&cf_quizid_ques=<?=$quiz_id; ?>">
                        <p class="btn btn-primary d-grid"><i class="fas fa-pencil-alt" ></i>&nbsp;Manage Survey Questions</p></a>
                        <br>
                        <a href="<?php echo  get_option('install_url'); ?>/index.php?page=cfquiz_all_optins&cf_quizid_resp=<?=$quiz_id; ?>">
                        <p class="btn btn-primary d-grid"><i class="fas fa-pencil-alt" ></i>&nbsp;View Response</p></a>
                    <?php
                      }
                      else
                      {
                    ?>
                        <p class="btn btn-primary d-grid"><i class="fas fa-pencil-alt" ></i>&nbsp;Manage Survey Questions</p>
                        <br>
                        <p class="btn btn-primary d-grid"><i class="fas fa-pencil-alt" ></i>&nbsp;View Response</p>
                    <?php
                      }
                    ?>
                </div>
            </div>
          </div>
        </div>
        <div class="row p-0 m-0 mt-5 shadow bg-white">
          <div class="col-lg-6 p-3" style="margin-top: 10px;" >
            <h4 class="text-primary mb-3 p-2">Enter Header Content</h4>
            <textarea name="cfquizo_header_content" id="cfquizo_header_content"  class="form-control"> <?php echo ((isset($cfquizo_header_content))? $cfquizo_header_content:''); ?></textarea>
          </div>
          <div class="col-lg-6 p-3" style="margin-top: 10px;" >
            <h4 class="text-primary mb-3 p-2">Enter Footer Content</h4>
            <textarea name="cfquizo_footer_content" id="cfquizo_footer_content" class="form-control" > <?php echo ((isset($cfquizo_footer_content))? $cfquizo_footer_content:''); ?></textarea>
          </div>
        </div>
        <div class="mt-5 p-3 bg-white">
          <h4 class=" text-primary ">Choose Theme</h4>
          <hr class="mt-2 mb-5">
          <div class="row text-center text-lg-left">
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label for="cfquizo_a">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/a.jpg" alt="Theme image">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_theme]" value="theme_a" id="cfquizo_a" <?php if((isset($cfquizo_theme)) && $cfquizo_theme == "theme_a"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label for="cfquizo_b">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/b.jpg" alt="Theme image">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_theme]" value="theme_b" id="cfquizo_b" <?php if(isset($cfquizo_theme) && $cfquizo_theme == "theme_b"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label for="cfquizo_c">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/c.jpg" alt="Theme image">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_theme]" value="theme_c" id="cfquizo_c" <?php if(isset($cfquizo_theme) && $cfquizo_theme == "theme_c"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label for="cfquizo_d">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/d.jpg" alt="Theme image">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_theme]" value="theme_d" id="cfquizo_d" <?php if( isset($cfquizo_theme) && $cfquizo_theme == "theme_d"){ echo "checked"; }  ?> >
            </div>
          </div>
        </div>
        <div class="mt-5 p-3 bg-white">
          <h4 class=" text-primary ">Choose Background</h4>
          <hr class="mt-2 mb-5">
          <div class="row text-center text-lg-left" id="connect_with_radio">
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label  for="no_bckground">
                <img class="img-fluid img-thumbnail" src="<?=  plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/none.jpg" alt="Theme image">
              </label>
              <input type="radio" name="cfquizo[cfquizo_bckground]" value="no_bckground" id="no_bckground" <?php if((isset($cfquizo_bckground)) && $cfquizo_bckground == "no_bckground"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label  for="bckground1">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/bckground1.jpg" alt="Background">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_bckground]" value="bckground1" id="bckground1" <?php if(isset($cfquizo_bckground) && $cfquizo_bckground == "bckground1"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label  for="bckground2">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/bckground2.jpg" alt="Bckground2">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_bckground]" value="bckground2" id="bckground2" <?php if(isset($cfquizo_bckground) && $cfquizo_bckground == "bckground2"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfquizo_images ">
              <label for="bckground3">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/bckground3.jpg" alt="bckground3">
              </label>
              <input type="radio"   name="cfquizo[cfquizo_bckground]" value="bckground3" id="bckground3" <?php if( isset($cfquizo_bckground) && $cfquizo_bckground == "bckground3"){ echo "checked"; }  ?> >
            </div>
            <input type="radio" name="cfquizo[cfquizo_bckground]" value="custom_bg_url" id="custom_url" <?php if( isset($cfquizo_bckground) && $cfquizo_bckground == "custom_bg_url"){ echo "checked"; }  ?>  >  &nbsp;&nbsp;&nbsp;&nbsp;  Custom Background URL
          <input type="url" class="form-control" placeholder="Enter Background URL (May you Please upload image first in Media option in sidebar or direct image url)" name="cfquizo[custom_bg_url_holder]" value="<?php if( isset($cfquizo_bckground) && isset($custom_bg_url_holder) ){echo $custom_bg_url_holder;}  ?>"    > 
          </div>
          
        </div>

          <button type="submit" class="btn btn-primary cfquizo_save_setting float-end m-3 " id="cfquizo_save_setting">
          Save Changes</button>
      </form>
</div>
<script type="text/javascript">
    let cfquizo_inp_ob=new CFquizoMangeInputs();
   <?php
      if( isset($returnInputs) && $returnInputs->num_rows > 0 )
      {
        while( $data_input = $returnInputs->fetch_assoc() )
        {
           echo "
              cfquizo_inp_ob.createINP( '".$data_input['name']."','".$data_input['placeholder']."','".$data_input['title']."','".$data_input['required']."','".$data_input['type']."' );
            ";
        }
      }
    else{
      ?>
        cfquizo_inp_ob.createINP('name',"Enter Name","Enter Name",1,"text");
        cfquizo_inp_ob.createINP('email',"Enter Email","Enter Email",1,"email");
      
      <?php
    }
   ?>
    document.querySelectorAll(".cfexcito_createinp_btn")[0].onclick=function(eve){
    eve.preventDefault();
    cfquizo_inp_ob.createINP();
    };
</script>