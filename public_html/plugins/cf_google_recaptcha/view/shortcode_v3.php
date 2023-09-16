<?php
global $mysqli;
global $dbpref;

$table = $dbpref . 'google_recaptcha';
$sql = $mysqli->query("SELECT * FROM  `" . $table . "` where id='$id'");
$rows = mysqli_fetch_assoc($sql);

$credentials = json_decode($rows['credentials']);
$site_key = $credentials->site_key;
$secret_key = $credentials->secret_key;
?>
<?php 
	if(isset($_POST['token'])) {
		//print_r($_POST);
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$data = [
			'secret' => $secret_key,
			'response' => $_POST['token'],
			// 'remoteip' => $_SERVER['REMOTE_ADDR']
		];

		$options = array(
		    'http' => array(
		      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		      'method'  => 'POST',
		      'content' => http_build_query($data)
		    )
		  );

		$context  = stream_context_create($options);
  		$response = file_get_contents($url, false, $context);

		$result= json_decode($response, true);	
		//print_r($result);
		
	}
	?>
<script src="https://www.google.com/recaptcha/api.js?render=<?=$site_key?>"></script>
<script>
    document.querySelector('form').addEventListener("submit", function() {
      // we stoped it
      event.preventDefault();
      // needs for recaptacha ready
      grecaptcha.ready(function() {
        // do request for recaptcha token
        // response is promise with passed token
        grecaptcha.execute('<?=$site_key?>',{
          action: 'submit'
        }).then(function(token) {
			$('form').prepend('<input type="hidden" name="token" value="' + token + '">');
            $('form').prepend('<input type="hidden" name="action" value="submit">');
			try{
			//alert("Verified");
			$('form').unbind('submit').submit();
		  }
			catch(err) {
					}
				});
      });
    });
  </script>

