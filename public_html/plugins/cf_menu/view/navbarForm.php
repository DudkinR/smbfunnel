<?php
$cfmenu_custom_url = $form_data['custom_url'];
$cfmenu_dragndrop = $form_data['dropndown'];
$extra_buttons = json_decode($form_data['extra_buttons'], true);
$logo_details = json_decode($form_data['logo_details'], true);
$manage_styles = json_decode($form_data['manage_styles'], true);
$isStickyNav = $manage_styles['cfmenu_navbar_sticky'];
$form_desc = $manage_styles['cfmenu_manage_slogan_text'];
$is_slogan = $manage_styles['cfmenu_navbar_slogan_drop'];
$selectTheme = $form_data['choose_theme'];
$cfmenu_nav_id = time();
$cfmenu_nav_id .= $id;
$cfmenu_nav_id = str_shuffle($cfmenu_nav_id . 'ctahywnkoqxzwl');

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if (gettype(json_decode(stripcslashes($cfmenu_dragndrop), true)) == "array") {
?>
    <link rel="stylesheet" href="<?= plugins_url('../themes/assets/css/' . $selectTheme . '.css', __FILE__) ?>">
    <style>
        .cfmenu_main {
            position: relative !important;
            transition: top 0.7s;
        }

        .cfmenu_nav_sticky {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100%;
        }
    </style>

    <?php
    require(plugin_dir_path(__FILE__) . '../themes/' . $selectTheme . '.php');
    ?>
    <style>
        <?php
        if (strlen($manage_styles['cfmenu_customCSS'])) {
            $css_data = $manage_styles['cfmenu_customCSS'];
            $css_data = str_replace('.this-form', '#cfmenu_nav' . $cfmenu_nav_id, $css_data);
            echo $css_data;
        }
        ?>
    </style>
    <script src="<?= plugins_url('../themes/assets/js/' . $selectTheme . '.js', __FILE__) ?>"></script>

    <script>
        // For making the navbar sticky
        var isSticky = "<?= $isStickyNav ?>";
        if (isSticky == 1) {
            window.onscroll = function() {
                cfMenuStickyNavbar(`<?= $isStickyNav ?>`)
            };

            var navbar = document.getElementById(`cfmenu_nav<?= $cfmenu_nav_id ?>`);
            var sticky = navbar.offsetTop;

            function cfMenuStickyNavbar(isSticky) {
                if (window.pageYOffset >= sticky) {
                    navbar.classList.add("cfmenu_nav_sticky");
                } else {
                    navbar.classList.remove("cfmenu_nav_sticky");
                }
            }
        }

        // Try to remove the extra ul and li from the navbar
        try {
            var cfmenuSelectOnlyLi = document.querySelector('nav.cfmenu_main').querySelectorAll('li');
            var cfmenuSelectOnlyUl = document.querySelector('nav.cfmenu_main').querySelectorAll('ul');
            for (var k = 0; k < cfmenuSelectOnlyLi.length; k++) {
                if (cfmenuSelectOnlyLi[k].innerHTML == "") cfmenuSelectOnlyLi[k].remove();
            }
            for (var k = 0; k < cfmenuSelectOnlyUl.length; k++) {
                if (cfmenuSelectOnlyUl[k].innerHTML == "") cfmenuSelectOnlyUl[k].remove();
            }
        } catch (e) {}
    </script>
<?php } ?>