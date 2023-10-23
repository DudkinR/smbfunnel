<?php
session_start();
//$url, $name, $type, $modify_index = 0,$cname,$url_path
//$create=$funnel->createFunnel($_POST['funnel_url'],$_POST['funnel_name'],$_POST['funnel_type'],$_POST['modify_index'],$_POST['cname']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	
<form action="req.php" method="post">
<div class="col-sm-6 mw120 mx-auto">
	<div class="card visual-pnl shadow">
	<div class="card-header theme-text bg-white border-bottom-0">
		Create project</div>
		 <div class="card-body">
			<span class="text-danger small mt-1">
			<input type="hidden" name="createfunnel" value="createfunnel">
			<input type="hidden" name="modify_index" value="0">
			</span> <div class="mb-3"><label>Funnel name</label>
			<input type="text" name="funnel_name"  placeholder="Add a title" class="form-control" value="ssssaa">
			</div> 
			<div class="mb-3">
				<label>Funnel type</label> 
			<select name="funnel_type" class="form-select">
			<option value="0">Select funnel type</option> 
			<option value="webinar">Webinar</option> 
			<option value="membership" selected>Membership</option> 
			<option value="sales">Sales</option> 
			<option value="blank">Custom</option>
			</select>
		</div> 
		<div class="mb-3">
			<label>Funnel URL</label>
			 <div class="input-group">
				<div data-bs-toggle="tooltip" title="" class="input-group-prepend" data-bs-original-title="Base URL">
					<span class="input-group-text">https://smbfunnels.com/</span>
				</div> 
				<input type="text" name="funnel_url" data-bs-toggle="tooltip" title="" placeholder="Enter path" class="form-control" data-bs-original-title="Path for the funnel" aria-label="Path for the funnel" value="https://smbfunnels.com/ssssaa">
			</div>
		</div> 
		<div class="mb-3"><label>CNAME</label>
		 <input type="checkbox" name="cname" value="1"> 
		 <div class="input-group">
			<div data-bs-toggle="tooltip" title="" class="input-group-prepend" data-bs-original-title="Base URL">
				<span class="input-group-text">https://</span></div> 
				<input type="text" name="url_path" data-bs-toggle="tooltip" title="" placeholder="Enter path" class="form-control" data-bs-original-title="Path for the CNAME" aria-label="Path for the CNAME" value="https://ssssaa.smbfunnels.com"> 
				<div data-bs-toggle="tooltip" title="" class="input-group-prepend" data-bs-original-title="Base URL">
					
				</div>
			</div>
		</div> 
		submit
		<input type="submit" name="submit" value="submit">
		
		Create funnel
		
		</div>
	</div>
</div>
</form>

</body>
</html>