$(document).ready(function()
{
    getAjaxResponseFromPHP();
    toggleDropdown();

    let authRequired = $('#authRequired');
    authRequired.on('click', function(){
        if(document.getElementById("authRequired").checked)
        {
            document.getElementById('showAuthInputs').style.display = "block";
            document.getElementById('cfglobal_username').required = true;
            document.getElementById('cfglobal_password').required = true;
        }
        else
        {
            document.getElementById('showAuthInputs').style.display = "none";
            document.getElementById('cfglobal_username').required = false;
            document.getElementById('cfglobal_password').required = false;
        }
    });
});

function setDataToInputs()
{
    var textareaValues = document.getElementById('cfglobal_textarea').value;
    if(textareaValues.length == 0) setSwalAnimation("Please enter HTML form", "error", 5000);
    else
    {
        $('#exportHTML').modal('hide');
        changeContentDynamic();
    }
}

/* Set the Input Contents Phase 1*/
function changeContentDynamic()
{    
    var content = $("textarea#cfglobal_textarea").val().trim();
    var a = [];
    content.replace(/<(.*?)>/g, function( match, g1 )
    {
        a.push(g1);
    });
    
    let cfglobal_inp_ob = new CFGlobalMangeInputs();
    
    for (var i = 0; i < a.length; i++)
    {
        if(a[i].includes("form"))
        {
            content.replace(/action="(.*?)"/g, function( match, g1 )
            {
                var apiURL = g1;
                $("#cfglobal_api_url").val(apiURL.toLowerCase());
            });
            content.replace(/method="(.*?)"/g, function( match, g1 )
            {
                var formMethod = g1;
                $("#cfglobal_form_methods").val(formMethod.toUpperCase());
            });
        }
        else if(a[i].includes('input') || a[i].includes('textarea'))
        {
            var name = "";
            var value = "";
            a[i].replace(/name="(.*?)"/g, function( match, g1 )
            {
                name = g1;
            });
            a[i].replace(/value="(.*?)"/g, function( match, g1 )
            {
                value = g1;
            });
            cfglobal_inp_ob.createINP( name, value, 0 );
        }
    }
}

function setValueToInputOnHeader(str)
{
    var selectedRadio = document.querySelector('input[name="body_data_format"]:checked');
    var setValueToInput = document.querySelector('input[value="'+str+'"]');
    if(selectedRadio)
    {
        if(setValueToInput == null)
        {
            if(str == 'application/json')
            {
                document.querySelector('input[value="application/x-www-form-urlencoded"]').value = document.querySelector('label[for="'+selectedRadio.id+'"]').innerHTML;
            }
            else
            {
                document.querySelector('input[value="application/json"]').value = document.querySelector('label[for="'+selectedRadio.id+'"]').innerHTML;
                
            }
        }
        else
        {
            document.querySelector('input[value="'+str+'"]').value = document.querySelector('label[for="'+selectedRadio.id+'"]').innerHTML;
            
        }
    }
}

function toggleDropdown()
{
    $('.cfglobal_dropdown').on('click', function(){
        $('.cfglobal_dropdown').toggleClass('active');
    });
}

function cfglobal_show_dropdown(getShowText)
{
    document.querySelector('.cfglobal_textBox').value = getShowText;
}

/* Get AJAX Data Response from HTML to parse data */
function getAjaxResponseFromPHP()
{
    $('#global_au_settings_form_test').on('click', function(e)
    {
        $('#global_au_settings_form').on('submit', function(e1){
            e1.preventDefault();
            createUpdateTest("test");
        });
    });
    
    $('#global_au_settings_form_submit').on('click', function(e)
    {
        $('#global_au_settings_form').on('submit', function(e1){
            e1.preventDefault();
            createUpdateTest();
        });
    });
}

