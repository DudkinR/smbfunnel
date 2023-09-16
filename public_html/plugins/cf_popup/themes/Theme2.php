<div id="cfrespo-modal_<?php echo $div_id; ?>" class="cf-respo-modal modal fade cfrespo-modelno-<?php echo $fid; ?> cfrespo-modal-<?php echo $div_id; ?>">
    <div class="modal-dialog cfrespo_theme-2">
        <div class="modal-content cfrespo-modal-body modal-content-center">
            <form id="themeData" method="post">
                <input type="hidden" name="cfrespo_form_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
                <input type="hidden" name="cfrespo_nonce" value="<?=cf_create_nonce('cfrespo_nonce_'.$fid .''); ?>"  >

                <div class="modal-header p-0 cfrespo-modal-header border-bottom-0">
                    <button type="button" id="cfrespo-modal-close-<?php echo $div_id; ?>" class="close" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
                </div>

                <div class="cfrespo-modal-header">
                    <?= $form_data['header_text']; ?>
                </div>
                
                <div class="modal-body text-center">
                    <?php 
                        $res1 = $this->cfrespoGetFormInput($fid);
                        $position = 1;
                        $this -> getPreviewInputs( $res1, $mysqli, $position, "form-group text-center" );
                    ?>
                    <div class="form-group">
                        <?php if(isset($show_err) && strlen($show_err)>0){ ?>
                            <p class="errorTxtCol" style="margin:1px; font-size:14px;"><?php echo $show_err; ?></p>
                        <?php } ?>
                    </div>

                    <div class="form-group border-bottom-0 text-<?= $button_align ?>">
                        <button type="submit" name="cfrespo_store_data" class="btn btn-primary">
                            <?php echo $form_data['submitBtnText']; ?>
                        </button>
                    </div>
                </div>
                    
                <div class="cfrespo-modal-footer">
                    <?php echo ((empty($form_data['footer_text']))? "" : $form_data['footer_text']); ?>
                </div>
            </form>
        </div>
    </div>
</div>