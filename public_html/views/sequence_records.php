<?php
// print_r($info);
$mysqli=$info['mysqli'];
$pref=$info['dbpref'];
if (isset($_POST['delrecid'])) {
  $id = $_POST['delrecid'];
  $delete ="delete from `".$pref."quick_sequence` where id=".$id;
  $mysqli->query($delete);
}

$id = $_GET['id'];
$id = $mysqli->real_escape_string($id);
if( $id == "old" )
{
  $sequence_id=" `sequence_id`='0' and";
  $fetch="Old Sequence Email";

}else{
  $sequence_id=" `sequence_id`=$id and";
  $rows = $mysqli->query("SELECT `title` FROM `".$pref."new_sequence` WHERE `id`=$id");
  $fetch = $rows->fetch_assoc()['title'];
  $fetch.=" Emails Records";  
}


$start_from=0;
if(isset($_GET["pagecount"]))
{
  $start_from=($_GET["pagecount"]*get_option('qfnl_max_records_per_page'))-get_option('qfnl_max_records_per_page');
}
$hashcount=$start_from;
$timelimit_condition=1;
  $date_between=dateBetween('time');
  if(strlen($date_between[0])>1)
  {
    $timelimit_condition=$date_between[0];
  }
if(isset($_POST['onpage_search']) && strlen($_POST['onpage_search'])>0)
{
  $_POST['onpage_search']=$mysqli->real_escape_string($_POST['onpage_search']);
  $query = "SELECT * FROM `".$pref."quick_sequence` where (`title` like '%".$_POST['onpage_search']."%' or `sentdata` like '%".$_POST['onpage_search']."%') and $sequence_id  `sequence` not in('compose')";
}
else
{
  $order_by='`id` desc';
  if(isset($_GET['arrange_records_order']))
  {
    $order_by=base64_decode($_GET['arrange_records_order']);
  }

  $query = "SELECT * FROM `".$pref."quick_sequence` where `sequence` not in('compose')  and $sequence_id ".$timelimit_condition." order by ".$order_by." LIMIT ".$start_from.", ".get_option('qfnl_max_records_per_page')."";
}

//echo $query;
$result = $mysqli->query($query);


$page_query=$mysqli->query("select count(`id`) as `countid` from `".$pref."quick_sequence` where  `sequence` not in('compose') and $sequence_id  ".$timelimit_condition."");
$page_ob=  $page_query->fetch_object();

$link='<a href="index.php?page=sequences"  style="cursor:pointer"> <i class="fas fa-arrow-left"></i> '.$fetch.'</a>';
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
					<?php echo arranger(array('id'=>'date')); ?>
					</div>
					<div class="col-md-4">
					<div class="mb-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="<?php echo t("Search With Email Title"); ?>" onkeyup="searchSequence(this.value)">
						</div>
					</div>
					</div>
</div>
<div class="row">
<div class="col-sm-12 ">
  <div class="table-responsive">
      <table class="table table-striped">
      <thead>
        <tr><th>#</th><th><?php w("Email&nbsp;Title"); ?></th><th><?php w("Selected Lists"); ?></th><th><?php w("Sequenced"); ?></th><th><?php w("Created On"); ?></th><th><?php w("Action"); ?></th></tr>
      </thead>
      <tbody id="keywordsearchresult">
      <!-- keyword search -->
<?php
while($res= $result->fetch_assoc()){
$usedbylists=0;
$getlistidtrim=explode("@",trim($res['listid'],'@'));
if(array_search("all",$getlistidtrim)>=0)
{
  $usedbylists="all";
}
elseif(is_numeric($getlistidtrim[0]))
{
  $usedbylists=count($getlistidtrim);
}
++$hashcount;
$num = $result->num_rows;
$listid = $res['listid'];
// echo $listid;
$list="";
if(strpos($listid,'@all@')!==false)
{
	$list="<a href='index.php?page=listrecords'><i>All Lists</i></a>";
}
else
{
	$sel = "select id,title from `".$pref."quick_list_records` where id in (".trim(str_replace('@',',',$listid),',').")";
	// echo $sel;
	$listquery=$mysqli->query($sel);
	// print_r($listquery);
	while($lists = $listquery->fetch_assoc())
	{
		$list .="<a href='index.php?page=createlist&listid=".$lists['id']."'>".$lists['title']."</a>, ";
		// echo $list;
	}

}
$title=htmlentities($res['title']);
          $action="<table class='actionedittable'><tr><td><a href='index.php?page=sequence&seqid=".$res['id']."&sequence_id=".$id."'><button class='btn unstyled-button' style='' data-bs-toggle='tooltip' title='".t("Edit Email")."'><i class='fas fa-edit text-primary'></i></button></a></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(this,event,'".$usedbylists."','sequence')\"><input type='hidden' value='".$res['id']."' name='delrecid'><button type='submit' class='btn unstyled-button'  data-bs-toggle='tooltip' title='".t('Delete Email')."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

          $sequenceed_on=($res['sequence']<1)? t('During Signup'): t("After \${1} Days",array($res['sequence']));
          if($res['sequence']=='1')
          {
            $sequenceed_on=t("Next day");
          }
        echo "<tr>
          <td>".t($hashcount)."</td>
          <td>".$title."</td>
          <td>".rtrim($list,",")."</td>
          <td>".$sequenceed_on."</td>
          <td>".date('d-M-Y h:ia',$res['time'])."</td>
          <td>".$action."</td>
        </tr>";
      }
     ?>
     <tr><td colspan=10 class="total-data"> <?php w("Total Emails"); ?>: <?php echo t($page_ob->countid); ?></td></tr>
    <!-- /keyword search --> 
    </tbody> 
    </table>
    </div>
    <div class="col-sm-12 row pt-2">
    <div class="col-sm-6 me-auto">
    <?php
       $currentpage=0;

       if(isset($_GET['pagecount']))
       {
        $currentpage=$_GET['pagecount'];
       }
       $pagenow=$_SERVER['REQUEST_URI']."&pagecount";
       echo createPager($page_ob->countid,$pagenow,$currentpage);
       ?>
    </div>
    <div class="col-sm-6">
    <a href="index.php?page=sequence&&sequence_id=<?=$id?>"><button class="btn theme-button" style="float:right;"><strong><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></strong></button></a>
    </div>
    </div>
</div></div></div></div></div>
<script>
    document.getElementById("commoncontainerid").innerHTML=`<?=$link?>`;
function searchSequence(search)
{
var ob=new OnPageSearch(search,"#keywordsearchresult");
ob.url="<?php echo getProtocol();?>";
ob.url +="<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
ob.	search();
}
</script>