<?php
$mysqli=$info['mysqli'];
$pref=$info['dbpref'];
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
					<?php echo arranger(array('`b`.id'=>'date','`count_members`'=>'Number Of Members')); ?>
					</div>
					<div class="col-md-4">
					<div class="input-group input-group-sm mb-3">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="<?php w('Enter funnel or page name or category'); ?>" onkeyup="searchMembershipFunnel(this.value)">
						</div>
					</div>
</div>

<div class="col-sm-12 nopadding">
<div class="table-responsive">
<table class="table table-striped">
<thead><th>#</th><th><?php w('Title'); ?></th><th><?php w('URL'); ?></th><th><?php w('Total&nbsp;Members'); ?></th><th><?php w('Created&nbsp;On'); ?></th><th><?php w('Action'); ?></th></thead>
<tbody id="keywordsearchresult">
<!-- keyword search -->
<?php

$funnel=$data_arr['funnel'];

if(isset($_POST['delfunnel']))
{
	$funnel->deleteFunnel($_POST['delfunnel']);
}

if(isset($_GET['hash_count']))
{
	$hashcount=$_GET['hash_count'];
}
if(isset($_GET['page_count']))
{
$datas=$funnel->getAllFunnelForView($_GET['page_count'],'membership');
}
else
{
$datas=$funnel->getAllFunnelForView(0,'membership');
}
$hashcount=0;

if(isset($_GET['page_count']))
{
	$hashcount=($_GET['page_count']*get_option('qfnl_max_records_per_page'))-get_option('qfnl_max_records_per_page');
}

$count=0;
$lastid=0;
if($datas['rows'])
{
$lastid=$datas['rows']->num_rows;
while($r=$datas['rows']->fetch_object())
{
	
	$count_used_in=$funnel->countIntegratedInOthers($r->id);
	$count_used_type="membership funnel";

++$hashcount;
++$count;
//$funneldata=$funnel->getFunnel($r->funnel_id,$select="`name`,`baseurl`");
$sumdata=$funnel->totalSumCountsFunelPages($r->funnel_id);
$action="<table class='actionedittable'><tr><td><a href='index.php?page=members&funnelid=".$r->funnel_id."'><button class='btn unstyled-button' data-bs-toggle='tooltip' title='".t('View Members')."'><i class='fas fa-user-shield text-success'></i></button></a></td><td><a href='index.php?page=create_funnel&id=".$r->funnel_id."'><button class='btn unstyled-button' data-bs-toggle='tooltip' title='".t('Edit Funnel')."'><i class='fas fa-edit text-primary'></i></button></a></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(this,event,'".$count_used_in."','".$count_used_type."')\"><input type='hidden' name='delfunnel' value='".$r->funnel_id."'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='".t('Delete Funnel')."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

$totalmembers=$r->count_members;

echo "<tr><td>".t($hashcount)."</td><td>".$r->funnel_name."</td><td><a href='".$r->funnel_baseurl."' target='_BLANK'>".$r->funnel_baseurl."</a></td><td><a href='index.php?page=members&funnelid=".$r->funnel_id."'>".t(number_format($totalmembers))."</a></td><td>".date('d-M-Y',$r->funnelcreatedon)."</td><td>".$action."</td></tr>";
}
}
?>
<tr><td colspan=10 class="total-data"><?php w('Total Projects') ?>: <?php echo t(number_format($datas['total_rows'])); ?></td></tr>
<!-- /keyword search -->
</tbody>
</table>
</div>

<div class="col-md-12 row border-top pt-2">
<div class="col-sm-6 me-auto">
<?php
$paging_url="index.php?page=membership_funnels&page_count";
$pagecount=0;
if(isset($_GET['page_count']))
{
	$pagecount=$_GET['page_count'];
}
echo createPager($datas['total_rows'],$paging_url,$pagecount,$lastid);
?>
</div>
</div>

</div>
</div></div></div>


<script>
function searchMembershipFunnel(search)
{
var ob=new OnPageSearch(search,"#keywordsearchresult");
ob.url="<?php echo getProtocol();?>";
ob.url +="<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
ob.	search();
}
</script>