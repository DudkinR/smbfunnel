<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>404</title>
	<link href="https://fonts.googleapis.com/css?family=Kanit:200" rel="stylesheet">

	<link type="text/css" rel="stylesheet" href="<?php if(function_exists('get_option')){echo get_option('install_url').'/assets/default-404-template/1/asset/css/style.css';} ?>" />

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
				<h1>404</h1>
			</div>
			<h2>Oops! Nothing was found</h2>
			<p>The page you are looking for might have been removed had its name changed or is temporarily unavailable. <a href="<?php if(function_exists('get_option')){echo get_option('default_404_page_url');} ?>"><?php if(function_exists('get_option')){echo get_option('default_404_page_button_text');} ?></a></p>
			<div class="notfound-social">
				
				<?php if(function_exists('get_option') && filter_var(get_option('default_404_page_logo'),FILTER_VALIDATE_URL)){ ?>
				<img src="<?php echo get_option('default_404_page_logo'); ?>" class="responsive">
				<?php } ?>
				
			</div>
		</div>
	</div>

</body>

</html>
