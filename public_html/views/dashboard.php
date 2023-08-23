<?php
$mysqli = $info['mysqli'];
$dbpref = $info['dbpref'];
$load = $data_arr['load'];
$funnel_ob = $load->loadFunnel();
$funnels_data = $funnel_ob->getAllFunnelForView(0, "", 5);
$sales_ob = $load->loadSell();
$sales_data = $sales_ob->visualOptisForSales('all', 0, "", 5);
$user_id = $_SESSION['user' . get_option('site_token')];

if (isset($_POST['saveSelectedDays'])) {
  if (isset($_POST['no_of_days']) && $_POST['no_of_days'] != -1) {
      $no_of_days = $_POST['no_of_days'];
  } else if (isset($_POST['cust_def_days'])) {
      $no_of_days = $_POST['cust_def_days'];
  } else {
      $no_of_days = 7;
  }
  update_option("no_of_days", $no_of_days);
?>
  <script>
      $(function() {
          $("#cf-dashboard-index").focus();
      })
  </script>
<?php
} elseif (get_option("no_of_days") === false) {
  $no_of_days = 7;
} else {
  $no_of_days = get_option("no_of_days");
}

if ($no_of_days == "t") {
  $sfshop_twentynine_minus = strtotime(date('d-M-Y') . "today");

  $sfshop_thirty = "Today";
  $sfshop_twentynine = 0;
  $sfshop_twentynine_minu = $sfshop_twentynine * (-1);
} else if ($no_of_days == "y") {
  $sfshop_twentynine_minus = strtotime("yesterday");
  $sfshop_thirty = "Yesterday";
  $sfshop_twentynine = 1;
  $sfshop_twentynine_minu = $sfshop_twentynine * (-1);
} else {
  $sfshop_thirty = $no_of_days;
  $sfshop_twentynine = $sfshop_thirty - 1;
  $sfshop_twentynine_minu = $sfshop_twentynine * (-1);
  $sfshop_twentynine_minus = strtotime(date('d-M-Y') . $sfshop_twentynine_minu . "days");
}

$members_ob = $load->loadMember();
if($_SESSION['access' . get_option('site_token')]==='admin'){
 $sql_text="select sum(viewcount) as `sumvisits` from `" . $dbpref . "quick_pagefunnel`";
}
else{
  $sql_text = "SELECT sum(`a`.`viewcount`) as `sumvisits`
  FROM `" . $dbpref . "quick_pagefunnel` AS `a`
  LEFT JOIN `" . $dbpref . "quick_pagefunnel` AS `b` ON `a`.`funnelid` = `b`.`funnelid`
  LEFT JOIN `" . $dbpref . "quick_funnels` AS `c` ON `b`.`funnelid` = `c`.`id`
  LEFT JOIN `" . $dbpref . "user_funnel` AS `d` ON `c`.`id` = `d`.`funnel_id`
  WHERE `d`.`user_id` =".$_SESSION['user' . get_option('site_token')] ;

}
$members_data =

  //total visitor
 $viewquery = $mysqli->query($sql_text);

$totalviews = 0;
if ($viewquery) {
  $r = $viewquery->fetch_object();
  if ($r->sumvisits > 0) {
    $totalviews = $r->sumvisits;
  }
}
// print_r($salescount);

if($_SESSION['access' . get_option('site_token')]==='admin'){
$member = "select count(`id`) as `countid` from `" . $dbpref . "quick_member`";
}
else{
  $member = "SELECT count(`a`.`id`) as `countid`
  FROM `" . $dbpref . "quick_member` AS `a`
  LEFT JOIN `" . $dbpref . "quick_pagefunnel` AS `b` ON `a`.`pageid` = `b`.`id`
  LEFT JOIN `" . $dbpref . "quick_funnels` AS `c` ON `b`.`funnelid` = `c`.`id`
  LEFT JOIN `" . $dbpref . "user_funnel` AS `d` ON `c`.`id` = `d`.`funnel_id`
  WHERE `d`.`user_id` =".$_SESSION['user' . get_option('site_token')] ;
}
$query = $mysqli->query($member);
$membercount = 0;
if ($query) {
  $r = $query->fetch_object();
  $membercount = $r->countid;
}

// print_r($membercount);

if($_SESSION['access' . get_option('site_token')]==='admin'){
$sentmails = "select `id` from `" . $dbpref . "quick_subscription_mail_schedule` where status=0 or status=1 or status=2";
}
else{
 $sentmails = "select `id` from `" . $dbpref . "quick_subscription_mail_schedule` where status=0 or status=1 or status=2 and `user_id` =".$_SESSION['user' . get_option('site_token')];
}

$query = $mysqli->query($sentmails);
$sentmailscount = $query->num_rows;

if($_SESSION['access' . get_option('site_token')]==='admin'){
$total_products = "SELECT `id` FROM `" . $dbpref . "all_products`";
}
else{
  $total_products = "SELECT `id` FROM `" . $dbpref . "all_products` WHERE `user_id` =".$_SESSION['user' . get_option('site_token')];
}
$pquery = $mysqli->query($total_products);
$products = $pquery->num_rows;

if(isset($_SESSION['access' . get_option('site_token')]) && $_SESSION['access' . get_option('site_token')]==='admin'){
  $emailists = "SELECT * FROM `". $dbpref . "quick_list_records`";
}
else{
  $emailists = "SELECT * FROM `". $dbpref . "quick_list_records` WHERE `user_id` =".$_SESSION['user' . get_option('site_token')];
}
$equery = $mysqli->query($emailists);
$lists = $equery->num_rows;

if($_SESSION['access' . get_option('site_token')]==='admin'){
$sent = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule` where status in ('1','2','3')";
}
else{
  $sent = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule` where status in ('1','2','3') and `user_id` =".$_SESSION['user' . get_option('site_token')];
}
$query = $mysqli->query($sent);
$sentcount = 0;
if ($query) {
  $r = $query->fetch_object();
  $sentcount = $r->countid;
}

