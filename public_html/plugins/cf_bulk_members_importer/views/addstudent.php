<?php
global $mysqli;
global $dbpref;
$students=$this->load('setup');
global $app_variant;
$app_variant = isset($app_variant)?$app_variant:"coursefunnels";

$install_url = get_option("install_url");
if( $app_variant == "shopfunnels" ){
    $student="Customer";
    $funne_type="Store";

}
elseif( $app_variant == "cloudfunnels" ){
    $student="Member";
    $funne_type="Funnel";

}
elseif( $app_variant == "coursefunnels" ){
    $student="Student";
    $funne_type="Funnel";

}
if(isset($_GET['funnel_id']))
{

  $funnel_id=$mysqli->real_escape_string($_GET['funnel_id']);
  $pages = get_funnel_pages($funnel_id);
}
else{
  $install_url=$install_url."/index.php?page=cfbulk_members_funnels";
  header("Location:".$install_url."");
}

$page_id=1;
foreach($pages as $page)
{
  if( $page['category'] == "register" || $page['category'] == "order" || $page['category'] == "login" )
  {
    $page_id = $page['id'];
    break;
  }else{
    $page_id = $page['id'];
  }
}

if(isset($_GET['cfbulk_members_id']))
{
  if( is_numeric( $_GET['cfbulk_members_id'] ) )
  {
    $student_id = $_GET['cfbulk_members_id'];
  
  }

  $student_id=$mysqli->real_escape_string($student_id);
  $student_data = $students->getSingleStudent($student_id);

  $cf_adds_name=$student_data['name'];
  $cf_adds_valid=$student_data['valid'];
  $cf_adds_email=$student_data['email'];
  $cf_adds_exf=json_decode($student_data['exf'],true);
  $funnel_id=$student_data['funnelid'];
  $page_id=$student_data['pageid'];
  $cf_adds_id=true;

}
else
{
  $cf_adds_name='';
  $cf_adds_email='';
  $student_id='';
  $cf_adds_exf=[];
  $cf_adds_valid=1;
  $cf_adds_id=false;
}
$courses = get_products();
$fnls = $students->getAllFunnels( $funnel_id );
$s_avatar= function( $fname,$setup_ob){ return $setup_ob->text_to_avatar($fname);};
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <a href="index.php?page=cfbulk_members_members&funnelid=<?=$funnel_id?>">
          <div class="d-flex text-white align-items-center">
            <?php
              echo $s_avatar($fnls[0]['name'],$students)."<span class='text-dark d-inline-block px-2' style='font-weight:600'>".$fnls[0]['name']."</sapn> ";
            ?>
            </div>
          </a>
      </div>
      <div class="col-md-7 align-self-center text-right">
          <div class="d-flex justify-content-end align-items-center">Create, edit, manage <?= $student;?></div>
      </div>
  </div>
  <input type="hidden" id="cf-software-type"  value="<?=$app_variant; ?>">
  <div class="container">
    <form  id="cfaddStudentManually"  autocomplete="off" spellcheck="false">
      <div class="card">
        <div class="card-header text-primary">
          <h4>Add <?= ucfirst($student);?> Manually</h4>
        </div>
        <div class="card-body">
          <input type="hidden" name="savestudents" value="<?=($student_id)?'update':'save' ?>">
          <input type="hidden" id="cfstudent_id" name="cfstudent_id" value="<?=$student_id; ?>">
          <input type="hidden" name="funnel_id" value="<?=$funnel_id; ?>">
          <input type="hidden" name="page_id" value="<?=$page_id?>">
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" required class="form-control" value="<?=$cf_adds_name  ?>" name="name" id="cfaddst_name" placeholder="Enter name" >
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" required class="form-control" value="<?=$cf_adds_email ?>" name="email" id="cfaddst_email" placeholder="Enter email" >
            </div>

            <?php if( $app_variant == 'coursefunnels' ) { ?>
            <div class="form-group">
            <label for="">Select Courses</label>
            <select id="select_courses" class="form-control select_courses" name="select_courses[]" multiple="multiple">
              <?php
              foreach ($courses as $value) {
                echo "<option value='" . $value['id'] . "'>" . $value['title'] . "</option>";
              }
              ?>
            </select>
            </div>
            <?php } ?>

            <div class="form-group">
            <label for="">Membership Status</label>
            <select  class="cfadd_student_cancel form-control" name="valid" >
              <option value="1" <?= ( $cf_adds_valid == 1 ) ? 'selected' : '' ?> >Activate</option>
              <option value="0" <?= ( $cf_adds_valid == 0 ) ? 'selected' :'' ?>>Deactivate</option>
            </select>
            </div>
            <?php if( $cf_adds_id ) { ?>  
            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#cf-adds-demo2">
              Change Password
            </button>
            <div id="cf-adds-demo2" class="p-4 collapse">
              <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" <?=($student_id)?'':'required'?> data-toggle="tooltip" data-placement="top" title="Please insert password with a minimum length of eight and combination of upper and lowercase characters, numbers and special characters" class="form-control" name="password" id="cfaddst_password" placeholder="Enter password" >
              </div>
              <div class="form-group">
                  <label for="">Confirm Password</label>
                  <input type="password" <?=($student_id)?'':'required'?> data-toggle="tooltip" data-placement="top" title="Please insert password with a minimum length of eight and combination of upper and lowercase characters, numbers and special characters" class="form-control" name="cpassword" id="cfaddst_cpassowrd" placeholder="Enter confirm password" >
              </div>
            </div>
            <?php } else { ?>
              <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" <?=($student_id)?'':'required'?> data-toggle="tooltip" data-placement="top" title="Please insert password with a minimum length of eight and combination of upper and lowercase characters, numbers and special characters" class="form-control" name="password" id="cfaddst_password" placeholder="Enter password" >
              </div>
              <div class="form-group">
                  <label for="">Confirm Password</label>
                  <input type="password" <?=($student_id)?'':'required'?> data-toggle="tooltip" data-placement="top" title="Please insert password with a minimum length of eight and combination of upper and lowercase characters, numbers and special characters" class="form-control" name="cpassword" id="cfaddst_cpassowrd" placeholder="Enter confirm password" >
              </div>
            <?php } ?>
              <hr>
            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#cf-adds-demo">Extra Fields</button>
            <div id="cf-adds-demo" class="p-4 collapse">
              <div id="cfadds-inputs">
                <?php  if( count($cf_adds_exf) >0 ){ ?>
                  <?php  foreach($cf_adds_exf as $in => $cf_adds){ ?>
                    <div class="input-group mb-3">
                      <input type="text" name="input_name[]" value="<?=trim($in)?>" class="form-control" placeholder="Enter Input Name">
                      <input type="text"  name="input_value[]" value="<?=trim($cf_adds)?>"  class="form-control" placeholder="Enter Input value">
                      <div class="input-group-prepend">
                        <button type="button" class="btn btn-danger cfadds-delete-inp">Delete</button>
                      </div>
                    </div>  
                  <?php } ?>
                <?php }else{ ?>
                  <div class="input-group mb-3">
                    <input type="text" name="input_name[]" class="form-control" placeholder="Enter Input Name">
                    <input type="text"  name="input_value[]"  class="form-control" placeholder="Enter Input value">
                    <div class="input-group-prepend">
                      <button type="button" class="btn btn-danger cfadds-delete-inp">Delete</button>
                    </div>
                  </div>
                <?php  } ?>
              </div>
              <button type="button" class="btn btn-success" id="cfadds-add-new">Add New</button>
            </div>
          </div>
        <div class="card-footer">
          <button class="btn btn-primary" id="addstudent_btn"> Save </button>
          <br>
          <br>
          <div class="alert alert-danger" id="cf-studnet-errr">
            <div class="cfstudent-err">

            </div>
          </div>
        </div>
      </div>
    </form>
  </div> 
</div>
<input type="hidden" id="cfaddstudent_ajax"  value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />