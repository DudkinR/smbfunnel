<?php
global $mysqli;
global $dbpref;

$table = $dbpref . 'google_recaptcha';
$sql = $mysqli->query("SELECT * FROM  `" . $table . "` where id='$id'");
$rows = mysqli_fetch_assoc($sql);

$credentials = json_decode($rows['credentials']);
$site_key=$credentials->site_key;
$secret_key=$credentials->secret_key;
?>

<?php

if(isset($_POST['g-recaptcha-response'])){

  if(empty($_POST['g-recaptcha-response']))
  {
   $captcha_error = 'Captcha is required';
  }
  else
  {
  
   $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response'].'&remoteip='.$_SERVER['REMOTE_ADDR']);
 
   $response_data = json_decode($response);
 
   if(!$response_data->success)
   {
    $captcha_error = 'Captcha verification failed';
   }
  }
  if($captcha_error == '')
 {
  $data = array(
   'success'  => true
  );
 }
 else
 {
  $data = array(
   'captcha_error'  => $captcha_error
  );
 }

 echo json_encode($data);
 
}

?>

  <!-- Google Recaptcha --> 
      <div class="g-recaptcha" data-sitekey="<?=$site_key?>"  data-callback="onSuccess" data-action="action"></div>
      <p id="errorid"></p>
    </div>
  <!-- Google Recaptcha --> 

      <script>
    $(document).ready(function () {
    document.querySelectorAll('form')[0].addEventListener("submit", function(evt) 
    {
    var response = grecaptcha.getResponse();
    
    if(response.length == 0) 
    {      
     
      evt.preventDefault(evt);
      document.getElementById("errorid").innerHTML = "Please verify that you are not a robot.";
      document.getElementById("errorid").style.color = "red";
      return false;
    }
    else {
             
      return true;
        
      };
  
    
  });
    
});

    var onSuccess = function (response) {
      if(response.length != 0){
      
      $("#errorid").empty();

      return true;
      }
    };
  </script>