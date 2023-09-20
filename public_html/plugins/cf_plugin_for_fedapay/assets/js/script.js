// Use jQuery via $(...)
$(document).ready(function(){
  
    // popup settings
    $("#fedapay_add_setting").on("submit", function(eve){
        eve.preventDefault();
        let btn=eve.target;
        btn.disabled=true;

        var fedapayBaseUrl=$("#fedapay_base_url").val();
        var fedapayAjax=fedapayBaseUrl+"/index.php?page=ajax";
        var fedapayFormData = $("#fedapay_add_setting").serialize();

        var postData= "action=cfpay_savefedapay&"+fedapayFormData;
            $.post(fedapayAjax  , postData, function( response ){
              btn.disabled=false;
              console.log(response);
              var response  = $.parseJSON(response);
              if(response.status==1){
                $(".fedapay_error").html("<b class='text-success'>"+response.message+"</b>");
                 setTimeout( () => {
                    window.location=document.referrer;
                  },200);
              
              }else if(response.status==0){
                $(".fedapay_error").html("<b class='text-danger'>"+response.message+"</b>");
              }
          });
    });
});

