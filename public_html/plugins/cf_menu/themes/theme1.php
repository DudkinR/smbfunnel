<?php
$plugins_url = plugins_url('assets/images', __FILE__);
if ($manage_styles['cfmenu_navbar_gradient_background_drop']) {
    $cfmenu_nav_background = 'linear-gradient(' . $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background1'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background2'] . ')';
} else {
    $cfmenu_nav_background = '#' . $manage_styles['cfmenu_navbar_gradient_background1'];
}
?>
<style>
    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 {
        background: <?=$cfmenu_nav_background?> !important;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> .navbar-brand {
        color: #<?=$logo_details['cfmenu_logo_color']?>;
        font-size: <?=$logo_details['cfmenu_logo_font_size']?>px;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> .tagline {
        color: #<?=$manage_styles['cfmenu_manage_slogan_color']?>;
        font-size: <?=$manage_styles['cfmenu_manage_slogan_font_size']?>px;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 ul li a,
    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 ul li i,
    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 .cfmenu_extra_buttons button,
    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 .cfmenu_extra_buttons button i {
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 ul>li:hover>a {
        background: #<?= $manage_styles['cfmenu_manage_nav_hoverbackcolor'] ?> !important;
        color: #<?= $manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 ul:hover li li {
        background: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 ul:hover i {
        color: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?>.cfmenu_theme1 .cfmenu_collapse.show .main-navigation {
        background-image: <?= $cfmenu_nav_background ?> !important;
        background-color: <?= $cfmenu_nav_background ?> !important;
    }
</style>
<div id="cfmenu_showSearchBox" class="container" style="display:none;"></div>
<nav class="cfmenu_theme1 navbar navbar-light navbar-expand-lg bg-light cfmenu_main" id="cfmenu_nav<?= $cfmenu_nav_id ?>" style="width:100% !important; z-index:99999 !important; margin-bottom:0;">
    <div class="container-fluid">
        <?php
        if ($logo_details['cfmenu_logo_type']) {
            self::setNavbarLogo($logo_details, $form_desc, $is_slogan, array('cfmenu_nav_id'=>$cfmenu_nav_id, 'manage_styles'=>$manage_styles['cfmenu_manage_html_icon']));
        }
        ?>

        <div class="cfmenu_collapse collapse navbar-collapse" id="cfmenuNavbarSupportedContent<?= $cfmenu_nav_id ?>">
            <?php
            echo self::extractShortcodeNavChildren(json_decode(stripcslashes($cfmenu_dragndrop), true), $manage_styles, $selectTheme);
            ?>
        </div>

        <div class="cfmenu_extra_buttons">
            <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
                <div class="cfmenu_search p-2">
                    <a href="javascript:void(0)" class="btn cfmenu_theme1-search-open">
                        <?= $extra_buttons['cfmenu_manage_search_icon'] ?>
                    </a>
                </div>
            <?php }
            if ($extra_buttons['cfmenu_navbar_cart_drop']) { ?>
                <div class="cfmenu_cart p-2">
                    <button type="button" class="btn" sf_goto__cart="1" sf_hide_cart_icon="0"><?= $extra_buttons['cfmenu_manage_cart_icon'] ?>
                        <?= $extra_buttons['cfmenu_manage_cart_text'] ?>
                    </button>
                </div>
            <?php }
            if ($extra_buttons['cfmenu_navbar_admin_drop']) { ?>
                <div class="cfmenu_admin p-2">
                    <button type="button" class="btn">
                        <?= $extra_buttons['cfmenu_manage_admin_icon'] ?>
                        <?= $extra_buttons['cfmenu_manage_admin_text'] ?>
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>
</nav>

<?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
    <div class="cfmenu_theme1-search container" style="z-index: 999999;">
        <div class="row">
            <div class="col-md-7 offset-md-2">
                <form action="" method="get">
                    <div class="form-group d-flex">
                        <div class="input-group">
                            <input type="text" placeholder="What are you looking for.." class="cfmenu_theme1-search-input form-control">
                            <div class="input-group-append">
                                <button type="submit" class="cfmenu_theme1-search-btn"><img class="cfmenu_theme1-icon-img" src="<?= $plugins_url ?>/icons8-search-50.png" alt=""> </button>
                            </div>
                        </div>
                        <button type="button" class="cfmenu_theme1-search-cancel"><img width="20px" src="<?= $plugins_url ?>/close.png" alt=""> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    try {
        //open search box
        let cfmenu_theme1_btn = document.querySelector(".cfmenu_theme1-search-open");
        cfmenu_theme1_btn.onclick = function(event) {
            event.preventDefault();
            cfmenu_theme1_fn(true);
        }
        //close search box
        let cfmenu_theme1_cancel = document.querySelector(".cfmenu_theme1-search-cancel");
        cfmenu_theme1_cancel.onclick = function(event) {
            event.preventDefault();
            cfmenu_theme1_fn(false);
        }
    } catch (e) {}

    function cfmenu_theme1_fn(action) {
        console.log('none');
        console.log(action);
        let cfmenu_theme1_nav = document.querySelector(".cfmenu_theme1.navbar");
        let cfmenu_theme1_search = document.querySelector(".cfmenu_theme1-search");
        if (cfmenu_theme1_search && cfmenu_theme1_nav) {
            if (action) {
                cfmenu_theme1_search.classList.add("cfmenu_theme1-search-active");
                cfmenu_theme1_nav.style.display = 'none';
            } else {
                cfmenu_theme1_search.classList.remove("cfmenu_theme1-search-active");
                cfmenu_theme1_nav.style.display = 'flex';
            }
        }
    }
</script>