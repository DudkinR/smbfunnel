// Use jQuery via $(...)
$(document).ready(function(){
  
    //will assign use as exit value
    $('#cfexito_use_as_exit').click(function(){
      if( $(this).prop("checked") == true ){
            $("#cfexito_use_as_exit_hidden").val("yes");
        }
        else if( $(this).prop("checked") == false ){
            $("#cfexito_use_as_exit_hidden").val("no");
        }
    });

    // popup settings
    $("#cfexito_AddSetting").on("submit", function(eve){
        eve.preventDefault();
        let btn=eve.target;
        btn.disabled=true;
        let cfexito_inps=cfexito_inp_ob.getInputs();
        
        $("#cfexito_save_setting").html('Saving.. <span class=" spinner-border spinner-border-sm"></span></button>');
            
        var header_content = encodeURIComponent(tinyMCE.get("cfexito_header_content").getContent());
        var footer_content = encodeURIComponent(tinyMCE.get("cfexito_footer_content").getContent());

        var postData= "action=myPopupFormAjax&"+$("#cfexito_AddSetting").serialize()+"&cfexito_header_content="+header_content+"&cfexito_footer_content="+footer_content+"&custom@cfexito_inps="+cfexito_inps;
        
        $.post( $("#cfexito_ajax").val() , postData, function( response ){
            btn.disabled=false;
            var response  = $.parseJSON(response);
            if(response.status==1){
            $("#cfexito_save_setting").html("Saved");
            
            setTimeout(function(){
            location.href="index.php?page=cfexito_popup_forms&cfexito_form_id="+response.form_id;
            }, 500);
            
            }else if(response.status==0){
              alert("Error: There was an error! Setting not saved");
            }
        });
    });

  $(".cfexitoFormdelete").on("click" ,function(eve){
      var conf = confirm( "Are you sure!" );
      
      if(conf){
        var deleteId=$(this).attr("data-id");;
        var postData= "action=myPopupFormAjax&cfexito_param=delete_form&id="+deleteId;
        
        $.post( $("#cfexito_ajax").val() , postData, function( response ){
            var response = JSON.parse(response);
            console.log(response);
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

  function gdprTinyMce(selector_name)
  {
      //'#cookie_message'
      tinymce.init({
        selector : selector_name,
        language: cf_tinymce_lang,
        convert_urls : false,
        height: 465,
        plugins: 'image,link,code',
        toolbar: 'undo redo | link image | code | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help   ',
        content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        '//www.tiny.cloud/css/codepen.min.css'
        ],
        // enable title field in the Image dialog
        image_title: true,
        images_upload_url : 'req.php',
        automatic_uploads : false,

        images_upload_handler : function(blobInfo, success, failure) {
          var xhr, formData;

          xhr = new XMLHttpRequest();
          xhr.withCredentials = false;
          xhr.open('POST', 'req.php');

          xhr.onload = function() {
          var json;

          if (xhr.status != 200) {
          failure('HTTP Error: ' + xhr.status);
          return;
          }
       
          json = JSON.parse(xhr.responseText.trim());
        
          if (!json || typeof json.location != 'string') {
            failure('Invalid JSON: ' + xhr.responseText);
            return;
          }
          success(json.location);
        };
        formData = new FormData();
        formData.append('tinymceimgupload',1);
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
      },
    });
  }

  //header
  gdprTinyMce("#cfexito_header_content");
  
  //footer
  gdprTinyMce("#cfexito_footer_content");
});

