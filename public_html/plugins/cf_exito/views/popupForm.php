<?php
  $header_content= $form_data['header_text'];
  $form_name= $form_data['form_name'];
  $footer_content= $form_data['footer_text'];
  $form_setup= json_decode($form_data['form_setup']);
  $fid= $form_data['id'];
  $custom_css=$form_data['form_css'];

  $div_id=time();
  $div_id .=$fid;
  $div_id =str_shuffle($div_id.'sdfghjkvbnijh');

  $delay_time =(int)(( isset( $form_setup->cfexito_delay_time ) && is_numeric($form_setup->cfexito_delay_time) ) ? $form_setup->cfexito_delay_time : 1);

  $header_b_color = ( !empty( $form_setup->cfexito_header_b_color) ) ? $form_setup->cfexito_header_b_color: "#2400eab";

  $header_t_color = ( !empty( $form_setup->cfexito_header_t_color) ) ? $form_setup->cfexito_header_t_color: "#000000";

  $footer_b_color = ( !empty( $form_setup->cfexito_footer_b_color) )  ? $form_setup->cfexito_footer_b_color: "#333333";

  $footer_t_color = ( !empty( $form_setup->cfexito_footer_t_color) )  ? $form_setup->cfexito_footer_t_color: "#ffffff";

  $success_b_color = ( !empty( $form_setup->cfexito_success_b_color ) ) ? $form_setup->cfexito_success_b_color : "#ff0000";

  $success_t_color = ( !empty( $form_setup->cfexito_success_t_color ) ) ? $form_setup->cfexito_success_t_color : "#ffffff";

  $submit_b_color = ( !empty( $form_setup->cfexito_submit_b_color ) ) ? $form_setup->cfexito_submit_b_color : "#ff0000";

  $submit_t_color = ( !empty( $form_setup->cfexito_submit_t_color ) ) ? $form_setup->cfexito_submit_t_color : "#ffffff";

  $submit_text = ( !empty( $form_setup->cfexito_submit_text ) ) ? $form_setup->cfexito_submit_text: "Subscribe us";

  $theme = ( !empty( $form_setup->cfexito_theme ) ) ? $form_setup->cfexito_theme : "theme_a";


  $use_as_exit = ( !empty( $form_setup->cfexito_use_as_exit) ) ? $form_setup->cfexito_use_as_exit: "";

  $popop_where_show = ( !empty( $form_setup->cfexito_popop_where_show) ) ? $form_setup->cfexito_popop_where_show : "";

  $form_animation = ( !empty( $form_setup->cfexito_form_animation) ) ? $form_setup->cfexito_form_animation : "";
