<?php
$mysqli = $info['mysqli'];
$pref = $info['dbpref'];
if( isset($_GET['sequence_id']) )
{
  $id=$_GET['sequence_id'];
  $id = $mysqli->real_escape_string($id);
  if( $id == "old" )
  {
    $fetch="Old Sequence Email";
    
  }else{
    $sequence_id=" `sequence_id`=$id and";
    $rows = $mysqli->query("SELECT `title` FROM `".$pref."new_sequence` WHERE `id`=$id");
    $fetch = $rows->fetch_assoc()['title'];
    $fetch.=" Sequene Email";
  }
  $seqid = $_GET['sequence_id'];
$link='<a href="index.php?page=sequence_records&id='.$id.'"  style="cu"><i class="fas fa-arrow-left"></i> '.$fetch.'</a>';
}
echo '<input type="hidden" id="sequence_id" value="'.$id.'">';
if (isset($_GET['seqid'])) {
  $_GET['seqid']=$mysqli->real_escape_string($_GET['seqid']);
  $select = "select * from `" . $pref . "quick_sequence` where id='" . $_GET['seqid'] . "'";
  $res = $mysqli->query($select);
  // print_r($res);
  while ($resul = $res->fetch_assoc()) {
    $idd = $resul['id'];
    $smtpid = $resul['smtpid'];
    $sentdata = $resul['sentdata'];
    $getbody = explode('@clickbrk@', $sentdata);
    // print_r($getbody);
    $subject = $getbody[0];
    $bodyy = $getbody[1];
    $unsubsmsgs = $getbody[2];
    $sequnce = $resul['sequence'];
    if($sequnce=="compose")
    {
      echo "<script>window.location='index.php?page=compose_mail&seqid=".$idd."'</script>";
      die();
    }
    // echo $sequnce;
    $listidd = $resul['listid'];
    $title = $resul['title'];
    $sequence_id = $resul['sequence_id'];
  }
}


register_tiny_editor("#sequence_editor");
?>

