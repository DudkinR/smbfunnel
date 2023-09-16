try {
    //open search box
    let cfmenu_theme6_btns = document.querySelectorAll(".cfmenu_theme6-search-open");

    if (cfmenu_theme6_btns.length > 0) {
        cfmenu_theme6_btns.forEach(function(cfmenu_theme6_btn) {
            cfmenu_theme6_btn.onclick = function(event) {
                event.preventDefault();
                cfmenu_theme6_fn(true);
            }
        });
    }
    //close search box
    let cfmenu_theme6_cancels = document.querySelectorAll(".cfmenu_theme6-search-cancel");
    if (cfmenu_theme6_cancels) {
        cfmenu_theme6_cancels.forEach(function(cfmenu_theme6_cancel) {
            cfmenu_theme6_cancel.onclick = function(event) {

                event.preventDefault();
                cfmenu_theme6_fn(false);
            }
        });

    }

    //close search box
    let cfmenu_theme6_open_menus = document.querySelectorAll(".cfmenu_theme6-navbar-toggler");
    if (cfmenu_theme6_open_menus) {
        // console.log(cfmenu_theme6_open_menus);
        cfmenu_theme6_open_menus.forEach(function(cfmenu_theme6_open_menu) {
            cfmenu_theme6_open_menu.onclick = function(event) {
                event.preventDefault();
                cfmenu_theme6_fn_open(true);
            }
        });
    }
    const ecomnav3_menuLinks = document.querySelectorAll(".cfmenu_theme6-menu-link");
    if (ecomnav3_menuLinks) {
        ecomnav3_fn_smallScreenMenu(ecomnav3_menuLinks);
    }
} catch (err) {}

function cfmenu_theme6_fn(action) {
    let cfmenu_theme6_search = document.querySelector(".cfmenu_theme6 .cfmenu_theme6-search");
    if (cfmenu_theme6_search) {
        if (action) {
            cfmenu_theme6_search.classList.add("cfmenu_theme6-search-active");
        } else {
            cfmenu_theme6_search.classList.remove("cfmenu_theme6-search-active");
        }
    }
}

function cfmenu_theme6_fn_open() {
    let menu = document.querySelector(".cfmenu_theme6-navbar-collapse");
    if (menu.style.display == "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }

}

function ecomnav3_fn_smallScreenMenu(ecomnav3_menuLinks) {
    if ($(window).innerWidth() <= 992) {
        ecomnav3_menuLinks.forEach(function(item) {
            item.onclick = function(event) {
                event.preventDefault();
                let next = item.nextElementSibling;
                if (next.style.display == 'block') {
                    next.style.display = 'none';
                } else {
                    next.style.display = 'block';
                }
            }
        });
    } else {
        ecomnav3_menuLinks.forEach(function(item) {
            item.onclick = function(event) {
                event.preventDefault();
                let next = item.nextElementSibling;
                if (next.style.display == 'block') {
                    next.style.display = 'none';
                } else {
                    next.style.display = 'block';
                }
            }
        });
    }
}