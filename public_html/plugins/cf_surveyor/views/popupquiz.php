<?php
  
  $header_content= $quiz_data['header_text'];
  $quiz_name= $quiz_data['quiz_name'];
  $footer_content= $quiz_data['footer_text'];
  $quiz_setup= json_decode($quiz_data['quiz_setup']);
  $fid= $quiz_data['id'];
  $custom_css=$quiz_data['quiz_css'];

  $div_id=time();
  $div_id .=$fid;
  $div_id =str_shuffle($div_id.'sdfghjkvbnijh');

  $delay_time =(int)(( isset( $quiz_setup->cfquizo_delay_time ) && is_numeric($quiz_setup->cfquizo_delay_time) ) ? $quiz_setup->cfquizo_delay_time : 1);
  
  $header_b_color = ( !empty( $quiz_setup->cfquizo_header_b_color) ) ? $quiz_setup->cfquizo_header_b_color: "#2400eab";  

  $footer_b_color = ( !empty( $quiz_setup->cfquizo_footer_b_color) )  ? $quiz_setup->cfquizo_footer_b_color: "#333333";

  $success_b_color = ( !empty( $quiz_setup->cfquizo_success_b_color ) ) ? $quiz_setup->cfquizo_success_b_color : "#ff0000";

  $success_t_color = ( !empty( $quiz_setup->cfquizo_success_t_color ) ) ? $quiz_setup->cfquizo_success_t_color : "#ff88ff";

  $submit_b_color = ( !empty( $quiz_setup->cfquizo_submit_b_color ) ) ? $quiz_setup->cfquizo_submit_b_color : "#ff0000";

  $submit_t_color = ( !empty( $quiz_setup->cfquizo_submit_t_color ) ) ? $quiz_setup->cfquizo_submit_t_color : "#ffffff";

  $submit_text = ( !empty( $quiz_setup->cfquizo_submit_text ) ) ? $quiz_setup->cfquizo_submit_text: "Submit Quiz";

  $theme = ( !empty( $quiz_setup->cfquizo_theme ) ) ? $quiz_setup->cfquizo_theme : "theme_a";
  
  $bckground = ( !empty( $quiz_setup->cfquizo_bckground ) ) ? $quiz_setup->cfquizo_bckground : "no_bckground";

  $custom_bg_url_holder = ( !empty( $quiz_setup->custom_bg_url_holder ) ) ? $quiz_setup->custom_bg_url_holder : "";  

  $use_as_exit = ( !empty( $quiz_setup->cfquizo_use_as_exit) ) ? $quiz_setup->cfquizo_use_as_exit: "";

  $popop_where_show = ( !empty( $quiz_setup->cfquizo_popop_where_show) ) ? $quiz_setup->cfquizo_popop_where_show : "";

  $allow_process_in_cf = ( !empty( $quiz_setup->allow_process_in_cf) ) ? $quiz_setup->allow_process_in_cf : "";

  $redirect_url = ( !empty( $quiz_setup->redirect_url) ) ? $quiz_setup->redirect_url : "";
 
  $quiz_animation = ( !empty( $quiz_setup->cfquizo_quiz_animation) ) ? $quiz_setup->cfquizo_quiz_animation : "";
 
?>

