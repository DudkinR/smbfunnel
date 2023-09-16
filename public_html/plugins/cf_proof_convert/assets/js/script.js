// Use jQuery via $(...)
"use strict";
$(document).ready(function(){
  //will assign use as exit value
  $('#cfproof_convert_rotative').click(function(){
    if( $(this).prop("checked") == true ){
      $("#cfproof_convert_rotative_hidden").val("yes");
    }
    else if( $(this).prop("checked") == false ){
      $("#cfproof_convert_rotative_hidden").val("no");
    }
  });
  $(".cfproof_convert_message_type").change(function(){
    var change=$(this).val();
    if(change=="f" || change=="b"){
        $(".cfproof_convert_overlay").show();  
    }else{
      $(".cfproof_convert_overlay").hide();
    }
  })
  $(".cfproof_convert_createfake_save").click(function(){
    $(".cfproof_convert_overlay").hide();
  })
  $(".cfproof_convert_close").click(function(){
    $(".cfproof_convert_overlay").hide();
  })
    // popup settings
  $("#cfproof_convert_AddSetting").on("submit", function(eve){
    eve.preventDefault();
    let btn=eve.target;
    btn.disabled=true;
    let cfproof_convert_fake_data=cfproof_convert_fake_ob.getInputs();
    $("#cfproof_convert_save_setting").html('Saving.. <span class=" spinner-border spinner-border-sm"></span></button>');
    var postData= "action=cfproof_convert_admin_ajax&"+$("#cfproof_convert_AddSetting").serialize()+"&cfproof_convert_fake_data="+cfproof_convert_fake_data;      
    $.post( $("#cfproof_convert_ajax").val() , postData, function( response ){
      btn.disabled=false;
      // console.log(response);
      var response  = $.parseJSON(response);
      if(response.status==1){
        $("#cfproof_convert_save_setting").html("Saved");    
          setTimeout(function(){
          location.href="index.php?page=cfproof_convert_setup_setting&cfproof_convert_setup_id="+response.setup_id;
          }, 500);  
      }else if(response.status==0){
        alert("Error: There was an error! Setting not saved");
      }
    });
  });
    $(".cfproof_convert_setup_delete").on("click" ,function(eve){
      var conf = confirm( "Are you sure!" );
      
      if(conf){
        var deleteId=$(this).attr("data-id");;
        var postData= "action=cfproof_convert_delete_ajax&cfproof_convert_param=delete_setup&id="+deleteId;
        
        $.post( $("#cfproof_convert_ajax").val() , postData, function( response ){
            var response = JSON.parse(response);
            if(response.status==1){
              setTimeout(function(){
                location.reload()
               }, 100);
            
            }else{
                alert("Error: User Not Deleted successfully! ") 
            }
        });
      }
  });
});