function createUpdateTest(hit_button="create")
{
    let showMessageAfterCurl = $('#showMessageAfterCurl');
    let getValidateValue = settingsValidate();
    
    showMessageAfterCurl.html('');
    if(!getValidateValue[0])
    {
        setSwalAnimation(getValidateValue[1], "warning");
        return;
    }
    
    let btn;
    
    if(hit_button == "test")
    {
        btn = $('#global_au_settings_form_test');
    }
    else
    {
        btn = $('#global_au_settings_form_submit');
    }
    $(btn).html(`Please wait...`);
    $(btn).prop('disabled', true);
    
    var dataVal = $('#global_au_settings_form').val();
    var cfglobal_inputs = cfglobal_inp_ob.getInputs();
    var cfglobal_header_inputs = cfglobal_header_inp_ob.getInputs();
    var form = "action=cfglobalajaxsettings&"+dataVal+$("#global_au_settings_form").serialize()+"&hit_button="+hit_button+"&custom@cfglobal_inps="+cfglobal_inputs+"&custom_header@cfglobal_inps="+cfglobal_header_inputs;
    $.ajax({
        url: $("#cfglobalau_ajax").val(),
        data: form,
        success: function(response){
            try{
                var result = JSON.parse(response);
                var msg = result.msg;
                var status = result.status;
                var action = result.action;
                if(status == 1)
                {
                    var res_url = result.url;
                }
            } catch(e) {
                var msg = "Something went wrong! Please try again.";
                var status = 400;
            }
            
            $('#global_au_settings_form').unbind();
            if(hit_button != "test")
            {
                if(status == 1)
                {
                    setSwalAnimation(msg, "success");
                    if(action == "CREATE") window.location.href = res_url;
                }
                else
                {
                    setSwalAnimation(msg, "error", 5000);
                    $(btn).html($('#cfglobalau_update_insert').val());
                    $(btn).prop('disabled', false);
                }
            }
            else
            {
                getMessage = msg;
                let str = `<div class="mb-4 shadow p-3 rounded"><iframe id="showDataInIframe"></iframe>`;
                str += `</div>`;
                showMessageAfterCurl.html(str);
                if(status == 200 || status ==201 || status == 202) var className = "green";
                else var className = "#f1c232";
                $('#cfGlobalReuestStatus').modal('show');
                var statusCode = `<div style="color:`+className+`; font-size: 20px;">Status: `+status+`</div>`;
                document.getElementById('showDataInIframe').src = "data:text/html;charset=utf-8," + escape(statusCode+"<br><br>"+getMessage);
            }
            if(hit_button=="test")
            {
                $(btn).html('TEST');
            }
            else
            {
                $(btn).html('Save Settings');
            }
            $(btn).prop('disabled', false);
        },
        error: function() {
            setSwalAnimation("Please check your internet connection", "error", 5000);
            if(hit_button=="test")
            {
                $(btn).html('TEST');
            }
            else
            {
                $(btn).html('Save Settings');
            }
            $(btn).prop('disabled', false);
        }
    });
}

/* Delete function to delete a row from a table. */
function cfGlobalDelFunction(delValue)
{
    var dataVal = 'cfglobalau_update_insert=DELETE&cfglobalau_form_id='+delValue;
    var form = "action=cfglobalajaxsettings&"+dataVal;
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed)
        {
            $.post( $("#cfglobalau_ajax").val() , form, function(success)
            {
                try{
                    var result = JSON.parse(success);
                    var status = result.status;
                    var msg = result.msg;
                }
                catch(e){
                    var status = 0;
                    var msg = 'Something went wrong! Please try again.';
                }
                if(status){
                    setSwalAnimation(msg, "success");
                    location.reload();
                }
                else setSwalAnimation(msg, "error", 5000);
            });
        }
    });
}

/* Alert and popup Swal with animation */
function setSwalAnimation( success, iconName, timeValue=2000 )
{
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

function settingsValidate()
{
    var titleValue = document.querySelector('#cfglobal_input_title').value;
    var apiURL = document.querySelector('#cfglobal_api_url').value;
    if(titleValue.length===0) return [false, "Please enter title."];
    else
    {
        if(apiURL.length===0) return [false, "Please enter url."];

        var res = apiURL.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
        return [res !== null, "Please enter valid URL."];
    }
}