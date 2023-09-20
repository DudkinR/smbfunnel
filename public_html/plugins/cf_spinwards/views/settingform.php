<?php

global $mysqli;
global $dbpref;
$optin_controller=$this->load('forms_control');
$cfspinnerwheel="";$cfspinnernum="";$cfspinfont="";$cfspinfontstyle="";$cfspinslicefontsize="";$cfspinmainheader="";$cfspinmaifooter="";
$cfspinwheeltype="";$cfspinner_theme="";$str_array="";
if(isset($_GET['cfspinner_wheelid']))
{
  if( is_numeric( $_GET['cfspinner_wheelid'] ) )
  {
    $cfspinner_wheelid = $_GET['cfspinner_wheelid'];
  }
$settings= $optin_controller->getSaveSettings($cfspinner_wheelid);


$cfspinnerwheel = $settings['cfspinnerwheel'];
$cfspinnernum = $settings['cfspinnernum'];
$cfspinfont = $settings['cfspinfont'];
$cfspinfontstyle = $settings['cfspinfontstyle'];
$cfspinslicefontsize = $settings['cfspinslicefontsize'];
$cfspinmainheader = $settings['cfspinmainheader'];
$cfspinmaifooter = $settings['cfspinmaifooter'];
$cfspinwheeltype = $settings['cfspinwheeltype'];
 $cfspinner_theme = $settings['cfspinner_theme'];
 $cfspinmainheader = $settings['cfspinmainheader'];
 $cfspinmaifooter = $settings['cfspinmaifooter'];
 $cfspinmailsub = $settings['cfspinmailsub'];
 $cf_spinner_mailerbody =$settings['cf_spinner_mailerbody'];
$fid = $settings['id'];
   $words= $settings['cfslicepricenames'];
$str_array = json_decode($words,true);
$lengtharr =sizeof($str_array);
 $table1=$dbpref.'spinner_popup_forminputs';
$cfspinnerbgimgurl =$settings['cfspinnerbgimgurl'];
 $returnInputs = $mysqli->query("SELECT * FROM `".$table1."` WHERE `cfspinwheelid`=".$cfspinner_wheelid." ORDER BY `position` ASC" );


}
?>





<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="">Wheel  Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
          <div class="d-flex justify-content-end align-items-center">Manage wheels</div>
      </div>
  </div>
    <ul class="nav nav-tabs md-tabs nav-justified theme-nav rounded-top  d-flex flex-column flex-sm-row" role="tablist">
        <li class="nav-item  settingbtn">
          <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">
           Basic</a>
         </li>
 
     <li class="nav-item tablebtn">
         <a class="nav-link" data-bs-toggle="tab" href="#menu2" role="tab">
         Preview</a>
       </li>

</ul>

<form method="post" id="wheelsetting" enctype="multipart/form-data">
<input type="hidden" id="cfspinsettingajax" value="<?php echo get_option("install_url") ?>/index.php?page=ajax">
<input type="hidden" name="cfspinner_wheel_id" value="<?php echo ((isset($fid))? $fid:0); ?>">
              <input type="hidden" name="cfspinner_param" value="<?php echo ((isset($fid))? 'update_settingForm':'save_settingForm'); ?>">
    <input type="hidden" name="cfspinnerwheeltheme" value="<?php echo $cfspinner_theme;?>">
    <div class="card p-4 py-0 border">
  <div class="tab-content ">
    <div id="home" class="container tab-pane active">
    <?php if(isset($_GET['cfspinner_wheelid']) && (!(isset($is_global_form) && $is_global_form))): ?>
    <div class="alert alert-warning">
      Use the shortcode  &nbsp;<span class="text-info">  <strong style="cursor:pointer;" onclick="copyText(`[show_wheel id=<?php echo $cfspinner_wheelid; ?>]`)" data-bs-toggle="tooltip" title="Copy to clipboard">[show_wheel id=<?php echo $cfspinner_wheelid; ?>]</strong>  </span>&nbsp; to show the wheel on any funnel page.
    </div>
    <div class="alert alert-warning">
      Use this  &nbsp;<span class="text-info">  <strong style="cursor:pointer;" onclick="copyText(`{winprize}`)" data-bs-toggle="tooltip" title="Copy to clipboard">{winprize}</strong>  </span>&nbsp; to send wining prize name in mailbody.
    </div>
  <?php endif; ?>
    <div class="row">
          <div class="col-lg-6 mt-4 p-4 py-0 border">
          <h4 class=" mb-3 p-2">Basic Wheel  Setting 
          </h4>
        
        <div class="mb-3">
       <label >Enter Wheel Name</label>
        <input type="text" class="form-control"  placeholder="Enter spin Wheel name "  name="cfspinnerwheel" value="<?php echo $cfspinnerwheel;?>" required> 
        </div>


         <div class="mb-3">
       <label >Enter Number of complete spins.</label>
        <input type="number" class="form-control"  placeholder="Enter 1 to 10 any number"  name="cfspinnernum" value="<?php echo $cfspinnernum;?>" min="1" max="10" required >
        </div>

        <div class="mb-3">
       <label >Select Type</label>
