<?php
$optins_ob=$data_arr['optinob'];
$funnel=$data_arr['funnel'];
if(isset($_POST['deleteoptin']))
{
$optins_ob->deleteOptin($_POST['deleteoptin']);
}
?>
<div class="container-fluid">
<div class="card pb-2  br-rounded" id="hidecard1">
    <div class="card-body pb-2" id="hidecard2">
<div class="row">
					<div class="col-md-2 mb-2">
					<?php echo createSearchBoxBydate(); ?>
					</div>
					<div class="col-md-3">
					<?php echo showRecordCountSelection(); ?>
					</div>
					<div class="col-md-3">
					<?php echo arranger(array('id'=>'Date')); ?>
					</div>
					<div class="mb-3 col-md-4">
						<div class="input-group input-group-sm mb-3   float-right">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="Search with name, email, IP or other fields" onkeyup="searchOptins(this.value)">
						</div>
					</div>
			</div>
<div class="row">
<div class="col-sm-12 nopadding">
<?php
$countpage=0;
if(isset($_GET['page_count'])){$countpage=$_GET['page_count'];}

if(isset($_GET['funnelid']))
{
$optin_datas=$optins_ob->visualOptisForFunnels($_GET['funnelid'],$countpage);
}
elseif(isset($_GET['pageid']))
{
$optin_datas=$optins_ob->visualOptisForFunnels($_GET['pageid'],$countpage);
}
else
{
$optin_datas=$optins_ob->visualOptisForFunnels('all',$countpage);
}

//echo $optin_datas['extracols'];
?>
  
<div class="table-responsive">
<table class="table table-striped">
<?php
//optins for specific funnel
 if(isset($_GET['funnelid'])){
 ?>
<thead><th>#</th>
<?php
$extracountcols=0;
if($optin_datas['extracols'] !=0)
{
  if(in_array('name',$optin_datas['extracols'])){echo "<th>Name</th>";};
  if(in_array('email',$optin_datas['extracols'])){echo "<th>Email</th>";};
	for($i=0;$i<count($optin_datas['extracols']);$i++)
	{
		if(in_array($optin_datas['extracols'][$i],array('name','email','password','reenterpassword')))
		{
			continue;
		}
		++$extracountcols;
		echo "<th>".$optin_datas['extracols'][$i]."</th>";
	}
}
?>
<th>IP</th><th>Page</th><th>Added On</th><th>Action</th></thead>
<tbody id="keywordsearchresult">
<!-- keyword search -->
<?php
$hashcount=0;
if(isset($_GET['hashcount']))
{
	$hashcount=$_GET['hashcount'];
}
while($r=$optin_datas['leads']->fetch_object())
{
	++$hashcount;

	$action="<table class='actionedittable'><tr><td><form action='' method='post'><input type='hidden' name='deleteoptin' value='".$r->id."'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='Delete Record'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

	$pagefunnel_data=$funnel->getPageFunnel($r->pageid,'','','id');

	if(!$pagefunnel_data){continue;}

	echo"<tr><td>".$hashcount."</td>";
if($optin_datas['extracols'] !=0)
{
  if(in_array('name',$optin_datas['extracols'])){echo "<td>".$r->name."</td>";};
  if(in_array('email',$optin_datas['extracols'])){echo "<td>".$r->email."</td>";};
	for($i=0;$i<count($optin_datas['extracols']);$i++)
	{
		if(in_array($optin_datas['extracols'][$i],array('name','email','password','reenterpassword')))
		{
			continue;
		}
		$jsn=json_decode($r->extras);
		$customrow=$optin_datas['extracols'][$i];
		if(isset($jsn->$customrow))
		{
			$jsn=$jsn->$customrow;
		}
		else
		{
			$jsn="NA";
		}
		echo "<td>".$jsn."</td>";
	}
}

  $url="<a href=".$optin_datas['base_url']."/".$pagefunnel_data->filename." target='_BLANK'>".$pagefunnel_data->filename."</a>";

  echo "<td>".$r->ipaddr."</td><td>".$url."</td><td>".date('d-M-Y',$r->addedon)."</td><td>".$action."</td></tr>";
}
?>
<tr><td colspan=1000000 class="total-data">Total Optins: <?php echo number_format($optin_datas['total']); ?></td></tr>
<!-- /keyword search -->
</tbody>
<?php
 }
 //ends here for specific
 ?>
</table>
</div>

<div class="col-sm-12 row nopadding">
<div class="col-6 mt-2 me-auto">
<?php
$nextpageurl=$_SERVER['REQUEST_URI']."&page_count";
$currentpage=0;
if(isset($_GET['page_count']))
{
	$currentpage=$_GET['page_count'];
}
echo createPager($optin_datas['total'],$nextpageurl,$currentpage);
?>
</div>
<div class="col-6 mt-2 text-right total-data">
<form action="index.php?page=export_csv" method="post">
	<?php if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2) { ?>
	<button type="button" class="btn theme-button" style="float:right;" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" value="<?php if(isset($_GET['funnelid'])){ echo $_GET['funnelid']; } ?>">
	<?php } else { ?>
	<button type="submit" class="btn theme-button" style="float:right;" name="optinto_csv" value="<?php if(isset($_GET['funnelid'])){ echo $_GET['funnelid']; } ?>">
	<?php } ?>
		<i class="fas fa-file-download"></i> Export To CSV
	</button>
</form>
</div>
</div>

</div>
</div>
</div></div></div>
<style>
.actionedittable,.actionedittable tr,.actionedittable td
{
	background-color:inherit !important;
	padding:0px !important;
	border:0px;
}
.actionedittable button
{
	margin-left:6px !important;
}
</style>
<script>
function searchOptins(search)
{
var ob=new OnPageSearch(search,"#keywordsearchresult");
ob.url="<?php echo getProtocol();?>";
ob.url +="<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
ob.search();
}
</script>
