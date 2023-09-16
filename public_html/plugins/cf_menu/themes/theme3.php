<?php
$plugins_url = plugins_url('assets/images', __FILE__);
if ($manage_styles['cfmenu_navbar_gradient_background_drop']) {
    $cfmenu_nav_background = 'linear-gradient(' . $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background1'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background2'] . ')';
} else {
    $cfmenu_nav_background = '#' . $manage_styles['cfmenu_navbar_gradient_background1'];
}
?>

<style>
    #cfmenu_nav<?= $cfmenu_nav_id ?> .nav-btn {
        background-color: <?= $cfmenu_nav_background ?>;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> {
        background: <?= $cfmenu_nav_background ?>;
        border-radius: <?= $manage_styles['cfmenu_manage_nav_border_radius'] ?><?= $manage_styles['cfmenu_manage_nav_border_radius_drop'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-link>a,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .nav-link>a {
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .btn.transparent {
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px;
        border: 1px solid #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        background: <?= $cfmenu_nav_background ?>;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-link:hover>a,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-link:first-child:hover~.arrow,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .btn.transparent:hover {
        color: #<?= $manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
        background: #<?= $manage_styles['cfmenu_manage_nav_hoverbackcolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .cfmenu_theme3_tagline {
        color: #<?= $manage_styles['cfmenu_manage_slogan_color'] ?>;
        font-size: <?= $manage_styles['cfmenu_manage_slogan_font_size'] ?>px;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .logo {
        font-size: <?= $logo_details['cfmenu_logo_font_size'] ?>px;
        color: #<?= $logo_details['cfmenu_logo_color'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .hamburger-menu div:before,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .hamburger-menu div:after,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .hamburger-menu div {
        background: #<?= $logo_details['cfmenu_logo_color'] ?> !important;
    }
</style>

<div class="cfmenu_theme3 container-fluid cfmenu_main<?=$cfmenu_nav_id?>" id="cfmenu_theme3" style="z-index:9999">
    <nav class="cfmenu_main cfmenu_<?= $selectTheme ?>" id="cfmenu_nav<?= $cfmenu_nav_id ?>" style="z-index:99999 !important;">
        <input type="checkbox" name="" id="check">
        <div class="logo-container">
            <?php if ($logo_details['cfmenu_logo_type'] == 1) { ?>
                <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>">
                    <span class="logo"> <?= $logo_details['cfmenu_logo_title'] ?> </span>
                    <?php if ($is_slogan) { ?><div class="cfmenu_theme3_tagline" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></div><?php } ?>
                </a>
            <?php } elseif ($logo_details['cfmenu_logo_type'] == 2) { ?>
                <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>">
                    <img width="100px" src="<?= $logo_details['cfmenu_logo_img_source'] ?>" alt="menu" class="logo" />
                    <?php if ($is_slogan) { ?><div class="cfmenu_theme3_tagline" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></div><?php } ?>
                </a>
                <?= $form_desc ?> -->
            <?php } ?>
        </div>

        <div class="nav-btn">
            <?php echo self::extractShortcodeNavChildren(json_decode(stripcslashes($cfmenu_dragndrop), true), $manage_styles, $selectTheme); ?>
            <div class="log-sign" style="--i: 1.8s">
                <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
                    <a href="javascript:void(0)" class="btn transparent cfmenu_theme3-search-open">
                        <?= $extra_buttons['cfmenu_manage_search_icon'] ?>&nbsp;<?= $extra_buttons['cfmenu_manage_search_text'] ?>
                    </a>
                <?php } ?>
                <?php if ($extra_buttons['cfmenu_navbar_cart_drop']) { ?>
                    <a class="btn transparent cfmenu_theme2-search-open" sf_goto__cart="1" sf_hide_cart_icon="0">
                        <?= $extra_buttons['cfmenu_manage_cart_icon'] ?>&nbsp;<?= $extra_buttons['cfmenu_manage_cart_text'] ?>
                    </a>
                <?php } ?>
                <?php if ($extra_buttons['cfmenu_navbar_admin_drop']) { ?>
                    <a href="#" class="btn transparent sf-login-text-link"><?= $extra_buttons['cfmenu_manage_admin_icon'] ?> &nbsp; <?= $extra_buttons['cfmenu_manage_admin_text'] ?></a>
                <?php } ?>
                <!-- <a href="#" class="btn solid">Sign up</a> -->
            </div>
        </div>

        <div class="hamburger-menu-container">
            <div class="hamburger-menu">
                <div></div>
            </div>
        </div>
    </nav>
</div>

<?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
    <div class="cfmenu_theme3-search container">
        <div class="row">
            <div class="col-md-7 offset-md-2">
                <form action="" method="get">
                    <div class="form-group d-flex">
                        <div class="input-group">
                            <input type="text" placeholder="What are you looking for.." class="cfmenu_theme3-search-input form-control">
                            <div class="input-group-append">
                                <button type="submit" class="cfmenu_theme3-search-btn"><img class="cfmenu_theme3-icon-img" src="<?= $plugins_url ?>/icons8-search-50.png" alt=""> </button>
                            </div>
                        </div>
                        <button type="button" class="cfmenu_theme3-search-cancel"><img width="20px" src="<?= $plugins_url ?>/close.png" alt=""> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    try {
        //open search box
        let cfmenu_theme3_btn = document.querySelector(".cfmenu_theme3-search-open");
        cfmenu_theme3_btn.onclick = function(event) {
            event.preventDefault();
            cfmenu_theme3_fn(true);
        }
        //close search box
        let cfmenu_theme3_cancel = document.querySelector(".cfmenu_theme3-search-cancel");
        cfmenu_theme3_cancel.onclick = function(event) {
            event.preventDefault();
            cfmenu_theme3_fn(false);
        }
    } catch (e) {
        console.log(e);
    }

    function cfmenu_theme3_fn(action) {
        let cfmenu_theme3_nav = document.querySelector(".cfmenu_theme3 nav");
        let cfmenu_theme3_search = document.querySelector(".cfmenu_theme3-search");
        if (cfmenu_theme3_search && cfmenu_theme3_nav) {
            if (action) {
                cfmenu_theme3_search.classList.add("cfmenu_theme3-search-active");
                cfmenu_theme3_nav.style.display = 'none';
            } else {
                cfmenu_theme3_search.classList.remove("cfmenu_theme3-search-active");
                cfmenu_theme3_nav.style.display = 'flex';
            }
        }
    }
</script>