<?php
global $mysqli;
global $dbpref;
$table = $dbpref . 'cf_social_setting';

$sql2 = $mysqli->query("SELECT * FROM `" . $table . "` ");
$rows = mysqli_fetch_assoc($sql2);
$icon_id = $rows['icon_id'];

$sql1 = $mysqli->query("SELECT * FROM `" . $table . "` ");

if ($sql1->num_rows > 0 && $settings = $sql1->fetch_assoc()) {
        $icon_shape = $settings['icon_shape'];
        $display = $settings['display'];
        $icon_color = $settings['icon_color'];
        $object = json_decode($icon_color);

        $facbook = $object->icon_facebook;
        $twitter = $object->icon_twitter;
        $instagram = $object->icon_instagram;
        $youtube = $object->icon_youtube;
        $google = $object->icon_google;
        $pinterest = $object->icon_pinterest;
        $linkedin = $object->icon_linkedin;
        $whatsapp = $object->icon_whatsapp;
        $skype = $object->icon_skype;
        $tumblr = $object->icon_tumblr;
        $yahoo = $object->icon_yahoo;
        $reddit = $object->icon_reddit;
        $digg = $object->icon_digg;
        $blogger = $object->icon_blogger;
        $buffer = $object->icon_buffer;
        $vkontakte = $object->icon_vkontakte;
        $xing = $object->icon_xing;
        $telegram = $object->icon_telegram;
} else {
        $icon_shape = '0';
        $display = 'block';
        $facbook = "#3B5998";
        $twitter = "#55ACEE";
        $instagram = " #fa55d4";
        $youtube = "#bb0000;";
        $google = "#ed1710";
        $pinterest = "#cb2027";
        $linkedin = "#00aff0";
        $whatsapp = "#13f848";
        $skype = "#00aff0";
        $tumblr = "#2c4762";
        $yahoo = "#430297";
        $reddit = "#ff5700";
        $digg = "#2217FF";
        $blogger = "#FF930D";
        $buffer = "#FFFBFA";
        $vkontakte = "#1f38b5";
        $xing = "#23825A";
        $telegram = "#559ede";
}
?>
<div class="container-fluid">
        <div class="row">
                <div class="col-md-12 mt-4">
                        <div class="d-sm-flex align-items-center justify-content-between mb-4 shadow p-3 rounded">
                                <h4 class="mb-0"><?php w('Social Share Setting'); ?></h4>
                        </div>
                </div>
        </div>
        <div class="row">
                <div class="col-sm-12 col-md-12">
                        <div class="w-100">
                                <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded" style="height: 280px !important">
                                        <h5 class="text-dark">Specific Locations</h5>
                                        <hr class="bg-primary">
                                        <div class="container-fluid">
                                                <form method="POST" id="cf_addsocialsetting">
                                                        <input type="hidden" value="<?= ((isset($icon_id) && $icon_id != 0) ? 'update' : 'create') ?>" name="update_insert">
                                                        <input type="hidden" value="<?= $icon_id ?>" name="icon_id">
                                                        <div class="mb-3">
                                                                <label for="">Display</label>
                                                                <select class="form-control" id="" name="cf_display">
                                                                        <option value="" disabled>select display</option>
                                                                        <option value="block">Sidebar</option>
                                                                        <option value="inline-block">Inline</option>

                                                                </select>
                                                        </div>

                                                        <div class="mb-3">
                                                                <label for="">Icon Shape</label>
                                                                <select class="form-control" id="" name="cf_shape">
                                                                        <option value="" disabled>select icone shape</option>
                                                                        <option value="0">Square</option>
                                                                        <option value="50">Circle</option>
                                                                </select>
                                                        </div>
                                        </div>
                                </div>
                        </div>
                </div>

                <div class="col-sm-12 col-md-12">
                        <div class="w-100">
                                <div class="shadow-lg border border-primary text-info p-3 mb-5 bg-white rounded" style="min-height: 400px !important">
                                        <h5 class="text-dark">Fill Icon Color</h5>
                                        <hr class="bg-primary">
                                        <div class="container-fluid">
                                                <div id="cfrespo_input_container" style="max-width:100%">
                                                        <div lvl="1" class="lvl-container mb-2">
                                                                <div class="row">
                                                                        <div class="col pdr-0">
                                                                                <div class="row">
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Facebook'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_facebook" value="<?= $facbook; ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Twitter'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_twitter" value="<?= $twitter ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Instagram'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_instagram" value="<?= $instagram ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Youtube'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_youtube" value="<?= $youtube ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Google'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_google" value="<?= $google ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Pinterest'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_pinterest" value="<?= $pinterest ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Linkedin'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_linkedin" value="<?= $linkedin ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Whatsapp'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_whatsapp" value="<?= $whatsapp ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Skype'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_skype" value="<?= $skype ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>

                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Tumblr'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_tumblr" value="<?= $tumblr ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>

                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Yahoo'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_yahoo" value="<?= $yahoo ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Reddit'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_reddit" value="<?= $reddit ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Digg'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_digg" value="<?= $digg ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Blogger'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_blogger" value="<?= $blogger ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Buffer'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_buffer" value="<?= $buffer ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Vkontakte'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_vkontakte" value="<?= $vkontakte ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Xing'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_xing" value="<?= $xing ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="jp">
                                                                                                <label class="col-form-label col-sm-5 align-self-center"><?php w('Telegram'); ?></label>
                                                                                                <div class="col text-end">
                                                                                                        <input name="icon_telegram" value="<?= $telegram ?>" data-jscolor="{}" class="jscolor form-control form-control-sm">
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>

                <button name="cf_submit_setting" id="cf_submit_setting" class="btn btn-success ms-2 mb-2">Save</button>
                </form>

        </div>
</div>

<input type="hidden" id="cf_social_setting_ajax" value="<?php echo get_option('install_url') . "/index.php?page=ajax"; ?>">