$(document).ready(function() {
    cfmenu_togglelogo();
    cfmenu_togglegradient();
    cfmenu_toggleslogan();
    submitSettingsForm();

    // add/remove checked class
    $(".cfmenu_choose_theme .image-radio").each(function() {
        if ($(this).find('input[type="radio"]').first().attr("checked")) {
            $(this).addClass('image-radio-checked');
        } else {
            $(this).removeClass('image-radio-checked');
        }
    });

    // sync the input state
    $(".cfmenu_choose_theme .image-radio").on("click", function(e) {
        $(".image-radio").removeClass('image-radio-checked');
        $(this).addClass('image-radio-checked');
        var $radio = $(this).find('input[type="radio"]');
        $radio.prop("checked", !$radio.prop("checked"));
        e.preventDefault();
    });
});

function submitSettingsForm() {
    $('#cfmenu_settings_form_data').on('submit', function(e) {
        e.preventDefault();
        $('#cfmenu_submit_btn').html(t(`Please wait...`));
        $('#cfmenu_submit_btn').prop('disabled', true);
        var custom_url = encodeURIComponent(cfmenu_inp_ob.getInputs());

        try {
            var dragNDropData = encodeURIComponent(JSON.stringify($('ul.cfmenu_normal_ul').sortable('serialize')));
        } catch (err) {
            var dragNDropData = JSON.stringify('');
        }
        var form = "action=cfmenuajaxsettingsdata&" + $('#cfmenu_settings_form_data').serialize() + "&stringifyData=" + dragNDropData + "&custom_url=" + custom_url;
        $.post($("#cfmenu_ajax").val(), form, function(success) {
            try {
                var json_parse = JSON.parse(success);
                var status = json_parse.status;
                var location = json_parse.location;
                var msg = json_parse.msg;
                var action = json_parse.action;
            } catch (err) {
                var status = 0;
                var msg = t("Something went wrong! Please try again.");
            }

            if (status) {
                setSwalAnimation(msg, 'success');
                if (action == 'create') window.location.href = location;
            } else {
                setSwalAnimation(msg, 'error', 5000);
            }
            $('#cfmenu_submit_btn').prop('disabled', false);
            $('#cfmenu_submit_btn').html(t(`Save Settings`));
        });
    });
}

function funnelpostData(radioId) {
    document.querySelector('.showPagesOfFunnel').innerHTML = `<i class="fa fa-spinner fa-spin" ></i>`;
    var form = "action=cfmenufunnelsajaxsettings&radioId=" + radioId;
    $.post($("#cfmenu_ajax").val(), form, function(success) {
        document.querySelector('.showPagesOfFunnel').innerHTML = success;
    });
}

function getNavDetails(radioId, extra = '', match_extra = '', isProd = false, isProdVal) {
    var getDropVal = document.getElementById('cfmenu_show_funnels_data_id').value;
    var checkedData = match_extra;
    var getRadioVal = extra;
    if (extra == '') {
        getRadioVal = document.getElementById(radioId).value.replace('-', ' ').toLowerCase().replace(/\b(\w)/g, s => s.toUpperCase());
        checkedData = radioId;
    }
    if (isProd) getRadioVal = isProdVal;
    var form = "action=cfmenufunnelspageajaxsettings&title=" + extra + "&getDropVal=" + getDropVal + "&radioId=" + radioId;
    if (document.getElementById(checkedData).checked == true) {
        var extraids = document.getElementById(checkedData).getAttribute('extraids');
        var direct_id = document.getElementById(checkedData).getAttribute('direct_id');
        $.post($("#cfmenu_ajax").val(), form, function(success) {
            cfmenu_inp_ob.createINP(getRadioVal, success, direct_id, extraids);
        });
    }
}

function cfmenu_show_funnels_data(selectValue) {
    document.querySelector('.showPagesOfFunnel').innerHTML = ``;
    document.querySelector('.showPagesOfFunnel').innerHTML = `<i class="fa fa-spinner fa-spin" ></i>`;
    var form = "action=cfmenushowfunnelsajaxsettings&selectValue=" + selectValue;
    $.post($("#cfmenu_ajax").val(), form, function(success) {
        document.querySelector('.showPagesOfFunnel').innerHTML = success;
    });
}

function cfmenu_togglelogo() {
    if ($('#cfmenu_logo_type option:selected').val() == 2) {
        $('#cfmenu_logo_text').css('display', 'none');
        $('.cfmenu_logo_position').css('display', 'flex');
        $('#cfmenu_logo_image').css('display', 'block');
    } else if ($('#cfmenu_logo_type option:selected').val() == 1) {
        $('#cfmenu_logo_image').css('display', 'none');
        $('.cfmenu_logo_position').css('display', 'flex');
        $('#cfmenu_logo_text').css('display', 'block');
    } else {
        $('#cfmenu_logo_image').css('display', 'none');
        $('.cfmenu_logo_position').css('display', 'none');
        $('#cfmenu_logo_text').css('display', 'none');
    }
}

