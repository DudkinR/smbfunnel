<?php
$plugins_url = plugins_url('assets/images', __FILE__);
if ($manage_styles['cfmenu_navbar_gradient_background_drop']) {
    $cfmenu_nav_background = 'linear-gradient(' . $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background1'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background2'] . ')';
} else {
    $cfmenu_nav_background = '#' . $manage_styles['cfmenu_navbar_gradient_background1'];
}
?>

<style>
    .cfmenu_theme5 .nav-item {
        text-align: left !important;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> {
        background: <?= $cfmenu_nav_background ?> !important;
        border-radius: <?= $manage_styles['cfmenu_manage_nav_border_radius'] ?><?= $manage_styles['cfmenu_manage_nav_border_radius_drop'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-menu,
    #cfmenu_nav<?=$cfmenu_nav_id?> .pull-right .btn {
        background: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links {
        background-color: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .nav-link,
    #cfmenu_nav<?=$cfmenu_nav_id?> .pull-right .btn {
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-header {
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        filter: invert(15%) !important;
        -webkit-filter: invert(5%);
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .nav-item::after {
        background-color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .nav-item:hover .nav-link,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-item {
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .nav-item:hover .nav-link {
        color: #<?= $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-item:hover,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .dropdown-item:focus {
        color: #<?= $manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
        background: #<?= $manage_styles['cfmenu_manage_nav_hoverbackcolor'] ?> !important;
    }

        #cfmenu_nav<?= $cfmenu_nav_id ?> .cfmenu_theme4_tagline {
            color: #<?= $manage_styles['cfmenu_manage_slogan_color'] ?>;
            font-size: <?= $manage_styles['cfmenu_manage_slogan_font_size'] ?>px;
        }

        #cfmenu_nav<?= $cfmenu_nav_id ?> .logo {
            font-size: <?= $logo_details['cfmenu_logo_font_size'] ?>px;
            color: #<?= $logo_details['cfmenu_logo_color'] ?>;
            border-radius: <?= $logo_details['cfmenu_logo_img_border_radius'] ?><?= $manage_styles['cfmenu_manage_nav_border_radius_drop'] ?>;
        }

        #cfmenu_nav<?= $cfmenu_nav_id ?> .menu-icon {
            color: #<?= $manage_styles['cfmenu_manage_icon_color'] ?>;
            background: #<?= $manage_styles['cfmenu_manage_icon_back'] ?>;
            padding: <?= $manage_styles['cfmenu_manage_icon_padding'] ?>px;
        }
</style>

<nav class="navbar navbar-light bg-light navbar-expand-lg cfmenu_theme5" id="cfmenu_nav<?= $cfmenu_nav_id ?>" style="z-index:9999; width:100%; padding:8px;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <?php if ($logo_details['cfmenu_logo_type'] == 1) { ?>
        <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>" class="navbar-brand">
            <?= $logo_details['cfmenu_logo_title'] ?>
            <?php if ($is_slogan) { ?><div class="cfmenu_theme4_tagline" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></div><?php } ?>
        </a>
    <?php } ?>
    <?php if ($logo_details['cfmenu_logo_type'] == 2) { ?>
        <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>" class="navbar-brand">
            <img width="<?= ($logo_details['cfmenu_logo_img_width'] == "") ? '100px' : $logo_details['cfmenu_logo_img_width'] . 'px' ?>" height="<?= ($logo_details['cfmenu_logo_img_height'] == "") ? "" : $logo_details['cfmenu_logo_img_height'] . 'px' ?>" src="<?= $logo_details['cfmenu_logo_img_source'] ?>" alt="menu" class="logo" />
            <?php if ($is_slogan) { ?><div class="cfmenu_theme4_tagline" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></div><?php } ?>
        </a>
    <?php } ?>

    <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mr-auto nav-fill">
            <?php
            foreach (json_decode(stripcslashes($cfmenu_dragndrop), true) as $key => $val) {
                if (isset($val['children']) && $val['children']) { ?>
                    <li class="nav-item px-4 dropdown">
                        <?= ($val['icon']!=='' ? $val['icon'].'&nbsp;': '') ?><a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $val['name'] ?></a>
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="servicesDropdown" style="z-index:9999">
                            <div class="d-md-flex align-items-start justify-content-start" style="z-index: 9999;">
                                <?php
                                if (isset($val['children'])) {
                                    foreach ($val['children'] as $key1 => $val1) { ?>
                                        <div>
                                            <div class="dropdown-header"><?= trim($val1['name']) ?></div>
                                            <?php
                                            if (isset($val1['children'])) {
                                                foreach ($val1['children'] as $key2 => $val2) { ?>
                                                    <a href="<?= $val2['url'] ?>" class="dropdown-item"><?= ($val2['icon'] !== '' ?$val2['icon'].'&nbsp;':'') ?><?= trim($val2['name']) ?></a>
                                            <?php }
                                            } ?>
                                        </div>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </li>
                <?php } else { ?>
                    <li class="nav-item px-4">
                        <a href="<?= $val['url'] ?>" class="nav-link"><?= $val['icon'] ?>&nbsp;<?= $val['name'] ?></a>
                    </li>
            <?php }
            } ?>
        </ul>

        <div class="col-sm-3 col-md-3 pull-right">
            <div class="d-flex">
                <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
                    <button class="btn cfmenu_theme5-search-open"><?= $extra_buttons['cfmenu_manage_search_icon'] ?>&nbsp; <?= $extra_buttons['cfmenu_manage_search_text'] ?></button>
                <?php } ?>
                <?php if ($extra_buttons['cfmenu_navbar_cart_drop']) { ?>
                    <button class="btn" sf_goto__cart="1" sf_hide_cart_icon="0"><?= $extra_buttons['cfmenu_manage_cart_icon'] ?>&nbsp; <?= $extra_buttons['cfmenu_manage_cart_text'] ?></button>
                <?php } ?>
                <?php if ($extra_buttons['cfmenu_navbar_admin_drop']) { ?>
                    <button class="btn">
                        <?= $extra_buttons['cfmenu_manage_admin_icon'] ?> &nbsp; <?= $extra_buttons['cfmenu_manage_admin_text'] ?>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
    <div class="cfmenu_theme5-search container" style="position: fixed; top:0; left:0;">
        <div class="row">
            <div class="col-md-7 offset-md-2">
                <form action="" method="get">
                    <div class="form-group d-flex">
                        <div class="input-group">
                            <input type="text" placeholder="What are you looking for.." class="cfmenu_theme5-search-input form-control">
                            <div class="input-group-append">
                                <button type="submit" class="cfmenu_theme5-search-btn"><img class="cfmenu_theme5-icon-img" src="<?= $plugins_url ?>/icons8-search-50.png" alt=""> </button>
                            </div>
                        </div>
                        <button type="button" class="cfmenu_theme5-search-cancel"><img width="20px" src="<?= $plugins_url ?>/close.png" alt=""> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    try {
        //open search box
        let cfmenu_theme5_btn = document.querySelector(".cfmenu_theme5-search-open");
        cfmenu_theme5_btn.onclick = function(event) {
            event.preventDefault();
            cfmenu_theme5_fn(true);
        }
        //close search box
        let cfmenu_theme5_cancel = document.querySelector(".cfmenu_theme5-search-cancel");
        cfmenu_theme5_cancel.onclick = function(event) {
            event.preventDefault();
            cfmenu_theme5_fn(false);
        }
    } catch (e) {
        console.log(e);
    }

    function cfmenu_theme5_fn(action) {
        let cfmenu_theme5_nav = document.querySelector("#cfmenu_nav<?= $cfmenu_nav_id ?>.navbar");
        let cfmenu_theme5_search = document.querySelector(".cfmenu_theme5-search");
        if (cfmenu_theme5_search && cfmenu_theme5_nav) {
            if (action) {
                cfmenu_theme5_search.classList.add("cfmenu_theme5-search-active");
                cfmenu_theme5_nav.style.display = 'none';
            } else {
                cfmenu_theme5_search.classList.remove("cfmenu_theme5-search-active");
                cfmenu_theme5_nav.style.display = 'flex';
            }
        }
    }
</script>