<link rel="stylesheet" href="<?php echo plugin_dir_url(dirname(__FILE__,1)).'assets/css/'.$theme.'.css'  ?>"/>
<style type="text/css">
  /* Style the submit button */
  .cfquizo-modal-<?php echo $div_id; ?> button[type=submit] {
    background-color: #<?=$submit_b_color ?>;
    color: #<?=$submit_t_color ?>;
    border: none;
  }
  .cfquizo-modal-<?php echo $div_id; ?> .cfquizo-modal-header
  {
    background-color: #<?=$header_b_color;  ?>;
  }
  .cfquizo-modal-<?php echo $div_id; ?>  .cfquizo-modal-footer
  {
    background-color: #<?=$footer_b_color;  ?>;
  }
  .cfquizo-modal-<?php echo $div_id; ?>  .cfquizo-success-message
  {
    background-color: #<?=$success_b_color;  ?>;
    color: #<?=$success_t_color;  ?>;
  }

  <?php
    if($quiz_animation == "t_to_c"): ?>
     /* Add Animation */
    @-webkit-keyframes cfquizo_animatetop {
      from {top: -300px; opacity: 0} 
      to {top:  0; opacity: 1}
    }
    @keyframes cfquizo_animatetop {
      from {top: -300px; opacity: 0} 
      to {top:  0; opacity: 1}
    }
    <?php  elseif($quiz_animation == "l_to_c"): ?>
     @-webkit-keyframes cfquizo_animatetop {
    from {left:  -300px; opacity: 0} 
      to {left:  0; opacity: 1}
    }
    @keyframes cfquizo_animatetop {
      from {left: -300px; opacity: 0} 
      to {left:  0; opacity: 1}
    }
    <?php  elseif($quiz_animation == "r_to_c"): ?>
     @-webkit-keyframes cfquizo_animatetop {
    from {right:  -300px; opacity: 0} 
      to {right:  0; opacity: 1}
    }
    @keyframes cfquizo_animatetop {
      from {right: -300px; opacity: 0} 
      to {right:  0; opacity: 1}
    }
    <?php  elseif($quiz_animation == "b_to_c"): ?>
     @-webkit-keyframes cfquizo_animatetop {
    from {bottom:  -300px; opacity: 0} 
      to {bottom:  0; opacity: 1}
    }
    @keyframes cfquizo_animatetop {
     from {bottom: -300px; opacity: 0} 
      to {bottom:  0; opacity: 1}
    }
    <?php elseif($quiz_animation == "c_to_c"): ?>
    @-webkit-keyframes cfquizo_animatetop {
       from {-webkit-transquiz: scale(0)} 
      to {-webkit-transquiz: scale(1)}
    }
    @keyframes cfquizo_animatetop {
       from {transquiz: scale(0)} 
      to {transquiz: scale(1)}
  }
 <?php endif; ?>  
