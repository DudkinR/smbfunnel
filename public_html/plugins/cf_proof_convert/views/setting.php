<?php
global $mysqli;
global $dbpref;

if(isset($_GET['cfproof_convert_setup_id']))
{
  if( is_numeric( $_GET['cfproof_convert_setup_id'] ) )
  {
    $setup_id = $_GET['cfproof_convert_setup_id'];
  }

  $setup_id=$mysqli->real_escape_string($setup_id);
  $table =$dbpref."cfproof_convert_setup";
   
  $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `id`=".$setup_id );

  $data = $returnOptions->fetch_assoc( );
  if(empty($data)){
    $no_permission=get_option('install_url')."/index.php?page=no_permission";
    header("Location:".$no_permission."");
  }
  $cfproof_convert_title = $data['title'];
  $setup  =  json_decode( $data['setup'] );
  $fake_datas  =  json_decode( $data['fake_data'],true );
  $fake_data=(!empty($fake_datas))?$fake_datas:[];
  $setup_css=json_decode($data['setup_css']);
  $cfproof_convert_text_color=(!empty($setup_css->text_color))?$setup_css->text_color:"#000000";
  $cfproof_convert_background_color= (!empty($color=$setup_css->background_color))?$setup_css->background_color:"#ffffff";
  $cfproof_convert_name_color=(!empty($setup_css->name_color))?$setup_css->name_color:"#000000";
  $cfproof_convert_address_color=(!empty($setup_css->address_color))?$setup_css->address_color:"#000000";
  $cfproof_convert_product_title_color=(!empty($setup_css->product_title_color))?$setup_css->product_title_color:"#000000";
  $cfproof_convert_product_link_color=(!empty($setup_css->product_link_color))?$setup_css->product_link_color:"#000000";
  $cfproof_convert_times_color=(!empty($setup_css->times_color))?$setup_css->times_color:"#000000";

  $custom_css=$mysqli->real_escape_string($setup_css->custom_css);
  $custom_css=explode("\\r\\n", rtrim( $custom_css, "\\r\\n"));
  
  $funnels=$data['funnels'];
  $setup_d = $data['id'];
  $notifications=$mysqli->real_escape_string($data['notification']);
  $notifications=explode("\\r\\n", rtrim( $notifications, "\\r\\n"));
  
  $cfproof_convert_product = ( !empty( $setup->product_id) ) ? $setup->product_id: "";
  $page_url =(!empty( $setup->page_url) ) ? $setup->page_url:"";
  $cfproof_convert_page_url=explode("\\r\\n",rtrim($page_url,"\\r\\n") );
  $cfproof_convert_message_type =(!empty( $setup->message_type) ) ? $setup->message_type:"r";
  $cfproof_convert_redirect_url=( !empty($setup->redirect_url) ) ?$setup->redirect_url:"#";
  $cfproof_convert_position=( !empty($setup->position) ) ?$setup->position:"";
  $cfproof_convert_rotative=( !empty($setup->rotative) ) ?$setup->rotative:"";
  $cfproof_convert_theme=( !empty($setup->theme) ) ?$setup->theme:"theme_a";
  $cfproof_convert_delay_time=( !empty($setup->delay_time) ) ?$setup->delay_time:15;
  $cfproof_convert_showing_time=( !empty($setup->showing_time) ) ?$setup->showing_time:10;
  $cfproof_convert_link_text=( !empty($setup->link_text) ) ?$setup->link_text:"Buy Now";
  $cfproof_convert_country=( !empty($setup->country) ) ?$setup->link_text:"Somewhere";
  $dont_display_after_click=(isset($setup->dont_display_after_click) && $setup->dont_display_after_click=='1')? true:false;

}
else
{
  $cfproof_convert_link_text="Buy Now";
  $cfproof_convert_text_color="#000000";
  $cfproof_convert_background_color= "#ffffff";
  $cfproof_convert_name_color="#000000";
  $cfproof_convert_address_color="#000000";
  $cfproof_convert_product_title_color="#000000";
  $cfproof_convert_product_link_color="#FF1919";
  $cfproof_convert_times_color="#000000";
  $cfproof_convert_title="";
  $cfproof_convert_product="";
  $cfproof_convert_page_url=[];
  $cfproof_convert_message_type="r";
  $cfproof_convert_rotative="yes";
  $cfproof_convert_position="bl";
  $cfproof_convert_redirect_url="#";
  $cfproof_convert_theme="theme_a";
  $funnels="";
  $notifications[]='{name} from {address}';
  $notifications[]='has purchased {product.title} !{product.link}';
  $notifications[]='{times_ago}';
  $custom_css=[];
  $cfproof_convert_delay_time=15;
  $cfproof_convert_showing_time=10;
  $dont_display_after_click=false;
  $cfproof_convert_country="Country";
}
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Proof Convert setup settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
          <div class="d-flex justify-content-end align-items-center">Create, edit, manage setup</div>
      </div>
  </div>
  <div class="container">
    <form  id="cfproof_convert_AddSetting"  autocomplete="off" spellcheck="false">
      <input type="hidden" id="cfproof_convert_ajax" name="cfproof_convert_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
      <input type="hidden" name="cfproof_convert_setup_id" value="<?php echo ((isset($setup_id))? $setup_id:0); ?>">
      <input type="hidden" name="cfproof_convert_param" value="<?php echo ((isset($setup_id))? 'update_setup':'save_setup'); ?>">
        <div class="text-primary">
          <h4>Add Title</h4>
          <div class="mb-3">
            <input type="text" required name="cfproof_convert_title" placeholder="Add Unique Title" class="form-control" value="<?= $cfproof_convert_title; ?>" >
          </div>
        </div>
        <h4 class="text-primary">Design</h4>
        <div class="bg-white shadow mb-4 p-4">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-6 text-center  ">
              <div class="cfproof_convert_theme">
                <label for="theme_a">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/theme_a.png" alt="Theme image">
              </label>
              </div>
              <input type="radio" class="cfproof_convert_theme_input"   name="cfproof_convert[theme]" value="theme_a" id="theme_a" 
              <?php if( isset( $cfproof_convert_theme ) && $cfproof_convert_theme == "theme_a"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-4 col-md-4 col-6 text-center">
              <div class="cfproof_convert_theme">
                <label for="theme_b">
                  <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/theme_b.png" alt="Theme image">
                </label>
              </div>
                <input type="radio" class="cfproof_convert_theme_input"  name="cfproof_convert[theme]" value="theme_b" id="theme_b" <?php if((isset( $cfproof_convert_theme )) && $cfproof_convert_theme == "theme_b"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-4 col-md-4 col-6 text-center">
              <div class="cfproof_convert_theme">
                <label for="theme_c">
                  <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/theme_c.png" alt="Theme image">
                </label>
              </div>
                <input type="radio"  class="cfproof_convert_theme_input" name="cfproof_convert[theme]" value="theme_c" id="theme_c" <?php if((isset( $cfproof_convert_theme )) && $cfproof_convert_theme == "theme_c"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-4 col-md-4 col-6 text-center">
              <div class="cfproof_convert_theme">
                <label for="theme_d">
                  <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/theme_d.png" alt="Theme image">
                </label>
              </div>
                <input type="radio" class="cfproof_convert_theme_input"  name="cfproof_convert[theme]" value="theme_d" id="theme_d" <?php if((isset( $cfproof_convert_theme )) && $cfproof_convert_theme == "theme_d"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-4 col-md-4 col-6 text-center">
              <div class="cfproof_convert_theme">
                <label for="theme_e">
                  <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/theme_e.png" alt="Theme image">
                </label>
              </div>
                <input type="radio" class="cfproof_convert_theme_input"  name="cfproof_convert[theme]" value="theme_e" id="theme_e" <?php if((isset( $cfproof_convert_theme )) && $cfproof_convert_theme == "theme_e"){ echo "checked"; }  ?> >
            </div>
            <div class="col-lg-4 col-md-4 col-6 text-center">
              <div class="cfproof_convert_theme">
                <label for="theme_f">
                  <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/image/theme_f.png" alt="Theme image">
                </label>
              </div>
                <input type="radio" class="cfproof_convert_theme_input"  name="cfproof_convert[theme]" value="theme_f" id="theme_f" <?php if((isset( $cfproof_convert_theme )) && $cfproof_convert_theme == "theme_f"){ echo "checked"; }  ?> >
            </div>
          </div>
        </div>
        <h4 class="text-primary">Setup</h4>
        <div class="bg-white shadow mb-4 p-4">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="email">Select Product:</label>
                <?php
                 $all_products = get_products(-1);
                ?>
                <select name="cfproof_convert[product_id]" class="form-control">
                  <?php
                  // $all_products=[];
                    foreach ($all_products as $key => $product) {
                      if($cfproof_convert_product==$product['id'])
                      {
                        echo "<option value='".$product['id']."' selected>".$product['title']."</option>";
                      }else{
                        echo "<option value='".$product['id']."'>".$product['title']."</option>";
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label>Select Funnel</label>
                <div class="dropdown">
                  <button type="button" class="btn border btn-block dropdown-toggle" data-bs-toggle="dropdown">Select Funnels</button>
                  <input type="hidden" name="cfproof_convert_funnels[]" value="f">
                  <div id="allprooducts" class="dropdown-menu btn-block  ps-2" style="overflow-y: auto;max-height: 150px;">
                    <?php 
                    $explod_funnel=explode(",", $funnels);
                    if(in_array("f",$explod_funnel))
                    {
                      if(in_array("all", $explod_funnel))
                        {
                          echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="cfproof_convert_funnels[]" value="all">All funnels </label></div>';
                      }else{
                        echo ' <div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="cfproof_convert_funnels[]" value="all">All funnels </label></div>';
                      }
                    }else{
                      echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="cfproof_convert_funnels[]" value="all">All funnels </label></div>';
                    }
                    $fnls = get_funnels();
                    foreach ( $fnls as $f ) {
                      if(in_array("f", $explod_funnel))
                      {
                        if(in_array($f['id'],$explod_funnel)){
                          echo ' <div class=""><label>&nbsp;<input type="checkbox" checked class="me-3" name="cfproof_convert_funnels[]" value="'  .  $f['id']  . '">' .  $f['name'] .  ' </label></div>';
                        }else{
                          echo '<div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="cfproof_convert_funnels[]" value="'  .  $f['id']  . '">' .  $f['name'] .  ' </label></div>';
                        }
                      }else{
                        echo '<div class=""><label>&nbsp;<input type="checkbox" class="me-3" name="cfproof_convert_funnels[]" value="'  .  $f['id']  . '">' .  $f['name'] .  ' </label></div>';
                      }
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="">Enter specific page URLs (one on each line):</label>
                <textarea name="cfproof_convert[page_url]" rows="4" class="form-control cfproof_convert_page_url"><?php foreach ($cfproof_convert_page_url as $page_url) {echo str_ireplace(" ", "", rtrim($page_url))."\r\n";} ?></textarea>
                <div class="cfproof_convert_notes">*Does not matter which funnel is selected on these pages the message needs to be displayed</div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="email">Message Data Type</label>
                <select class="form-control cfproof_convert_message_type" name="cfproof_convert[message_type]">
                  <option value="r" <?php if($cfproof_convert_message_type=="r"){ echo "selected"; } ?> >Real</option>
                  <option value="f" <?php if($cfproof_convert_message_type=="f"){ echo "selected"; } ?> >Auto Generated</option>
                  <option value="b" <?php if($cfproof_convert_message_type=="b"){ echo "selected"; } ?> >Both</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >Delay Time Between each Notification</label>
                <div class="input-group mb-3">
                  <input type="number" class="form-control" placeholder="Delay Time" name="cfproof_convert[delay_time]" value="<?=$cfproof_convert_delay_time?>">
                  <div class="input-group-append">
                    <span class="input-group-text">seconds</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >Display each notification for * seconds</label>
                <div class="input-group mb-3">
                  <input type="number" class="form-control" placeholder="Showing Time" name="cfproof_convert[showing_time]" value="<?=$cfproof_convert_showing_time; ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">seconds</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >Notification Position</label>
                <select class="form-control cfproof_convert_position" name="cfproof_convert[position]">
                  <option value="bl" <?php if($cfproof_convert_position=="bl"){ echo "selected"; } ?> >Bottom Left</option>
                  <option value="br" <?php if($cfproof_convert_position=="br"){ echo "selected"; } ?> >Bottom Right</option>
                  <option value="tl" <?php if($cfproof_convert_position=="tl"){ echo "selected"; } ?> >Top Left</option>
                  <option value="tr" <?php if($cfproof_convert_position=="tr"){ echo "selected"; } ?> >Top Right</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label>Enter Redirect url:</label>
                <input type="text" class="form-control" value="<?=$cfproof_convert_redirect_url ?>"  name="cfproof_convert[redirect_url]">
                <div class="cfproof_convert_notes">*If you want user redirect after click on notifcation</div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3 py-1">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <input type="checkbox" value="checked" 
                        <?php 
                        if(isset($cfproof_convert_rotative) && $cfproof_convert_rotative == "yes" )
                        {
                          echo "checked";
                        } 
                        ?> id="cfproof_convert_rotative" />
                      </div>
                    </div>
                    <input type="hidden" id="cfproof_convert_rotative_hidden"  name="cfproof_convert[rotative]" value="<?php echo ((isset($cfproof_convert_rotative))? $cfproof_convert_rotative:''); ?>"  >
                    <p class="form-control">If there are no more messages to display, it will show the previous messages on loop </p>
                  </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><input type="checkbox" value=1 name="cfproof_convert[dont_display_after_click]" <?php if($dont_display_after_click){echo "checked";} ?>></span></div>
                <p class="form-control">Don't display the notification after click</p>
              </div>
            </div>
            </div>
        </div>
        <h4 class="text-primary">Content</h4>
        <div class="bg-white shadow mb-4 p-4">
          <h5 class="text-primary">Add Notification Content</h5>
          <textarea rows="5" name="cfproof_convert_notification" class="form-control"><?php foreach ($notifications as $notifcation) {echo str_ireplace(" ", " ", rtrim($notifcation))."\r\n";} ?></textarea>
          <div class="mb-3 mt-4">
            <h6 for="email">{product.link} Text</h6>
              <input type="text" name="cfproof_convert[link_text]" class="form-control" value="<?=$cfproof_convert_link_text; ?>">              
          </div>
        </div>
        <h4 class="text-primary">Customize</h4>
        <div class="bg-white shadow mb-4 p-4">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label >Text Color</label>
                <input class="jscolor form-control form-control-sm" value="<?=$cfproof_convert_text_color; ?>" name="cfproof_convert_css[text_color]">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >Background Color</label>
                <input class="jscolor form-control form-control-sm" value="<?= $cfproof_convert_background_color; ?>" name="cfproof_convert_css[background_color]">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >{name} Color</label>
                <input class="jscolor form-control form-control-sm" value="<?=$cfproof_convert_name_color; ?>" name="cfproof_convert_css[name_color]">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >{address} Color</label>
                <input class="jscolor form-control form-control-sm" value="<?=$cfproof_convert_address_color; ?>" name="cfproof_convert_css[address_color]" >
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >{product.title} Color</label>
                <input class="jscolor form-control form-control-sm" value="<?= $cfproof_convert_product_title_color; ?>" name="cfproof_convert_css[product_title_color]" value="">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >{product.link} Color</label>
                <input class="jscolor form-control form-control-sm" value="<?= $cfproof_convert_product_link_color; ?>" name="cfproof_convert_css[product_link_color]" value="">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label >{times_ago} Color</label>
                <input class="jscolor form-control form-control-sm" value="<?= $cfproof_convert_times_color; ?>" name="cfproof_convert_css[times_color]" value="">
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="email">Custom Css</label>
                 <textarea rows="5" name="cfproof_convert_css[custom_css]" class="form-control"><?php foreach ($custom_css as $css) {echo str_ireplace(" ", " ", rtrim($css))."\r\n";} ?></textarea>
                <p class="mt-0" style="font-size:12px !important;opacity:0.7;">**Use base selector name <strong>.this-setup</strong><br>Example: <br><strong>.this-setup input[type=text]<br>{border-radous: 5px;}</strong>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="cfproof_convert_overlay">
          <div class="cfproof_convert_body  col-sm-6">
            <div class="card pnl visual-pnl">
              <div class="card-header">Enter Virtual Data
                <i class="fas fa-times cfproof_convert_close"></i>
              </div>
              <div class="card-body " style="max-height:500px;overflow-y:auto;">
                <div class="cfproof_convert_fake_container" id="cfproof_convert_fake_container">
                  
                </div>
                <div class="text-center">
                  <button type="button" class="btn btn-primary cfproof_convert_createfake_btn mt-2 me-2"><i class="fas fa-pencil-alt" type="button"></i>&nbsp;Create New&nbsp;</button>
                  <button type="button" class="btn btn-success cfproof_convert_createfake_save mt-2"><i class="fas fa-save" type="button"></i>&nbsp;Save</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-primary cfproof_convert_save_setting float-end m-3 " id="cfproof_convert_save_setting">Save Changes</button>
      </form>
  </div> 
</div>
<script type="text/javascript">
  let cfproof_convert_fake_ob=new CFProofConvertMangeInputs();
  //to create input fieldcreateINP=function(name='',placeholder='',title='',required=1, type='text'){}
  <?php if( isset($fake_data) && count($fake_data) > 0 ) {
      foreach( $fake_data as $f_data ){
      echo "cfproof_convert_fake_ob.createINP( '".$f_data['name']."','".$f_data['email']."','".$f_data['address']."');";
      }
    }
    else{
      ?>
      cfproof_convert_fake_ob.createINP('John Doe',"john@gmail.com","New jersy, USA");
      cfproof_convert_fake_ob.createINP('Ama watson',"watson@gmail.com","New Youk,USA");
      <?php
    }
   ?>
  document.querySelectorAll(".cfproof_convert_createfake_btn")[0].onclick=function(eve){
    eve.preventDefault();
    cfproof_convert_fake_ob.createINP();
  };
</script>