<div class="container-fluid " id="sequence_app">
  <?php
  if (isset($_GET['seqid'])) { ?>
    <input type="hidden" id="seqid" value="<?php if (isset($idd)) {
                                                echo $idd;
                                              } else {
                                                echo "";
                                              } ?>">
    <input type="hidden" id="smtpid" value="<?php if (isset($smtpid)) {
                                                echo $smtpid;
                                              } else {
                                                echo "";
                                              } ?>">
    <input type="hidden" id="listidd" value="<?php if (isset($listidd)) {
                                                  echo $listidd;
                                                } else {
                                                  echo "";
                                                } ?>">
    <input type="hidden" id="emailsub" value="<?php if (isset($subject)) {
                                                  echo htmlentities($subject);
                                                } else {
                                                  echo "";
                                                } ?>">
    <textarea id="emailbody" style="display:none;">
    <?php 
    if (isset($bodyy)) {echo htmlentities($bodyy);} else {echo "";} ?>
    </textarea>
    <input type="hidden" id="unsubs" value="<?php if (isset($unsubsmsgs)) {
                                                echo htmlentities($unsubsmsgs);
                                              } else {
                                                echo "";
                                              } ?>">
    <input type="hidden" id="sequencee" value="<?php if (isset($sequnce)) {
                                                    echo $sequnce;
                                                  } else {
                                                    echo "";
                                                  } ?>">
    <input type="hidden" id="seqtitle" value="<?php if (isset($title)) {
                                                  echo htmlentities($title);
                                                } else {
                                                  echo "";
                                                } ?>">
    
  <?php } ?>
  <!--<div class="col-sm-3"></div>-->
  <div class="col-md-12 nopadding">
    <div class="card pb-2  br-rounded">
      <div class="card-body pb-2">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>{{t("Email Title")}}:</label>
              <input type="text" v-model="seqtitle" id="seqtitle" class="form-control" v-bind:placeholder="t('Enter Title')">
            </div>
            <div class="mb-3">
              <label>{{t('Select SMTP')}}</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend"><span class="input-group-text">{{t('Select SMTP')}}</span></div>
                <select class="form-select" v-model="smtpid">
                  <option value='php'>{{t('Default Hosting Mailer')}}</option>
                  <?php
                  $gettheid = "select * from `" . $pref . "quick_smtp_setting`";
                  $getresult = $mysqli->query($gettheid);
                  // print_r($getresult->num_rows);
                  while ($row = $getresult->fetch_assoc()) {
                    echo "<option value=" . $row['id'] . ">" . $row['title'] . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label>{{t('Select Sequence')}}</label>
              <div class="input-group  mb-3">
                <div class="input-group-prepend"><span class="input-group-text">{{t('Select Sequence')}}</span></div>
                <select class="form-select" v-model="sequence_id">
                  <option value="-1">Select</option>
                  <?php
                  $getresults = $mysqli->query("SELECT *  FROM `" . $pref . "new_sequence`");
                  while ($row1 = $getresults->fetch_assoc()) {
                    echo "<option value=" . $row1['id'] . ">" . $row1['title'] . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <input type="hidden" id="listids" value="">
              <div class=" row ">
                <div class="col-md-6">

                  <label>{{t('Select List')}}:</label>
                  <div class="dropdown">
                    <button type="button" class="btn theme-button btn-block dropdown-toggle" data-bs-toggle="dropdown">
                      {{t('Select Lists')}}
                    </button>

                    <div id="alllistrecords" class="dropdown-menu btn-block  pl-2">
                      <label>&nbsp;<input class="me-3" type="checkbox" value="<?php echo "all"; ?>">{{t('All')}}</label>

                      <?php
                      $gettheid = "select * from `" . $pref . "quick_list_records`";
                      $getresult = $mysqli->query($gettheid);
                      // $selected = "";
                      while ($row = $getresult->fetch_assoc()) {

                        echo ' <div class=""><label>&nbsp;<input type="checkbox" class="me-3" value="' . $row['id'] . '">' . $row['title'] .  ' </label></div>';
                      }

                      ?>

                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label>{{t('Manage Sequence')}}:</label>

                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">{{t('Send Mail')}}</span></div>
                    <select class="form-select" id="sequencedays" v-model="sequencedays">
                      <option value='0'>{{t('During Signup')}}</option>
                      <?php
                      for ($i = 1; $i <= 365; $i++) {
                        echo "<option value=" . $i . ">".t("After \${1} Days",array($i))."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="mt-2">{{t('Email Subject')}}:</label>
              <input type="text" v-model="clicksubject" v-bind:placeholder="t('Email Subject')" class="form-control">
            </div>

            <div class="mb-3">
              <label class="mt-2">{{t('Unsubscription Message')}}:</label>
              <textarea v-model="unsubsmsg" v-bind:placeholder="t('Enter Unsubscription Message')" rows="6" class="form-control"></textarea>
            </div>
          </div>
          <div class="col-md-6">

            <label>{{t('Email Body')}}:</label>
            <textarea v-model="clickbody" id="sequence_editor" v-bind:placeholder="t('Enter placeholder')" class="form-control "></textarea>

            <span v-html="t(err)"></span>
            <button type="button" class="mt-2 btn theme-button float-right btn-block" v-on:click="sequencesubmit">{{t('Add To Mail Sequence List')}}</button>
            <input type="hidden" id="checkexist">


          </div>

          <!-- <textarea v-model="clickbody" placeholder="Email Body" class="form-control" rows=10></textarea> -->






          <!-- <div class="col-sm-3"></div> -->
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById("commoncontainerid").innerHTML=`<?=$link?>`;
  <?php
  if (isset($title) && strlen($title) > 1) {
    echo 'modifytitle("' . $title . '","Sequence");';
  }
  ?>
</script>