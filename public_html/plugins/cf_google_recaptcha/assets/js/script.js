// Use jQuery via $(...)
$(document).ready(function(){
    // popup settings
    $("#cf_google_add_form").on("submit", function(eve){
        eve.preventDefault();
        let btn=eve.target;
        btn.disabled=true;

        var google_recaptchaBaseUrl=$("#cf_google_base_url").val();
        var google_recaptchaAjax=google_recaptchaBaseUrl+"/index.php?page=ajax";
        var google_recaptchaFormData = $("#cf_google_add_form").serialize();

        var postData= "action=cf_google_recaptcha_save&"+google_recaptchaFormData;
            $.post(google_recaptchaAjax  , postData, function( response ){
              btn.disabled=false;
              console.log(response);
              var response  = $.parseJSON(response);
              if(response.status==1){
                $(".google_recaptcha_error").html("<b class='text-success'>"+response.message+"</b>");
                 setTimeout( () => {
                    window.location=document.referrer;
                  },200);
              
              }else if(response.status==0){
                $(".google_recaptcha_error").html("<b class='text-danger'>"+response.message+"</b>");
              }
          });
    });
    
});


