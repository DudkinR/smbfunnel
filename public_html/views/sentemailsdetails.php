<?php
$mysqli = $info['mysqli'];
$pref = $info['dbpref'];

if(isset($_GET))
  {
    foreach($_GET as $getindex=>$getvalue)
    {
      $_GET[$getindex]=$mysqli->real_escape_string($getvalue);
    }
  }



  $startpagecount=0;

if(isset($_GET['page_count']))
{
if(is_numeric($_GET['page_count']))
{
  $startpagecount=($_GET['page_count']*get_option('qfnl_max_records_per_page'))-get_option('qfnl_max_records_per_page');
}
}
$hashcount=$startpagecount;


if (isset($_POST['delrecid'])) {
  $id = $mysqli->real_escape_string($_POST['delrecid']);
  $delete ="delete from `".$pref."quick_subscription_mail_schedule` where id=".$id;
  $mysqli->query($delete);
}
elseif (isset($_POST['deleteres'])) {
  $seqid = $mysqli->real_escape_string($_POST['deleteres']);
  $delete ="delete from `".$pref."quick_subscription_mail_schedule` where seqid='".$seqid."' and status not in('-1')";
  $mysqli->query($delete);
}

$page = '';

$status="";
$seqid = "";

$headertitle="Mail Reports";