if($_SESSION['access' . get_option('site_token')]==='admin'){
$seen = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule` where status in ('2','3')";
}
else{
  $seen = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule` where status in ('2','3') and `user_id` =".$_SESSION['user' . get_option('site_token')];
}
$query = $mysqli->query($seen);
$seencount = 0;
if ($query) {
  $r = $query->fetch_object();
  $seencount = $r->countid;
}

if($_SESSION['access' . get_option('site_token')]==='admin'){
  $total_sql="select count(distinct(concat(`email`,`funnelid`,`pageid`))) as `countid` from `" . $dbpref . "quick_member` where `email` not in ('', ' ')";
}
else{
  $total_sql = "SELECT count(distinct(concat(`a`.`email`, `a`.`funnelid`, `a`.`pageid`))) as `countid`
  FROM `" . $dbpref . "quick_member` AS `a`
  LEFT JOIN `" . $dbpref . "quick_pagefunnel` AS `b` ON `a`.`pageid` = `b`.`id`
  LEFT JOIN `" . $dbpref . "quick_funnels` AS `c` ON `b`.`funnelid` = `c`.`id`
  LEFT JOIN `" . $dbpref . "user_funnel` AS `d` ON `c`.`id` = `d`.`funnel_id`
  WHERE `d`.`user_id` =".$_SESSION['user' . get_option('site_token')]." and `a`.`email` not in ('', ' ')
  ";
}
$totalmembers_qry = $mysqli->query($total_sql);
$totalmembers = 0;
if ($totalmembers_qry) {
  $tatalmemberob = $totalmembers_qry->fetch_object();
  $totalmembers = $tatalmemberob->countid;
}

/// What is the permission of the user
$sql_text="SELECT * FROM `" . $dbpref . "users` WHERE `id` = " . $user_id;
 $permission_query = $mysqli->query($sql_text);
//$arr=explode(',',$data->permission); if(in_array($_GET['page'],$arr)||in_array('admin',$arr))
$res_permission = $permission_query->fetch_object();
$permission = explode(',', $res_permission->permission);

?>
<style>
  html,
  body,
  #qfnlsaleschart #qfnlsitevisitschart #qfnlsitesequencechart #qfnlmembershipchart {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
  }

  .anychart-credits {
    display: none;
  }
