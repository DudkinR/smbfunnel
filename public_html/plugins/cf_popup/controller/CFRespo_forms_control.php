<?php
// Main AJAX function to split the data.
if(!class_exists('CFRespo_forms_control')) {
    class CFRespo_forms_control {
        function __construct($arr) {
            $this->loader=$arr['loader'];
        }

        public function getAjaxRequest( $ajax_data ) {
            global $mysqli;
            global $dbpref;

            $form_table = $dbpref.'respo_popup_form';
            $form_extra_settings = $dbpref.'respo_extra_settings';
            $form_inputs = $dbpref.'respo_popup_inputs';

            if($ajax_data['cfrespo_param'] == "delete") {
                $formId = $mysqli -> real_escape_string($ajax_data['form_id']);
                $result = $mysqli->query( "SELECT * FROM `".$form_table."` WHERE `formId`='$formId'" );
                $row = $result->fetch_assoc();

                $mysqli->query("DELETE `".$form_table."`, `".$form_extra_settings."` FROM `".$form_table."` INNER JOIN `".$form_extra_settings."` ON `".$form_table."`.formId=`".$form_extra_settings."`.formId WHERE `".$form_table."`.formId='$formId'");
                $mysqli->query("DELETE FROM $form_inputs WHERE form_id='$formId'");
                $echo_array = array(
                    'id' => 0,
                    'msg' => 'Successfully Deleted' 
                );
                echo json_encode( $echo_array );
                return;
            }

            else if($ajax_data['cfrespo_param'] == "insert"){
                echo $this -> insertAndUpdateSettingsForm( $ajax_data, $mysqli, $dbpref );
                return;
            }

            else if($ajax_data['cfrespo_param'] == "update"){
                echo $this -> insertAndUpdateSettingsForm( $ajax_data, $mysqli, $dbpref );
                return;
            }

            else if($ajax_data['cfrespo_param'] == "preview") {
                $this -> previewData( $ajax_data, $mysqli );
                return;
            }

            else {
                echo json_encode( array('Error', 'No Permission') );
            }

            die();
        }

        function previewData( $ajax_data, $mysqli ) {
            $formName = $mysqli -> real_escape_string( htmlspecialchars($ajax_data['form_name']));
            $headerText = stripcslashes($mysqli -> real_escape_string( trim($ajax_data['header_text'])));
            $footerText = stripcslashes($mysqli -> real_escape_string( trim($ajax_data['footer_text'])));

            $formBackCol = $mysqli -> real_escape_string($ajax_data['formBackCol']);
            $headerBackCol = $mysqli -> real_escape_string($ajax_data['headerBackCol']);
            $headerPadding = $mysqli -> real_escape_string($ajax_data['headerPadding']);
            $headerMargin = $mysqli -> real_escape_string($ajax_data['headerMargin']);
            $footerBackCol = $mysqli -> real_escape_string($ajax_data['footerBackCol']);
            $footerPadding = $mysqli -> real_escape_string($ajax_data['footerPadding']);
            $footerMargin = $mysqli -> real_escape_string($ajax_data['footerMargin']);
            $submitBackCol = $mysqli -> real_escape_string($ajax_data['submitBackCol']);
            $submitBtnText = $mysqli -> real_escape_string($ajax_data['submit_btn_text']);
            $submitBtnCol = $mysqli -> real_escape_string($ajax_data['submitBtnCol']);
            $errorTxtCol = $mysqli -> real_escape_string($ajax_data['errorTxtCol']);
            $button_align = $mysqli -> real_escape_string($ajax_data['cfrespo_button_align']);
            $form_appear = $mysqli -> real_escape_string($ajax_data['formAnimation']);
            $custom_css = $mysqli -> real_escape_string($ajax_data['customCSS']);
            $form_width = $mysqli -> real_escape_string($ajax_data['cfrespo_form_width']);

            $delay = $mysqli -> real_escape_string($ajax_data['popup_delay_time']);
            $themeId =strtolower($mysqli -> real_escape_string($ajax_data['select_theme']));
            $theme_css = plugins_url('../themes/assets/css/'.$themeId.'.css', __FILE__);
            $div_id = 0;
            $this -> add_css( $form_appear, $formBackCol, $submitBackCol, $submitBtnCol, $headerBackCol, $footerBackCol, $div_id, $form_width, $headerPadding, $headerMargin, $footerPadding, $footerMargin, $errorTxtCol );
            echo "<link rel='stylesheet' href='$theme_css'>";

            $cus_input = [];
            
            foreach ( $ajax_data as $key => $data ) {
                $data_expload = explode( "@", $key );
                if( $data_expload[0] == "custom" ) {
                    $key = trim( htmlspecialchars( stripcslashes( $key ) ) );
                    $c_data=json_decode( $data, true );
                    $cus_input=$c_data;
                }
            }

            $position = 1; ?>

            <script>
                $('#previewThemeForm').on('submit', function(e) {
                    e.preventDefault();
                });
                function cfrespoPreviewScript(div_id) {
                    let popupFormModal = document.getElementById(`cfrespo-modal_${div_id}`);
                    let close_btn = document.getElementById(`cfrespo-modal-close-${div_id}`);
                    popupFormModal.classList.add('show');
                    popupFormModal.style.display="block";
                    close_btn.onclick = function() {
                        popupFormModal.classList.remove('show');
                        popupFormModal.style.display="none";
                    }
                    window.onclick = function(event) {
                        if (event.target == popupFormModal) {
                            popupFormModal.classList.add('show');
                            popupFormModal.style.display="none";
                        }
                    };
                }
            </script>
            
            <?php
            if( $themeId == "theme1" ) {
              ?>
                  <div  class="modal cfrespo_preview_modal fade cfrespo-modal-<?php echo $div_id; ?>" id="cfrespo-modal_<?php echo $div_id; ?>" aria-hidden="true" role="dialog">
                    <div class="modal-dialog cfrespo_theme-1" role="document">
                      <div class="modal-content">
                        <div class="modal-header border-0">
                            <button type="button" id="cfrespo-modal-close-<?php echo $div_id; ?>" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="cfrespo-modal-header mb-3"><?php echo $headerText; ?></div>
                            <form method="post" id="previewThemeForm">
                                <?php $this -> getPreviewInputs( $cus_input, $mysqli, $position, "form-group text-center" ); ?>
                                <div class="errorTxtCol mb-3 text-center">
                                    Error color
                                </div>
                                <div class="text-<?= $button_align ?>">
                                  <button type="submit" class="btn p-2"><?php echo $submitBtnText; ?></button>
                                </div>

                            </form>
                        </div>
                        <div class="cfrespo-modal-footer bootstrap-modal-footer" style="text-align: auto !important;">
                            <?php echo $footerText; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <script>
                      cfrespoPreviewScript(`<?php echo $div_id; ?>`);
                  </script>
              <?php
              return;
            }

            else if( $themeId == "theme2" ) {
                ?>
                <div id="cfrespo-modal_<?php echo $div_id; ?>" class="modal cfrespo_preview_modal fade cfrespo-modal-<?php echo $div_id; ?>">
                  <div class="modal-dialog cfrespo_theme-2">
                      <div class="modal-content">
                          <form  id="previewThemeForm">
                                <div class="modal-header border-bottom-0">
                                    <button type="button" id="cfrespo-modal-close-<?php echo $div_id; ?>" class="close" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="cfrespo-modal-header mb-3">
                                        <?php echo $headerText; ?>
                                    </div>
                                    <?php $this -> getPreviewInputs( $cus_input, $mysqli, $position, "form-group text-center" ); ?>
                                    <div class="errorTxtCol text-center">
                                        Error color
                                    </div>
                                    <div class="form-group border-bottom-0 text-<?= $button_align ?>">
                                        <button type="submit" class="btn btn-primary"><?php echo $submitBtnText; ?></button>
                                    </div>
                                </div>
                                
                                <div class="cfrespo-modal-footer bootstrap-modal-footer">
                                    <?php echo $footerText; ?>
                                </div>
                          </form>
                      </div>
                  </div>
              </div>
              <script>
                cfrespoPreviewScript(`<?php echo $div_id; ?>`);
              </script>
              <?php
              return;
            }

            else if( $themeId == "theme3" ) {
                ?>
                <div id="cfrespo-modal_<?php echo $div_id; ?>" class="modal cfrespo_preview_modal fade cfrespo-modal-<?php echo $div_id; ?>" aria-hidden="true" role="dialog">
                    <div class="cfrespo_theme-3 modal-dialog modal-dialog-center" role="document">
                        <div class="container-fluid modal-content border-0 cfrespo-modal-body" style="background:#<?= $formBackCol ?> !important; border-radius: 30px !important;">
                            <form  id="previewThemeForm" method="post">

                                <div class="row">
                                    <div class="container px-5 my-3 py-2">
                                        <div class="my-auto p-0 modal-header border-0">
                                            <button type="button" id="cfrespo-modal-close-<?php echo $div_id; ?>" class="close my-auto ml-auto" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
                                        </div>

                                        <div class="modal-body p-0">
                                            <div class="cfrespo-modal-header"><?= $headerText ?></div>

                                            <?php $this -> getPreviewInputs( $cus_input, $mysqli, $position, 'form-row', 'my-3 p-4'); ?>

                                            <div class="errorTxtCol text-center">
                                                Error color
                                            </div>

                                            <div class="form-row justify-content-<?= $button_align ?> text-<?= $button_align ?>">
                                                <div class="col-lg-7">
                                                    <button type="submit" class="btn1 mt-3 mb-5 px-3 py-2 border-0 rounded">
                                                        <?= $submitBtnText ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="border-0 mb-3 cfrespo-modal-footer bootstrap-modal-footer">
                                            <?= $footerText ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                      cfrespoPreviewScript(`<?php echo $div_id; ?>`);
                  </script>
            <?php
            }
        }

        function getPreviewInputs( $cus_input, $mysqli, $position, $divClass='', $inputClass='' ) {
            $header_count = 0;
            foreach ($cus_input as $key => $value) {
                $custom_place = $mysqli->real_escape_string(  trim(htmlspecialchars( $value['placeholder']) )   );
                $custom_name = $mysqli->real_escape_string( trim(htmlspecialchars( $value['name']) ) );
                $custom_type =$mysqli->real_escape_string( trim(htmlspecialchars( $value['type']) )  );
                $custom_title = $mysqli->real_escape_string( trim(htmlspecialchars( $value['title']) )  );
                $custom_postion = $mysqli->real_escape_string( trim(htmlspecialchars( $position ) ) );
                $custom_required = $mysqli->real_escape_string( trim(htmlspecialchars( $value['required']) ) );
            ?>
                <div class="<?= $divClass; ?>">
                    <?php
                    if(in_array($custom_type, array('text', 'number', 'password', 'email'))) { ?>
                        <input class="form-control <?= $inputClass; ?>" type="<?php echo ((isset($custom_type))? $custom_type:''); ?>" name="<?php echo ((isset($custom_name))? $custom_name:''); ?>" placeholder="<?php echo ((isset($custom_place))? $custom_place:''); ?>" title="<?php echo ((isset($custom_title))? $custom_title:''); ?>" <?php echo ((isset($custom_required) && $custom_required === '0')? '':'required'); ?> />
                    <?php 
                    }
                                                            
                    else if(in_array($custom_type, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'))) {
                        ++$header_count;
                        echo "<".trim($custom_type)."    class=' <?= $inputClass; ?> cfrespo-header cfrespo-header-".$header_count."'>".$custom_title."</".trim($custom_title).">";
                    }
                                                            
                    else if( $custom_type == "textarea" ){ ?>
                        <textarea class="form-control <?= $inputClass; ?>" name="<?php echo ((isset($custom_name))? $custom_name:''); ?>" placeholder="<?php echo ((isset($custom_place))? $custom_place:''); ?>" title="<?php echo ((isset($custom_title))? $custom_title:''); ?>" <?php echo ((isset($custom_required) && $custom_required === '0')? '':'required'); ?>></textarea>
                    <?php 
                    }
                                                            
                    else if( $custom_type == 'radio' ) { ?>
                        <label class="lbl-radio <?= $inputClass; ?>">
                            <input type="<?php echo ((isset($custom_type))? $custom_type:''); ?>"  name="<?php echo ((isset($custom_name))? $custom_name:''); ?>"  value="<?php echo ((isset($custom_title))? $custom_title:''); ?>" <?php echo ((isset($custom_required) && $custom_required === '0')? '':'required'); ?> />&nbsp; <?= $custom_place; ?>
                        </label>
                    <?php
                    }
                                                            
                    else if( $custom_type == 'checkbox') { ?>
                        <label class="lbl-checkbox <?= $inputClass; ?>">
                            <input type="<?php echo ((isset($custom_type))? $custom_type:''); ?>"  name="<?php echo ((isset($custom_name))? $custom_name:''); ?>"  value="<?php echo ((isset($custom_title))? $custom_title:''); ?>" <?php echo ((isset($custom_required) && $custom_required === '0')? '':'required'); ?> />&nbsp; <?= $custom_place; ?>    
                        </label>
                    <?php
                    }
                                                            
                    else { ?>
                        <input class="form-control <?= $inputClass; ?>" type="<?php echo ((isset($custom_type))? $custom_type:''); ?>"  name="<?php echo ((isset($custom_name))? $custom_name:''); ?>"  placeholder="<?php echo ((isset($custom_place))? $custom_place:''); ?>"  value="<?php echo ((isset($custom_title))? $custom_title:''); ?>" <?php echo ((isset($custom_required) && $custom_required === '0')? '':'required'); ?> />
                    <?php
                    }
                    ?>
                </div>
                <?php
                $position++;
            }
            return;
        }

        function insertAndUpdateSettingsForm( $ajax_data, $mysqli, $dbpref ) {
            $form_table = $dbpref.'respo_popup_form';
            $form_extra_settings = $dbpref.'respo_extra_settings';
            $form_inputs = $dbpref.'respo_popup_inputs';
            $formName = $mysqli -> real_escape_string($ajax_data['form_name']);
            $formId = $mysqli -> real_escape_string($ajax_data['form_id']);
            $headerText = $mysqli -> real_escape_string( trim($ajax_data['header_text']));
            $footerText = $mysqli -> real_escape_string( trim($ajax_data['footer_text']));
            $themeId = $mysqli -> real_escape_string($ajax_data['select_theme']);
            
            $formBackCol = $mysqli -> real_escape_string($ajax_data['formBackCol']);
            $headerBackCol = $mysqli -> real_escape_string($ajax_data['headerBackCol']);
            $headerPadding = (int)$mysqli -> real_escape_string( trim($ajax_data['headerPadding']));
            $headerMargin = (int)$mysqli -> real_escape_string( trim($ajax_data['headerMargin']));
            $footerBackCol = $mysqli -> real_escape_string($ajax_data['footerBackCol']);
            $footerPadding = (int)$mysqli -> real_escape_string( trim($ajax_data['footerPadding']));
            $footerMargin = (int)$mysqli -> real_escape_string( trim($ajax_data['footerMargin']));
            $submitBackCol = $mysqli -> real_escape_string($ajax_data['submitBackCol']);
            $submitBtnText = $mysqli -> real_escape_string($ajax_data['submit_btn_text']);
            $submitBtnCol = $mysqli -> real_escape_string($ajax_data['submitBtnCol']);
            $errorTxtCol = $mysqli -> real_escape_string($ajax_data['errorTxtCol']);
            $button_align = $mysqli -> real_escape_string($ajax_data['cfrespo_button_align']);
            $form_width = (int)$mysqli -> real_escape_string($ajax_data['cfrespo_form_width']);
            $form_appear = $mysqli -> real_escape_string($ajax_data['formAnimation']);
            $delay = $mysqli -> real_escape_string($ajax_data['popup_delay_time']);
            $use_as_exit = isset($ajax_data['use_as_exit']) ? 1 : 0;
            $use_as_delay = $mysqli -> real_escape_string(((isset($ajax_data['use_as_delay']))? $ajax_data['use_as_delay']:0));
            $custom_css = $mysqli -> real_escape_string($ajax_data['customCSS']);
            $don_show = isset($ajax_data['don_show']) ? 1 : 0;
            $on_btn_click = isset($ajax_data['on_btn_click']) ? 1 : 0;
            $allow_process_in_cf = $mysqli -> real_escape_string($ajax_data['allow_process_in_cf']);
            $diplay_setup = $mysqli -> real_escape_string($ajax_data['display_setup']);
            $url = $mysqli -> real_escape_string($ajax_data['redirect_url']);

            if( $ajax_data['cfrespo_param'] == "insert" ) {
                $mysqli-> query("INSERT INTO `".$form_table."`(`form_name`, `header_text`, `footer_text`, `theme_id`, `created_at`, `updated_at`) VALUES ('$formName', '$headerText', '$footerText', '$themeId', now(), now())");
                $newFormId = $mysqli->insert_id;
                $mysqli-> query("INSERT INTO `".$form_extra_settings."`(`formId`, `formBackCol`, `headBackCol`, `headerPadding`, `headerMargin`, `footBackCol`, `footerPadding`, `footerMargin`, `submitBackCol`, `submitBtnText`, `submitBtnCol`, `errorTxtCol`, `button_align`, `form_width`, `form_appear`, `delay_value`, `use_as_exit`, `use_as_delay`, `custom_css`, `don_show`, `on_btn_click`, `allow_process_in_cf`, `display_setup`, `redirect_url`, `created_at`, `updated_at`) VALUES ('$newFormId', '$formBackCol', '$headerBackCol', '$headerPadding', '$headerMargin', '$footerBackCol', '$footerPadding', '$footerMargin', '$submitBackCol', '$submitBtnText', '$submitBtnCol', '$errorTxtCol', '$button_align', '$form_width', '$form_appear', '$delay', '$use_as_exit', '$use_as_delay', '$custom_css', '$don_show', '$on_btn_click', '$allow_process_in_cf', '$diplay_setup', '$url', now(), now())");

                $this -> custom_input( $ajax_data, $mysqli, $dbpref, $newFormId, $formId );

                $echo_array = array(
                    'id' => $newFormId,
                    'msg' => 'Form created successfully.' 
                );

                return json_encode( $echo_array );
            }

            else if ( $ajax_data['cfrespo_param'] == "update" ) {
                $mysqli-> query("UPDATE $form_table SET form_name='$formName', header_text='$headerText', footer_text='$footerText', theme_id='$themeId', updated_at=now() WHERE formId='$formId'" );

                $mysqli-> query("UPDATE $form_extra_settings SET formBackCol='$formBackCol', headBackCol='$headerBackCol', headerPadding='$headerPadding', headerMargin='$headerMargin', footBackCol='$footerBackCol', footerPadding='$footerPadding', footerMargin='$footerMargin', submitBackCol='$submitBackCol', submitBtnText='$submitBtnText', submitBtnCol='$submitBtnCol', errorTxtCol='$errorTxtCol', button_align='$button_align', form_width='$form_width', form_appear='$form_appear', delay_value='$delay', use_as_exit='$use_as_exit', use_as_delay='$use_as_delay', custom_css='$custom_css', don_show='$don_show', on_btn_click='$on_btn_click', allow_process_in_cf='$allow_process_in_cf', display_setup='$diplay_setup', redirect_url='$url', updated_at=now() WHERE formId='$formId'");
                $mysqli->query("DELETE FROM $form_inputs WHERE form_id='$formId'");

                $this -> custom_input( $ajax_data, $mysqli, $dbpref, $formId );

                $echo_array = array( 
                    'id' => $formId, 
                    'msg'=>'Form updated successfully.' 
                );

                return json_encode( $echo_array );
            }
        }

        function add_css( $form_appear, $formBackCol, $submit_b_color, $submit_t_color, $header_b_color, $footer_b_color, $div_id, $form_width=400, $headerPadding, $headerMargin, $footerPadding, $footerMargin, $errorTxtCol ) {
            $position = '';
            ?>
            <style>
            .cfrespo-modal-<?php echo $div_id; ?> button[type=submit] {
                background-color: #<?=$submit_b_color ?>;
                color: #<?=$submit_t_color ?>;
                transition: 0.5s;
                border: none;
            }
            .cfrespo-modal-<?php echo $div_id; ?> button[type=submit]:hover {
                background: #<?php echo $submit_t_color; ?>;
                color: #<?php echo $submit_b_color; ?>;
                border: 1px solid gray !important;
                transition: 0.5s;
            }
            .cfrespo-modal-<?php echo $div_id; ?> .cfrespo-modal-header {
                background-color: #<?=$header_b_color;  ?>;
                padding: <?=$headerPadding;?>px !important;
                margin: <?=$headerMargin;?>px !important;
            }
            .cfrespo-modal-<?php echo $div_id; ?> .cfrespo-modal-footer {
                background-color: #<?=$footer_b_color;  ?>;
                padding: <?=$footerPadding;?>px !important;
                margin: <?=$footerMargin;?>px !important;
            }
            .cfrespo-modal-<?php echo $div_id; ?> .modal-content {
                background: #<?=$formBackCol;  ?> !important;
            }

            .cfrespo-modal-<?php echo $div_id; ?> .errorTxtCol {
                color: #<?=$errorTxtCol;  ?> !important;
            }

            @media (min-width: 576px) {
                .cfrespo-modal-<?php echo $div_id; ?> .modal-dialog {
                    max-width: <?=$form_width;  ?>px !important;
                }
            }


            <?php
            if($form_appear == "t_t_c"): ?>
             /* Add Animation */
            @-webkit-keyframes cfrespo_animatetop {
              from {top: -300px; opacity: 0} 
              to {top:  0; opacity: 1}
            }
            @keyframes cfrespo_animatetop {
              from {top: -300px; opacity: 0} 
              to {top:  0; opacity: 1}
            }
            <?php  elseif($form_appear == "l_t_c"): ?>
             @-webkit-keyframes cfrespo_animatetop {
            from {left:  -300px; opacity: 0} 
              to {left:  0; opacity: 1}
            }
            @keyframes cfrespo_animatetop {
              from {left: -300px; opacity: 0} 
              to {left:  0; opacity: 1}
            }
            <?php  elseif($form_appear == "r_t_c"): ?>
             @-webkit-keyframes cfrespo_animatetop {
            from {right:  -300px; opacity: 0} 
              to {right:  0; opacity: 1}
            }
            @keyframes cfrespo_animatetop {
              from {right: -300px; opacity: 0} 
              to {right:  0; opacity: 1}
            }
            <?php  elseif($form_appear == "b_t_c"): ?>
             @-webkit-keyframes cfrespo_animatetop {
            from {bottom:  -300px; opacity: 0} 
              to {bottom:  0; opacity: 1}
            }
            @keyframes cfrespo_animatetop {
             from {bottom: -300px; opacity: 0} 
              to {bottom:  0; opacity: 1}
            }
            <?php elseif($form_appear == "c_t_c"): ?>
            @-webkit-keyframes cfrespo_animatetop {
               from {-webkit-transform: scale(0)} 
              to {-webkit-transform: scale(1)}
            }
            @keyframes cfrespo_animatetop {
               from {transform: scale(0)} 
              to {transform: scale(1)}
            }
            <?php endif; ?>

            .cfrespo-modal-<?php echo $div_id; ?> .modal-content {
                -webkit-animation-name: cfrespo_animatetop !important; /* Safari & Chrome */
                   -moz-animation-name: cfrespo_animatetop !important; /* Firefox */
                    -ms-animation-name: cfrespo_animatetop !important; /* IE */
                     -o-animation-name: cfrespo_animatetop !important; /* Opera */
                        animation-name: cfrespo_animatetop !important;

                          -webkit-animation-duration: 0.4s !important; /* Safari & Chrome */
                             -moz-animation-duration: 0.4s !important; /* Firefox */
                              -ms-animation-duration: 0.4s !important; /* IE */
                               -o-animation-duration: 0.4s !important; /* Opera */
                                  animation-duration: 0.4s !important;
            }
            </style>
            <style id="width_css_style"></style>
            <?php
            return;
        }
        
        function custom_input( $ajax_data, $mysqli, $dbpref, $last_id ) {
            $table = $dbpref.'respo_popup_inputs';
            $cus_input = [];
            
            foreach ( $ajax_data as $key => $data ) {
                $data_expload = explode( "@", $key );
                if( $data_expload[0] == "custom" )
                {
                    $key = trim( htmlspecialchars( stripcslashes( $key ) ) );
                    $c_data=json_decode( $data, true );
                    $cus_input=$c_data;
                }
            }
        
            $position = 1;
            foreach ($cus_input as $key => $value) {
                $this -> cfrespo_custom_input( $value, $last_id, $position, $mysqli, $table );
                $position++;
            }
        }
        
        function cfrespo_custom_input( array $custom = [], $formId = "" , $position="", $mysqli, $table ) {
            $custom_place = $mysqli->real_escape_string(  trim(htmlspecialchars( $custom['placeholder']) )   );
            $custom_name = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['name']) ) );
            $custom_type =$mysqli->real_escape_string( trim(htmlspecialchars( $custom['type']) )  );
            $custom_title = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['title']) )  );
            $custom_postion = $mysqli->real_escape_string( trim(htmlspecialchars( $position ) ) );
            $custom_required = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['required']) ) );
            $mysqli -> query( "INSERT INTO `".$table."`(`form_id`, `name`, `placeholder`, `type`,`title`, `position`, `required`) VALUES ('".$formId."' ,'".$custom_name."' ,'".$custom_place."' ,'".$custom_type."' ,'".$custom_title."','".$custom_postion."' ,'".$custom_required."' )" );
        }
        
        
        function fetchData( $mysqli, $table ) {
            $result = $mysqli->query( "SELECT MAX(formId) FROM $table" );
            $num_row = mysqli_num_rows($result);
            if($num_row > 0) {
                $row = mysqli_fetch_row($result);
                $highest_id = $row[0];
                return $highest_id+1;
            }
        
            return 1;
        }

        function initFormSubmit() {
            $cfrespo_form_submit_err="";
            if(isset($_POST['cfrespo_store_data'])) {
                $optin_ob= $this->loader->load('optin_control');
                $err=$optin_ob->storeLeadsRespo();
                if(isset($err['status']) && $err['status']===0) {
                    $cfrespo_form_submit_err=$err['msg'];
                }
            }
            $GLOBALS['cfrespo_form_submit_err']=$cfrespo_form_submit_err;
        }

        function getFormUI($id= null, $config_version=0) {
            if( !self::doControlCookieForSubscription($id )){ return; }
            $form_data = self::getFormSetup($id);

            if($form_data) {
                global $cfrespo_form_submit_err;
                $show_err = $cfrespo_form_submit_err;
                require plugin_dir_path( dirname(__FILE__,1) )."/view/shortCode.php";
            }
            else{echo "";}
        }

        function getFormSetup( $form_id = null ) {
            global $mysqli;
            global $dbpref;
            $form_table=$dbpref.'respo_popup_form';
            $form_extra_settings=$dbpref.'respo_extra_settings';

            $form_id = trim( $mysqli->real_escape_string( $form_id ) );
            $r = $mysqli->query("SELECT * FROM $form_table INNER JOIN $form_extra_settings on $form_table.formId=$form_extra_settings.formId WHERE $form_table.formId='$form_id'");

             if( $r->num_rows > 0) {
                $data = $r->fetch_assoc();
                return $data;
             }
             return 0;
        }

        function cfrespoGetFormInput( $form_id=null ) {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'respo_popup_inputs';

            $form_id = trim($mysqli->real_escape_string( $form_id ));
            $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `form_id`=".$form_id." ORDER BY `position` ASC");

            if( $returnOptions->num_rows > 0) {
                return $returnOptions;
            }
            return 0;
        }

        function doControlCookieForSubscription( $form_id, $doo='get' ) {
            $setup=self::getFormSetup($form_id );
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            $url.= $_SERVER['HTTP_HOST'];
            $url.= $_SERVER['REQUEST_URI'];

            if($setup) {
                if($doo=='get') {
                    if(isset( $setup['don_show']) && $setup['don_show'] == 1) { 
                        if(isset( $_COOKIE['cfrespo_form_subscribed_'.$form_id] )) {
                            return false;
                        }
                    }
                }
                else if($doo=='set') {
                    setcookie( 'cfrespo_form_subscribed_'.$form_id , 1, time()+86400*30, '/' );
                }
            }
            return true;
        }

        function getValidInputs( $id ) {
            global $mysqli;
            global $dbpref;
            $id=$mysqli->real_escape_string($id);

            $arr=array();
            $table= $dbpref.'respo_popup_inputs';
            $qry=$mysqli->query("select distinct(`name`) from `".$table."` where `form_id`=".$id."");

            while($r=$qry->fetch_object()) {
                array_push($arr,$r->name);
            }
            return $arr;
        }

        function getMiniForms( $formId=false ) {
            global $mysqli;
            global $dbpref;
            $table = $dbpref.'respo_popup_form';

            $arr = array();

            $cond = "";
            if($formId) {
                $formId = $mysqli -> real_escape_string($formId);
                $cond=" where `formId`=".$formId;
            }

            $qry = $mysqli -> query("select `formId`, `form_name` from `".$table."`".$cond." order by `formId` desc");

            while($r = $qry -> fetch_object()) {
                $arr[$r -> formId] = $r -> form_name;
            }

            return $arr;
        }

        function loadGlobalForms($config_version=0) {
            global $mysqli;
            global $dbpref;
            $form_extra_settings = $dbpref.'respo_extra_settings';
            $qry = $mysqli->query( "SELECT `formId` FROM $form_extra_settings WHERE `display_setup`=1" );
            while($r=mysqli_fetch_object($qry)) {
                self::getFormUI($r->formId, $config_version);
            }
        }
    }
}
?>