if (isset($_GET['status']) || isset($_GET['seqid'])) {
$getstatus = $_GET['status'];
if ($getstatus == 'opened') {
	$status = "(status=2 or status=3)";
  $seqid = $_GET['seqid'];
  $headertitle="Opened Mails";
}
elseif($getstatus == "unopened") {
	$status = "status=1";
  $seqid = $_GET['seqid'];
  $headertitle="Unopened Mails";
}
elseif ($getstatus == "sent") {
  $status = "(status=1 or status=2 or status=3)";
  $seqid = $_GET['seqid'];
  $headertitle="Sent Mails";
}
elseif ($getstatus == "notsent") {
  $status = "status=0";
  $seqid = $_GET['seqid'];
  $headertitle="Not Sent";
}
elseif ($getstatus == "unsubscribe") {
  $status = "status=3";
  $seqid = $_GET['seqid'];
  $headertitle="Unsubscribes";
}

$keyword_search="";
$dokeywordsearch=0;
       if(isset($_POST['onpage_search']) && strlen($_POST['onpage_search'])>0)
       {
        $_POST['onpage_search']=$mysqli->real_escape_string($_POST['onpage_search']);
        $keyword_search=" and `extraemails` like '%".$_POST['onpage_search']."%'";
        ++$dokeywordsearch;
       }
       $order_by='`id` desc';
       if(isset($_GET['arrange_records_order']))
       {
         $order_by=base64_decode($_GET['arrange_records_order']);
       }
if($getstatus=="links_visits")
{
  $datebetween=dateBetween('createdon');
 
  $seqid=$_GET['seqid'];

  if($dokeywordsearch===0)
  {
  $query="select * from `".$pref."quick_subscription_mail_schedule` where stoken in(select distinct(`email_token`) from `".$pref."email_links_visits` where `sequence_id`='".$seqid."'".$datebetween[1]." and `visited`='1')".$keyword_search." order by ".$order_by." limit ".$startpagecount.",".get_option('qfnl_max_records_per_page')."";
  }
  else
  {
    $query="select * from `".$pref."quick_subscription_mail_schedule` where stoken in(select distinct(`email_token`) from `".$pref."email_links_visits` where `sequence_id`='".$seqid."' and `visited`='1') order by `id` desc limit ".$startpagecount.",".get_option('qfnl_max_records_per_page')."";
  }

  $totalqry=$mysqli->query("select count(`id`) as `countid` from `".$pref."quick_subscription_mail_schedule` where stoken in(select distinct(`email_token`) from `".$pref."email_links_visits` where `sequence_id`='".$seqid."' and `visited`='1')");

  $headertitle="Links Visits";
}
else
{
  $datebetween=dateBetween('time');

  if($dokeywordsearch===0)
  {
  $query = "SELECT * FROM `".$pref."quick_subscription_mail_schedule` where ".$status."".$datebetween[1]." and seqid=".$seqid." order by ".$order_by." limit ".$startpagecount.",".get_option('qfnl_max_records_per_page')."";
  }
  else
  {
    $query = "SELECT * FROM `".$pref."quick_subscription_mail_schedule` where ".$status." and seqid=".$seqid."".$keyword_search." order by id desc";
  }

  $totalqry=$mysqli->query("SELECT count(`id`) as `countid` FROM `".$pref."quick_subscription_mail_schedule` where ".$status."".$datebetween[1]." and seqid=".$seqid." order by id desc");

}

$result = $mysqli->query($query);
}
else
{
  $table=$pref."quick_sequence";
       $datebetween=dateBetween('time');
       if(strlen($datebetween[0])>0)
        {$datebetween[0]="where ".$datebetween[0];}
       
        $linksdatebetween=dateBetween('createdon');
      $subquery_array=array
      (
        "(select count(id) from `".$pref."quick_subscription_mail_schedule`  where seqid=`a`.id and (status=1 or status=2 or status=3)".$datebetween[1].") as `total_sent`",

        "(select count(id) from `".$pref."quick_subscription_mail_schedule`  where seqid=`a`.id and status=2 ".$datebetween[1].") as `total_open`",

       "(select count(id) from `".$pref."quick_subscription_mail_schedule`  where seqid=`a`.id and status='1' ".$datebetween[1].") as `total_unopen`",

       "(select count(id) from `".$pref."quick_subscription_mail_schedule`  where seqid=`a`.id and status='0'".$datebetween[1].") as `unsents`",

        "(select count(id) from `".$pref."quick_subscription_mail_schedule`  where seqid=`a`.id and status=3".$datebetween[1].") as `unsubscribed`",


        "(select max(`time`) as `lasttime` from `".$pref."quick_subscription_mail_schedule` where `seqid`=`a`.id and time not in('0')".$datebetween[1].") as `lastsenttime`",

         "(select count(`id`) as `countid` from `".$pref."email_links_visits` where `sequence_id`=`a`.id and `visited`='1'".$linksdatebetween[1].") as `total_linksvisits`",
      );
      
       $arrange_orderby="`time` desc"; $arrange_orderby_main="order by `time` desc";

       if(isset($_GET['arrange_records_order']))
       {
            $arrangeorderbytype=base64_decode($_GET['arrange_records_order']);
            if(strpos($arrangeorderbytype,'time')===0)
            { 
              $arrange_orderby=$arrangeorderbytype;
            // }
            // else
            // {
              $arrange_orderby_main=" order by ".$arrangeorderbytype;
            }
       }

       $keyword_search="";
       if(isset($_POST['onpage_search']) && strlen($_POST['onpage_search'])>0)
       {
        $_POST['onpage_search']=$mysqli->real_escape_string($_POST['onpage_search']);
        $keyword_search=" and `a`.title like '%".$_POST['onpage_search']."%' or `a`.sentdata like '%".$_POST['onpage_search']."%'";

        $sql="select `a`.id,`a`.title,`a`.sequence,".implode(",",$subquery_array)." from `".$table."` as `a` where `id` in(select `seqid` from `".$pref."quick_subscription_mail_schedule` order by `time` desc)".$keyword_search."";
       }
       else
       {
        $sql="select `a`.id,`a`.title,`a`.sequence,".implode(",",$subquery_array)." from `".$table."` as `a` where `id` in(select `seqid` from `".$pref."quick_subscription_mail_schedule`".$datebetween[0]." order by ".$arrange_orderby.")".$arrange_orderby_main." limit ".$startpagecount.", ".get_option('qfnl_max_records_per_page')."";
       }

        $totalqry=$mysqli->query("select count(`id`) as `countid` from `".$table."` where `id` in(select `seqid` from `".$pref."quick_subscription_mail_schedule`".$datebetween[0]." order by `time` desc)");
       //echo $sql;
        $resultseq = $mysqli->query($sql);
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
          <?php if(!isset($_GET['status']) && !isset($_GET['seqid'])){  ?>
					<div class="col-md-3">
					<?php echo arranger(array('time'=>'date','total_open'=>'Opened Mails','total_unopen'=>'Unopened Mails','unsents'=>'Unsent Mails','unsubscribed'=>'Number of Unsubscribers','total_linksvisits'=>'Links Visits')); ?>
					</div>
          <?php }else{ ?>
          <div class="col-md-3">    
          <?php echo arranger(array('time'=>'date')); ?>
          </div>
          <?php } ?>    
					<div class="col-md-4">
					<div class="mb-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="<?php if(!isset($_GET['status']) && !isset($_GET['seqid'])){echo t("Enter Title Of the Sequence");}else{echo t("Enter Email Id");} ?>" onkeyup="searchMailsendingHistory(this.value)">
						</div>
					</div>
					</div>
</div>
<div class="row">
<div class="col-lg-12 ">
  <div class="table-responsive">
      <table class="table table-striped">

        <?php if(isset($_GET['status'])){ ?>
        <thead>
        <tr>
          <th>#</th>
          <th><?php w("Email"); ?></th>
          <?php
          if($_GET['status']=="links_visits")
          {
            echo "<th>".t("Links&nbsp;Visits")."</th>";
          }
          ?>
          <th><?php w("Subject"); ?></th>
          <th><?php w("List ID"); ?></th>
          <th><?php w("Date"); ?></th>
          <th><?php w("Options"); ?></th>
        </tr>
        </thead>
<tbody id="keywordsearchresult">
<!-- keyword search -->      
<?php
while($res= $result->fetch_assoc()){
  ++$hashcount;
$num = $result->num_rows;

        // $subscount = "select id from `".$pref."quick_email_lists` where listid=".$res['id'];
        // $subscnt = $mysqli->query($subscount);
        // $subsnum = $subscnt->num_rows;

          $action="<table class='actionedittable'><tr><td><form action='' method='post' onsubmit='return confirmDeletion(this,event)'><input type='hidden' value='".$res['id']."' name='delrecid'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='".t("Delete Record")."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";
		$subject = explode("@clickbrk@", $res['sentdata']);
		$getsubj = $subject['0'];

		$listid = $res['listid'];
// echo $listid;
$list="";
if(strpos($listid,'@all@')!==false)
{
	$list="<a href='index.php?page=listrecords'><i>".t("All Lists")."</i></a>";
}
else
{
	$sel = "select id,title from `".$pref."quick_list_records` where id in (".trim(str_replace('@',',',$listid),',').")";
	// echo $sel;
	$listquery=$mysqli->query($sel);
	// print_r($listquery);
  $gotlist=0;
  if($listquery && $listquery->num_rows>0)
  {
    while($lists = $listquery->fetch_assoc())
    {
      ++$gotlist;
      $list .=" <a href='index.php?page=createlist&listid=".$lists['id']."'>".$lists['title']."</a>,";
    }
  }
  else
  {
    $list="Not Found";
  }
  if($gotlist>0)
  {
    $list=rtrim($list,',');
  }

}

$linksvisitstd="";
if(isset($_GET['status']))
{
  if($_GET['status']=="links_visits")
  {
    $linsvisitedbetween=dateBetween('createdon');
    $linksvisitstd="<td>";
    $linksvisitsqry=$mysqli->query("select distinct(`url`) from `".$pref."email_links_visits` where `email_token`='".$res['stoken']."' and `visited`='1'".$linsvisitedbetween[1]);

    while($reslinks=$linksvisitsqry->fetch_object())
    {
      $linksvisitstd.="<a href='".$reslinks->url."' target='_BLANK'>".$reslinks->url."</a>,";
    }
    $linksvisitstd=rtrim($linksvisitstd,",");
    $linksvisitstd .="</td>";
  }
}

        echo "<tr>
          <td>".t($hashcount)."</td>
          <td>".$res['extraemails']."</td>
          ".$linksvisitstd."
          <td><a href='index.php?page=sequence&seqid=".$_GET['seqid']."'>".$getsubj."</a></td>
          <td>".$list."</td>
          <td>".date('d-M-Y h:ia',$res['time'])."</td>
          <td>".$action."</td>
        </tr>";
      }
     ?>
<!-- keyword search -->     
</tbody>
  <?php }
  else{  ?>
<thead>  
<tr>
          <th>#</th>
          <th><?php w("Title"); ?></th>
          <th><?php w("Sent&nbsp;Mails"); ?></th>
          <th><?php w("Total&nbsp;Opens"); ?></th>
          <th><?php w("Total&nbsp;Unopens"); ?></th>
          <th><?php w("Links&nbspVisits"); ?></th>
          <th><?php w("Unsubscribed"); ?></th>
          <th><?php w("Last&nbsp;Sent"); ?></th>
          <th><?php w("Options"); ?></th>
        </tr>
</thead>
<tbody id="keywordsearchresult">
<!-- keyword search -->
<?php
while($row =$resultseq->fetch_assoc())
{
         ++$hashcount;      
         
         $get_sequence_type="&nbsp;(Sequenced)";
         $get_sequence_compose="sequence";
         if($row['sequence']=='compose')
         {
           $get_sequence_type="&nbsp;(Composed)";
           $get_sequence_compose="compose_mail";
         }
                            $action="<table class='actionedittable'><tr><td><a href='index.php?page=".$get_sequence_compose."&seqid=".$row['id']."'><button class='btn unstyled-button' style='' data-bs-toggle='tooltip' title='".t('Edit Sequence')."'><i class='fas fa-edit text-primary'></i></button></a></td><td><form action='' method='post' onsubmit='return confirmDeletion(this,event)'><input type='hidden' value='".$row['id']."' name='deleteres'><button type='submit' class='btn unstyled-button'  data-bs-toggle='tooltip' title='".t("Delete Sequence")."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

                         
                            $totalsent = $row['total_sent'];
                            $totalopen=$row['total_open'];
                            $totalunopen=$row['total_unopen'];
                            $unsents=$row['unsents'];
                            $unsubscribed=$row['unsubscribed'];
                            $lastsenttime="N/A";
                            if(strlen($row['lastsenttime'])>0)
                            {
                            $lastsenttime=date('d-M-Y h:ia',$row['lastsenttime']);
                            }
                            $totallinksvisits=$row['total_linksvisits'];
 


 
      $dateqrystr="";
      $tempdateqrystr=$_GET;
      unset($tempdateqrystr['page']);
      if(count($tempdateqrystr)>0)
      {
        foreach($tempdateqrystr as $tempdateqrystrindex=>$tempdateqrystrdata)
        {
          $dateqrystr .="&".$tempdateqrystrindex."=".$tempdateqrystrdata;
        }
      }
      $totalunsent="";
      $persantage_opened=0;
      $persentage_unopened=0;
      $persentage_linksvisits=0;
      $persentage_unsubscribes=0;
if($totalsent>0)
{
    $persantage_opened=number_format(($totalopen/$totalsent)*100,2);
    $persentage_unopened=number_format(($totalunopen/$totalsent)*100,2);
    $persentage_linksvisits=number_format(($totallinksvisits/$totalsent)*100,2);
    $persentage_unsubscribes=number_format(($unsubscribed/$totalsent)*100,2);
}

    if($unsents>0)
    {
    $totalunsent='(<a href="index.php?page=sentemailsdetails&status=notsent&seqid='.$row['id'].$dateqrystr.'" style="color:#e60073">'.t('Unable&nbsp;to&nbsp;send').':&nbsp;'.t(number_format($unsents)).'</a>)';
    }

        echo"<tr><td>".t($hashcount)."</td><td><a href='index.php?page=sequence&seqid=".$row['id']."'>".$row['title'].$get_sequence_type."</a></td><td><a href='index.php?page=sentemailsdetails&status=sent&seqid=".$row['id'].$dateqrystr."'>".t(number_format($totalsent))."</a> ".$totalunsent."</td><td><a href='index.php?page=sentemailsdetails&status=opened&seqid=".$row['id'].$dateqrystr."'>".t(number_format($totalopen))." <span class='percentage'>".t($persantage_opened)."%</span></a></td><td><a href='index.php?page=sentemailsdetails&status=unopened&seqid=".$row['id'].$dateqrystr."'>".t(number_format($totalunopen))." <span class='percentage'>".t($persentage_unopened)."%</span></a></td><td><a href='index.php?page=sentemailsdetails&status=links_visits&seqid=".$row['id'].$dateqrystr."'>".t(number_format($totallinksvisits))." <span class='percentage'>".t($persentage_linksvisits)."%</span></a></td><td><a href='index.php?page=sentemailsdetails&status=unsubscribe&seqid=".$row['id'].$dateqrystr."'>".t(number_format($unsubscribed))." <span class='percentage'>".t($persentage_unsubscribes)."%</span></a></td><td>".$lastsenttime."</td><td>".$action."</td></tr>";
                           }
                         }

?>
<!-- /keyword search -->
</tbody>
<tbody><tr><td class="total-data" colspan=10><?php w("Total Records"); ?>: <?php $totalqryob=$totalqry->fetch_object(); echo t(number_format($totalqryob->countid)); ?></td></tr></tbody>
</table>
</div>

<div class="col-sm-12 row pt-2">
<div class="col-sm-6 me-auto">
<?php 
$pagecount=0;
if(isset($_GET['page_count']))
{
  $pagecount=$_GET['page_count'];
}
echo createPager($totalqryob->countid,$_SERVER['REQUEST_URI']."&page_count", $pagecount); 
?>
</div>
<div cals="col-sm-6"></div>
</div>


</div></div></div></div></div>
<script>
document.getElementById("commoncontainerid").innerHTML="<?php echo t($headertitle); ?>";
function searchMailsendingHistory(search)
{
var ob=new OnPageSearch(search,"#keywordsearchresult");
ob.url="<?php echo getProtocol();?>";
ob.url +="<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
ob.	search();
}
</script>
