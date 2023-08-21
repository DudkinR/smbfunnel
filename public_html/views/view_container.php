<?php
ob_start();
if (isset($_POST['cf_do_change_language'])) {
    registerTranslation($_POST['cf_do_change_language']);
    update_option('app_language', $_POST['cf_do_change_language']);
    update_option('qfnl_setup_token', time());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="">
    <title>
        <?php if (isset($_GET['page']) && ($_GET['page'] == "create_funnel")) {
            echo (isset($_GET['id'])) ? "Edit Funnel - CloudFunnels" : "Create Funnel - CloudFunnels";
        } else {
            echo w($title) . ' - CloudFunnels';
        } ?>
    </title>
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <!--Toaster Popup message CSS -->

    <!-- Custom CSS -->
    <link href="assets/theme-assets/dist/css/style.css" rel="stylesheet">
    <link href="assets/theme-assets/dist/css/style.min.css" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="assets/theme-assets/dist/css/pages/dashboard1.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/fontawesome/css/all.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    <?php
    echo "<script>var global_req_is_gcp=false;</script>";
    ?>
    <!-- language -->
    <?php
    $lang_setup_token = time();
    if (get_option('qfnl_setup_token')) {
        $lang_setup_token = get_option('qfnl_setup_token');
    }

    $lang_setup_version = get_option('qfnl_current_version');
    ?>
    <script src="./lang/cache.js?v=<?php echo $lang_setup_token; ?>"></script>
    <script src="./assets/js/html_entities.js?v=<?php echo $lang_setup_version; ?>"></script>
    <script src="./assets/js/lang.js?v=<?php echo $lang_setup_version; ?>"></script>
    <!-- /language -->

    <?php echo $header; ?>
    <?php if (isset($plugin_loader) && $plugin_loader) {
        echo $plugin_loader->attachToContent('admin_head', array());
    } ?>
</head>

<body class="dark fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">CloudFunnels</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php?page=login">
                        <!-- Logo icon --><b>
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->

                            <!-- Light Logo icon -->
                            <img src="assets/theme-assets/assets/images/logo.png" alt="homepage" class="light-logo img-responsive" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text --><span>
                            <!-- dark Logo text -->
                            <img src="assets/theme-assets/assets/images/logo-text.png" alt="homepage" class="light-logo" /></a>
                    <!-- Light Logo text -->
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav me-auto">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User Profile -->
                        <!-- ============================================================== -->
                        <script>
                            var showprofile = 0;

                            function viewProfileContainer() {
                                if (showprofile == 0) {
                                    document.getElementById("profilecontroldiv").style.display = "block";
                                    showprofile = 1;
                                } else {
                                    document.getElementById("profilecontroldiv").style.display = "none";
                                    showprofile = 0;
                                }
                            }
                        </script>
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" id="profilepicopener" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="viewProfileContainer()"><img src="<?php
                                                                                                                                                                                                                                                    $site_token_for_dashboard = get_option('site_token');
                                                                                                                                                                                                                                                    echo $_SESSION['user_profile_picture' . $site_token_for_dashboard]; ?>" alt="user" class=""> <span class="hidden-md-down"><?php echo $_SESSION['user_name' . $site_token_for_dashboard]; ?> &nbsp;<i class="fa fa-angle-down"></i></span> </a>
                            <div class="dropdown-menu dropdown-menu-right animated flipInY bg-dark text-white" id="profilecontroldiv" style="display:none;">
                                <!-- text-->
                                <a href="index.php?page=createmultiuser&id=<?php echo $_SESSION['user' . $site_token_for_dashboard] ?>" class="dropdown-item text-white"><i class="ti-user"></i> <?php w('My Profile'); ?></a>
                                <!-- text-->
                                <div class="dropdown-divider"></div>
                                <?php
                                global $pro_upgrade_url;
                                global $upgrade_url;
                                if (!$_SESSION['user_plan_type' . $site_token_for_dashboard]) {  ?>
                                    <a href="<?php echo $pro_upgrade_url; ?>" target="_BLANK" class="dropdown-item text-white" class="pro-upgrade-link"><i class="far fa-arrow-alt-circle-up"></i> <?php w('Upgrade to Pro'); ?></a>
                                    <div class="dropdown-divider"></div>
                                <?php }
                                if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2) {  ?>
                                    <a href="<?php echo $upgrade_url; ?>" target="_BLANK" class="dropdown-item text-white" class="pro-upgrade-link"><i class="far fa-arrow-alt-circle-up"></i> <?php w('Upgrade'); ?></a>
                                    <div class="dropdown-divider"></div>
                                <?php } ?>
                                <!-- text-->
                                <a href="index.php?page=logout" class="dropdown-item text-white"><i class="fa fa-power-off"></i> <?php w('Logout'); ?></a>
                                <!-- text-->
                            </div>
                        </li>
                    </ul>
                    <i class="fas fa-globe globelanguagechanger" data-bs-toggle="tooltip" title="Select Language"></i>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                    <?php if($_SESSION['access' . get_option('site_token')]==='admin') {?>
                       <li class="user-pro"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><img src="assets/img/profile/qfnlladdprofile.png" alt="user-img" class="img-circle"><span class="hide-menu">
                            Admin
                        </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="index.php?page=new_admin">New Admin</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-user"></i> My Profile</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-wallet"></i> My Balance</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-email"></i> Inbox</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-settings"></i> Account Setting</a></li>
                                <li><a href="javascript:void(0)"><i class="fa fa-power-off"></i> Logout</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php
                        $page = trim($_GET['page']);

                        $dashboard_permission_page_arr = array();
                        if (isset($_SESSION['permission_page_arr' . $site_token_for_dashboard]) && is_array($_SESSION['permission_page_arr' . $site_token_for_dashboard])) {
                            $dashboard_permission_page_arr = $_SESSION['permission_page_arr' . $site_token_for_dashboard];
                        }
                        if (in_array('dashboard', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                            <li id="li-dashboard">
                                <a class="waves-effect waves-dark sidebar-submenu-a" href="index.php?page=dashboard" aria-expanded="false"><i class='fas fa-tachometer-alt'></i> <span class="hide-menu"><?php w('Dashboard'); ?></span></a>
                            </li>
                        <?php } ?>
                        <?php
                        $page_array1 = array("all_funnels", "membership_funnels", "media", "optins", "analysis", "members", "create_funnel");
                        if (!empty(array_intersect($page_array1, $dashboard_permission_page_arr)) || in_array('admin', $dashboard_permission_page_arr)) {
                        ?>
                            <li id="li-products">
                                <a href="#submenu1" data-bs-toggle="collapse" aria-expanded="false" class="waves-effect waves-dark sidebar-submenu-a  <?= in_array($page, $page_array1) ? 'active' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <span class="menu-collapsed "> <i class='fas fa-book'></i> <span class="hide-menu"> <?php w('Funnel Content'); ?></span> </span>
                                        <span class="hide-menu"><i class="fas  <?= in_array($page, $page_array1) ? 'fa-caret-up' : 'fa-caret-down'; ?> sidebar-submenu-i pl-2 "></i></span>
                                    </div>
                                </a>
                                <!-- Submenu content -->
                                <div id='submenu1' class="collapse sidebar-submenu <?= in_array($page, $page_array1) ? 'show' : ''; ?>">
                                    <?php if (in_array('all_funnels', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=all_funnels" class="<?= ("all_funnels" == $page || "create_funnel" == $page || "optins" == $page) ? 'active' : ''; ?>">
                                            <i class='fas fa-funnel-dollar pr-2'></i> <span class="menu-collapsed hide-menu"> Funnels and sites</span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('membership_funnels', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=membership_funnels" class="<?= ("membership_funnels" == $page || "members" == $page) ? 'active' : ''; ?>">
                                            <i class='fas fa-users pr-2'></i>
                                            <span class="menu-collapsed hide-menu">Members</span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('media', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=media" class="<?= ("media" == $page) ? 'active' : ''; ?>">
                                            <i class='fas fa-photo-video pr-2'></i> <span class="menu-collapsed hide-menu">Media</span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('analysis', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=analysis" class="<?= ("analysis" == $page) ? 'active' : ''; ?>">
                                            <i class='fas fa-chart-pie pr-2'></i> <span class="menu-collapsed hide-menu">Analytics</span>
                                        </a>
                                    <?php } ?>
                                </div>

                            </li>
                        <?php } ?>
                        <?php
                        $page_array2 = array("sales", "products");
                        if (!empty(array_intersect($page_array2, $dashboard_permission_page_arr)) || in_array('admin', $dashboard_permission_page_arr)) {
                        ?>
                            <li id="li-sales">
                                <a href="#submenu2" data-bs-toggle="collapse" aria-expanded="false" class="waves-effect waves-dark sidebar-submenu-a  <?= in_array($page, $page_array2) ? 'active' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <span class="menu-collapsed"> <i class='fas fa-box-open'></i><span class="hide-menu"> <?php w('Products'); ?></span> </span>
                                        <span class="hide-menu"> <i class="fas <?= in_array($page, $page_array2) ? 'fa-caret-up' : 'fa-caret-down'; ?> sidebar-submenu-i pl-2"></i> </span>
                                    </div>
                                </a>
                                <!-- Submenu content -->
                                <div id='submenu2' class="collapse sidebar-submenu  <?= in_array($page, $page_array2) ? 'show' : ''; ?>">
                                    <?php if (in_array('products', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=products" class="<?= ($page == 'products') ? 'active' : ''; ?>">
                                            <i class='fas fa-box-open pr-2'></i> <span class="menu-collapsed hide-menu"><?php w('Products'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('sales', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=sales" class="<?= ("sales" == $page) ? 'active' : ''; ?>">
                                            <i class='fas fa-hand-holding-usd pr-2'></i> <span class="menu-collapsed hide-menu"><?php w('Sales'); ?></span>
                                        </a>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } ?>
                        <?php
                        $page_array3 = array("listrecords", "compose_mail", "sequence_records", "sequence", 'sequences', "sentemailsdetails", "smtp_table", "createlist", "smtp_create");
                        if (!empty(array_intersect($page_array3, $dashboard_permission_page_arr)) || in_array('admin', $dashboard_permission_page_arr)) {
                        ?>
                            <li id="li-lists">
                                <a href="#submenu3" data-bs-toggle="collapse" aria-expanded="false" class="waves-effect waves-dark sidebar-submenu-a  <?= in_array($page, $page_array3) ? 'active' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <span class="menu-collapsed"> <i class="fas fa-paper-plane"></i> <span class="hide-menu"><?php w('Email Marketing'); ?> </span> </span>
                                        <span class="hide-menu"> <i class="fas <?= in_array($page, $page_array3) ? 'fa-caret-up' : 'fa-caret-down '; ?> sidebar-submenu-i pl-2"></i></span>

                                    </div>
                                </a>
                                <!-- Submenu content -->
                                <div id='submenu3' class="collapse sidebar-submenu  <?= in_array($page, $page_array3) ? 'show' : ''; ?>">
                                    <?php if (in_array('listrecords', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=listrecords" class="<?= ("listrecords" == $page || "createlist" == $page) ? 'active' : ''; ?>">
                                            <i class="fas fa-clipboard-list pr-2"></i> <span class="menu-collapsed hide-menu"><?php w('Lists'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('compose_mail', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=compose_mail" class="<?= ("compose_mail" == $page) ? 'active' : ''; ?>">
                                            <i class="fas fa-paper-plane pr-2"></i> <span class="hide-menu menu-collapsed"><?php w('Compose&nbsp;Mail'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('sequence_records', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=sequences" class="<?= ("sequence_records" == $page || "sequences" == $page || "sequence" == $page) ? 'active' : ''; ?>">
                                            <i class="fas fa-calendar-alt pr-2"></i> <span class="hide-menu menu-collapsed"><?php w('Sequences'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('sentemailsdetails', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=sentemailsdetails" class="<?= ("sentemailsdetails" == $page) ? 'active' : ''; ?>">
                                            <i class="fas fa-mail-bulk pr-2"></i> <span class="hide-menu menu-collapsed"><?php w('Mailing History'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('smtp_table', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                        <a href="index.php?page=smtp_table" class="<?= ("smtp_table" == $page || "smtp_create" == $page) ? 'active' : ''; ?>">
                                            <i class="fas fa-at pr-2"></i> <span class="hide-menu menu-collapsed"><?php w('SMTPs'); ?></span>
                                        </a>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } ?>

                        <?php
                        if (in_array('plugins', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                            <li id="li-integrations"> <a class="waves-effect waves-dark" href="index.php?page=plugins" aria-expanded="false"><i class="fas fa-plug"></i> <span class="hide-menu"> <?php w('Plugins'); ?></span></a>
                            </li>
                        <?php } ?>
                        <!-- All Plugin menues -->
                        <?php
                        if (isset($_GET['page']) && $_GET['page'] == 'createmultiuser') {
                            $GLOBALS['user_screen_plugin_pages'] = array();
                        }
                        if (isset($plugin_menues) && is_array($plugin_menues) && count($plugin_menues)) {
                            $count = 50;
                            foreach ($plugin_menues as $plugin_menue_index => $plugin_menues_data) {

                                //print_r($plugin_menues_data);
                                if ((isset($plugin_menues_data[0]['menu_slug']) && $plugin_menues_data[0]['menu_slug'] === 'cfcourse_all_course') /*|| (isset($plugin_menues_data['parent_slug']) && $plugin_menues_data['parent_slug']==='cfcourse_all_course')*/) {
                                }
                                //   if(isset($plugin_page) && isset($plugin_page[0]['parent_slug']) && $plugin_page[0]['parent_slug']==$plugin_menue_index ||( isset($plugin_menues_data[0]['menu_slug']) && $plugin_menues_data[0]['menu_slug']==='cfcourse_all_course' ) )
                                //     {
                                //       $is_current_menu_submenu=true;
                                //     }  
                        ?>
                                <?php
                                if (in_array($plugin_menue_index, $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) {
                                    if (isset($GLOBALS['user_screen_plugin_pages'])) {
                                        $GLOBALS['user_screen_plugin_pages'][$plugin_menue_index] = $plugin_menues_data[0]['menu_title'];
                                    }
                                    $cf_submenu_arr = [];
                                    for ($i = 1; $i < count($plugin_menues_data); $i++) {
                                        $cf_submenu_isactiveclass = "";
                                        if ($plugin_menues_data[$i]['menu_slug'] == $_GET['page']) {
                                            $cf_submenu_arr[] = $plugin_menues_data[$i]['menu_slug'];
                                        }
                                    }

                                ?>
                                    <li id="li-zapier_integration<?= $count ?> <?php if (isset($is_current_menu_submenu) && $is_current_menu_submenu) {
                                                                                    echo " active";
                                                                                } ?> cf-course-plugin-menu">
                                        <a href='#submenu<?= $count ?>' data-bs-toggle="collapse" aria-expanded="false" class="waves-effect waves-dark sidebar-submenu-a   <?php if (isset($is_current_menu_submenu) && $is_current_menu_submenu) {
                                                                                                                                                                                echo ' active';
                                                                                                                                                                            } ?>">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <span class="menu-collapsed">
                                                    <?php if (strlen(trim($plugin_menues_data[0]['icon_url'])) > 0) {

                                                        echo '<i><img src="' . $plugin_menues_data[0]['icon_url'] . '" style="max-height:16px;margin-bottom:5px;"></i>';
                                                    } else { ?>
                                                        <i class="fas fa-bullseye"></i>
                                                    <?php } ?>
                                                    <span class="hide-menu"><?php echo $plugin_menues_data[0]['menu_title']; ?></span>
                                                </span>
                                                <span class="hide-menu"> <i class="fas sidebar-submenu-i <?= ((isset($_GET['page']) && ($_GET['page'] == $plugin_menue_index || in_array($_GET['page'],  $cf_submenu_arr)))) ? 'fa-caret-up' : 'fa-caret-down'; ?>  pl-2"></i></span>
                                            </div>
                                        </a>

                                        <?php
                                        $cf_submenu_show = ((isset($_GET['page']) && ($_GET['page'] == $plugin_menue_index || in_array($_GET['page'],  $cf_submenu_arr)))) ? 'show' : '';
                                        echo '<div id="submenu' . $count . '"  class="collapse sidebar-submenu ' . $cf_submenu_show . '">';
                                        // if((isset($is_current_menu_submenu) && $is_current_menu_submenu))
                                        // {

                                        if (isset($plugin_menues_data[0]['submenu']) && $plugin_menues_data[0]['submenu']) {

                                            $cf_submenu_isactiveclass = "";

                                            if (isset($_GET['page']) && $_GET['page'] == $plugin_menue_index) {
                                                $cf_submenu_isactiveclass = 'active';
                                            }
                                        ?>
                                            <a href='index.php?page=<?= $plugin_menue_index; ?>' class=" <?= $cf_submenu_isactiveclass; ?>">
                                                <span class="hide-menu menu-collapsed"><?= $plugin_menues_data[0]['submenu']; ?></span>
                                            </a>
                                        <?php


                                        }

                                        // }
                                        // if($is_current_menu_submenu )
                                        // {

                                        for ($i = 1; $i < count($plugin_menues_data); $i++) {
                                            $cf_submenu_isactiveclass = "";
                                            if ($plugin_menues_data[$i]['menu_slug'] == $_GET['page']) {
                                                $cf_submenu_isactiveclass = "active";
                                            }
                                        ?>
                                            <a href="index.php?page=<?= $plugin_menues_data[$i]['menu_slug'] ?>" class=" <?= $cf_submenu_isactiveclass; ?>">
                                                <span class="hide-menu menu-collapsed"><?= $plugin_menues_data[$i]['menu_title']; ?></span>
                                            </a>
                                        <?php

                                        }

                                        // }
                                        echo ' </div></li>';
                                        ?>
                                    <?php }
                                $count++; ?>


                            <?php }
                        } ?>
                            <?php
                            $page_array5 = array("settings", "multiuser_table", "gdpr", "createmultiuser");
                            if (!empty(array_intersect($page_array5, $dashboard_permission_page_arr)) || in_array('admin', $dashboard_permission_page_arr)) {
                            ?>
                                    <li id="li-settings">
                                        <a href="#submenu5" data-bs-toggle="collapse" aria-expanded="false" class="waves-effect waves-dark sidebar-submenu-a  <?= in_array($page, $page_array5) ? 'active' : ''; ?>">
                                            <div class="d-flex w-100 justify-content-between align-items-center">

                                                <span class="menu-collapsed"> <i class="fas fa-cog"></i><span class="hide-menu"> <?php w('Settings'); ?> </span> </span>
                                                <span class="hide-menu"> <i class="fas <?= in_array($page, $page_array5) ? 'fa-caret-up' : 'fa-caret-down'; ?> sidebar-submenu-i pl-2"></i></span>

                                            </div>
                                        </a>
                                        <!-- Submenu content -->
                                        <div id='submenu5' class="collapse sidebar-submenu <?= in_array($page, $page_array5) ? 'show' : ''; ?>">
                                            <?php if (in_array('settings', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                                <a href="index.php?page=settings" class="<?= ("settings" == $page) ? 'active' : ''; ?>">
                                                    <i class="fas fa-cog pr-2"></i> <span class="menu-collapsed hide-menu"><?php w('Settings'); ?></span>
                                                </a>
                                            <?php } ?>
                                            <?php if (in_array('multiuser_table', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                                <a href="index.php?page=multiuser_table" class="<?= ("multiuser_table" == $page ||  "createmultiuser" == $page) ? 'active' : ''; ?>">
                                                    <i class="fas fa-users-cog pr-2"></i> <span class="hide-menu menu-collapsed"><?php w('Users'); ?></span>
                                                </a>
                                            <?php } ?>
                                            <?php if (in_array('gdpr', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                                <a href="index.php?page=gdpr" class="<?= ("gdpr" == $page) ? 'active' : ''; ?>">
                                                    <i class="fas fa-user-lock pr-2"></i> <span class="hide-menu menu-collapsed"><?php w('GDPR Settings'); ?></span>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </li>
                                <?php } ?>

                                <?php if (in_array('app_guide', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                                    <li id="li-app_guide">
                                        <a class="waves-effect waves-dark sidebar-submenu-a" href="index.php?page=app_guide" aria-expanded="false"><i class='fas fa-question-circle'></i><span class="hide-menu"><?php w('Help'); ?></span></a>
                                    </li>
                                <?php } ?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <?php if (!$plugin_page) { ?>
                    <div class="row page-titles">
                        <div class="col-md-5 align-self-center">
                            <h4 class="text-themecolor" id="commoncontainerid"><?php echo w($title); ?></h4>
                        </div>
                        <div class="col-md-7 align-self-center text-right">
                            <div class="d-flex justify-content-end align-items-center">
                                <?php echo w($data_arr['page_description']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- End Bread crumb and right sidebar toggle -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Info box -->
                    <!-- ============================================================== -->
                    <div class="card-group">

                    </div>
                <?php } ?>
                <!-- ============================================================== -->
                <!-- End Info box -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Over Visitor, Our income , slaes different and  sales prediction -->
                <!-- ============================================================== -->
                <div class="row">
                    <?php
                    if (!$_SESSION['user_plan_type' . $site_token_for_dashboard] && in_array($content, array('analysis.php'))) {
                        $content = 'pro_upgrade.php';
                    }
                    require_once($content);
                    ?>
                </div>
                <!-- ============================================================== -->
                <!-- Comment - table -->
                <!-- ============================================================== -->
                <div class="row">
                </div>
                <!-- ============================================================== -->
                <!-- End Comment - chats -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Over Visitor, Our income , slaes different and  sales prediction -->
                <!-- ============================================================== -->
                <div class="row">

                </div>
                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Todo, chat, notification -->
                <!-- ============================================================== -->
                <div class="row">
                </div>
                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <div class="right-sidebar">

                </div>
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        <footer class="footer">
            <div class="row">
                <div class="col cf_main_footer_element">
                    <?php if (!$plugin_page) { ?>
                        <a onclick='viewTutorial("<?php
                                                    //$data_arr['tutorial_link']="";
                                                    echo (isset($data_arr['tutorial_link']) && filter_var($data_arr['tutorial_link'], FILTER_VALIDATE_URL)) ? $data_arr['tutorial_link'] : "https://cloudfunnels.in/membership/members#tutorials" ?>")' style="cursor:pointer;color: rgb(31, 87, 202);"><i class="fas fa-play"></i>&nbsp;<?php w('Watch Tutorials'); ?></a>
                    <?php } ?>
                </div>
                <div class="col text-right"><a href="https://teknikforce.com" target="_BLANK"><img class="image-responsive" src="assets/img/tekniklogo.png" style="max-width:180px !important;"></a></div>
            </div>
        </footer>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Upgrade popup modal -->
        <!-- ============================================================== -->
        <div class="modal" id="upgradeFromFreeModal" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="text-end"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <div class="card-body text-center">
                            <h5>This Feature Is Not Available In Free Version</h5>
                            <img src="assets/img/free_upgrade.png" class="img-fluid my-3">
                            <p>To get this feature please buy CloudFunnels</p>
                            <a class="btn btn-primary" style="border-radius: 50px;" target="_blank" href="https://getcloudfunnels.in">Click here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End popup modal -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <!-- Bootstrap popper Core JavaScript -->
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="assets/theme-assets/dist/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="assets/theme-assets/dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="assets/theme-assets/dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="assets/theme-assets/dist/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!--morris JavaScript -->
    <!-- Popup message jquery -->
    <!-- Chart JS -->
    <script src="assets/theme-assets/dist/js/dashboard1.js"></script>
    <?php echo $footer; ?>

    <?php if (isset($plugin_loader) && $plugin_loader) {
        echo $plugin_loader->attachToContent('admin_footer', array());
    } ?>

</body>
<script src="assets/js/auto_update.js"></script>
<script>
    try {
        var containerautoupdate = new qfnlAutoUpdate();
        containerautoupdate.init();
    } catch (errr) {}

    function sidebarSelectorForUnreservedPages() {
        //selectors are present inside commanded array
        /*['li-dashboard','li-funnels','li-membership','li-products','li-sales','li-payment','li-autoresponders','li-lists','li-sequence','li-mailing-history','li-smtps','li-users','li-grpr','li-settings','li-guide','li-zapier_integration']*/
        try {
            <?php
            if (isset($_GET['page'])) {
                $selectedli_viewcontainer = 0;
                if ($_GET['page'] == "create_funnel" || $_GET['page'] == "optins") {
                    //funnels
                    $selectedli_viewcontainer = "li-funnels";
                } elseif ($_GET["page"] == "members") {
                    //membership
                    $selectedli_viewcontainer = "li-membership";
                } elseif ($_GET['page'] == "analysis") {
                    //analysis
                    $selectedli_viewcontainer = "li-analysis";
                } elseif ($_GET['page'] == "createlist") {
                    //lists
                    $selectedli_viewcontainer = "li-lists";
                } elseif ($_GET['page'] == "sequence") {
                    //sequences
                    $selectedli_viewcontainer = "li-sequence";
                } elseif ($_GET['page'] == "compose_mail") {
                    //sequences
                    $selectedli_viewcontainer = "li-compose_mail";
                } elseif ($_GET['page'] == "sentemailsdetails") {
                    $selectedli_viewcontainer = "li-mailing-history";
                } elseif ($_GET['page'] == "smtp_create") {
                    $selectedli_viewcontainer = "li-smtps";
                } elseif ($_GET['page'] == "createmultiuser") {
                    $selectedli_viewcontainer = "li-users";
                }
                if ($selectedli_viewcontainer !== 0) {
                    echo ' 
                var lidoc=document.getElementById("sidebarnav").querySelectorAll("li#' . $selectedli_viewcontainer . '");
                lidoc.classList.add("active");
                lidoc.getElementsByTagName("a")[0].classList.add("active");
                ';
                }
            }
            ?>
            //sidebarnav
        } catch (errrrrr) {
            console.log(errrrrr)
        }
    }
    sidebarSelectorForUnreservedPages();

    (function() {
        let doc = document.querySelectorAll(".globelanguagechanger")[0];
        doc.onclick = function() {
            let popup = document.createElement("div");
            let langs = {};
            let selected_lang = `<?php echo ((get_option('app_language')) ? get_option('app_language') : 'lang_english_en'); ?>`;
            <?php
            global $cf_available_languages;
            if (is_array($cf_available_languages)) {
                echo "langs=JSON.parse(`" . json_encode($cf_available_languages) . "`);";
            }
            ?>
            popup.classList.add('lang_changer_popup');
            let langs_div = ``;
            for (let i in langs) {
                langs_div += ((i == selected_lang) ? `<div class='specific text-primary' code='${i}'><i class='fas fa-check text-success'></i>&nbsp;&nbsp;<strong>${langs[i]}</strong></div>` : `<div class='specific' code='${i}'><strong>${langs[i]}</strong></div>`);
            }
            let content = `<div class="card pnl" style="margin-bottom:0px;">
        <div class="card-header">
            <div class="row">
                <div class="col-10"><?php w('Select Language'); ?></div>
                <div class="col-2 text-right closelanguageselector"><i class="fas fa-times-circle" style="cursor:pointer;"></i></div>
            </div>
        </div>
        <div class="card-body">${langs_div}</div>
        </div>`;
            popup.innerHTML = content;
            document.body.appendChild(popup);
            setTimeout(function() {
                document.querySelectorAll(".lang_changer_popup .specific").forEach(doc => {
                    doc.addEventListener('click', function() {
                        let code = this.getAttribute('code');
                        let frm = document.createElement("form");
                        frm.action = "";
                        frm.method = "POST";
                        let inp = document.createElement('input');
                        inp.type = "hidden";
                        inp.name = "cf_do_change_language";
                        inp.value = code;
                        frm.appendChild(inp);
                        document.body.appendChild(frm);
                        frm.submit();
                    });
                });
                document.querySelectorAll(".closelanguageselector")[0].onclick = function() {
                    document.body.removeChild(popup);
                };
            }, 200);
        };
    })();
</script>

</html>
<?php
$container_page_content = ob_get_contents();
ob_get_clean();
$container_page_content = str_replace("@@qfnl_install_url@@", get_option('install_url'), $container_page_content);
echo $container_page_content;
?>