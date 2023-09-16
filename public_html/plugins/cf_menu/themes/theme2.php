<?php
$plugins_url = plugins_url('assets/images', __FILE__);
if ($manage_styles['cfmenu_navbar_gradient_background_drop']) {
    $cfmenu_nav_background = 'linear-gradient(' . $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background1'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background2'] . ')';
} else {
    $cfmenu_nav_background = '#' . $manage_styles['cfmenu_navbar_gradient_background1'];
}
?>

<style>
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header {
        background: <?= $cfmenu_nav_background ?> !important;
        border-radius: <?= $manage_styles['cfmenu_manage_nav_border_radius'] ?><?= $manage_styles['cfmenu_manage_nav_border_radius_drop'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu .menu-mobile-header,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs {
        background: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs>ul>li>a,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs.menu-mega>.list-item>ul>li>a {
        color: <?= "#" . $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li>a {
        color: <?= "#" . $manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .brand {
        color: #<?= $logo_details['cfmenu_logo_color'] ?>;
        font-size: <?= $logo_details['cfmenu_logo_font_size'] ?>;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu-mobile-toggle span {
        background-color: #<?= $manage_styles['cfmenu_manage_icon_color'] ?> !important;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header-item-right .menu-icon {
        color: #<?= $manage_styles['cfmenu_manage_icon_color'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs {
        border-top: 3px solid <?= "#" . $manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs.menu-mega>.list-item>ul>li>a:hover,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header-item-right a:hover,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li:hover>a,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu .menu-mobile-header .menu-mobile-title,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs.menu-column-4>.list-item .title,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .header .menu>ul>li .menu-subs>ul>li>a:hover {
        color: <?= "#" . $manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
    }
</style>


<div id="cfmenu_nav<?= $cfmenu_nav_id ?>" class="cfmenu_theme2">
    <header class="header">
        <div class="container">
            <div class="wrapper">
                <div class="header-item-left">
                    <?php if ($logo_details['cfmenu_logo_type'] == 1) { ?>
                        <h1><a href="<?= $logo_details['cfmenu_logo_click_url'] ?>" class="brand">Logo
                        <?php if ($is_slogan) { ?><div class="cfmenu_theme4_tagline" style="margin: 0px; letter-spacing: 3px; font-size:12px"><?= $form_desc ?></div><?php } ?></a></h1>
                    <?php } else if ($logo_details['cfmenu_logo_type'] == 2) { ?>
                        <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>" class="brand">
                        <img width="<?= ($logo_details['cfmenu_logo_img_width'] == "") ? '100px' : $logo_details['cfmenu_logo_img_width'] . 'px' ?>" height="<?= ($logo_details['cfmenu_logo_img_height'] == "") ? "" : $logo_details['cfmenu_logo_img_height'] . 'px' ?>" src="<?= $logo_details['cfmenu_logo_img_source'] ?>" alt="menu" class="logo" />
                        <?php if ($is_slogan) { ?><div class="cfmenu_theme4_tagline" style="margin: 0px; letter-spacing: 3px; font-size: 12px"><?= $form_desc ?></div><?php } ?>
                        </a>
                    <?php } ?>
                </div>
                <div class="header-item-center">
                    <div class="overlay"></div>
                    <nav class="menu">
                        <div class="menu-mobile-header">
                            <button type="button" class="menu-mobile-arrow"><i class="fas fa-angle-left"></i></button>
                            <div class="menu-mobile-title"></div>
                            <button type="button" class="menu-mobile-close"><i class="fas fa-times"></i></button>
                        </div>

                        <ul class="menu-section">
                            <?php foreach (json_decode(stripcslashes($cfmenu_dragndrop), true) as $key => $val) {
                                if (isset($val['children']) && $val['children']) { ?>
                                    <li class="menu-item-has-children">
                                        <a href="javascript:void(0)"><?= $val['name'] ?> <i class="fas fa-angle-right"></i></a>
                                        <?php if (isset($val['children']) && count($val['children']) > 1) { ?>
                                            <div class="menu-subs menu-mega menu-column-4">
                                                <?php foreach ($val['children'] as $key1 => $val1) { ?>
                                                    <div class="list-item">
                                                        <h4 class="title"><?= $val1['name'] ?></h4>
                                                        <ul>
                                                            <?php if (isset($val1['children'])) {
                                                                foreach ($val1['children'] as $key2 => $val2) { ?>
                                                                    <li><a href="<?= $val2['url'] ?>"><?= ($val2['icon'] !== '' ? '&nbsp;' : '') ?><?= $val2['name'] ?></a></li>
                                                            <?php }
                                                            } ?>
                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } elseif (isset($val['children'])) { ?>
                                            <div class="menu-subs menu-column-1">
                                                <ul>
                                                    <?php foreach ($val['children'] as $key1 => $val1) { ?>
                                                        <li><a href="<?= $val1['url'] ?>"><?= ($val1['icon'] !== '' ? '&nbsp;' : '') ?> <?= $val1['name'] ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                    </li>
                                <?php } else { ?>
                                    <li><a href="<?= $val['url'] ?>"><?= ($val['icon'] !== '' ? '&nbsp;' : '') ?><?= $val['name'] ?></a></li>
                            <?php }
                            } ?>
                        </ul>
                    </nav>
                </div>

                <div class="header-item-right">
                    <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
                        <a href="javascript:void(0)" id="search-btn" class="menu-icon" onclick="cfmenu_show_search_bar()"><?= $extra_buttons['cfmenu_manage_search_text'] ?><?= $extra_buttons['cfmenu_manage_search_icon'] ?></a>
                    <?php } ?>
                    <?php if ($extra_buttons['cfmenu_navbar_cart_drop']) { ?>
                        <a sf_goto__cart="1" sf_hide_cart_icon="0" class="menu-icon"><?= $extra_buttons['cfmenu_manage_cart_text'] ?><?= $extra_buttons['cfmenu_manage_cart_icon'] ?></a>
                    <?php } ?>
                    <?php if ($extra_buttons['cfmenu_navbar_admin_drop']) { ?>
                        <a href="javascript:void(0)" class="menu-icon"><?= $extra_buttons['cfmenu_manage_admin_text'] ?><?= $extra_buttons['cfmenu_manage_admin_icon'] ?></a>
                    <?php } ?>
                    <button type="button" class="menu-mobile-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>
</div>

<div id="search-overlay<?= $cfmenu_nav_id ?>" class="block cfmenu_search-overlay cfmenu_theme2_search">
    <div class="centered">
        <div id='search-box'>
            <i id="close-btn" class="fa fa-times" onclick="cfmenu_hide_search_bar()"></i>
            <form action='/search' id='search-form' method='get' target='_top'>
                <input id='search-text' name='q' placeholder='Search' type='text' />
                <button id='search-button' type='submit'>
                    <span>Search</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function cfmenu_show_search_bar() {
        $('#cfmenu_nav<?= $cfmenu_nav_id ?> #search-btn i').hide();
        console.log($('#search-overlay<?= $cfmenu_nav_id ?>'))
        $('#search-overlay<?= $cfmenu_nav_id ?>').fadeIn();
    }

    function cfmenu_hide_search_bar() {
        $('#search-overlay<?= $cfmenu_nav_id ?>').fadeOut();
        $('#cfmenu_nav<?= $cfmenu_nav_id ?> #search-btn i').show();
    }
</script>