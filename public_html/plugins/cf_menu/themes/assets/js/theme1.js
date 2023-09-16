function showRelative(navId) {
    var main_container = document.querySelector("#cfmenu_nav" + navId);
    if (main_container.querySelector('.cfmenu_collapse').classList.contains("show")) {
        main_container.querySelector('.navbar-toggler').classList.remove("showRelative");
    } else {
        main_container.querySelector('.navbar-toggler').classList.add("showRelative");
    }
}

function cfmenu_showSearchBox() {
    var searchBox = document.getElementById('cfmenu_showSearchBox');
    if (searchBox.querySelector('form') != null) {
        searchBox.style.display = "none";
        searchBox.removeChild(searchBox.querySelector('form'));
    } else {
        var form = document.createElement('form');
        form.setAttribute("method", "post");
        var inp_html = `
        <div class="input-group">
            <input type="text" name="" placeholder="Search our store" class="form-control">
            <div class="input-group-append">
                <span class="input-group-text">
                    <button type="button" class="btn close" onclick="cfmenu_showSearchBox()" aria-label="Close">X</button>
                </span>
            </div>
        </div>`;
        form.innerHTML = inp_html;
        searchBox.style.display = "block";
        searchBox.appendChild(form);
    }
}