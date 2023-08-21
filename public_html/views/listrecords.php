<?php
// print_r($info);
$mysqli = $info['mysqli'];
$pref = $info['dbpref'];
if (isset($_POST['delrecid'])) {
  $id = $_POST['delrecid'];
  $delete = "delete from `" . $pref . "quick_list_records` where id=" . $id;
  $mysqli->query($delete);
}
$start_from = 0;
$user_id=$_SESSION['user' . get_option('site_token')];
if($_SESSION['access' . get_option('site_token')]==='admin'){
  $add_user='';
}
else{
  $add_user=" AND `a`.`user_id` = ".$user_id;
}

if (isset($_GET['pagecount'])) {
  $start_from = ($_GET['pagecount'] * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');
}
$hashcount = $start_from;
$timelimit_condition = 1;
 $subscribercount_query = "(select count(`id`) from `" . $pref . "quick_email_lists` where `listid`=`a`.id ".$add_user." ) as `subscribers_count`";
if (isset($_POST['onpage_search']) && strlen($_POST['onpage_search'])) {
  $search_keywords = $mysqli->real_escape_string($_POST['onpage_search']);

  $query = "SELECT `a`.*," . $subscribercount_query . " FROM `" . $pref . "quick_list_records` as `a` where `a`.title like '%" . $search_keywords . "%' ".$add_user." or a.`id` in (select distinct(`listid`) from `" . $pref . "quick_email_lists` where `name` like '%" . $search_keywords . "%' or `email` like '%" . $search_keywords . "%' or `ipaddr` like '%" . $search_keywords . "%' or `exf` like '%:\"%" . $search_keywords . "%\"%')";
} else {
  $date_between = dateBetween('creationdate');
  if (strlen($date_between[0]) > 1) {
    $timelimit_condition = $date_between[0];
  }
  $order_by = "`a`.id desc";
  if (isset($_GET['arrange_records_order'])) {
    $order_by = base64_decode($_GET['arrange_records_order']);
  }

  $query = "SELECT `a`.*," . $subscribercount_query . " FROM `" . $pref . "quick_list_records` as `a` where " . $timelimit_condition . " ".$add_user." order by " . $order_by . " LIMIT " . $start_from . ", " . get_option('qfnl_max_records_per_page') . "";
}
//echo $query;
$result = $mysqli->query($query);
 $sql_text="select count(`id`) as `countid` from `" . $pref . "quick_list_records` where " . $timelimit_condition . "";
$totalpage_query = $mysqli->query($sql_text);
$total_ob = $totalpage_query->fetch_object();
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
          <?php echo arranger(array('`a`.id' => 'date', 'subscribers_count' => 'Number Of Subscribers')); ?>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend ">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" class="form-control form-control-sm" placeholder="Enter List Title Or Subscriber Name, Email, IP or Other Fields " onkeyup="searchList(this.value)">
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12 nopadding">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>List Name</th>
                <th>Subscribers Count</th>
                <th>Date Created</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody id="keywordsearchresult">
              <!-- keyword search -->
              <?php
              $hashcount = 0;
              while ($res = $result->fetch_assoc()) {

                $times_the_list_used = $mysqli->query("select count(distinct(`funnelid`)) as `countid` from `" . $pref . "quick_pagefunnel` where `lists` like '" . $res['id'] . "@%' or `lists` like '%@" . $res['id'] . "@%' or `lists` like '%@" . $res['id'] . "'");

                $times_the_list_used_count = 0;
                if ($times_the_list_used_res = $times_the_list_used->fetch_object()) {
                  $times_the_list_used_count = $times_the_list_used_res->countid;
                }
                ++$hashcount;
                $num = $result->num_rows;

                // $subscount = "select id from `" . $pref . "quick_email_lists` where listid=" . $res['id'];
                //$subscnt = $mysqli->query($subscount);
                $subsnum = $res['subscribers_count'];

                if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2) {
                  $action = "<table class='actionedittable'><tr><td><a href='index.php?page=createlist&listid=" . $res['id'] . "'><button class='btn unstyled-button' style='' data-bs-toggle='tooltip' title='" . t('Edit List') . "'><i class='fas fa-edit text-primary'></i></button></a></td><td><form action='index.php?page=export_csv&type=list' method='post'><button type='button' class='btn  unstyled-button' data-bs-toggle='modal' data-bs-target='#upgradeFromFreeModal' name='listid' title='" . t('Download As CSV') . "'><i class='fas fa-download text-success'></i></button></form></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(" . $times_the_list_used_count . ",'list')\"><button type='submit' class='btn unstyled-button' value='" . $res['id'] . "' name='delrecid' data-bs-toggle='tooltip' title='" . t('Delete List') . "'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";
                } else {
                  $action = "<table class='actionedittable'><tr><td><a href='index.php?page=createlist&listid=" . $res['id'] . "'><button class='btn unstyled-button' style='' data-bs-toggle='tooltip' title='" . t('Edit List') . "'><i class='fas fa-edit text-primary'></i></button></a></td><td><form action='index.php?page=export_csv&type=list' method='post'><button type='submit' class='btn  unstyled-button' value='" . $res['id'] . "' name='listid' data-bs-toggle='tooltip' title='" . t('Download As CSV') . "'><i class='fas fa-download text-success'></i></button></form></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(" . $times_the_list_used_count . ",'list')\"><button type='submit' class='btn unstyled-button' value='" . $res['id'] . "' name='delrecid' data-bs-toggle='tooltip' title='" . t('Delete List') . "'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";
                }

                if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $hashcount > 1) {
                  break;
                }

                echo "<tr>
          <td>" . $hashcount . "</td>
          <td>" . $res['title'] . "</td>
          <td>" . $subsnum . "</td>
          <td>" . date('d-M-Y', $res['creationdate']) . "</td>
          <td>" . $action . "</td>
        </tr>";
              }
              ?>
              <tr>
                <td colspan=10 class="total-data">Total Lists: <?php echo ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $total_ob->countid >= 1) ? 1 : $total_ob->countid; ?></td>
              </tr> <!-- /keyword search -->
            </tbody>
          </table>
        </div>

        <div class="col-md-12 row  nopadding">
          <div class="col-6 mt-2">
            <?php
            $nextpageurl = $_SERVER['REQUEST_URI'] . "&pagecount";
            $current_page = 0;
            if (isset($_GET['pagecount'])) {
              $current_page = $_GET['pagecount'];
            }
            echo createPager($total_ob->countid, $nextpageurl, $current_page);
            ?>
          </div>
          <div class="col-6 mt-2 text-right">
            <?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $total_ob->countid >= 1) { ?>
              <a data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> Create New</button></a>
            <?php } else { ?>
              <a href="index.php?page=createlist"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> Create New</button></a>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<script>
  function searchList(search) {
    var ob = new OnPageSearch(search, "#keywordsearchresult");
    ob.url = "<?php echo getProtocol(); ?>";
    ob.url += "<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
    ob.search();
  }
</script>
<style type="text/css">

</style>