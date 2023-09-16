<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Under Construction</title>
	<link href="https://fonts.googleapis.com/css?family=Kanit:200" rel="stylesheet">

	<link type="text/css" rel="stylesheet" href="<?php if(function_exists('get_option')){echo get_option('install_url').'/assets/under_construction/1/asset/css/style.css';} ?>" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
      <div class="notfound-social">
				
				<?php if(function_exists('get_option') && filter_var(get_option('default_under_page_logo'),FILTER_VALIDATE_URL)){ ?>
				<img src="<?php echo get_option('default_under_page_logo'); ?>" class="responsive">
				<?php } ?>
				
			</div>
			</div>
			<h2><?php
  if(get_option("underconstruction_page_title")!="")
  {
    echo get_option("underconstruction_page_title");
  }else{
      echo "Under Construction";
  }
  
  ?></h2>
			<p><?php
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
