<?php
  global $mysqli;
  global $dbpref;

  // Include the main css file.
  
  $position = '';
  $form_appear = $form_data['form_appear'];
  $button_align = $form_data['button_align'];
  $header_b_color = $form_data['headBackCol'];
  $headerPadding = $form_data['headerPadding'];
  $headerMargin = $form_data['headerMargin'];
  $formBackCol = $form_data['formBackCol'];
  $footerPadding = $form_data['footerPadding'];
  $footerMargin = $form_data['footerMargin'];
  $submit_b_color = $form_data['submitBackCol'];
  $submit_t_color = $form_data['submitBtnCol'];
  $footer_b_color = $form_data['footBackCol'];
  $errorTxtCol = $form_data['errorTxtCol'];
  $form_name = $form_data['form_name'];
  $fid = $form_data['formId'];
  $custom_css = $form_data['custom_css'];
  $form_width = $form_data['form_width'];
  
  $main_css = plugins_url('../themes/assets/css/'. strtolower($form_data['theme_id']).'.css', __FILE__);
  echo "<link rel='stylesheet' href='$main_css'>";

  $div_id = time();
  $div_id .= $fid;
  $div_id = str_shuffle($div_id.'sdfghjkvbnijh');
  
  // Setup the animation type.
	self::add_css( $form_appear, $formBackCol, $submit_b_color, $submit_t_color, $header_b_color, $footer_b_color, $div_id, $form_width, $headerPadding, $headerMargin, $footerPadding, $footerMargin, $errorTxtCol );

  // Add the css if the user add the custom css.
  if(strlen($custom_css)>0) {
    $data="<style>".$custom_css."</style>";
    $data=str_replace('.this-form', '.cfrespo-modal-'.$div_id, $data);
    echo $data;
  }

  // Choose theme using the theme id.
  if($form_data['theme_id'] == "Theme1"){
    include plugin_dir_path(__FILE__).'../themes/Theme1.php';
  }
  
  else if($form_data['theme_id'] == "Theme2"){
    include plugin_dir_path(__FILE__).'../themes/Theme2.php';
  }
  
  else if($form_data['theme_id'] == "Theme3"){
    include plugin_dir_path(__FILE__).'../themes/Theme3.php';
  }
  
  else if($form_data['theme_id'] == "Theme4"){
    include plugin_dir_path(__FILE__).'../themes/Theme4.php';
  }
?>
  
<script src = "<?php echo plugins_url('../assets/js/user_script.js?v='.$config_version, __FILE__ ); ?>"></script>
<script>
  cfrespoDoLoadUserSideScript(
    `<?php echo $div_id; ?>`, 
    `<?php echo $fid; ?>`, 
    <?php echo $form_data['use_as_exit']; ?>,
    <?php echo $form_data['use_as_delay']; ?>, 
    <?php echo $form_data['on_btn_click']; ?>, 
    parseInt("<?php echo $form_data['delay_value']; ?>")*1000,
    <?php echo ((strlen($show_err) > 0) ? 'true':'false'); ?>,
    <?php echo $form_width; ?>
  );
</script>