<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="cfspinwheeltype" style="width: -webkit-fill-available;
">
<option  <?php if($cfspinwheeltype=="Popup"){echo "selected";} ?> value="Popup">Popup</option>
<option <?php if($cfspinwheeltype=="Embeded"){echo "selected";} ?> value="Embeded">Embeded</option>


</select> 
    </div> 
 

    
         </div>
         <div class="col-lg-6 mt-4 p-4 py-0 border ">
         <h4 class=" mb-3 p-2">Basic Font  Setting 
          </h4>
         <div class="mb-3">
       <label >Select Font for Wheel</label>
<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="cfspinfont" style="width: -webkit-fill-available;
">
<option  <?php if($cfspinfont=="Arial"){echo "selected";} ?> value="Arial">Arial</option>
<option  <?php if($cfspinfont=="Poppins"){echo "selected";} ?> value="Poppins">Poppins</option>
<option  <?php if($cfspinfont=="Roboto"){echo "selected";} ?> value="Roboto">Roboto</option>
<option  <?php if($cfspinfont=="Georgia"){echo "selected";} ?> value="Georgia">Georgia</option>
<option  <?php if($cfspinfont=="Verdana"){echo "selected";} ?> value="Verdana">Verdana</option>
<option  <?php if($cfspinfont=="Times"){echo "selected";} ?> value="Times">Times</option>

</select> 
    </div>   

   <div class="mb-3">
       <label >Select Font  style</label>
<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="cfspinfontstyle" style="width: -webkit-fill-available;
">
<option  <?php if($cfspinfontstyle=="normal"){echo "selected";} ?> value="normal">Normal</option>
<option  <?php if($cfspinfontstyle=="bold"){echo "selected";} ?> value="bold">Bold</option>
<option  <?php if($cfspinfontstyle=="bolder"){echo "selected";} ?> value="bolder">Bolder</option>
<option  <?php if($cfspinfontstyle=="lighter"){echo "selected";} ?> value="lighter">Lighter</option>

</select> 
    </div>  
    
    <div class="mb-3">
       <label >Enter Font Size</label>
        <input type="number" class="form-control"  placeholder="Enter  font size for slices text"  name="cfspinslicefontsize" value="<?php if(!empty($cfspinslicefontsize)) { echo $cfspinslicefontsize;}else{ echo "23";}?>" required>
        </div>


     </div>
       

     
         
</div>
<br>

<div class="row ">
          <div class="col-lg-6 mt-4 p-4 py-0 border">
          <h4 class=" mb-3 p-2">Mail  Setting 
          </h4>
          <div class="mb-3">
       <label >Enter Subject of your email</label>
       <textarea name="cfspinmailsub" id="cfspinmailsub"  class="form-control" > <?php if(!empty($cfspinmailsub)) {echo $cfspinmailsub;}else{echo "Congratulations,You won";} ?></textarea>
 
        </div>
  <p>Enter Body Of your email</p>
  <textarea name="cf_spinner_mailerbody" id="cf_spinner_mailerbody"  class="form-control" > <?php if(!empty($cf_spinner_mailerbody)) {echo $cf_spinner_mailerbody;}else{echo "You Have  won {winprize}";} ?></textarea>