</style>
<?php
/********************************************************************************************************************
*  This block is starts at 125 ends at line no. 335 this will show the theme_a related design.                      *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/

if($theme=="theme_a")
  {
?>

  <div id="cfquizo-modal_<?php echo $div_id; ?>" class="cfquizo-modal cfquizo-modal-<?php echo $div_id; ?>" style=" max-width: 100%;
   width: 100%; display:none; ">
  <!-- cfquizo-modal content -->
    <div class="cfquizo-modal-content">
      <div class="cfquizo-quiz">
          <span id="cfquizo-modal-close-<?php echo $div_id; ?>" class="cfquizo-modal-close" data-bs-toggle="tooltip" title="Close quiz">&times;</span>
          <div class="cfquizo-modal-header">
            <div class="header_text" >
              <?=$header_content; ?>
            </div>
          </div>
          <div class="cfquizo-modal-body">

            <form action="" method="post" id="regForm">

<div class="row d-flex justify-content-around" id="show_questions">

  <div class="col-lg-6">
     <center> <h3 class="quiz_label">Select Your Answer Below:</h3></center>
    <div class="progress" id="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progbr">
    </div>
  </div>
<?php  
            $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="py-0 tab">
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3 mx-5" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" >
                      &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" style="opacity: 1;" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />
                      <div>'.htmlentities(base64_decode($opt_value)).'</div></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
      
?>
<div class="p-4 py-0 tab"  id="form_design" class="mb-3"><center>
  <input type="hidden" name="cfquizo_quiz_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
  <input type="hidden" id="cfquizo_user_ajax" name="cfquizo_user_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
  <input type="hidden" name="cfquizo_nonce" value="<?=cf_create_nonce('cfquizo_nonce_'.$fid .''); ?>"  >
  <input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
  <input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                echo "<h3>See your results</h3>";
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control" 
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input  class="form-control" 
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }//end of while loop
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php     } 
               ?>
              <input type="hidden" name='submit_button' value='submit_button' >
             <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
           </center>
           </div>
      </div>
      

<?php
if(strlen(($custom_bg_url_holder))>17   &&  $bckground=='custom_bg_url')
{  
?>

<div class="col-lg-6"><img id="imgctrl"  src="<?php echo $custom_bg_url_holder ?>"></div>

<?php
}
else
{

     if($bckground=='no_bckground')
      {
        echo '<div  class="col-lg-6 mt-5 d-flex justify-content-center align-items-center"><h1>No Background</h1></div>';
      }
      else
      { ?>


<div class="col-lg-6"><img id="imgctrl" src="<?php echo plugins_url('../assets/image/'.$bckground.'.jpg',__FILE__) ?>"></div>

<?php
}
}
?>

         </div>
            </form>
          </div>
          <input type="hidden" id="cfquiz_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
          <div class="cfquizo-modal-footer">
              <?= $footer_content;  ?>
          </div>
        </div>    
    </div>
</div>
<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<script src="<?php echo plugins_url('../assets/js/user_script.js?v='.$config_version,__FILE__); ?>"></script>
<script>
  cfquizoDoLoadUserSideScript(`<?php echo $div_id; ?>`,<?php echo (int)$delay_time; ?>,<?php if($use_as_exit=='yes'){echo 'true';}else{echo 'false';} ?>,<?php echo ((strlen($show_err)>0)? 'true':'false'); ?>);
</script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  echo $data;
}


}//theme a ends here.. 


/********************************************************************************************************************
*  This block is starts at 330 ends at line no. 515 this will show the "theme_b" related design.                    *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_b")
{
?>

  <div id="cfquizo-modal_<?php echo $div_id; ?>" class="cfquizo-modal cfquizo-modal-<?php echo $div_id; ?>" style=" max-width: 100%;
   width: 100%; display:none; ">
  <!-- cfquizo-modal content -->
    <div class="cfquizo-modal-content">
      <div class="cfquizo-quiz">
          <span id="cfquizo-modal-close-<?php echo $div_id; ?>" class="cfquizo-modal-close" data-bs-toggle="tooltip" title="Close quiz">&times;</span>
          <div class="cfquizo-modal-header">
            <div class="header_text" >
              <?=$header_content; ?>
            </div>
          </div>
          <div class="cfquizo-modal-body">

            <form action="" method="post" id="regForm">

<div class="row d-flex justify-content-around" id="show_questions">

  <div class="col-lg-12">
     <center> <h3 class="quiz_label">Answer these simple questions.</h3></center>
    <div class="progress" id="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progbr">
    </div>
  </div>
<?php  
            $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="py-0 tab">
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" >
                      &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" style="opacity: 1;" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />&nbsp;&nbsp;&nbsp;&nbsp;
                      <div>'.htmlentities(base64_decode($opt_value)).'</div></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
      
?>
<br>
<div class="tab"  id="form_design" class="mb-3"><center>
  <br>
  <input type="hidden" name="cfquizo_quiz_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
  <input type="hidden" id="cfquizo_user_ajax" name="cfquizo_user_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
  <input type="hidden" name="cfquizo_nonce" value="<?=cf_create_nonce('cfquizo_nonce_'.$fid .''); ?>"  >
  <input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
  <input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                echo "<h3>See your results</h3><br>";
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }//end of while loop
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php     } 
               ?>
              <input type="hidden" name='submit_button' value='submit_button' >
             <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
           </center>
           </div><br>
      </div>
         </div>
            </form>
          </div>
          <input type="hidden" id="cfquiz_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
          <div class="cfquizo-modal-footer">
              <?= $footer_content;  ?>
          </div>
        </div>    
    </div>
</div>
<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<script src="<?php echo plugins_url('../assets/js/user_script.js?v='.$config_version,__FILE__); ?>"></script>
<script>
  cfquizoDoLoadUserSideScript(`<?php echo $div_id; ?>`,<?php echo (int)$delay_time; ?>,<?php if($use_as_exit=='yes'){echo 'true';}else{echo 'false';} ?>,<?php echo ((strlen($show_err)>0)? 'true':'false'); ?>);
</script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  echo $data;
}
}//theme b ends here.. 


/********************************************************************************************************************
*  This block is starts at 515 ends at line no. 690 this will show the "theme_c" related design.                    *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_c")
{
?>

  <div id="cfquizo-modal_<?php echo $div_id; ?>" class="cfquizo-modal cfquizo-modal-<?php echo $div_id; ?>" style=" max-width: 100%;
   width: 100%; display:none; ">
  <!-- cfquizo-modal content -->
    <div class="cfquizo-modal-content">
      <div class="cfquizo-quiz">
          <span id="cfquizo-modal-close-<?php echo $div_id; ?>" class="cfquizo-modal-close" data-bs-toggle="tooltip" title="Close quiz">&times;</span>
          <div class="cfquizo-modal-header">
            <div class="header_text" >
              <?=$header_content; ?>
            </div>
          </div>
          <div class="cfquizo-modal-body">

            <form action="" method="post" id="regForm">

<div class="row d-flex justify-content-around" id="show_questions">

  <div class="col-lg-12">
     <center> <h3 class="quiz_label">Answer these simple questions</h3></center>
    <div class="progress" id="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progbr">
    </div>
  </div>
<?php  
            $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="py-0 tab">
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" ><center>
                      <input type="radio" style="opacity: 0;" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />&nbsp;&nbsp;&nbsp;&nbsp;
                      <div>'.htmlentities(base64_decode($opt_value)).'</div></center></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
      
?>
<br>
<div class="tab"  id="form_design" class="mb-3"><center>
  <br>
  <input type="hidden" name="cfquizo_quiz_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
  <input type="hidden" id="cfquizo_user_ajax" name="cfquizo_user_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
  <input type="hidden" name="cfquizo_nonce" value="<?=cf_create_nonce('cfquizo_nonce_'.$fid .''); ?>"  >
  <input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
  <input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                echo "<h3>See your results</h3><br>";
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }//end of while loop
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php     } 
               ?>
              <input type="hidden" name='submit_button' value='submit_button' >
             <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
           </center>
           </div><br>
      </div>
         </div>
         <br>
            </form>
          </div>
          <input type="hidden" id="cfquiz_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
          <div class="cfquizo-modal-footer">
              <?= $footer_content;  ?>
          </div>
        </div>    
    </div>
</div>
<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<script src="<?php echo plugins_url('../assets/js/user_script.js?v='.$config_version,__FILE__); ?>"></script>
<script>
  cfquizoDoLoadUserSideScript(`<?php echo $div_id; ?>`,<?php echo (int)$delay_time; ?>,<?php if($use_as_exit=='yes'){echo 'true';}else{echo 'false';} ?>,<?php echo ((strlen($show_err)>0)? 'true':'false'); ?>);
</script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  echo $data;
}
}//theme c ends here.. 


/********************************************************************************************************************
*  This block is starts at 695 ends at line no. 880 this will show the "theme_d" related design.                    *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_d")
{
?>

  <div id="cfquizo-modal_<?php echo $div_id; ?>" class="cfquizo-modal cfquizo-modal-<?php echo $div_id; ?>" style=" max-width: 100%;
   width: 100%; display:none; ">
  <!-- cfquizo-modal content -->
    <div class="cfquizo-modal-content">
      <div class="cfquizo-quiz">
          <span id="cfquizo-modal-close-<?php echo $div_id; ?>" class="cfquizo-modal-close" data-bs-toggle="tooltip" title="Close quiz">&times;</span>
          <div class="cfquizo-modal-header">
            <div class="header_text" >
              <?=$header_content; ?>
            </div>
          </div>
          <div class="cfquizo-modal-body">

            <form action="" method="post" id="regForm">

<div class="row d-flex justify-content-around" id="show_questions">

  <div class="col-lg-12">
     <center> <h3 class="quiz_label">Answer these simple questions</h3></center>
   
<?php  
            $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="py-0 tab">
             <span id="counter_nu">'.$counter.'/'.$qry->num_rows.'</span>
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" ><center>
                      <input type="radio" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />&nbsp;&nbsp;&nbsp;&nbsp;
                      <div>'.htmlentities(base64_decode($opt_value)).'</div></center></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
      
?>
<div class="tab"  id="form_design" class="mb-3"><center>
  <br>
  <input type="hidden" name="cfquizo_quiz_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
  <input type="hidden" id="cfquizo_user_ajax" name="cfquizo_user_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
  <input type="hidden" name="cfquizo_nonce" value="<?=cf_create_nonce('cfquizo_nonce_'.$fid .''); ?>"  >
  <input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
  <input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                echo "<h3>See your results</h3><br>";
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }//end of while loop
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php     } 
               ?>
              <input type="hidden" name='submit_button' value='submit_button' >
             <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
           </center>
           </div><br>
      </div>
         </div>
         <br>
            </form>
          </div>
          <input type="hidden" id="cfquiz_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
          <div class="cfquizo-modal-footer">
              <?= $footer_content;  ?>
          </div>
        </div>    
    </div>
</div>
<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<script src="<?php echo plugins_url('../assets/js/user_script.js?v='.$config_version,__FILE__); ?>"></script>
<script>
  cfquizoDoLoadUserSideScript(`<?php echo $div_id; ?>`,<?php echo (int)$delay_time; ?>,<?php if($use_as_exit=='yes'){echo 'true';}else{echo 'false';} ?>,<?php echo ((strlen($show_err)>0)? 'true':'false'); ?>);
</script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  echo $data;
}
}//theme d ends here.. 


?>