<?php
$plugins_url = plugins_url('assets/images', __FILE__);
if ($manage_styles['cfmenu_navbar_gradient_background_drop']) {
    $cfmenu_nav_background = 'linear-gradient(' . $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background1'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background2'] . ')';
} else {
    $cfmenu_nav_background = '#' . $manage_styles['cfmenu_navbar_gradient_background1'];
}
?>

<style>
    #cfmenu_nav<?= $cfmenu_nav_id ?>,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .search-box .go-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> #show-search:checked~.search-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .search-box input{
        background: <?= $cfmenu_nav_background ?>;
        border-radius: <?= $manage_styles['cfmenu_manage_nav_border_radius'] ?><?= $manage_styles['cfmenu_manage_nav_border_radius_drop'] ?> !important;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links ul{
        width: max-content;
        background: <?= $cfmenu_nav_background ?>;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links li a {
        color: #<?=$manage_styles['cfmenu_manage_nav_textcolor']?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
    }
    
    #cfmenu_nav<?= $cfmenu_nav_id ?> .search-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .cart-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .admin-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .search-box .go-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> #show-search:checked~.search-icon,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .search-box input,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .search-box input::placeholder,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links ul li a,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links ul li a .fa,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links li label {
        color: #<?=$manage_styles['cfmenu_manage_nav_textcolor']?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
    }

    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links label:hover,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links ul >li:hover > a,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links li ul li:hover,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links label:hover .fa,
    #cfmenu_nav<?= $cfmenu_nav_id ?> .content .links li ul li:hover .fa {
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
        border-radius: <?=$logo_details['cfmenu_logo_img_border_radius']?><?=$manage_styles['cfmenu_manage_nav_border_radius_drop']?>;
    }
    #cfmenu_nav<?= $cfmenu_nav_id ?> .menu-icon {
        color: #<?=$manage_styles['cfmenu_manage_icon_color']?>;
        background: #<?=$manage_styles['cfmenu_manage_icon_back']?>;
        padding: <?=$manage_styles['cfmenu_manage_icon_padding']?>px;
    }
</style>

<div class="wrapper cfmenu_theme4" id="cfmenu_nav<?=$cfmenu_nav_id?>">
    <nav>
        <input type="checkbox" id="show-search">
        <input type="checkbox" id="show-menu">
        <label for="show-menu" class="menu-icon"><?=($manage_styles['cfmenu_manage_html_icon']=="")?'<i class="fas fa-bars"></i>':$manage_styles['cfmenu_manage_html_icon']?></label>
        <div class="content">
            <div class="logo">
            <?php if ($logo_details['cfmenu_logo_type'] == 1) { ?>
                <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>" style="max-height: <?=$logo_details['cfmenu_logo_height']?>px; min-width:<?=$logo_details['cfmenu_logo_width']?>px;">
                    <?= $logo_details['cfmenu_logo_title'] ?>
                    <?php if ($is_slogan) { ?><div class="cfmenu_theme4_tagline" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></div><?php } ?>
                </a>
            <?php } ?>
            <?php if ($logo_details['cfmenu_logo_type'] == 2) { ?>
                <a href="<?= $logo_details['cfmenu_logo_click_url'] ?>">
                    <img width="<?=($logo_details['cfmenu_logo_img_width']=="")?'100px':$logo_details['cfmenu_logo_img_width'].'px' ?>" height="<?=($logo_details['cfmenu_logo_img_height']=="")?"":$logo_details['cfmenu_logo_img_height'].'px'?>" src="<?= $logo_details['cfmenu_logo_img_source'] ?>" alt="menu" class="logo" />
                    <?php if ($is_slogan) { ?><div class="cfmenu_theme4_tagline" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></div><?php } ?>
                </a>
                <?php } ?>
            </div>
            <?php echo self::extractShortcodeNavChildren(json_decode(stripcslashes($cfmenu_dragndrop), true), $manage_styles, $selectTheme); ?>
        </div>

        <!-- <section class="p-0 d-flex align-items-center"> -->
        <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
        <label for="show-search" class="search-icon"><?=$extra_buttons['cfmenu_manage_search_icon']?>&nbsp; <?=$extra_buttons['cfmenu_manage_search_text']?></label>
        <form action="#" class="search-box">
            <input type="text" placeholder="Type Something to Search..." required>
            <button type="submit" class="go-icon"><i class="fas fa-long-arrow-alt-right"></i></button>
        </form>
        <?php } ?>
        <?php if ($extra_buttons['cfmenu_navbar_cart_drop']) { ?>
        <label class="cart-icon" sf_goto__cart="1" sf_hide_cart_icon="0"><?=$extra_buttons['cfmenu_manage_cart_icon']?>&nbsp; <?=$extra_buttons['cfmenu_manage_cart_text']?></label>
        <?php } ?>
        <?php if ($extra_buttons['cfmenu_navbar_admin_drop']) { ?>
            <label class="admin-icon">
                <?= $extra_buttons['cfmenu_manage_admin_icon'] ?> &nbsp; <?= $extra_buttons['cfmenu_manage_admin_text'] ?>
            </label>
        <?php } ?>
        <!-- </section> -->
    </nav>
</div>