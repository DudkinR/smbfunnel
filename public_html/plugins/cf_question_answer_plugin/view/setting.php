<?php
global $mysqli;
global $dbpref;

$table = $dbpref . 'cfproduct_question_setting';
$sql = $mysqli->query("SELECT * FROM  `" . $table . "`  ");
$rows = mysqli_fetch_assoc($sql);
$ui_id = $rows['ui_id'];

?>


<div class="col-md-12 px-0">

    <input type="hidden" id="cf_ajax_style" value="<?php echo get_option('install_url') . "/index.php?page=ajax"; ?>">

    <form class="cfpro-rev-setting-form" id="cf_style_form">
        <input type="hidden" value="<?= ((isset($ui_id) && $ui_id != 0) ? 'update' : 'create') ?>" name="update_insert">
        <input type="hidden" value="<?= $ui_id ?>" name="ui_id">


        <h5 class="text-dark mt-2 text-center"><?php w('Change Style'); ?></h5>
        <hr class="bg-primary">
        <div class="form-group p-3 pb-0">

            <div class="form-group row">
                <label class="col-form-label col-sm-5 align-self-center"><?php w('Question Font'); ?></label>
                <div class="col input-group">
                    <input type="number" name="que_font" value="<?= $rows['que_font']; ?>" class="form-control" placeholder="<?php w('Enter The Font Size'); ?>">
                    <div class="input-group-append">
                        <span class="input-group-text">px</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-5 align-self-center"><?php w('Answer Font'); ?></label>
                <div class="col input-group">
                    <input type="number" name="ans_font" value="<?= $rows['ans_font']; ?>" class="form-control" placeholder="<?php w('Enter The Font Size'); ?>">
                    <div class="input-group-append">
                        <span class="input-group-text">px</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-5 align-self-center"><?php w('Question Text Color'); ?></label>
                <div class="col text-right">
                    <input name="que_tcolor" value="<?= $rows['que_tcolor']; ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-5 align-self-center"><?php w('Answer Text Color'); ?></label>
                <div class="col text-right">
                    <input name="ans_tcolor" value="<?= $rows['ans_tcolor']; ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-5 align-self-center"><?php w('Question text background'); ?></label>
                <div class="col text-right">
                    <input name="que_bg" value="<?= $rows['que_bg']; ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-5 align-self-center"><?php w('Answer text background'); ?></label>
                <div class="col text-right">
                    <input name="ans_bg" value="<?= $rows['ans_bg']; ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                </div>
            </div>
        </div>
</div>
</div>
<button id="cf_style_save" class="btn my-1 btn-primary btn-sm" type="submit"><?php w('Save'); ?></button> &nbsp;&nbsp;
</form>
</div>

<script src="<?php echo plugins_url('../assets/js/jscolor.js', __FILE__) ?>"></script>
<input type="hidden" id="get_option_url" value="<?= get_option('install_url'); ?>">
<script src="<?php echo plugins_url('../assets/js/sweet_alert.js', __FILE__) ?>"></script>