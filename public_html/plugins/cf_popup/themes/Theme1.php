<div id="cfrespo-modal_<?php echo $div_id; ?>" class="cf-respo-modal modal fade cfrespo-modelno-<?php echo $fid; ?> cfrespo-modal-<?php echo $div_id; ?>" style="z-index:999999999 !important;" aria-hidden="true" role="dialog">
    <div class="modal-dialog cfrespo_theme-1" role="document">
        <div class="modal-content modal-content-center cfrespo-modal-body">
            <form id="themeData" method="post">
                <input type="hidden" name="cfrespo_form_id" value="<?=cf_enc($fid,"encrypt"); ?> ">
                <input type="hidden" name="cfrespo_nonce" value="<?=cf_create_nonce('cfrespo_nonce_'.$fid .''); ?>"  >

                <div class="modal-header border-0 px-4">
                    <button type="button" id="cfrespo-modal-close-<?php echo $div_id; ?>" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body px-4">
                    <div class="p-3 cfrespo-modal-header mb-3"><?= $form_data['header_text']; ?></div>
                    <?php 
                        $res1 = $this->cfrespoGetFormInput($fid);
                        $position = 1;
                        $this -> getPreviewInputs( $res1, $mysqli, $position, "form-group text-center" );
                    ?>
                    <div class="form-group">
                        <center>
                            <?php if(isset($show_err) && strlen($show_err)>0){ ?>
                              <p class="errorTxtCol" style="margin:1px; font-size:14px;"><?php echo $show_err; ?></p>
                            <?php } ?>
                        </center>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="text-<?= $button_align ?>">
                            <button type="submit" name="cfrespo_store_data" class="rounded px-4 py-3">
                                <?php echo $form_data['submitBtnText']; ?>
                            </button>
                        </div>
                    </div>
                </div>
                    
                <div class="cfrespo-modal-footer">
                    <?php echo $form_data['footer_text']; ?>
                </div>
            </form>
        </div>
    </div>
</div>