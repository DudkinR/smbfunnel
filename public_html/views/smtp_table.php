<?php
$mysqli = $info['mysqli'];
$pref = $info['dbpref'];
if (isset($_POST['userid'])) {
  $id = $_POST['userid'];
  $delete = "delete from `" . $pref . "quick_smtp_setting` where id=" . $id;
  $mysqli->query($delete);
}
$start_from = 0;
if (isset($_GET["pagecount"])) {
  $start_from = ($_GET['pagecount'] * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');
}
$hashcount = $start_from;
$timelimit_condition = 1;
$date_between = dateBetween('created_at');
if (strlen($date_between[0]) > 1) {
  $timelimit_condition = $date_between[0];
}
$getcountofusing = "(select count(`id`)from `" . $pref . "quick_sequence` where `smtpid`=`" . $pref . "quick_smtp_setting`.id) as `usedby_sequence`,(select count(`id`)from `" . $pref . "quick_funnels` where `primarysmtp`=`" . $pref . "quick_smtp_setting`.id) as `usedby_funnel`";
if (isset($_POST['onpage_search']) && strlen($_POST['onpage_search']) > 0) {
  $_POST['onpage_search'] = $mysqli->real_escape_string($_POST['onpage_search']);

  $keyword_search = "`title` like '%" . $_POST['onpage_search'] . "%' or `hostname` like '%" . $_POST['onpage_search'] . "%' or `port` like '%" . $_POST['onpage_search'] . "%' or `encryption` like '%" . $_POST['onpage_search'] . "%' or `fromname` like '%" . $_POST['onpage_search'] . "%' or `fromemail` like '%" . $_POST['onpage_search'] . "%' or `username` like '%" . $_POST['onpage_search'] . "%' or `replyname` like '%" . $_POST['onpage_search'] . "%' or `replyemail` like '%" . $_POST['onpage_search'] . "%'";

  $query = "SELECT *," . $getcountofusing . " FROM `" . $pref . "quick_smtp_setting` where " . $keyword_search . "  order by `id` DESC";
} else {
  $order_by = '`id` desc';
  if (isset($_GET['arrange_records_order'])) {
    $order_by = base64_decode($_GET['arrange_records_order']);
  }

  $query = "SELECT *," . $getcountofusing . " FROM `" . $pref . "quick_smtp_setting` where " . $timelimit_condition . " order by " . $order_by . " LIMIT " . $start_from . ", " . get_option('qfnl_max_records_per_page') . "";
}

$result = $mysqli->query($query);

$totalpage_query = $mysqli->query("select count(`id`) as `countid` from `" . $pref . "quick_smtp_setting` where " . $timelimit_condition . "");
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
          <?php echo arranger(array('id' => 'date')); ?>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend ">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" class="form-control form-control-sm" placeholder="<?php w("Enter SMTP Title Or Other Crentials"); ?>" onkeyup="searchSMTP(this.value)">
            </div>
          </div>
        </div>
      </div>
      <div class="row" id="smtpcontainer">
        <div class="col-md-12 ">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th><?php w("Title"); ?></th>
                  <th><?php w("Host Name"); ?></th>
                  <th><?php w("Username"); ?></th>
                  <th><?php w("Date Created"); ?></th>
                  <th><?php w("Action"); ?></th>
                </tr>
              </thead>
              <tbody id="keywordsearchresult">
                <!-- keyword search -->
                <?php
                while ($res = $result->fetch_assoc()) {
                  ++$hashcount;
                  $action = "<table class='actionedittable'><tr><td><a href='index.php?page=smtp_create&id=" . $res['id'] . "'><button class='btn unstyled-button' style='' data-bs-toggle='tooltip' title='" . t('Edit') . "'><i class='fas fa-edit text-primary'></i></button></a></td><td><button class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t("Test This SMTP") . "' onclick=testSMTP(" . $res['id'] . ",'" . base64_encode($res['title']) . "')><i class='fas fa-eye text-info'></i></button></td><td><form action='' method='post' onsubmit='return confirmDeletionSMTP(this,event," . $res['usedby_sequence'] . "," . $res['usedby_funnel'] . ");'><input type='hidden' value='" . $res['id'] . "' name='userid'><button type='submit' class='btn unstyled-button'  data-bs-toggle='tooltip' title='" . t("Delete SMTP") . "'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

                  echo "<tr>
          <td>" . t($hashcount) . "</td>
          <td>" . $res['title'] . "</td>
          <td>" . $res['hostname'] . "</td>
          <td>" . $res['username'] . "</td>
          <td>" . date('d-M-Y', $res['created_at']) . "</td>
          <td>" . $action . "</td>
        </tr>";
                }
                ?>
                <tr>
                  <td colspan=10 class='total-data'><?php w("Total SMTPs"); ?>: <?php echo t($total_ob->countid); ?></td>
                </tr>
                <!-- /keyword search -->
              </tbody>
            </table>
          </div>

          <div class="col-sm-12 row nopadding">
            <div class="col-sm-6 mt-2">
              <?php
              $nextpageurl = $_SERVER['REQUEST_URI'] . "&pagecount";
              $current_page = 0;
              if (isset($_GET['pagecount'])) {
                $current_page = $_GET['pagecount'];
              }
              echo createPager($total_ob->countid, $nextpageurl, $current_page);
              ?>
            </div>
            <div class="col-sm-6 mt-2 text-right">
              <?php if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2 && $total_ob->countid>=1 ) { ?>
                <a data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></button></a>
              <?php } else { ?>
                <a href="index.php?page=smtp_create"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></button></a>
              <?php } ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var dbgcontainer = 0;

  function testSMTP(id, title) {
    if (dbgcontainer != 0) {
      document.getElementById("smtpcontainer").removeChild(dbgcontainer);
      dbgcontainer = 0;
    }
    var divdata = "<div class='overlay'><div class='card pnl api-forms'><div class='card-header' style='position:relative;'>" + t("Test SMTP") + ": " + atob(title) + " <i id='closesmtptestdiv' class='fas fa-times-circle float-right cursor-pointer'></i></div><div class='card-body'><div class='from-group'><input type='text' class='form-control mt-2' placeholder='" + t('Enter To Name') + "'><input type='email' class='form-control mt-2' placeholder='" + t("Enter To Email") + "'><input type='text' class='form-control mt-2' placeholder='" + t("Enter Email Title") + "'><input type='text' class='form-control' placeholder='" + t("Enter Body") + "'><select class='form-select mt-2'><option value=0>" + t("Debug Off") + "</option><option value=1>" + t("Debug On") + "</option></select><center><strong><span style='margin-top:4px;margin-bottom:2px;font-size:15px;'></span></strong></center><button class='form-control btn theme-button mt-2'>" + t("Send Email") + "</button><p style='max-height:150px;overflow:auto;'></p></div></div></div></div>";

    var div = document.createElement("div");





    div.innerHTML = divdata;

    document.getElementById("smtpcontainer").appendChild(div);

    var smtpivclose = function() {
      document.getElementById("smtpcontainer").removeChild(div);
      dbgcontainer = 0;
    };

    doEscapePopup(smtpivclose);
    document.getElementById("closesmtptestdiv").onclick = function() {
      smtpivclose()
    };
    dbgcontainer = div;
    var request = new ajaxRequest();

    div.getElementsByTagName("button")[0].onclick = function() {
      var errspan = div.getElementsByTagName("span")[0];
      div.getElementsByTagName("p")[0].innerHTML = "";
      errspan.innerHTML = "<font color='green'>" + t("Sending Please Wait...") + "</font>";
      var debug = div.getElementsByTagName("select")[0].value;
      var inputs = div.getElementsByTagName("input");
      var reqstr = {
        "qmlrtestsmtp": 1,
        "toname": inputs[0].value,
        "toemail": inputs[1].value,
        "emailsubject": inputs[2].value,
        "emailbody": inputs[3].value,
        "debug": debug,
        "smtpid": id
      };
      request.postRequestCb('req.php', reqstr, function(data) {
        data = data.trim();

        if (data == '1') {
          errspan.innerHTML = "<font color='green'>" + t("Mail Sent Successfully") + "</font>";
        } else if (data == '0') {
          errspan.innerHTML = "<font style='color:#e6005c;'>" + t("Unable To Send Mail") + "</font>";
        } else {
          errspan.innerHTML = "<font color='green'>" + t("View Debug Data Below") + "</font>";
          div.getElementsByTagName("p")[0].innerHTML = "<br><strong><u>" + t("Debugged Data") + ": </u></strong><br>" + data;
        }
      });
    };
  }

  function searchSMTP(search) {
    var ob = new OnPageSearch(search, "#keywordsearchresult");
    ob.url = "<?php echo getProtocol(); ?>";
    ob.url += "<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
    ob.search();
  }

  function confirmDeletionSMTP(continues, e, bysequence, byfunnel) {
    e.preventDefault();
    var data = t("Are You Sure?");
    if (bysequence != 0 && byfunnel != 0) {
      data = t("The SMTP used by ${1} sequences and ${2} funnels, are you sure about deleting this SMTP", [bysequence, byfunnel]);
    } else if (bysequence > 0) {
      data = t("The SMTP used by ${1} sequences are you sure about deleting this SMTP", [bysequence]);
    } else if (byfunnel > 0) {
      data = t("The SMTP used by ${1} funnels are you sure about deleting this SMTP", [byfunnel]);
    }
    Swal.fire({
      title: data,
      text: t("You won't be able to revert this!"),
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: t("Yes, delete it!"),
    }).then((result) => {
      if (result.isConfirmed) {
        continues.submit();
      } else {
        return false;
      }
    });
  }
</script>



<style type="text/css">
  .actionedittable,
  .actionedittable tr,
  .actionedittable td {
    background-color: inherit !important;
    padding: 0px !important;
    border: 0px;
  }

  .actionedittable button {
    margin-left: 6px !important;
  }
</style>