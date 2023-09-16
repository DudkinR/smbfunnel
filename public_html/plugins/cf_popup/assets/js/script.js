function delFunction(delValue) {
    var confirmation = confirm("Do you want to delete?");

    if (confirmation) {
        event.preventDefault();
        var dataVal = 'cfrespo_param=delete&form_id='+delValue;
        var form = "action=settingsPageData&"+dataVal;

        $.post( $("#cfrespo_ajax").val() , form, function( success ) {
            var response = JSON.parse(success);
            $('.message').html('<div class="alert alert-success fade show">'+ response.msg +'</div>');
            setTimeout(function(){
                location.reload();
                $('.message').css({'transition':'0.5s','opacity': '0'});
            }, 1000);
        });
    }
}

function copyText(textVal) {
    navigator.clipboard.writeText(textVal);
}

$(document).ready(function(){
    $("#cfresp_use_as_delay").on("click", function(){
        if($(this).is(":checked")){
            $("#cfresp-delay-time").show(200);
        }else{
            $("#cfresp-delay-time").hide(200);
        }
    });

    $('#preview').on('click', function(e) {
        e.preventDefault();
        var cfrespo_form_width = $('#cfrespo_form_width').val();
        let btn = $(this).find(".cfrespo_preview_setting");
        $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> Preview`);
        if(cfrespo_form_width <= 100){
            alert("Please enter the greater than 100px.");
            return false;
        }

        else if (cfrespo_form_width=="") {
            alert("Please enter the value of form width.");
            return false;
        }
        let cfrespo_inps=cfrespo_inp_ob.getInputs();
        var header_content = encodeURIComponent(tinyMCE.get("header_text").getContent());
        var footer_content = encodeURIComponent(tinyMCE.get("footer_text").getContent());
        var form = "action=settingsPageData&"+$("#testForm").serialize()+"&header_text="+header_content+"&footer_text="+footer_content+"&custom@cfrespo_inps="+cfrespo_inps+"&cfrespo_param=preview";

        $.post( $("#cfrespo_ajax").val() , form, function( success ){
            $('#shortcodePreview').html(success);
            $('#myModal').modal('show');
        });
    });

    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        var cfrespo_form_width = $('#cfrespo_form_width').val();
        let btn = $(this).find(".cfrespo_save_setting");
        if(cfrespo_form_width <= 100){
            alert("Please enter the greater than 100px.");
            return false;
        }
        let cfrespo_inps=cfrespo_inp_ob.getInputs();
        var cfrespo_param = $('#cfrespo_param').val();
        var header_content = encodeURIComponent(tinyMCE.get("header_text").getContent());
        var footer_content = encodeURIComponent(tinyMCE.get("footer_text").getContent());
        var form = "action=settingsPageData&"+$("#testForm").serialize()+"&header_text="+header_content+"&footer_text="+footer_content+"&custom@cfrespo_inps="+cfrespo_inps+"&cfrespo_param="+cfrespo_param;
        $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> Saving`);
        $.post( $("#cfrespo_ajax").val() , form, function( success ) {
            var response = JSON.parse(success);
            form_id = response.id;
            insertUrl = $('#cfrespo_ajax_insertUrl').val();
            alert( response.msg );
            window.location.href = insertUrl + form_id;
        });
    });
});

