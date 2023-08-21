<?php
global $mysqli;
global $dbpref;
global $app_variant;
$table = $dbpref . 'cf_social_share';
$sql = $mysqli->query("SELECT * FROM `" . $table . "` ORDER BY `social_id` ASC ");
$num_rows = mysqli_num_rows($sql);
?>

<div class="card card-body">
  <p class="p-3 mt-1 rounded bg-info text-white">
    Use this shortcode <span class="text-white"> <strong style="cursor:pointer;" onclick="copyText(`[cf_Social_share]`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cf_Social_share]</strong> </span> to show <b>social share icon on page </b>
  </p>
</div>
<div class="container-fluid bg-white">
  <div class="row">
    <div class="col-md-12 mt-4">
      <div class="d-sm-flex align-items-center justify-content-between mb-4 shadow p-3 rounded">
        <h5 class="mb-0"><?php w('Social share'); ?></h5>
        <div><?php w('Create, edit and manage your social share icon'); ?></div>
      </div>
    </div>
  </div>



  <div class="row">
    <div class="col-md-12 text-center">
      <div class="card pb-2  br-rounded">
        <div class="card-body pb-2">
          <div class="row">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <!-- keyword search -->
                <tbody>
                  <?php
                  $check_data = array();
                  if ($num_rows > 0) {
                    $i = 1;
                    while ($row = mysqli_fetch_array($sql)) {
                      $str = $row['network_name'];

                      $object = json_decode($str);
                      $icon = $object->icon;
                      $name = $object->name;
                      array_push($check_data, $name);

                  ?>
                      <tr>

                        <td scope="col"><?php echo $i; ?></i></td>
                        <td scope="col">
                          <div class="social_icons"><i class="<?php echo $icon; ?>"></i></div>
                        </td>
                        <td scope="col"><?php echo $name; ?></td>
                        <td scope="col">
                          <a class="btn edit_btn" id="<?php echo $row["social_id"]; ?>" name="edit_answer" data-bs-toggle="tooltip" title="<?php w('Edit'); ?>"><i class="fas fa-edit text-primary"></i></a>&nbsp;
                          <a class="btn delete_btn" id="<?php echo $row["social_id"]; ?>" name="delete_answer" data-bs-toggle="tooltip" title="<?php w('Delete'); ?>"><i class="fas fa-trash text-danger"></i></a>
                        </td>
                      </tr>
                    <?php
                      $i++;
                    }
                  } else {
                    ?>
                    <tr>
                      <td scope="col" colspan="12" class="text-center">No any created social share icon</td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
              <form method="post" id="cf_social_form">
                <table class="table col-md-12 table-striped" id="dynamic_field">
                </table>
                <table class="table col-md-12">
                  <tr>
                    <td class="col-md-8  float-start"><button name="cf_submit" id="cf_submit" class="submit_btn btn btn-success mt-3  float-start">Submit</button></td>
                    <td class="col-md-4"><button type="button" id="add" name="add" class="add_social_btn btn btn-primary mt-3">Add Social Network</button></td>
                  </tr>
                </table>
              </form>

              <!-- /keyword search -->

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="delete_form"></form>
      <div class="modal-body">
        <p>Are you sure you want to delete this? </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="delete_recordes">Delete</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="edit_form_Modal" tabindex="-1" role="dialog" aria-labelledby="studentModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Network</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="update_social_form" method="POST">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="modal-body">

          <select name="network_update" id="cf_modal_social" class="form-control">

          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="update_social" name="update_social">Edit Network</button>
        </div>
      </form>
    </div>
  </div>
</div>


<input type="hidden" id="cf_social_ajax" value="<?php echo get_option('install_url') . "/index.php?page=ajax"; ?>">
<input type="hidden" id="cf_social_check" value='<?php echo json_encode($check_data); ?>'>
<!-- <input type="hidden" name="cf_social_url" id="cf_social_url" value="<?php echo CF_SOCIAL_SHARE_PLUGIN_DIR_PATH . "/view/social.json"; ?>" /> -->
<input type="hidden" name="cf_social_url" id="cf_social_url" value="<?php echo plugin_dir_url(__FILE__) . "social.json"; ?>" />