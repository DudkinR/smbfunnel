<?php
$name = "";
$email = "";
$password = "";
$value = "";
$value1 = "";
$value2 = "";
$value3 = "";
$value4 = "";
$value5 = "";
$value6 = "";
$value7 = "";
$value8 = "";
$value9 = "";
$value10 = "";
$value11 = "";
$value12 = "";
$value13 = "";
$permission = array('');
if (isset($_GET['id'])) {
  // echo $_GET['id'];
  $mysqli = $info['mysqli'];
  $pref = $info['dbpref'];
  $id = $mysqli->real_escape_string($_GET['id']);
  $sql = "select * from `" . $pref . "users` WHERE id=" . $id;
  //echo $sql;
  $result = $mysqli->query($sql);
  $res = $result->fetch_assoc();
  $name = $res['name'];
  $password = $res['password'];
  $email = $res['email'];
  $permission = $res['permission'];
  $profile_picture = $res['profile_picture'];
  $permission = explode(",", $permission);
}

?>

<script type="text/javascript">
  var expanded = false;

  function showCheckboxes() {
    var checkbox = document.getElementById("checkbox");
    if (!expanded) {
      checkbox.style.display = "block";
      expanded = true;
    } else {
      checkbox.style.display = "none";
      expanded = false;
    }
  }

  var gotpassworderr=0;
  function valPasswrd(sostatsuccess = 1) {
    var pass = document.getElementById("usrpass").value;
    var reentrpass = document.getElementById("reentrpass").value;

    if (reentrpass != pass) {

      document.getElementById("user-err-msg").innerHTML = "<strong>Password Didn't Match</strong>";
      ++gotpassworderr;
      return false;
    } else if(gotpassworderr>0) {
      gotpassworderr=0;
      if (sostatsuccess == 1) {
        document.getElementById("user-err-msg").innerHTML = "";
      }
      return true;
    }
  }
</script>