function cfmenu_toggleslogan() {
    if ($('#cfmenu_navbar_slogan option:selected').val() == 1) {
        $('.cfmenu_show_slogan').css('display', 'flex');
    } else $('.cfmenu_show_slogan').css('display', 'none');
}

function cfmenu_toggleextrabutton(name) {
    if ($('#cfmenu_navbar_' + name + ' option:selected').val() == 1) {
        $('.cfmenu_show_' + name).css('display', 'flex');
    } else $('.cfmenu_show_' + name).css('display', 'none');
}

function cfmenu_togglegradient() {
    if ($('#cfmenu_navbar_gradient_background_drop option:selected').val() == 1) {
        $('.cfemnu_navbar_show_gradient_details').css('display', 'block');
    } else {
        $('.cfemnu_navbar_show_gradient_details').css('display', 'none');
    }
}

/* To update the values to the drag n drop */
function cfmenu_add_update_nav(getBtnId) {
    let getOnlyInt = getBtnId.match(/(\d+)/)[0];
    let searchId = 'lvl-' + getOnlyInt;
    let inpnameVal = document.querySelector('#' + searchId + ' .inpname').value.trim();
    let inpextraIds = document.querySelector('#' + searchId + ' .extraids').value.trim();
    let direct_id = document.querySelector('#' + searchId + ' .direct_id').value.trim();
    let inpurlVal = document.querySelector('#' + searchId + ' .inpurl').value.trim();
    let inpIcon = document.querySelector('#' + searchId + ' .inpicon').value.trim();
    inpIcon = inpIcon.replace(/"/g, "'");
    let cfmenunav_inputs = document.querySelector('.cfmenu_normal_ul');

    if (inpnameVal == "" || inpurlVal == "") {
        setSwalAnimation("Please enter the nav details.", "warning", 5000);
        return;
    }

    if (document.querySelector('#alink_' + getOnlyInt)) {
        let getATag = document.querySelector('#alink_' + getOnlyInt);
        getATag.textContent = inpnameVal;
        getATag.setAttribute('a-source', inpurlVal);
        getATag.setAttribute('a-icon', inpIcon);
        getATag.setAttribute('a-extraIds', inpextraIds);
        getATag.setAttribute('direct_id', direct_id);
    } else {
        let createli = document.createElement('li');
        createli.classList.add('li_id-' + getOnlyInt);
        let createli_tags = `<div id="alink_${getOnlyInt}" class="mb-2" a-extraIds="${inpextraIds}" a-icon="${inpIcon}" direct_id="${direct_id}" a-source="${inpurlVal}">${inpnameVal}</div>`;

        createli.innerHTML = createli_tags;
        cfmenunav_inputs.appendChild(createli);
    }
}

function cfmenu_remove_nav(getBtnId) {
    let getOnlyInt = getBtnId.match(/(\d+)/)[0];
    let child = document.querySelector('.li_id-' + getOnlyInt);
    if (child) {
        let parent = child.parentNode;
        parent.removeChild(child);
    }
}

/* Delete function to delete a row from a table. */
function cfmenuDelFunction(delValue) {
    var dataVal = 'cfmenu_update_insert=DELETE&cfmenu_form_id=' + delValue;
    var form = "action=cfmenuajaxsettingsdata&" + dataVal;
    Swal.fire({
        title: t('Are you sure?'),
        text: t("You won't be able to revert this!"),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: t('Yes, delete it!')
    }).then((result) => {
        if (result.isConfirmed) {
            $.post($("#cfmenu_ajax").val(), form, function(success) {
                try {
                    var result = JSON.parse(success);
                    var status = result.status;
                    var msg = result.msg;
                } catch (e) {
                    var status = 0;
                    var msg = t("Something went wrong! Please try again.");
                }
                if (status) {
                    setSwalAnimation(msg, "success");
                    location.reload();
                } else setSwalAnimation(msg, "error", 5000);
            });
        }
    });
}

/* Alert and popup Swal with animation */
function setSwalAnimation(success, iconName, timeValue = 2000) {
    var toastMixin = Swal.mixin({
        toast: true,
        icon: iconName,
        title: 'Message',
        animation: false,
        position: 'top-right',
        showConfirmButton: false,
        timer: timeValue,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    toastMixin.fire({
        animation: true,
        title: success
    });
}

function cfmenuPreviewNavExtract(data, i) {
    let html = `
    <li>
        <a href="${data[i]['url']}">${data[i]['name']}</a>`;
    if (data.hasOwnProperty('children')) {
        html += `
        
        `;
    }
}

function cfmenuCopyText(textVal) {
    navigator.clipboard.writeText(textVal);
    setSwalAnimation(t("Copied successfully"), 'success');
}