</style>
<div class="container-fluid  no-padding">
  <?php
 // echo "<hr>"; print_r($permission);  echo "<hr>";
  ?>
  <?php if ($funnels_data['total_rows'] > 0) { ?>
    <div class="row">
      <div class="col-sm-12" id="crdcontainer">
        <div class="col-md-12 nopadding ">
          <div class="row">
            <div class="col-md-12 dashboard-nav d-flex">
            
              <ul class="dashboard-topnav">
                <?php if (in_array('create_funnel', $permission) || in_array('admin', $permission)) { ?>
                  <a href="index.php?page=create_funnel">
                  <li><i class="fas fa-funnel-dollar"></i>&nbsp;<?php w('Create&nbsp;Funnel'); ?>1</li>
                </a>
                <?php } ?>
               
                <?php if (in_array('products', $permission) || in_array('admin', $permission)) { ?>
                <a href="index.php?page=products">
                  <li><i class="fas fa-box-open"></i>&nbsp;<?php w('Create&nbsp;Product'); ?></li>
                </a>
                <?php } ?>
                <?php if (in_array('createlist', $permission) || in_array('admin', $permission)) { ?>
                <a href="index.php?page=createlist">
                  <li><i class="fas fa-clipboard-list"></i>&nbsp;<?php w('Create&nbsp;List'); ?></li>
                </a>
                <?php } ?>
                <?php if (in_array('compose_mail', $permission) || in_array('admin', $permission)) { ?>
                <a href="index.php?page=compose_mail">
                  <li><i class="fas fa-paper-plane"></i>&nbsp;<?php w('Send&nbsp;A&nbsp;Mailer'); ?></li>
                </a>
                <?php } ?>
                <?php if (in_array('sales', $permission) || in_array('admin', $permission)) { ?>
                <a href="index.php?page=sales">
                  <li><i class="fas fa-hand-holding-usd"></i>&nbsp;<?php w('See&nbsp;Sales'); ?></li>
                </a>
                <?php } ?>
                <?php if($_SESSION['access' . get_option('site_token')]==='admin'){ ?>
                <a href="index.php?page=multiuser_table">
                  <li><i class="fas fa-users-cog"></i>&nbsp;<?php w('See&nbsp;Users'); ?></li>
                </a>
                <?php } ?>
              </ul>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12 row nopadding">
            <?php if (in_array('all_funnels', $permission) || in_array('admin', $permission)) { ?>    
              <!-- <div class="col-sm-2"></div> -->
              <div class="col-md-3 hovers">
                <a href="index.php?page=all_funnels">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Funnels Count') ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas col-sm fa-funnel-dollar fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($funnels_data['total_rows'])); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('sales', $permission) || in_array('admin', $permission)) { ?>

              <div class="col-md-3 hovers">
                <a href="index.php?page=sales">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Total Sales'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas col-sm fa-hand-holding-usd fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($sales_data['total'])); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('all_funnels', $permission) || in_array('admin', $permission)) { ?>
              <div class="col-md-3 hovers">
                <a href="index.php?page=all_funnels">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Total Visits'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas col-sm fa-eye fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($totalviews)); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('membership_funnels', $permission) || in_array('admin', $permission)) { ?>
              <!-- <div class="col-sm-2"></div> -->
              <div class="col-md-3 hovers">
                <a href="index.php?page=membership_funnels">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Members'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas col-sm fa-users  fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($totalmembers)); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('products', $permission) || in_array('admin', $permission)) { ?>        
              <div class="col-md-3 hovers">
                <a href="index.php?page=products">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Total Products'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas fa-box-open fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($products)); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('sequence_records', $permission) || in_array('admin', $permission)) { ?>
              <div class="col-md-3 hovers">
                <a href="index.php?page=listrecords">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Email Lists'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas fa-clipboard-list fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($lists)); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('sentemailsdetails', $permission) || in_array('admin', $permission)) { ?>
              <div class="col-md-3 hovers">
                <a href="index.php?page=sentemailsdetails">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Sent Mails'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas fa-envelope fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($sentcount)); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
              <?php } ?>
              <?php if (in_array('sentemailsdetails', $permission) || in_array('admin', $permission)) { ?>
              <div class="col-md-3 hovers">
                <a href="index.php?page=sentemailsdetails">
                  <div class="wqmlrdesc blockone ">
                    <p class="subtitle"><?php w('Mails Opened'); ?></p>
                    <div class="row nopadding">
                      <i class="me-auto fas fa-envelope-open-text fa-x2"></i>
                      <h2 class="blocknumber col-sm text-end"><?php w(number_format($seencount)); ?></h2>
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <?php } ?>
            <br>
            <!-- graphes-->

            <?php
             
                        $lastthirtydays = array();
                        $lastthirtydayviews = array();
                        $lastthirtdayconverts = array();
                        $lastthirtydaysmembers = array();
                        $lastthirtdaymails = array();
                        $lastthirtdayopens = array();
                        $lastthirtdayunsubs = array();
                        $lastthirtydaylinksvisits = array();

                        for ($i = 0; $i <= $sfshop_twentynine; $i++) {
                            $temponeofthirtydays = date('d-M-Y', strtotime(date('d-M-Y') . "-" . $i . "days"));
                            $lastthirtydays[$temponeofthirtydays] = 0;
                            $lastthirtydayviews[$temponeofthirtydays] = 0;
                            $lastthirtdayconverts[$temponeofthirtydays] = 0;
                            $lastthirtydaysmembers[$temponeofthirtydays] = 0;
                            $lastthirtdaymails[$temponeofthirtydays] = 0;
                            $lastthirtdayopens[$temponeofthirtydays] = 0;
                            $lastthirtdayunsubs[$temponeofthirtydays] = 0;
                            $lastthirtydaylinksvisits[$temponeofthirtydays] = 0;
                        }
                        //members
                       
                        $hold_mdates = array();
                        $lastthirtymembershipquery = $mysqli->query("select `date_created`, `email`, `funnelid` from `" . $dbpref . "quick_member` where `date_created`>='" . $sfshop_twentynine_minus . "' and email not in('',' ')");
                        if ($lastthirtymembershipquery->num_rows) {
                            while ($r = $lastthirtymembershipquery->fetch_object()) {
                                if (array_search($r->email . '-' . $r->funnelid, $hold_mdates) !== false) {
                                    continue;
                                }

                                ++$lastthirtydaysmembers[date('d-M-Y', $r->date_created)];

                                array_push($hold_mdates, $r->email . '-' . $r->funnelid);
                            }
                        }

                        //visits
                        if($_SESSION['access' . get_option('site_token')]==='admin')
                        { 
                          $sql_text ="select `visitedon` from `" . $dbpref . "site_visit_record`
                           where `visitedon`>='" . $sfshop_twentynine_minus . "' ";
                        }
                        else{
                          $sql_text = "SELECT `a`.`visitedon`
                          FROM `" . $dbpref . "site_visit_record` AS `a`
                          LEFT JOIN `" . $dbpref . "quick_pagefunnel` AS `b` ON `a`.`visit_pageid` = `b`.`id`
                          LEFT JOIN `" . $dbpref . "quick_funnels` AS `c` ON `b`.`funnelid` = `c`.`id`
                          LEFT JOIN `" . $dbpref . "user_funnel` AS `d` ON `c`.`id` = `d`.`funnel_id`
                          WHERE `d`.`user_id` = " . $_SESSION['user' . get_option('site_token')] . "
                          AND `a`.`visitedon` >= '" . $sfshop_twentynine_minus . "'";

                        }
                     //   echo $sql_text;

                        $visitsviewlastthirtyquery = $mysqli->query($sql_text);
                        if ($visitsviewlastthirtyquery->num_rows) {
                            while ($r = $visitsviewlastthirtyquery->fetch_object()) {
                                ++$lastthirtydayviews[date('d-M-Y', $r->visitedon)];
                            }
                        }  
                        //convertedon
                        if($_SESSION['access' . get_option('site_token')]==='admin')
                        { 
                          $sql_text = "select `convertedon` from `" . $dbpref . "site_visit_record` where `convertedon`>='" . $sfshop_twentynine_minus . "' and `convert_optinid` not in('0')";
                        }
                        else{
                          $sql_text = "SELECT `a`.`convertedon`
                          FROM `" . $dbpref . "site_visit_record` AS `a`
                          LEFT JOIN `" . $dbpref . "quick_pagefunnel` AS `b` ON `a`.`visit_pageid` = `b`.`id`
                          LEFT JOIN `" . $dbpref . "quick_funnels` AS `c` ON `b`.`funnelid` = `c`.`id`
                          LEFT JOIN `" . $dbpref . "user_funnel` AS `d` ON `c`.`id` = `d`.`funnel_id`
                          WHERE `d`.`user_id` = " . $_SESSION['user' . get_option('site_token')] . "
                          AND `a`.`convertedon` >= '" . $sfshop_twentynine_minus . "'
                          AND `a`.`convert_optinid` NOT IN ('0')";
                        }
                       // echo $sql_text;
                        $convertlastthirtyquery = $mysqli->query($sql_text);
                        if ($convertlastthirtyquery->num_rows) {
                            while ($r = $convertlastthirtyquery->fetch_object()) {
                                ++$lastthirtdayconverts[date('d-M-Y', $r->convertedon)];
                            }
                        }
                        //sales
                        $saleslastthirtyquery = $mysqli->query("select `addedon` from `" . $dbpref . "all_sales` where `addedon`>='" . $sfshop_twentynine_minus . "'");

                        if ($saleslastthirtyquery->num_rows) {
                            while ($r = $saleslastthirtyquery->fetch_object()) {
                                ++$lastthirtydays[date('d-M-Y', $r->addedon)];
                            }
                        }


                        //sent mails
                        $totalsentlastthirtyquery = $mysqli->query("select `time` from `" . $dbpref . "quick_subscription_mail_schedule` where `time`>='" . $sfshop_twentynine_minus . "' and `status` in ('1','2','3')");
                        // print_r($totalsentlastthirtyquery);
                        if ($totalsentlastthirtyquery->num_rows) {
                            while ($r = $totalsentlastthirtyquery->fetch_object()) {
                                ++$lastthirtdaymails[date('d-M-Y', $r->time)];
                            }
                        }


                        //opened
                        $openedlastthirtyquery = $mysqli->query("select `time` from `" . $dbpref . "quick_subscription_mail_schedule` where `time`>='" . $sfshop_twentynine_minus . "' and `status` in ('2','3')");

                        if ($openedlastthirtyquery->num_rows) {
                            while ($r = $openedlastthirtyquery->fetch_object()) {
                                ++$lastthirtdayopens[date('d-M-Y', $r->time)];
                            }
                        }

                        //unsubs
                        $unsubslastthirtyquery = $mysqli->query("select `time` from `" . $dbpref . "quick_subscription_mail_schedule` where `time`>='" . $sfshop_twentynine_minus . "' and `status` in ('3')");
                        if ($unsubslastthirtyquery->num_rows) {
                            while ($r = $unsubslastthirtyquery->fetch_object()) {
                                ++$lastthirtdayunsubs[date('d-M-Y', $r->time)];
                            }
                        }
                        //links visits
                        $linksvisitsthirtyquery = $mysqli->query("select `createdon` from `" . $dbpref . "email_links_visits` where `visited`='1' and `createdon`>='" . $sfshop_twentynine_minus . "'");
                        if ($linksvisitsthirtyquery->num_rows) {
                            while ($r = $linksvisitsthirtyquery->fetch_object()) {
                                //$lastthirtydaylinksvisits
                                ++$lastthirtydaylinksvisits[date('d-M-Y', $r->createdon)];
                            }
                        }


                        $saleslastthirtydatearr = array();
                        $saleslastthirtysalesarr = array();
                        $lastthirtydays = array_reverse($lastthirtydays);
                        foreach ($lastthirtydays as $lastthirtydaysindex => $lastthirtydaysdata) {
                            array_push($saleslastthirtydatearr, "'" . date('d-M', strtotime($lastthirtydaysindex)) . "'");
                            array_push($saleslastthirtysalesarr, $lastthirtydaysdata);
                        }


                        $lastthirtydayviews = array_reverse($lastthirtydayviews);
                        $viewslastthirtydatearr = array();
                        $viewslastthirtyviewsarr = array();
                        foreach ($lastthirtydayviews as $lastthirtydayviewsindex => $lastthirtydayviewsdata) {
                            array_push($viewslastthirtydatearr, "'" . date('d-M', strtotime($lastthirtydayviewsindex)) . "'");
                            array_push($viewslastthirtyviewsarr, $lastthirtydayviewsdata);
                        }
                        $lastthirtdayconverts = array_reverse($lastthirtdayconverts);
                        $convertslastthirtydatearr = array();
                        $convertslastthirtyconvertsarr = array();
                        foreach ($lastthirtdayconverts as $lastthirtydayconvertsindex => $lastthirtydayconvertsdata) {
                            array_push($convertslastthirtydatearr, "'" . date('d-M', strtotime($lastthirtydayconvertsindex)) . "'");
                            array_push($convertslastthirtyconvertsarr, $lastthirtydayconvertsdata);
                        }

                        $lastthirtydaysmembers = array_reverse($lastthirtydaysmembers);
                        $lastthirtydaysmembersdatearr = array();
                        $lastthirtydaysmemberscountarr = array();

                        foreach ($lastthirtydaysmembers as $lastthirtydaysmembersindex => $lastthirtydaysmembersvalue) {
                            array_push($lastthirtydaysmembersdatearr, "'" . date('d-M', strtotime($lastthirtydaysmembersindex)) . "'");
                            array_push($lastthirtydaysmemberscountarr, $lastthirtydaysmembersvalue);
                        }

                        $lastthirtdaymails = array_reverse($lastthirtdaymails);
                        $sentmailslastthirtydatearr = array();
                        $totalmailslastthirtytotalarr = array();
                        foreach ($lastthirtdaymails as $lastthirtydaytotalindex => $lastthirtydaytotalsentdata) {
                            array_push($sentmailslastthirtydatearr, "'" . date('d-M', strtotime($lastthirtydaytotalindex)) . "'");
                            array_push($totalmailslastthirtytotalarr, $lastthirtydaytotalsentdata);
                        }
                        //print_r($totalmailslastthirtytotalarr);
                        // print_r(implode(',', $totalmailslastthirtytotalarr));

                        $lastthirtdayopens = array_reverse($lastthirtdayopens);
                        $openslastthirtydatearr = array();
                        $opensmailslastthirtyopensarr = array();
                        foreach ($lastthirtdayopens as $lastthirtydayopensindex => $lastthirtydayopensdata) {
                            array_push($openslastthirtydatearr, "'" . date('d-M', strtotime($lastthirtydayopensindex)) . "'");
                            array_push($opensmailslastthirtyopensarr, $lastthirtydayopensdata);
                        }


                        $lastthirtdayunsubs = array_reverse($lastthirtdayunsubs);
                        $unsubslastthirtydatearr = array();
                        $unsubsmailslastthirtyunsubsarr = array();
                        foreach ($lastthirtdayunsubs as $lastthirtydayunsubsindex => $lastthirtydayunsubsdata) {
                            array_push($unsubslastthirtydatearr, "'" . date('d-M', strtotime($lastthirtydayunsubsindex)) . "'");
                            array_push($unsubsmailslastthirtyunsubsarr, $lastthirtydayunsubsdata);
                        }

                        $lastthirtydaylinksvisits = array_reverse($lastthirtydaylinksvisits);
                        $linksvisirsdatesarr = array();
                        $linksvisitsthirdaysarr = array();
                        foreach ($lastthirtydaylinksvisits as $lastthirtydaylinksvisitsindex => $lastthirtydaylinksvisitsval) {
                            array_push($linksvisirsdatesarr, date('d-M', strtotime($lastthirtydaylinksvisitsindex)));
                            array_push($linksvisitsthirdaysarr, $lastthirtydaylinksvisitsval);
                        }

                        //print_r($viewslastthirtydatearr);
                      
                        ?>

            <div class="col-md-12">
              
                <div class="row pb-3 my-3">
                  <div class="col-sm-8">
                      <h4 class="text-black op-6"><?php
                          if ($sfshop_thirty == "Today" || $sfshop_thirty == "Yesterday") {
                              echo t('Showing record for ${1}', array(lcfirst(t($sfshop_thirty))));
                          } else {
                              echo t("Showing record for last \${1} day(s)", array($sfshop_thirty));
                          }
                          ?>
                        </h4>
                  </div>
                  
                  <div class="col-sm-4">
                      <div class="dropdown">
                          <button style="background-color: #1f57ca;color:white" class="btn setup-button dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fas fa-calendar-alt"></i>&nbsp;<?= t('Select Days'); ?>
                          </button>
                          <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                              <div class="container">
                                  <div class="row">
                                      <div class="col-sm-12">
                                          <form class="daysSelection" action="" method="POST">
                                              <label class="form-control"><input type="radio" name="no_of_days" value="t">&nbsp;<?= t('Today'); ?></label>
                                              <label class="form-control"><input type="radio" name="no_of_days" value="y">&nbsp;<?= t('Yesterday'); ?></label>
                                              <label class="form-control"><input type="radio" name="no_of_days" value="7">&nbsp;<?= t('Last 7 days'); ?></label>
                                              <label class="form-control"><input type="radio" name="no_of_days" value="15">&nbsp;<?= t('Last 15 days'); ?></label>
                                              <label class="form-control"><input type="radio" name="no_of_days" value="30">&nbsp;<?= t('Last 30 days'); ?></label>
                                              <label class="form-control">
                                                  <input type="radio" name="no_of_days" id="custom-days" value="-1">&nbsp; <?= t('Enter custom days'); ?>
                                              </label>
                                              <input class="form-control cust-no_of_days" id="cf-custom-days" type="number" placeholder="<?= t('Enter days'); ?>" min="0" value="0" name="cust_def_days" style="display: none;">
                                              <button name="saveSelectedDays" class="btn btn-warning btn-block mt-2 theme-button"><i class="fas fa-check"></i>&nbsp;<?= t('Save'); ?></button>
                                          </form>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
       
                </div>
              <div class="graph-class  justify-content-center  ">
                <div class="row justify-content-center">
                <?php if (in_array('sales', $permission) || in_array('admin', $permission)) { ?>
                  <div class="col-md-6">
                    <div class="card pnl">
                      <div class="card-header">
                      <?php
                                                        if ($sfshop_thirty == "Today" || $sfshop_thirty == "Yesterday") {
                                                            echo t('${1} Sales', array(t($sfshop_thirty)));
                                                        } else {
                                                            echo t("\${1} Day's Sales", array($sfshop_thirty));
                                                        }
                                                        ?>
                      </div>
                      <div class="card-body qfnldashboardgraph" id="qfnlsaleschart" style="height: 350px;"></div>
                    </div>
                   </div>
               <?php } ?>
               <?php if (in_array('all_funnels', $permission) || in_array('admin', $permission)) { ?>
                  <div class="col-md-6 ">
                    <div class="card pnl ">
                      <div class="card-header"><?php
                                                                        if ($sfshop_thirty == "Today" || $sfshop_thirty == "Yesterday") {
                                                                            echo t("\${1} Website Visits & Converted Visitors", array(t($sfshop_thirty)));
                                                                        } else {
                                                                            echo t("\${1} Day's Website Visits & Converted Visitors", array($sfshop_thirty));
                                                                        } ?></div>
                      <div class="card-body qfnldashboardgraph" id="qfnlsitevisitschart" style="height: 350px;"></div>
                    </div>
                  </div>
                <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="row  justify-content-center ">
        <?php if (in_array('membership_funnels', $permission) || in_array('admin', $permission)) { ?>
          <div class="col-md-6">
            <div class="card pnl">
              <div class="card-header"><?php
                  if ($sfshop_thirty == "Today" || $sfshop_thirty == "Yesterday") {
                      echo t('${1} Mail Sending Report', array(t($sfshop_thirty)));
                  } else {
                      echo t("\${1} Day's Mail Sending Report", array($sfshop_thirty));
                  }
                  ?>
                </div>
              <div class="card-body qfnldashboardgraph" id="qfnlsitesequencechart" style="height: 350px;"></div>
            </div>
          </div>
          <?php } ?>
          <?php if (in_array('membership_funnels', $permission) || in_array('admin', $permission)) { ?>        
          <div class="col-md-6">
            <div class="card pnl">
              <div class="card-header"><?php
                  if ($sfshop_thirty == "Today" || $sfshop_thirty == "Yesterday") {
                      echo t('${1} Membership Report', array(t($sfshop_thirty)));
                  } else {
                      echo t("\${1} Day's Membership Report", array($sfshop_thirty));
                  } ?></div>
              <div class="card-body qfnldashboardgraph" id="qfnlmembershipchart" style="height: 350px;"></div>
            </div>
          </div>
          <?php } ?>
        </div>

        <script>
          var saleslastthirtysalesarr = "<?php echo implode(',', $saleslastthirtysalesarr); ?>";
          var saleslastthirtydatearr = "<?php echo implode(',', $saleslastthirtydatearr); ?>";
          create_new_Charts(saleslastthirtysalesarr, saleslastthirtydatearr, 'qfnlsaleschart');

          //visits and converted chart
          var viewslastthirtydatearr = "<?php echo implode(',', $viewslastthirtydatearr); ?>";
          var viewslastthirtyviewsarr = "<?php echo implode(',', $viewslastthirtyviewsarr); ?>";
          var convertslastthirtyconvertsarr = "<?php echo implode(',', $convertslastthirtyconvertsarr); ?>";

          create_new_Charts_website_visits(viewslastthirtydatearr, viewslastthirtyviewsarr, convertslastthirtyconvertsarr, 'qfnlsitevisitschart');

          //Last 30days emails
          var sentmailslastthirtydatearr = "<?php echo implode(',', $sentmailslastthirtydatearr); ?>";
          var totalmailslastthirtytotalarr = "<?php echo implode(',', $totalmailslastthirtytotalarr); ?>";
          var opensmailslastthirtyopensarr = "<?php echo implode(',', $opensmailslastthirtyopensarr); ?>";
          var linksvisitsthirdaysarr = "<?php echo implode(',', $linksvisitsthirdaysarr); ?>";
          var unsubsmailslastthirtyunsubsarr = "<?php echo implode(',', $unsubsmailslastthirtyunsubsarr); ?>";
          create_new_mail(sentmailslastthirtydatearr, totalmailslastthirtytotalarr, opensmailslastthirtyopensarr, linksvisitsthirdaysarr, unsubsmailslastthirtyunsubsarr, 'qfnlsitesequencechart');


          //membership chart
          var lastthirtydaysmembersdatearr = "<?php echo implode(',', $lastthirtydaysmembersdatearr); ?>";
          var lastthirtydaysmemberscountarr = "<?php echo implode(',', $lastthirtydaysmemberscountarr); ?>";

          create_new_membership(lastthirtydaysmembersdatearr, lastthirtydaysmemberscountarr, 'qfnlmembershipchart');
        </script>

        <!-- Sales Table -->
        <?php if (in_array('sales', $permission) || in_array('admin', $permission)) { ?>
        <div class="row justify-content-center">
          <div class="col-md-12 col-sm-11 sortable">
            <div class="card pnl">
              <div class="card-header"><?php w('Latest Sales'); ?></div>
              <div class="card-body">
                <div class="table-responsive">

                  <table class="table table-striped">
                    <thead>                      
                      <th>#</th>
                      <th><?php w('Payment&nbsp;Id') ?></th>
                      <th><?php w('Purchase&nbsp;Name'); ?></th>
                      <th><?php w('Purchase&nbsp;Email') ?></th>                                            
                      <th><?php w('Date'); ?></th>
                      <th><?php w('Action'); ?></th>
                    </thead>
                    <tbody id="srchmember">
                      <!--srch-->
                      <?php
                      $hashcount = 0;

                      while ($r = $sales_data['sales']->fetch_object()) {
                        ++$hashcount;

                        $shippedclass = "btn-warning";
                        if ($r->shipped == "1") {
                          $shippedclass = "btn-success";
                        }
                        $date = date('d-M-Y', $r->addedon);

                        $product = "";

                        $productdata = $sales_ob->getProduct($r->productid);

                        if ($productdata) {
                          $product = "(#" . $productdata->productid . ") " . $productdata->title . "";

                          $product = "<a href='index.php?page=products&product_id=" . $r->productid . "' target='_BLANK'>" . $product . "</a>";
                        }

                        $parent_product = "N/A";
                        $mysqli = $info['mysqli'];
                        $dbpref = $info['dbpref'];
                        $checkotherproducts_query = $mysqli->query("select `id`,`productid`,`title` from `" . $dbpref . "all_products` where id in(select `productid` from `" . $dbpref . "all_sales` where `parent` in('" . $r->productid . "') and `payment_id`='" . $r->payment_id . "')");

                        if ($checkotherproducts_query->num_rows > 0) {
                          $parent_product = "";
                          while ($tempr = $checkotherproducts_query->fetch_object()) {
                            $parent_product .= "<a href='index.php?page=products&product_id=" . $tempr->id . "'>(#" . $tempr->productid . ") " . $tempr->title . "</a> ,";
                          }
                          $parent_product = rtrim($parent_product, " ,");
                        }


                        echo "<tr><td>" . t($hashcount) . "</td><td>" .$r->payment_id . "</td><td>" . $r->purchase_name . "</td><td>" . $r->purchase_email . "</td><td>" . $date . "</td><td onclick='viewPurchaseDetail(".$r->id.")' style='cursor:pointer;'><i class='fas fa-eye' style='margin-right:4px;'></i></td></tr>";
                      }
                      
                      ?>
                      <!--/srch-->
                    </tbody>

                  </table>
                </div>
                <div class="text-end"><a href="index.php?page=sales"><button class="btn theme-button mt-2"><?php w('All Sales'); ?></button></a></div>
              </div>
            </div>
          </div>
        </div>
        <?php } 
       
        ?>
        <?php if (in_array('sequence', $permission) || in_array('admin', $permission)) { ?>
                <!-- Sequence Table -->
                <div class="row justify-content-center">
          <div class="col-md-12 col-sm-11">
            <div class="card pnl">
              <div class="card-header"><?php w('Latest email campaigns'); ?></div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr class="">
                        <th>#</th>
                        <th><?php w('Title'); ?></th>
                        <th><?php w('Total&nbsp;Mails&nbsp;Sent'); ?></th>
                        <th><?php w('Total&nbsp;Opened'); ?></th>
                        <th><?php w('Total&nbsp;Unopens'); ?></th>
                        <th><?php w('Links&nbsp;Visits'); ?></th>
                        <th><?php w('Unsubscribes') ?></th>
                        <th><?php w('Last&nbsp;Sent'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $table = $dbpref . "quick_sequence";
                      $sql = "select `id`,`title` from `" . $table . "` where `sequence` not in ('compose') and `id` in (select `seqid` from `" . $dbpref . "quick_subscription_mail_schedule` order by time desc)limit 5";
                      $resultseq = $mysqli->query($sql);
                      $count = 0;
                      while ($row = $resultseq->fetch_assoc()) {
                        ++$count;
                        $sql3 = "select count(`id`) from `" . $dbpref . "quick_subscription_mail_schedule`  where seqid='" . $row['id'] . "' and status in('1','2','3')";
                        // echo $sql3;

                        $result = $mysqli->query($sql3);
                        // print_r($result);
                        $totalsent = 0;
                        while ($total = $result->fetch_assoc()) {
                          $totalsent = $total['count(`id`)'];
                        }


                        $sql2 = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule`  where seqid='" . $row['id'] . "' and status='2' ";
                        //echo $sql2;

                        $res = $mysqli->query($sql2);
                        //print_r($res);
                        $totalopen = 0;
                        if ($r = $res->fetch_assoc()) {
                          $totalopen = $r['countid'];
                        }

                        $sql5 = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule`  where seqid='" . $row['id'] . "' and status='3' ";
                        //echo $sql2;

                        $res = $mysqli->query($sql5);
                        //print_r($res);
                        $totalunsubscribe = 0;
                        if ($r = $res->fetch_assoc()) {
                          $totalunsubscribe = $r['countid'];
                        }

                        $query = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule`  where seqid='" . $row['id'] . "' and status=1 ";
                        $resul = $mysqli->query($query);
                        // print_r($res);
                        $totalunopen = 0;
                        if ($r = $resul->fetch_assoc()) {
                          $totalunopen = $r['countid'];
                        }

                        $query = $mysqli->query("select count(`id`) as `countvisits` from `" . $dbpref . "email_links_visits` where `sequence_id`='" . $row['id'] . "' and `visited`='1'");

                        $totllinksvisits = 0;
                        if ($r = $query->fetch_object()) {
                          $totllinksvisits = $r->countvisits;
                        }

                        $lastmailsentquery = $mysqli->query("select max(`time`) `maxtime` from `" . $dbpref . "quick_subscription_mail_schedule` where status not in('0','-1') and time not in('0') and `seqid`='" . $row['id'] . "'");

                        $lastsent = "N/A";
                        if ($r = $lastmailsentquery->fetch_object()) {
                          $lastsent = date('d-M-Y h:ia', $r->maxtime);
                        }
                        $query = "select count(`id`) as `countid` from `" . $dbpref . "quick_subscription_mail_schedule`  where `seqid`='" . $row['id'] . "' and `status`='0'";
                        //echo $query;
                        $unsentresultt = $mysqli->query($query);
                        $unsents = 0;
                        while ($unsents_ob = $unsentresultt->fetch_assoc()) {
                          $unsents = $unsents_ob['countid'];
                        }
                        $totalunsent = "";
                        $persantage_opened = 0;
                        $persentage_unopened = 0;
                        $persentage_linksvisits = 0;
                        $persentage_unsubscribes = 0;
                        if ($totalsent > 0) {
                          $persantage_opened = number_format(($totalopen / $totalsent) * 100, 2);
                          $persentage_unopened = number_format(($totalunopen / $totalsent) * 100, 2);
                          $persentage_linksvisits = number_format(($totllinksvisits / $totalsent) * 100, 2);
                          $persentage_unsubscribes = number_format(($totalunsubscribe / $totalsent) * 100, 2);
                        }

                        if ($unsents > 0) {
                          $totalunsent = '(<a href="index.php?page=sentemailsdetails&status=notsent&seqid=' . $row['id'] . '" style="color:#e60073">' . t('Unable&nbsp;to&nbsp;send:&nbsp;') . '' . t(number_format($unsents)) . '</a>)';
                        }

                        echo "<tr><td>" . t($count) . "</td><td><a href='index.php?page=sequence&seqid=" . $row['id'] . "'>" . $row['title'] . "</a></td><td><a href='index.php?page=sentemailsdetails&status=sent&seqid=" . $row['id'] . "'>" . t(number_format($totalsent)) . "</a> " . $totalunsent . "</td><td><a href='index.php?page=sentemailsdetails&status=opened&seqid=" . $row['id'] . "'>" . t(number_format($totalopen)) . " (" . $persantage_opened . "%)</a></td><td><a href='index.php?page=sentemailsdetails&status=unopened&seqid=" . $row['id'] . "'>" . t(number_format($totalunopen)) . " (" . t($persentage_unopened) . "%)</a></td><td><a href='index.php?page=sentemailsdetails&status=links_visits&seqid=" . $row['id'] . "'>" . t(number_format($totllinksvisits)) . " (" . t($persentage_linksvisits) . "%)</a></td><td><a href='index.php?page=sentemailsdetails&status=unsubscribe&seqid=" . $row['id'] . "'>" . t(number_format($totalunsubscribe)) . " (" . t($persentage_unsubscribes) . "%)</a></td>
				<td>" . $lastsent . "</td></tr>";
                      }


                      ?>

                    </tbody>
                  </table>
                </div>

                <div class="text-end"><a href="index.php?page=sentemailsdetails"><button class="btn theme-button mt-2"><?php w('All campaigns'); ?></button></a></div>

              </div>
            </div>
          </div>

        </div>
        <?php } 
            ?>
        <?php if (in_array('all_funnels', $permission) || in_array('admin', $permission)) { ?>
        <!-- Members Table -->
        <div class="row justify-content-center">
          <div class="col-md-12 col-sm-11">
            <div class="card pnl">
              <div class="card-header"><?php w('Your Funnels'); ?></div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <th>#</th>
                      <th><?php w('Project'); ?></th>
                      <th><?php w('Type'); ?></th>
                      <th><?php w('Pages'); ?></th>
                      <th><?php w('Visits'); ?></th>
                      <th><?php w('Converted'); ?></th>
                      <th><?php w('Bounces'); ?></th>
                      <th><?php w('Created&nbsp;On') ?></th>
                    </thead>
                    <tbody>
                      <?php
                      $hashcount = 0;
                      if ($funnels_data['rows']) {
                        while ($r = $funnels_data['rows']->fetch_object()) {
                          ++$hashcount;

                          //$funneldata=$funnel_ob->getFunnel($r->funnel_id,$select="`name`,`baseurl`");
                          $sumdata = $funnel_ob->totalSumCountsFunelPages($r->funnel_id);
                          $action = "<table class='actionedittable'><tr><td><a href='index.php?page=create_funnel&id=" . $r->funnel_id . "'><button class='btn btn-info'><i class='fas fa-pen-fancy'></i></button></a></td><td><a href='index.php?page=optins&funnelid=" . $r->funnel_id . "'><button class='btn btn-success'><i class='fas fa-users'></i></button></a></td><td><form action='' method='post'><input type='hidden' name='delfunnel' value='" . $r->funnel_id . "'><button type='submit' class='btn btn-danger'><i class='fas fa-trash'></i></button></form></td></tr></table>";

                          $bounces = $r->ab_viewcount - $r->ab_convertcount;

                          $converted_percentage = 0;
                          $bounces_percentage = 0;
                          if ($bounces < 0) {
                            $bounces = 0;
                          }
                          if ($r->ab_viewcount > 0) {
                            $converted_percentage = number_format((($r->ab_convertcount / $r->ab_viewcount) * 100), 2);

                            $bounces_percentage = number_format((($bounces / $r->ab_viewcount) * 100), 2);
                          }

                          $display_funnel_type = $r->funnel_type;
                          if ($display_funnel_type == "blank") {
                            $display_funnel_type = "Custom";
                          }
                          $display_funnel_type = ucfirst($display_funnel_type);
                          echo "<tr><td>" . t($hashcount) . "</td><td><a href='" . $r->funnel_baseurl . "' target='_BLANK'>" . $r->funnel_name . "</a></td><td>" . t($display_funnel_type) . "</td><td>" . t(number_format($r->sumpages)) . "</td><td>" . t(number_format($r->ab_viewcount)) . "</td><td class=''>" . t(number_format($r->ab_convertcount)) . " <span class='percentage'>" . t($converted_percentage) . "%</span></td><td class=''>" . t(number_format($bounces)) . " <span class='percentage'>" . t($bounces_percentage) . "%</span></td><td>" . date('d-M-Y', $r->funnelcreatedon) . "</td></tr>";
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <div class="text-end"><a href="index.php?page=all_funnels"><button class="btn theme-button mt-2"><?php w('All Funnels') ?></button></a></div>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>


      </div>
    </div>
  <?php } else { ?>
    <div class="row ">
      <div class="col-lg-5 dash-init mx-auto align-self-center">
        <div class="card">
          <div class="card-header bg-white theme-text"><?php w('Welcome To CloudFunnels'); ?></div>
          <div class="card-body text-center">

            <div class="p-3">
              <h3 class="theme-text card-text"><?php w('Start off something awesome'); ?></h3>
            </div>
            <div class="p-3"> <a href="index.php?page=create_funnel" class="btn btn-app theme-button" role="button"><i class="fas fa-funnel-dollar"></i><?php w('Create a funnel now'); ?></a></div>
          </div>
        </div>
      </div>
    </div>
    <!--<div class="row">
    <div class="col-sm-12 dash-init">
        <center>
            <img src="assets/img/logo-text.png" alt="CloudFunnels Logo">
            <h3 class="theme-text dashboaed-init-text">Start off something awesome</h3>
            <a href="index.php?page=create_funnel"><button class="btn btn-primary theme-button"><i class="fas fa-funnel-dollar"></i>&nbsp;&nbsp;Create a funnel now</button></a>
        </center>
    
    </div>
</div>-->

  <?php } ?>
</div>
<script>
    function dropdown_days() {
        document.getElementById("set_my_days").submit();
    }
    $(function() {

        $("#custom-days").on("click", function() {
            $("#cf-custom-days").toggle();
        });
    });
</script>
<script>
  var globdiv;

  function viewPurchaseDetail(id) {
    var request = new ajaxRequest();
    var div = document.createElement("div");
    div.setAttribute('class', 'row');
    var container = "<div class='col-sm-6' style='top:60%;left:60%;position:fixed;transform:translate(-50%,-50%);z-index:9999999;'><div class='card pnl visual-pnl'><div class='card-header'>Detail <i class='fas fa-times closethidiv' style='right:20px;top:20px;position:absolute;cursor:pointer;'></i></div><div class='card-body purchasedetailqmlr' style='max-height:400px;overflow-y:auto;'>" + t("Loading...") + "</div></div></div>";
    div.innerHTML = container;
    //alert(div);
    var maindiv = document.getElementById("crdcontainer");
    try {
      maindiv.removeChild(globdiv);
    } catch (err) {
      console.log(err.message);
    }
    globdiv = div;
    maindiv.appendChild(div);
    document.getElementsByClassName("closethidiv")[0].onclick = function() {
      maindiv.removeChild(div);
    };
    request.postRequestCb('req.php', {
      "viewpurchasedetail": id
    }, function(data) {
      var arr = data.trim();
      if (arr.length > 2) {
        arr = arr.split('@sbreak@');
        arr[0] = arr[0].replace(/&quot;/g, '"');
        arr[0] = arr[0].replace(/(?:\r\n|\r|\n)/g, '');
        var shipping = JSON.parse(arr[0]);
        try {
          var shippingtable = "<div class='table-responsive'><table class='table table-striped'><thead><tr><th colspan=2>" + t('Shipping Detail') + "</th></tr></thead><tbody>";
          for (var i in shipping) {
            shippingtable += "<tr><td>" + i + "</td><td>" + shipping[i] + "</td></tr>";
          }
        } catch (err) {
          console.log(err.message);
        }

        var steppayment = arr[3];
        shippingtable = steppayment + shippingtable;

        shippingtable += "</tbody><thead ><th colspan=2>Payment Method</th></thead><tbody>";
        try {
          shippingtable += "<tr><td colspan=2>" + arr[1] + "</td></tr>";
        } catch (err) {
          console.log(err.message);
        }
        shippingtable += "</tbody></table></div>";

      }
      document.getElementsByClassName("purchasedetailqmlr")[0].innerHTML = shippingtable;
    });
  }
  authPurchaseData();
</script>
