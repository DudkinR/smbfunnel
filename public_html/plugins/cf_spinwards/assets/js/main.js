$(document).ready(function(){


    $(".cfwheelcheck").click(function(){
        let checked=0;
        if( $( this ).is(":checked") ){
            checked=1;
        }else{
            checked=0;
        }
        var id=$(this).attr("data-id");
        var postData= "action=spinnerwheelAjax&id="+id+"&checked="+checked;
        $.post( $("#cfexito_ajax").val() , postData, function( response ){

        });
    })
    var id=$(this).attr("data-id");
        var idd= "action=spinnerwheelAjax&id="+id;
    

    


      



            function gdprTinyMce(selector_name)
            {
                //'#cookie_message'
                tinymce.init({
                  selector : selector_name,
                  language: cf_tinymce_lang,
                  convert_urls : false,
                  height:300,
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
          
            //headerA 
            gdprTinyMce("#cf_spinner_header_Content");
            
            //footer
            gdprTinyMce("#cf_spinner_footer_Content");

//mailerbody
gdprTinyMce("#cf_spinner_mailerbody");
//mailsubj
            $("#wheelsetting").on("submit", function(event){
                  event.preventDefault();
                  
                  let cfspin_inp = cfspin_inp_ob.getInputs();
                // alert("test");
                 //console.log("SS"+cfspin_inp);
                  var postData= "action=settingform&"+$(this).serialize() +"&custominputs="+cfspin_inp;
                // console.log(postData);
                // console.log(cfspin_inp);
                // console.log("hello");

                  $.post( $("#cfspinsettingajax").val() , postData, function( response ){

                 // console.log(response);
                    var response  = $.parseJSON(response);
                  //  console.log(response);
                    if(response.status==1){
                    
                    setTimeout(function(){
                    location.href="index.php?page=CFSpinner_settingform&cfspinner_wheelid="+response.wheelid;
                    }, 500);
                    
                    }else if(response.status==0){
                      alert("Error: There was an error! Setting not saved");
                    }                    
                  });
              })





    
    });

    
    