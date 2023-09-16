<?php
$plugins_url = plugins_url('assets/images', __FILE__);
if ($manage_styles['cfmenu_navbar_gradient_background_drop']) {
    $cfmenu_nav_background = 'linear-gradient(' . $manage_styles['cfmenu_navbar_gradient_background_combination_drop'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background1'] . ', #' . $manage_styles['cfmenu_navbar_gradient_background2'] . ')';
} else {
    $cfmenu_nav_background = '#' . $manage_styles['cfmenu_navbar_gradient_background1'];
}
?>

<style>
    #cfmenu_nav<?=$cfmenu_nav_id?> .cfmenu_theme6-navbar {
        background: <?= $cfmenu_nav_background ?> !important;
        border-radius: <?= $manage_styles['cfmenu_manage_nav_border_radius'] ?><?= $manage_styles['cfmenu_manage_nav_border_radius_drop'] ?> !important;
    }
    #cfmenu_nav<?=$cfmenu_nav_id?> .cfmenu_theme6-navbar .sub-menu {
        background: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .dropdown-menu,
    #cfmenu_nav<?=$cfmenu_nav_id?> .pull-right .btn {
        background: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .content .links {
        background-color: <?= $cfmenu_nav_background ?> !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .nav-link,
    #cfmenu_nav<?=$cfmenu_nav_id?> .cfmenu_theme6-navbar-nav li a,
    #cfmenu_nav .pull-right .btn {
        color: <?= "#".$manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        font-size: <?= $manage_styles['cfmenu_manage_nav_items_font_size'] ?>px !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .cfmenu_theme6-navbar-brand {
        color: #<?=$logo_details['cfmenu_logo_color']?>;
        font-size: <?=$logo_details['cfmenu_logo_font_size']?>;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .dropdown-header {
        color: <?= "#".$manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
        filter: invert(15%) !important;
        -webkit-filter: invert(5%);
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .nav-item::after {
        background-color: <?= "#".$manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .cfmenu_theme6-navbar-nav li:hover a,
    #cfmenu_nav<?=$cfmenu_nav_id?> .nav-item:hover .nav-link,
    #cfmenu_nav<?=$cfmenu_nav_id?> .dropdown-item {
        color: <?= "#".$manage_styles['cfmenu_manage_nav_textcolor'] ?> !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .cfmenu_theme6-navbar-nav li:hover a,
    #cfmenu_nav<?=$cfmenu_nav_id?> .nav-item:hover .nav-link {
        color: <?= "#".$manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
    }

    #cfmenu_nav<?=$cfmenu_nav_id?> .dropdown-item:hover,
    #cfmenu_nav<?=$cfmenu_nav_id?> .dropdown-item:focus {
        color: <?= "#".$manage_styles['cfmenu_manage_nav_hovercolor'] ?> !important;
        background: <?= "#".$manage_styles['cfmenu_manage_nav_hoverbackcolor'] ?> !important;
    }

</style>

<div class = "cfmenu_theme6-main-wrapper cfmenu_theme6" id="cfmenu_nav<?=$cfmenu_nav_id?>">
    <nav class = "cfmenu_theme6-navbar">
        <div class = "brand-and-icon d-flex justify-content-between align-items-center">
            <button type = "button" class = "cfmenu_theme6-navbar-toggler">
                <img src="<?=$plugins_url?>/menu.png" alt="menu" class="cfmenu_theme6-icon-img" />
            </button>
            <?php if ($logo_details['cfmenu_logo_type'] == 1) { ?>
                <a href = "<?=$logo_details['cfmenu_logo_click_url']?>" class = "cfmenu_theme6-navbar-brand"> <?= $logo_details['cfmenu_logo_title'] ?></a>
            <?php } ?>
            <?php if ($logo_details['cfmenu_logo_type'] == 2) { ?>
                <a href = "<?=$logo_details['cfmenu_logo_click_url']?>" class = "cfmenu_theme6-navbar-brand"> <?= $logo_details['cfmenu_logo_title'] ?>
                    <img width="<?= ($logo_details['cfmenu_logo_img_width'] == "") ? '100px' : $logo_details['cfmenu_logo_img_width'] . 'px' ?>" height="<?= ($logo_details['cfmenu_logo_img_height'] == "") ? "" : $logo_details['cfmenu_logo_img_height'] . 'px' ?>" src="<?= $logo_details['cfmenu_logo_img_source'] ?>" alt="menu" class="logo" />
                </a>
            <?php } ?>
            <div class="cfmenu_theme6-mobile-icons d-flex">
                <a class="pr-3 cfmenu_theme6-search-open"> <img class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/icons8-search-50.png" alt="search"> </a>
                <a class="pr-1 cfmenu_theme6-icon-img-a" sf_goto__cart="1" href="javascript:void(0)"> <img class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/bag.png" alt="cart icon" /> </a>
            </div>
        </div>

        <div class = "cfmenu_theme6-navbar-collapse">
            <div class="d-flex py-3 px-2 cfmenu_theme6-mobile-header align-items-center justify-content-between">
                <a href="#" class="sf-login-text-link d-flex align-items-center">
                    <img class="cfmenu_theme6-icon-img mr-1" src="<?=$plugins_url?>/icons8-user-64.png" alt=""><span class="sf-login-text"> Login </span>
                </a>
                <button type="button" id="cfmenu_theme6-close-menu" class="mr-2 cfmenu_theme6-navbar-toggler"><img width="20px" class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/close.png" alt=""> </button>
            </div>
            <ul class = "cfmenu_theme6-navbar-nav">
                <?php
                $count=1;
                foreach (json_decode(stripcslashes($cfmenu_dragndrop), true) as $key => $val) {
                    if ( isset( $val['children'] ) && $val['children'] ) { ?>
                        <li>
                            <a href = "#" class = "cfmenu_theme6-menu-link"><?=$val['icon'] ?>&nbsp;<?= $val['name'] ?> <span class = "drop-icon"> <i class = "fas fa-chevron-down"></i> </span> </a>
                            <div class = "sub-menu  pt-3">
                                <?php
                                    if ( isset( $val['children'] ) ) {
                                    foreach ( $val['children'] as $key1 => $val1) { 
                                        if( $count <= 2 )
                                        {
                                            ?>
                                            <!-- item -->
                                            <div class = "sub-menu-item">
                                                <h4><?= $val1['name'] ?></h4>
                                                <ul> 
                                                    <?php
                                                    if ( isset( $val1['children'] ) ) {
                                                        foreach ( $val1['children'] as $key2 => $val2 ) { ?>
                                                            <li><a href = "<?= $val2['url'] ?>"><?=$val2['icon'] ?>&nbsp;<?= $val2['name'] ?></a></li>
                                                    <?php } } ?>
                                                </ul>
                                            </div>
                                            <?php
                                        }
                                        else{
                                            if( $count == 3 )
                                                {
                                                    $cdatas = self::getCollections($val1['direct_id']);
                                                    if( $cdatas )
                                                    {
                                                        $media = json_decode($cdatas['media'],true);
                                                        $first_image = ( isset($media[0]) && !empty($media[0]) ) ?$media[0]:"$plugins_url/dummy-products.png";
                                                        ?>
    
                                                        <div class = "sub-menu-item">
                                                            <a href="<?= $cdatas['curl']; ?>" target="_blank" ><img src = "<?=$first_image?>" alt = "product image" /></a>
                                                        </div>
                                                        <!-- item -->
                                                            <div class = "sub-menu-item">
                                                                <h2><?= $cdatas['title']; ?></h2>
                                                                <a href="<?= $cdatas['curl']; ?>" target="_blank" class = "btn">shop here</a>
                                                            </div>
                                                        <!-- end of item -->
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <!-- item -->
                                                            <div class = "sub-menu-item">
                                                                <h2>The Latest Product Is Here</h2>
                                                                <a href="#" target="_blank" class = "btn">shop here</a>
                                                            </div>
                                                        <!-- end of item -->
                                                        <!-- item -->
                                                        <div class = "sub-menu-item">
                                                            <a href="<?= (isset($cdatas['curl'])?$cdatas['curl']:'#'); ?>" target="_blank"  >
                                                                <img src = "<?=$plugins_url?>/dummy-products.png" alt = "product image" />
                                                            </a>
                                                        </div>
                                                        <!-- end of item -->
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        <?php
                                        }
                                        $count++;
                                        }
                                    } ?>
                            </div>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="<?= $val['url'] ?>" ><?= $val['name'] ?></a>
                        </li>
                    <?php    }
                }   ?>
            </ul>
        </div>
        <div class="cfmenu_theme6-nabvar-icons">
            <ul class="cfmenu_theme6-navbar-nav">
            <?php if ($extra_buttons['cfmenu_navbar_admin_drop']) { ?>
                <li class="nav-item cfmenu_theme6-icon-user">
                <a href="#"  title="<?= $extra_buttons['cfmenu_manage_admin_text'] ?>" class="sf-login-text-link nav-link d-flex align-items-center">
                    <img class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/icons8-user-64.png" alt=""><span class="sf-login-text ml-1"> </span>
                </a>
                </li>
                <?php } ?>
                <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
                    <li class="nav-item"><a
                    title="<?= $extra_buttons['cfmenu_manage_search_text'] ?>"  class="nav-link cfmenu_theme6-search-open"> <img class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/icons8-search-50.png" alt=""> </a> </a></li>
                <?php } ?>
                <?php if ( $extra_buttons['cfmenu_manage_cart_icon'] ) { 
                    if( !empty( $extra_buttons['cfmenu_manage_cart_icon'] ) )
                    { ?>
                        <li class="nav-item"> 
                            <a sf_goto__cart="1" sf_hide_cart_icon="0" title="<?= $extra_buttons['cfmenu_manage_cart_text'] ?>" class="nav-link ">
                            <?= $extra_buttons['cfmenu_manage_cart_icon'] ?>
                        </a></li>
                        <?php
                    }else{
                        ?>
                        <li class="nav-item"> <a sf_goto__cart="1"
                        title="<?= $extra_buttons['cfmenu_manage_cart_text'] ?>" 
                        class="nav-link "> <img class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/bag.png" alt=""> </a></li>
                <?php } } ?>
            </ul>
        </div>
    </nav>
    <?php if ($extra_buttons['cfmenu_navbar_search_drop']) { ?>
        <div class="cfmenu_theme6-search ">
            <div class="row">
                <div class="col-md-7 offset-md-2">
                    <form action="" method="get">
                        <div class="form-group d-flex">
                            <div class="input-group">
                                <input type="text" placeholder="<?= $extra_buttons['cfmenu_manage_search_text'] ?>" 
                                name="q"
                                class="cfmenu_theme6-search-input">
                                <div class="input-group-append">
                                    <button type="submit" class="cfmenu_theme6-search-btn"><img class="cfmenu_theme6-icon-img" src="<?=$plugins_url?>/icons8-search-50.png" alt=""> </button>
                                </div>
                            </div>
                            <button type="button" class="cfmenu_theme6-search-cancel"><img width="20px" src="<?=$plugins_url?>/close.png" alt=""> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>