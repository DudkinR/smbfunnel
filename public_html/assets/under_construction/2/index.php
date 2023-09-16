<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Under Construction</title
	<link href="https://fonts.googleapis.com/css?family=Quicksand:700" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="<?php if(function_exists('get_option')){echo get_option('install_url').'/assets/under_construction/2/asset/css/style.css';} ?>" />


</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-bg">
				<div></div>
				<div></div>
				<div></div>
			</div>
			<?php if(function_exists('get_option') && filter_var(get_option('default_under_page_logo'),FILTER_VALIDATE_URL)){ ?>
			<div class="notfound-social">
					<img src="<?php echo get_option('default_under_page_logo'); ?>" class="responsive"> 
			</div>
			<?php } ?>
			<h2>
			<?php
  if(get_option("underconstruction_page_title")!="")
  {
    echo get_option("underconstruction_page_title");
  }else{
      echo "Under Construction";
  }
  
  ?>
			</h2>
			<p class="message"><?php
  if(get_option("underconstruction_page_descritption")!="")
  {
    echo get_option("underconstruction_page_descritption");
  }else{
      echo "This site is under construction. Please check back soon.";
  }
  
  ?></p>
		</div>
	</div>

</body>

</html>
