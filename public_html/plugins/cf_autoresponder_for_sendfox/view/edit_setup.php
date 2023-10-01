
 <?php
 
 
 global $mysqli;
 global $dbpref;
 $optin_controller=$this->load('forms_control');
 $title="";$apikey="";$listid="";
 if(isset($_GET['autoid']))
 {
   if( is_numeric( $_GET['autoid'] ) )
   {
     $autoid = $_GET['autoid'];
   }
  
 $settings= $optin_controller->getSaveSettings($autoid);


$title=$settings['title'];
$apikey =$settings['apikey'];
$listid = $settings['listid'];
$id = $settings['id'];
  }
 ?>
  
  
  
  
  
  
  <div class="container-fluid" id="cfpay_payment_methods">
<div class="row page-titles mb-4">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Sendfox</h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">Create, edit and manage your Autoresponder methods</div>
    </div>
</div>
    <div class="row">
        <div class="col-sm-12" >
            <div class="row justify-content-center align-items-center">
                <!-- script for popup -->
                <div class="col-sm-6" > 
                    <div class="card pnl visual-pnl" >
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <span>SendFox</span>
                                </div>
                            </div>
                        </div>

                        <form method="post" id="sanboxpoup">
                        <div class="card-body">
                        <input type="hidden" name="autotype" value="sendfox">
                        <input type="hidden" name="id" value="<?php echo ((isset($id))? $id:'0');?>">
                        <input type="hidden" id="cfsendfox_userajax" name="cfsendfox_userajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
                          <div class="mb-3">
                            <label for="title">Enter Title</label>
                            <input type="text" class="form-control" placeholder="Enter Title" required="" value="<?php echo $title;?>" name="title"> 
                          </div>


                          <div class="mb-3">
                            <label for="api-key">Enter API Key</label>
                            <input type="text" class="form-control" placeholder="Enter API Key" required="" name="apikey" value="<?php echo $apikey;?>">
                          </div>

                          <div class="mb-3">
                            <label for="list-id">Enter List ID</label>
                            <input type="text" class="form-control" placeholder="Enter List ID" required="" name="listid" value="<?php echo $listid;?>">
                          </div>

                          <div class="mb-3">
                            <label for="email">Enter Email</label>
                            <input type="text" class="form-control" placeholder="Enter Unique Email ID Not Present In List" name="email" required="">
                          </div>

                          <div class="sendfox_error text-center" style="margin-bottom:1rem !important;">
                                    
                                    </div> 
                          <div class="mb-3">
                            <button type="submit" class="btn theme-button  form-control btnclr cfsandfox_save_setting" id="cfsandfox_save_setting">Authenticate & Save</button>
                          </div>
                          </div>
                        </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <script type='text/javascript'>
function openpopup() {
  var x = document.getElementById("popup");
  if (x.style.display === "none") {
    x.style.display = "block";
  }
}
function closepopup()
{
    var x = document.getElementById("popup");
    if (x.style.display === "block") {
    x.style.display = "none";
  }

}



// // Use jQuery via $(...)
$(document).ready(function(){
  // popup settings
  // alert("test");

  $("#sanboxpoup").on("submit", function(eve){
    // alert("test");

      eve.preventDefault();
// alert("test");
      var postData= "action=myPopupFormAjax&"+$("#sanboxpoup").serialize();
      
      $.post( $("#cfsendfox_userajax").val() , postData, function( response ){
          console.log(response);
          var response  = $.parseJSON(response);
              if(response.status==1){

                $(".sendfox_error").html("<b class='text-success'>"+response.message+"</b>");
                 setTimeout( () => {
                    window.location=document.referrer;
                  },200);
              
              }else if(response.status==0){
                $(".sendfox_error").html("<b class='text-danger'>"+response.message+"</b>");
              }
       
      });
  });
});



</script>