</div>
<div class="col-lg-6 mt-4 p-4 py-0 border">
<h4 class=" mb-3 p-2">Create Custom Input Field</h4>
           
           <div class="mb-3 p-3" >
             <div class="row">
               <div class="col-sm-12">
                 <div id="cfspinner_input_container" style="max-width:100%"></div>
               </div>
             </div>
             <button class="btn btn-primary btn-block cfexcito_createinp_btn mt-2"><i class="fas fa-pencil-alt" type="button"></i>&nbsp;Create New</button>
           </div>
           </div>
           </div>

   <div class="row border">
          <div class="col-lg-6 p-3" style="margin-top: 10px;" >
            <h4 class=" mb-3 p-2">Enter Header Content</h4>
            <textarea name="cf_spinner_header_Content" id="cf_spinner_header_Content"  class="form-control" > <?php if(!empty($cfspinmainheader)) {echo $cfspinmainheader;}else{echo "Spin To  Win";} ?></textarea>
          </div>
          <div class="col-lg-6 p-3" style="margin-top: 10px;" >
            <h4 class=" mb-3 p-2">Enter Footer Content</h4>
            <textarea name="cf_spinner_footer_Content" id="cf_spinner_footer_Content" class="form-control" > <?php if(!empty($cfspinmaifooter)) { echo $cfspinmaifooter;}else{ echo "-One spin per user";} ?></textarea>
          </div>
        </div>
        


