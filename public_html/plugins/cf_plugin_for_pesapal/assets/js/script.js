// Use jQuery via $(...)
$(document).ready(function(){
  
    // popup settings
    $("#pesapal_add_setting").on("submit", function(eve){
        eve.preventDefault();
        let btn=eve.target;
        btn.disabled=true;

        var pesapalBaseUrl=$("#pesapal_base_url").val();
        var pesapalAjax=pesapalBaseUrl+"/index.php?page=ajax";
        var pesapalFormData = $("#pesapal_add_setting").serialize();

        var postData= "action=cfpay_savepesapal&"+pesapalFormData;
            $.post(pesapalAjax  , postData, function( response ){
              btn.disabled=false;
              console.log(response);
              var response  = $.parseJSON(response);
              if(response.status==1){

                $(".pesapal_error").html("<b class='text-success'>"+response.message+"</b>");
                 setTimeout( () => {
                    window.location=document.referrer;
                  },200);
              
              }else if(response.status==0){
                $(".pesapal_error").html("<b class='text-danger'>"+response.message+"</b>");
              }
          });
    });
});