?>
<link rel="stylesheet" href="<?php echo plugins_url('../assets/css/'.$theme.'.css?v='.$config_version,__FILE__) ?>"/>
<style type="text/css">
  /* Style the submit button */
  .cfexito-modal-<?php echo $div_id; ?> button[type=submit] {
    background-color: #<?=$submit_b_color ?>;
    color: #<?=$submit_t_color ?>;
    border: none;
  }
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-header
  {
    background-color: #<?=$header_b_color;  ?>;
  }
  .cfexito-modal-<?php echo $div_id; ?>  .cfexito-modal-footer
  {
    background-color: #<?=$footer_b_color;  ?>;
  }
  .cfexito-modal-<?php echo $div_id; ?>  .cfexito-success-message
  {
    background-color: #<?=$success_b_color;  ?>;
    color: #<?=$success_t_color;  ?>;
  }
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body p,
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body h1,
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body h2,
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body h3,
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body h4,
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body h5,
  .cfexito-modal-<?php echo $div_id; ?> .cfexito-modal-body h6
  {
    color: #000000;
    padding: 2px;
    text-align: left;
  }

  <?php
    if($form_animation == "t_to_c"): ?>
     /* Add Animation */
    @-webkit-keyframes cfexito_animatetop {
      from {top: -300px; opacity: 0} 
      to {top:  0; opacity: 1}
    }
    @keyframes cfexito_animatetop {
      from {top: -300px; opacity: 0} 
      to {top:  0; opacity: 1}
    }
    <?php  elseif($form_animation == "l_to_c"): ?>
     @-webkit-keyframes cfexito_animatetop {
    from {left:  -300px; opacity: 0} 
      to {left:  0; opacity: 1}
    }
    @keyframes cfexito_animatetop {
      from {left: -300px; opacity: 0} 
      to {left:  0; opacity: 1}
    }
    <?php  elseif($form_animation == "r_to_c"): ?>
     @-webkit-keyframes cfexito_animatetop {
    from {right:  -300px; opacity: 0} 
      to {right:  0; opacity: 1}
    }
    @keyframes cfexito_animatetop {
      from {right: -300px; opacity: 0} 
      to {right:  0; opacity: 1}
    }
    <?php  elseif($form_animation == "b_to_c"): ?>
     @-webkit-keyframes cfexito_animatetop {
    from {bottom:  -300px; opacity: 0} 
      to {bottom:  0; opacity: 1}
    }
    @keyframes cfexito_animatetop {
     from {bottom: -300px; opacity: 0} 
      to {bottom:  0; opacity: 1}
    }
    <?php elseif($form_animation == "c_to_c"): ?>
    @-webkit-keyframes cfexito_animatetop {
       from {-webkit-transform: scale(0)} 
      to {-webkit-transform: scale(1)}
    }
    @keyframes cfexito_animatetop {
       from {transform: scale(0)} 
      to {transform: scale(1)}
  }
 <?php endif; ?>  
</style>
<!-- The cfexito-modal -->
  <div id="cfexito-modal_<?php echo $div_id; ?>" class="cfexito-modal cfexito-modal-<?php echo $div_id; ?>" style=" max-width: 100%;
   width: 100%; display:none; ">
  <!-- cfexito-modal content -->
    <div class="cfexito-modal-content">
      <div class="cfexito-form">
          <span id="cfexito-modal-close-<?php echo $div_id; ?>" class="cfexito-modal-close" data-toggle="tooltip" title="Close Form">&times;</span>
          <div class="cfexito-modal-header">
            <div class="header_text" >
              <?=$header_content; ?>
            </div>
          </div>
          <div class="cfexito-modal-body" style="max-height:320px;overflow:auto;width:100%;">
            <form action="" method="post" id="cfexito_AddUserData">
              <input type="hidden" name="cfexito_form_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
              <input type="hidden" id="cfexito_user_ajax" name="cfexito_user_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
              <input type="hidden" name="cfexito_nonce" value="<?=cf_create_nonce('cfexito_nonce_'.$fid .''); ?>"  >
              <?php
                $fetch_data= $this->cfexitoGetFormInput( $fid );
                $header_count=0;
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfexito-header cfexito-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea 
                    name="<?=$data['name'];  ?>" 
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
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>"
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
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else {
                      ?>
                    <input 
                      type="<?= $data['type'] ?>" 
                      name="<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                }
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php } ?>
              <button type="submit" name="cfexito_store_data"><?php echo $submit_text; ?></button>
            </form>
          </div>
          <div class="cfexito-modal-footer">
              <?= $footer_content;  ?>
          </div>
        </div>    
    </div>
</div>

<script src="<?php echo plugins_url('../assets/js/user_script.js?v='.$config_version,__FILE__); ?>"></script>
<script>
  cfexitoDoLoadUserSideScript(`<?php echo $div_id; ?>`,<?php echo (int)$delay_time; ?>,<?php if($use_as_exit=='yes'){echo 'true';}else{echo 'false';} ?>,<?php echo ((strlen($show_err)>0)? 'true':'false'); ?>);
</script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-form', '.cfexito-modal-'.$div_id, $data);
  echo $data;
}
?>