<?php  
  if($cfspinner_theme =="cfwheelcolbg")
  {
  ?><br>
  <h4>  You can edit slice Name ,background color and font color</h4><br>
<?php   

foreach ($str_array as $key=> $value) {
  
  ?>  
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabell[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="<?php echo $value['cfslicelabel'];?>" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolorr[]"   placeholder="Choose color for particulat label"   value="<?php echo $value['cfslicelabelcolor'];?>" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolorr[]"    placeholder="Choose color for particulat label"   value="<?php echo $value['cfslicefontcolor'];?>" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>



    <?php
  }
}elseif ($cfspinner_theme == "cfwheelimgbg")
{
  ?>
  <br>
   <h4>  You can edit slice Name ,background Image  and font color</h4><br>
    <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Image URL</label>
              </div>
              <div class="col-lg-9">
                <div class="input-group mb-3">
                <input type="text" name="cfspinnerwheelimgurll" id="cfspinner-image-r"  value="<?php  echo $cfspinnerbgimgurl;  ?>" class="form-control">
                  <div class="input-group-append">
           <button class="btn btn-success" onclick="cfspinnermedia('#cfspinner-image-r', false)">Upload</button>
                  </div>
               
                </div>
              </div>
            </div>
            <p style="color:red;">The image size should be 300*300 and shape is circle</p>
  <?php
  foreach ($str_array as $key=> $value) {

  
  ?>

<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabell[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off"  value="<?php echo $value['cfsliceimglabel'];?>" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolorr[]"    placeholder="Choose color for particulat label"  value="<?php echo $value['cfsliceimgfontcolor'];?>" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>









<?php
  }
}else{?>




 <div class="mt-5 p-3 bg-white">
          <h4 class=" ">Choose Wheel Type</h4>
          <div class="row text-center text-lg-left">
            <div class="col-lg-3 col-md-4 col-6 text-center cfexito_images ">
              <label for="cfexito_a">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/img/basicwheel.png" alt="Theme image">
              </label>
              Color Background <input type="radio"   name="cfspinner_theme" value="cfwheelcolbg" required data-id="bank" <?php if($cfspinner_theme=="cfwheelcolbg") {echo "checked";}?> />
            </div>
            <div class="col-lg-3 col-md-4 col-6 text-center cfexito_images ">
              <label for="cfexito_b">
                <img class="img-fluid img-thumbnail" src="<?= plugin_dir_url( dirname(__FILE__) ) ?>/assets/img/basicimg.png" alt="Theme image">
              </label>
              Image background <input type="radio"   name="cfspinner_theme" value="cfwheelimgbg"   data-id="school" <?php if($cfspinner_theme=="cfwheelimgbg") {echo "checked";}?>  />
            </div>
          
          
          </div>
        </div>
<?php }?>

        <div id="bank" class="none">
        <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Image URL</label>
              </div>
              <div class="col-lg-9">
                <div class="input-group mb-3">
                <input type="text" name="cfspinnerwheelimgurl" id="cfspinner-image-r"  value="<?php $plugin_url = plugin_dir_url( dirname( __FILE__,1 ) );
			$plugin_url = rtrim($plugin_url,"/");echo $plugin_url."/assets/img/planes.png" ;?>" class="form-control">
                  <div class="input-group-append">
           <button class="btn btn-success" onclick="cfspinnermedia('#cfspinner-image-r', false)">Upload</button>
                  </div>
                </div>
              </div>
            </div>            <p style="color:red;">The image size should be 300*300 and shape is circle</p>

            
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 1" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 2" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 3" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 4" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 5" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-4">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfsliceimglabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 6" required>
    </div>
  
    <div class="mb-3 col-sm-4">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfsliceimgfontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-4">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>

  
        

        
        </div>
<div id="school" class="none">

<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 1" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label"  value="#02366f" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    placeholder="Choose color for particulat label"  value="#000000" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 2" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#fd4928" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    value="#000000"  placeholder="Choose color for particulat label"  required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 3" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#f8b214" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    value="#000000"  placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 4" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#01b78f" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"   value="#000000"  placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>

  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 5" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label"  value="#02366f" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    value="#000000"  placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 6" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#fd4928" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    value="#000000"  placeholder="Choose color for particulat label"  required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 7" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#f8b214" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"   value="#000000"  placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 8" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#01b78f" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    value="#000000"  placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  <div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 9" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label"  value="#02366f" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"   value="#000000"   placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
  
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 10" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#fd4928" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    value="#000000"  placeholder="Choose color for particulat label"  required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 11" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#f8b214" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"   value="#000000"  placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>
<div class="form-row input_fields_container ">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" value="Prize 12" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" value="#01b78f" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"   value="#000000"   placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>







</div>
 <div class="mb-3">
  <button type="submit" class="btn theme-button btnclr float-end" name="save">Save Settings</button>
  <br>
</div>
</div>
<!-- </div> -->
 
<div id="menu2" class="container tab-pane fade"><br>

<?php
if(isset($_GET['cfspinner_wheelid']))
{
       global $mysqli;
      global $dbpref;
      $table= $dbpref.'spinwheel_setting';
      $sql = "SELECT * FROM ".$table." where `id`=".$_GET['cfspinner_wheelid'];
      $qru = $mysqli->query($sql);
       $rs = $qru->fetch_assoc();
      $words= $rs['cfslicepricenames'];


     

?>
 <canvas id="canvas" width="880" height="440"
    data-responsiveMinWidth="180"
    data-responsiveScaleHeight="true"   
    data-responsiveMargin="50"
    >


</canvas>

  
  </div>
  <?php  
if($cfspinner_theme == "cfwheelcolbg")
{

?>
  <script>
     
      let theWheel = new Winwheel({
          'numSegments'  : <?php echo $lengtharr;?>,     
          'outerRadius'  : 212,  
          'textFontSize' : <?php echo $cfspinslicefontsize;?>,  
          'segments'     :        
          [
          <?php

             foreach ($str_array as $value) {
?>
             {'fillStyle' : '<?php echo $value['cfslicelabelcolor']; ?>','textFillStyle':'<?php echo $value['cfslicefontcolor']; ?>', 'textFontFamily' : '<?php echo $rs['cfspinfont'];  ?>','textFontWeight':'<?php echo $rs['cfspinfontstyle'];  ?>', 'text' : '<?php echo $value['cfslicelabel'];?>'},

             <?php }?>
           
          ],
          'animation' :            
          {
              'type'     : 'spinToStop',
                 'pins'         :'true',
                     'duration' : 5,   
              'spins'    : <?php echo $cfspinnernum;?>,    
'callbackFinished' : alertPrize                }
      });
      
      function startSpin()
    {
        theWheel.stopAnimation(false);
 
        theWheel.rotationAngle = theWheel.rotationAngle % 360;
 
        theWheel.startAnimation();
    }
    function alertPrize(indicatedSegment)
            {
                alert("You have won " + indicatedSegment.text);
            }




  </script>

<?php
 }elseif ($cfspinner_theme == "cfwheelimgbg") {
  ?>
 <script>
            // Create new wheel object specifying the parameters at creation time.
            let theWheel = new Winwheel({
              'numSegments'  : <?php echo $lengtharr;?>,     
              'outerRadius'       : 150,       // Set outer radius so wheel fits inside the background.
          'textFontSize' : <?php echo $cfspinslicefontsize;?>,       // Set outer radius so wheel fits inside the background.
                'drawMode'          : 'image',   // drawMode must be set to image.
                'drawText'          : true,      // Need to set this true if want code-drawn text on image wheels
     
              'segments'     :        
          [
          <?php

             foreach ($str_array as $value) {
?>
             {'textFillStyle':'<?php echo $value['cfsliceimgfontcolor']; ?>', 'textFontFamily' : '<?php echo $rs['cfspinfont'];  ?>','textFontWeight':'<?php echo $rs['cfspinfontstyle'];  ?>', 'text' : '<?php echo $value['cfsliceimglabel'];?>'},

             <?php }?>
           
          ],
                'animation' :                   // Specify the animation to use.
                {
                    'type'     : 'spinToStop',
                    'duration' : 5,     // Duration in seconds.
                    'spins'    : 8,     // Number of complete spins.
                    'callbackFinished' : alertPrize
                }
            });

            // Create new image object in memory.
            let loadedImg = new Image();

            // Create callback to execute once the image has finished loading.
            loadedImg.onload = function()
            {
                theWheel.wheelImage = loadedImg;    // Make wheelImage equal the loaded image object.
                theWheel.draw();                    // Also call draw function to render the wheel.
            }

            // Set the image source, once complete this will trigger the onLoad callback (above).
            loadedImg.src = "<?php echo $cfspinnerbgimgurl;?>";


           
            function startSpin()
            {
                
                if (wheelSpinning == false) {
                   

                 
                    theWheel.startAnimation();

                  
                    wheelSpinning = true;
                }
            }


          
            // -------------------------------------------------------
            function alertPrize(indicatedSegment)
            {
                alert("The wheel stopped on " + indicatedSegment.text);
            }
        </script><?php  }}
else{
echo"<center><p>First Save Wheel setting to see an preview</p></center>";
}?>

</div>




</div>
</div>
</div>
</form>

<script>
$(document).ready(function() {
var max_fields_limit      = 12;
var x = 2; 
$('.add_more_button').click(function(e){ 
  e.preventDefault();
  if(x < max_fields_limit){
      x++; 

      $('.input_fields_container').append(`
<div class="form-row">
    <div class="mb-3 col-sm-3">
      <label for="">Enter Slice Name</label>
      <input type="text" class="form-control"    name="cfslicelabel[]" placeholder="Price  name  like Ex ALLMost ,No luck,10 %off" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice background color</label>
      <input type="color" class="form-control"    name="cfslicelabelcolor[]"   placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
      <label for="inputPassword4">Choose Slice font  color</label>
      <input type="color" class="form-control"    name="cfslicefontcolor[]"    placeholder="Choose color for particulat label" required>
    </div>
    <div class="mb-3 col-sm-3">
<button  class="btn btn-danger remove_field"  style="margin-left:10px;margin-top:30px;"  >Remove</button>
</div>
  </div>`); 
   }
});  
$('.input_fields_container').on("click",".remove_field", function(e){
  e.preventDefault();  
    $(this).closest(".form-row").remove();  x--;
})
});
</script>
<style type="text/css">
.add_more_button{
margin-bottom:10px;
}
.none {
    display:none;
}
</style>
<script type="text/javascript">
    let cfspin_inp_ob=new CFSpinnerMangeInputs();


    <?php
      if( isset($returnInputs) && $returnInputs->num_rows > 0 )
      {
        while( $data_input= $returnInputs->fetch_assoc() )
        {
           echo "
           cfspin_inp_ob.createINP( '".$data_input['name']."','".$data_input['placeholder']."','".$data_input['title']."','".$data_input['required']."','".$data_input['type']."' );
            ";
        }
      }
    else{
      ?>
  
        cfspin_inp_ob.createINP("name","Enter Name","Enter Name",1,"text");
        cfspin_inp_ob.createINP("email","Enter Email","Enter Email",1,"email");
        
      
      <?php
    }
   ?>

    document.querySelectorAll(".cfexcito_createinp_btn")[0].onclick=function(eve){
    eve.preventDefault();
    cfspin_inp_ob.createINP();
  //  console.log(cfspin_inp_ob);
    };


    $(':radio').change(function (event) {
    var id = $(this).data('id');
    $('#' + id).addClass('none').siblings().removeClass('none');
});








        function cfspinnermedia(selector, html)
        {
            try
            {
                //here calling open media
                openMedia(function(content){
                    try
                    {
                        document.querySelectorAll(selector)[0].value= content;
                    }catch(err){console.log(err);}
                }, html);
            }catch(err){console.log(err)}
        }
</script>
<?php
//here we are imporing  cf_media
cf_media();
?>