<?php
// print_r($info);
$mysqli = $info['mysqli'];
$pref = $info['dbpref'];
if (isset($_POST['delrecid'])) {
  $id = $_POST['delrecid'];
  $delete = "delete from `" . $pref . "new_sequence` where id=" . $id;
  $delete1 = "delete from `" . $pref . "quick_sequence` where sequence_id=" . $id;
  $mysqli->query($delete);
  $mysqli->query($delete1);
}
$user_id = $_SESSION['user' . get_option('site_token')];
$access=$_SESSION['access'. get_option('site_token')];
$start_from = 0;
if (isset($_GET["pagecount"])) {
  $start_from = ($_GET["pagecount"] * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');
}
$hashcount = $start_from;
$timelimit_condition = 1;
$date_between = dateBetween('time');
if (strlen($date_between[0]) > 1) {
  $timelimit_condition = $date_between[0];
}
if (isset($_POST['onpage_search']) && strlen($_POST['onpage_search']) > 0) {
  $_POST['onpage_search'] = $mysqli->real_escape_string($_POST['onpage_search']);
  if($access=='admin'){
    $query = "SELECT * FROM `" . $pref . "new_sequence` where `title` like '%" . $_POST['onpage_search'] . "%'";
  }
  else{
    $query = "SELECT * FROM `" . $pref . "new_sequence` where `title` like '%" . $_POST['onpage_search'] . "%' and `user_id`=$user_id";
  }
  
} else {
  $order_by = '`id` desc';
  if (isset($_GET['arrange_records_order'])) {
    $order_by = base64_decode($_GET['arrange_records_order']);
  }
  if($access=='admin'){
    $query = "SELECT * FROM `" . $pref . "new_sequence` where  " . $timelimit_condition . " order by " . $order_by . " LIMIT " . $start_from . ", " . get_option('qfnl_max_records_per_page') . "";
  }
  else{
    $query = "SELECT * FROM `" . $pref . "new_sequence` where  " . $timelimit_condition . " and `user_id`=$user_id order by " . $order_by . " LIMIT " . $start_from . ", " . get_option('qfnl_max_records_per_page') . "";
  }
  }

//echo $query;
$result = $mysqli->query($query);
if($access=='admin'){
  $sql_text = "select count(`id`) as `countid` from `" . $pref . "new_sequence` where  " . $timelimit_condition . "";
}
else{
  $sql_text = "select count(`id`) as `countid` from `" . $pref . "new_sequence` where  " . $timelimit_condition . " and `user_id`=$user_id";
}

$page_query = $mysqli->query($sql_text);
$page_ob =  $page_query->fetch_object();
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
          <?php echo arranger(array('id' => 'date')); ?>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend ">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" class="form-control form-control-sm" placeholder="<?php echo t("Search With Title"); ?>" onkeyup="searchSequence(this.value)">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12 ">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th><?php w("Sequence&nbsp;Title"); ?></th>
                  <th><?php w("Total Emails"); ?></th>
                  <th><?php w("Created On"); ?></th>
                  <th><?php w("Action"); ?></th>
                </tr>
              </thead>
              <tbody id="keywordsearchresult">
                <!-- keyword search -->
                <?php
                while ($res = $result->fetch_assoc()) {
                  ++$hashcount;
                  $num = $result->num_rows;
                  $title = htmlentities($res['title']);
                  $title_e = base64_encode($title);
                  $description = base64_encode($res['description']);
                  $id  = $res['id'];
                  $row = $mysqli->query("SELECT COUNT(*) as `total` FROM `" . $pref . "quick_sequence` WHERE `sequence_id`=$id");

                  $total = $row->fetch_assoc()['total'];
                  $action = "<table class='actionedittable'><tr><td><button class='btn unstyled-button' onClick='editSequence(`$title_e`,`$description`,`$id`)' data-bs-toggle='tooltip' data-bs-toggle='modal' data-bs-target='#exampleModal'  title='" . t("Edit Sequence") . "'><i class='fas fa-edit text-primary'></i></button></td><td><a href='index.php?page=sequence_records&id=$id'><button class='btn unstyled-button' data-bs-toggle='tooltip'   data-original-title='View Emails'><i class='fas fa-eye text-info'></i></button></a></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(this,event,'sequence')\"><input type='hidden' value='$id' name='delrecid'><button type='submit' class='btn unstyled-button'  data-bs-toggle='tooltip' title='" . t('Delete Sequence') . "'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";
                  echo "<tr>
          <td>" . t($hashcount) . "</td>
          <td>" . $title . "</td>
          <td><a href='index.php?page=sequence_records&id=$id'>$total</a></td>
          <td>" . date('d-M-Y h:ia', (int)$res['created_on']) . "</td>
          <td>" . $action . "</td>
        </tr>";
                }
                $row1 = $mysqli->query("SELECT COUNT(*) as `total` FROM `" . $pref . "quick_sequence` WHERE `sequence_id`='0' AND `sequence` not in('compose')");
                $total1 = $row1->fetch_assoc()['total'];
                $old_total = 0;
                if ($total1 >= 1) {
                  $old_total = 1;
                ?>
                  <tr>
                    <td><?= $hashcount + 1; ?></td>
                    <td>Old Sequence</td>
                    <td><a href='index.php?page=sequence_records&id=old'><?= $total1 ?></a></td>
                    <td><?= date('d-M-Y h:ia', time()) ?></td>
                    <td>
                      <table class="actionedittable">
                        <tr>
                          <td><a href='index.php?page=sequence_records&id=old'><button class='btn unstyled-button' data-bs-toggle='tooltip' data-original-title='View Emails'><i class='fas fa-eye text-info'></i></button></a></td>
                          <td></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                <?php }
                $total_seq = $page_ob->countid == 0 ? 0 : $page_ob->countid + $old_total;
                ?>
                <tr>
                  <td colspan=10 class="total-data"> <?php w("Total Sequences"); ?>: <?php echo $total_seq; ?></td>
                </tr>
                <!-- /keyword search -->
              </tbody>
            </table>
          </div>
          <div class="col-sm-12 row pt-2">
            <div class="col-sm-6 me-auto">
              <?php
              $currentpage = 0;

              if (isset($_GET['pagecount'])) {
                $currentpage = $_GET['pagecount'];
              }
              $pagenow = $_SERVER['REQUEST_URI'] . "&pagecount";
              echo createPager($page_ob->countid, $pagenow, $currentpage);
              ?>
            </div>
            <div class="col-sm-6">
              <?php if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2 && ($total_seq>=1) ) { ?>
                <button type="button" class="btn theme-button" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" style="float:right;"><strong><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></strong></a>
              <?php } else { ?>
                <button type="button" class="btn theme-button" onClick="openModelSequence()" style="float:right;"><strong><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></strong></a>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Sequence</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="sequence_id" value="">
        <div class="mb-3">
          <label for="recipient-name" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="recipient-name">
          <span class="text-danger" style="visibility: hidden;" required id="sequencetitle"><small>Please fill this field</small></span>
        </div>
        <div class="mb-3">
          <label for="message-text" class="col-form-label">Description</label>
          <textarea class="form-control" id="message-text"></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn theme-button" id="editbtnsequence" onClick="checkSequenceForm()">Create Now</button>
      </div>
    </div>
  </div>
</div>
<script>
  function editSequence(p1, p2, p3) {
    document.getElementById("sequencetitle").innerHTML = '';
    document.getElementById("recipient-name").value = atob(p1);
    document.getElementById("message-text").innerHTML = atob(p2);
    document.getElementById("sequence_id").value = p3;
    document.getElementById("editbtnsequence").innerHTML = 'Update';
    $('#exampleModal').modal('toggle');
  }

  function openModelSequence() {
    document.getElementById("sequencetitle").innerHTML = '';
    document.getElementById("recipient-name").value = '';
    document.getElementById("message-text").innerHTML = '';
    document.getElementById("sequence_id").value = '';

    $('#exampleModal').modal('toggle');

  }

  function checkSequenceForm() {
    document.getElementById("sequencetitle").innerHTML = '';
    let title = document.getElementById("recipient-name").value;
    let description = document.getElementById("message-text").value;
    let sequence_id = document.getElementById("sequence_id").value;

    if (title.length <= 0) {
      document.getElementById("sequencetitle").innerHTML = '<small>Please fill this field</small>';
      document.getElementById("sequencetitle").style.visibility = 'visible';
      return false;
    } else {
      var request = new ajaxRequest();
      var data = {
        "new_sequence": 1,
        "title": title,
        "description": description,
        "update": sequence_id
      };
      request.postRequestCb('req.php', data, function(result) {
        if (result.trim() != '0') {
          window.location.href = "index.php?page=sequence_records&id=" + result;
        } else {
          alert('There is some error, Please try again');
        }

      });
    }
  }

  function searchSequence(search) {
    var ob = new OnPageSearch(search, "#keywordsearchresult");
    ob.url = "<?php echo getProtocol(); ?>";
    ob.url += "<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
    ob.search();
  }
</script>