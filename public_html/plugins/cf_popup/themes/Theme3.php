<div id="cfrespo-modal_<?php echo $div_id; ?>" class="modal cfrespo_preview_modal cfrespo-modelno-<?php echo $fid; ?> cfrespo-modal-<?php echo $div_id; ?>" aria-hidden="true">
    <div class="cfrespo_theme-3 modal-dialog">
        <div class="container-fluid modal-content modal-content-center border-0 cfrespo-modal-body" style="background:transparent; border-radius: 30px !important;">
            <form id="themeData" method="post">
                <input type="hidden" name="cfrespo_form_id" value="<?=cf_enc($fid,"encrypt"); ?>" />
                <input type="hidden" id="cfrespo_user_ajax" name="cfrespo_cfrespo_ajax" value="<?php echo get_option('install_url')."/index.php?page=ajax"; ?>" />
                <input type="hidden" name="cfrespo_nonce" value="<?=cf_create_nonce('cfrespo_nonce_'.$fid .''); ?>" />

                <div class="row px-4">
                    <div class="container-fluid my-3">
                        <div class="my-auto p-0 modal-header border-0">
                            <button type="button" id="cfrespo-modal-close-<?php echo $div_id; ?>" class="close my-auto ml-auto" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
                        </div>

                        <div class="modal-body p-0">
                            <div class="cfrespo-modal-header p-3"><?= $form_data['header_text']; ?></div>

                            <?php 
                                $res1 = $this->cfrespoGetFormInput($fid);
                                $position = 1;
                                $this -> getPreviewInputs( $res1, $mysqli, $position, 'form-row', 'my-3 p-3');
                            ?>

                            <div class="form-row justify-content-center text-center">
                                <div class="col-lg-7">
                                    <?php if(isset($show_err) && strlen($show_err)>0){ ?>
                                        <p class="errorTxtCol" style="margin:1px; font-size:14px;"><?php echo $show_err; ?></p>
                                    <?php } ?>
                                    <br>
                                    <button type="submit" name="cfrespo_store_data" class="btn1 mb-5 px-3 py-2 border-0 rounded">
                                        <?php echo $form_data['submitBtnText']; ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border-0 mb-3 cfrespo-modal-footer">
                            <?php echo ((empty($form_data['footer_text']))? "" : $form_data['footer_text']); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>