<div class="container-fluid">
<div class="col-md-12 ">
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data" onsubmit="return valPasswrd(0)">
        <div class="row">
            <div class="col-md-6">
              <!-- <div class="col-md-12 "> -->
              <label><?php w("Enter Name") ?></label>
              <div class="mb-3">
                <input type="text" class="form-control" placeholder="<?php w("Enter Username"); ?>" name="name" value="<?php echo $name; ?>">
              </div>

              <label><?php w("Enter Email"); ?></label>
              <div class="mb-3">
                <input type="email" class="form-control" placeholder="<?php w("Enter Email ID"); ?>" name="email" value="<?php echo $email; ?>" required>
              </div>
              <label><?php w("Enter Password"); ?></label>
              <div class="mb-3">
                <input type="password" class="form-control" id="usrpass" placeholder="<?php w("Enter Password"); ?>" name="password" value="">
              </div>

              <label><?php w("Re-enter Password"); ?></label>
              <div class="mb-3">
                <input type="password" class="form-control" id="reentrpass" placeholder="<?php w("Re-enter Password"); ?>" name="password" value="" onkeyup="valPasswrd()">
              </div>
              <?php if($_SESSION['access' . get_option('site_token')]==='admin'){ ?>
              <!-- bg-success -->
              <label class="bg-success"><?php w("Check admin"); ?></label>
              <div class="mb-3">
                <!-- checkbox Sadmin -->
                <input type="checkbox"  id="role" name="role" value="admin" <?php 
              if(isset($res['id'])){
                 $sql_text="SELECT * FROM `".$pref."administrator` WHERE user_id=".$res['id']." ";
                 $adm_res= $mysqli->query($sql_text);
                 $type_user=$adm_res->fetch_assoc();
                 if($type_user['type']=='admin') {echo "checked";}  /* */
                }
                 ?>
                 onchange="if(this.checked){document.getElementById('Sadmin_label').innerHTML='admin';}else{document.getElementById('Sadmin_label').innerHTML='user';}"
                 >
                <label for="Sadmin" id="Sadmin_label"><?php w($type_user['type']); ?></label>
                
              </div>
              <?php } ?>
              <label><?php w("Add Permissions"); ?></label>
              <div class="dropdown">
                <button type="button" class="btn dropdown-toggle btn-block" id="drpdwnstyle" data-bs-toggle="dropdown">
                  <?php w("Select User Permission"); ?>
                </button>

                <div id="permissiondiv" class="dropdown-menu btn-block" style="max-height: 350px;overflow-y: auto;">

                  <div style="padding: 0px 10px 0px 10px;width: 500px;">
                  <?php 
                   if (in_array('admin', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="admin" <?php if (in_array('admin', $permission)) {echo "checked";} ?>><?php w("All Pages"); ?></label>
                  <?php } 
                  if (in_array('dashboard', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="dashboard" <?php if (in_array('dashboard', $permission)) {echo "checked";} ?>><?php w("Dashboard"); ?></label>
                  <?php }
                  if (in_array('all_funnels', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>

                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="all_funnels,create_funnel,optins,page_builder" <?php if (in_array('all_funnels', $permission)) {echo "checked";} ?>><?php w("Funnels & Optins"); ?></label>
                  <?php }
                  if (in_array('membership_funnels', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>

                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="membership_funnels,members" <?php if (in_array('membership_funnels', $permission)) {echo "checked";} ?>><?php w("Members"); ?></label>
                  <?php }
                  if (in_array('analysis', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="analysis" <?php if (in_array('analysis', $permission)) {echo "checked";} ?>><?php w("Analysis"); ?></label>  
                  <?php }
                  if (in_array('media', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>                    
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="media" <?php if (in_array('media', $permission)) {echo "checked";} ?>><?php w("Media"); ?></label>
                  <?php }
                  if (in_array('products', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="products" <?php if (in_array('products', $permission)) {echo "checked";} ?>><?php w("Products"); ?></label>
                  <?php }
                  if (in_array('sales', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="sales" <?php if (in_array('sales', $permission)) {echo "checked";} ?>><?php w("Sales"); ?></label>
                  <?php }
                  if (in_array('plugins', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="plugins" <?php if (in_array('plugins', $permission)) {echo "checked";} ?>><?php w("Plugins"); ?></label>
                  <?php }
                  if(isset($GLOBALS['user_screen_plugin_pages']) && is_array($GLOBALS['user_screen_plugin_pages']))
                    {
                      foreach($GLOBALS['user_screen_plugin_pages'] as $user_screen_plugin_pages_index=>$user_screen_plugin_pages_val){
                        if (in_array($user_screen_plugin_pages_index, $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) {
                          ?>
                          <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="<?php echo $user_screen_plugin_pages_index; ?>" <?php if (in_array($user_screen_plugin_pages_index, $permission)) {echo "checked";
                          } ?>><?php w($user_screen_plugin_pages_val);echo"&nbsp;";echo"("; w('Plugin'); echo ")"; ?></label>
                          <?php }
                        }
                    }
                    if (in_array('listrecords', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                     <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="listrecords,createlist" <?php if (in_array('listrecords', $permission)) {echo "checked";} ?>><?php w("Lists"); ?></label>
                    <?php }
                    if (in_array('compose_mail', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                      <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="compose_mail" <?php if (in_array('compose_mail', $permission)) {echo "checked";} ?>><?php w("Compose Mail"); ?></label>
                    <?php }
                    if (in_array('sequence_records', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                      <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="sequence_records,sequence" <?php if (in_array('sequence_records', $permission)) {echo "checked";} ?>><?php w("Sequences"); ?></label>
                    <?php }
                     if (in_array('sentemailsdetails', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="sentemailsdetails" <?php if (in_array('sentemailsdetails', $permission)) {echo "checked";
                    } ?>><?php w("Mailing History"); ?></label>
                    <?php }
                    if (in_array('smtp_table', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                      <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="smtp_table,smtp_create" <?php if (in_array('smtp_table', $permission)) {echo "checked";} ?>><?php w("SMTPs"); ?></label>
                    <?php }
                    if (in_array('multiuser_table', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                      <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="multiuser_table,createmultiuser" <?php if (in_array('multiuser_table', $permission)) {echo "checked";} ?>><?php w("Users"); ?></label>
                    <?php }
                    if (in_array('gdpr', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="gdpr" <?php if (in_array('gdpr', $permission)) {echo "checked";} ?>><?php w("GDPR Configuration"); ?></label>
                    <?php }
                    if (in_array('settings', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="settings" <?php if (in_array('settings', $permission)) {echo "checked";} ?>><?php w("Settings"); ?></label>
                    <?php }
                    if (in_array('app_guide', $dashboard_permission_page_arr) || in_array('admin', $dashboard_permission_page_arr)) { ?>
                    <label>&nbsp;<input type="checkbox" name="permission[]" class="checkstyle" value="app_guide" <?php if (in_array('app_guide', $permission)) {echo "checked";} ?>><?php w("Guidence"); ?></label>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <label class="mt-2"><?php w("Enter Your Current Password"); ?></label>
              <div class="mb-3" data-bs-toggle="tooltip" title="<?php w("Please provide your current password to create a new user or to Modify existing user's credentials"); ?>">
                <input type="password" class="form-control" name="current_user_current_pass" placeholder="<?php w("Please Enter Your Current Password To Continue"); ?>" required>
              </div>
              <div class="mb-3 mt-2">
              <center><p id="user-err-msg" class='<?php echo (isset($GLOBALS['user_addorupdate_err']))? "text-danger":"text-success"; ?>'><?php if(isset($GLOBALS['user_addorupdate_err'])){echo "<strong>".t("Unable to save settings, may be another user with same email exists or signature password is wrong.")."</strong>";}elseif(isset($_POST["save"])){echo "<strong>".t("Data Saved Successfully")."</strong>";} ?></p></center>
                <button type="submit" class="btn theme-button btnclr float-right btn-block" name="save"><?php w("Save Settings"); ?></button>

              </div>
            </div>

            <div class="col-md-6 justify-content-center mt-5 order-first">
              <div class="mt-2 text-center"> <label><?php w("Upload/Change Profile Picture"); ?></label></div>
              <div id="userprofileimage" class="avatar-wrapper">

                <img class="profile-pic" src="<?php echo $profile_picture; ?>" />
                <div class="upload-button">
                  <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                </div>
                <input type="file" class="file-upload" name="fileToUpload" id="filetouploadinp" accept="image/x-png,image/gif,image/jpeg">
              </div>



            
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  function observePermissions() {
    var parent = document.getElementById("permissiondiv");
    var doc = parent.querySelectorAll("input[type=checkbox]");

    parent.onclick = function() {
      for (var i = 1; i < doc.length; i++) {
        if (doc[0].checked) {
          doc[i].checked = false;
          doc[i].disabled = false;
        } else {
          doc[i].disabled = false;
        }
      }
    }
  }
  observePermissions();
  <?php
  if (isset($name) && strlen($name) > 0) {
    echo 'modifytitle("' . $name . '","Users");';
  }
  ?>
</script>
<script>
  $(document).ready(function() {

    var readURL = function(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('.profile-pic').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
      }
    }

    $(".file-upload").on('change', function() {
      readURL(this);
    });

    $(".upload-button").on('click', function() {
      $(".file-upload").click();
    });
  });
</script>
<style type="text/css">
  .bg {
    border-radius: 10px;
    padding-left: 100px;
    padding-right: 100px;
  }

  .multiselect {
    width: 200px;
  }

  .selectBox {
    position: relative;
  }

  .selectBox select {
    width: 100%;
    font-weight: bold;
  }

  .checkstyle {
    margin-right: 5px;
  }

  #drpdwnstyle {
    border: 1px solid #ced4da;
    font-size: 14px;
    color: gray;
  }

  #permissiondiv label {
    display: block;
  }

  .avatar-wrapper {
    position: relative;
    height: 200px;
    width: 200px;
    margin: 12px auto;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 1px 1px 15px -5px black;
    transition: all .3s ease;
  }

  .avatar-wrapper:hover {
    transform: scale(1.05);
    cursor: pointer;
  }

  .avatar-wrapper:hover .profile-pic {
    opacity: .5;
  }

  .avatar-wrapper .profile-pic {
    height: 100%;
    width: 100%;
    transition: all .3s ease;
  }

  .avatar-wrapper .profile-pic:after {
    font-family: FontAwesome;
    content: "\f007";
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    position: absolute;
    font-size: 190px;
    background: #ecf0f1;
    color: #34495e;
    text-align: center;
  }

  .avatar-wrapper .upload-button {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
  }

  .avatar-wrapper .upload-button .fa-arrow-circle-up {
    position: absolute;
    font-size: 100px;
    top: 24%;
    left: 51px;
    text-align: center;
    opacity: 0;
    transition: all .3s ease;
    color: #34495e;
  }

  .avatar-wrapper .upload-button:hover .fa-arrow-circle-up {
    opacity: .9;
  }
</style>