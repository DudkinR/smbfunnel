
<script>
function myFunction(str) {
  var sid="cpycfshortcode"+str;
  var cpyval=document.getElementById(sid);
  cpyval.style.display="block";
  cpyval.select();
  document.execCommand("copy");
  cpyval.style.display="none";
}


</script>

<?php
global $mysqli;
if(isset($_POST['cfwheeldelete']))
{ global $mysqli;
    global $dbpref;
    $table= $dbpref.'spinwheel_setting';
    $idd = $_POST['cfwheeldelete'];
    $delete ="delete from `".$table."` where id='".$idd."'";
 $mysqli->query($delete);
}
if(isset($_POST['cfwheeldeleteuser']))
{ global $mysqli;
    global $dbpref;
    $table= $dbpref.'spinwheelusers';
    $idd = $_POST['cfwheeldeleteuser'];
    $delete ="delete from `".$table."` where id='".$idd."'";
 $mysqli->query($delete);
}
$wheelstatus="";
$formid = "";
$headertitle="Wheel Details";
$headercontent ="Manage Wheels";


if (isset($_GET['wheelstatus']) ) {
$getwheelstatus = $_GET['wheelstatus'];
if ($getwheelstatus == 'wheels') {
  $wheelstatus = "";
   $wheelid = $_GET['wheelid'];
  $headertitle="User Details";
  $headercontent ="Manage Users";


  global $mysqli;
            global $dbpref;
            $table= $dbpref.'spinwheelusers';
            $qry=$mysqli->query("select count(`id`) as `total_wheels` from `".$table."` where `wheelid` =".$wheelid."");
             $r=$qry->fetch_object();
             $forms_ob=$this->load('forms_control');
             $page_count=1;
    if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
    {
        $page_count=(int)$_GET['page_count'];
    }
            $total_wheels = $r->total_wheels;
            $max_leads_limit=(int)get_option('qfnl_max_records_per_page');
           $total_user = $forms_ob->getUserWheelcount($wheelid);
         
           $wheels=$forms_ob->getALLWheelusers($total_user, $max_leads_limit, $page_count,$wheelid);
            
            
  }
}
$total_wheels=0;
$forms_ob=$this->load('forms_control');

    $page_count=1;
    if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
    {
        $page_count=(int)$_GET['page_count'];
    }
    ob_start();
    if(!isset($_GET['wheelstatus']) && !isset($_GET['wheelid'])){ $cfplaceholder = t("Enter Wheel Name ");}else{ $cfplaceholder = t("Enter User Name ");}
    echo '<div class="col-md-2  mb-2">
          '.createSearchBoxBydate().'
          </div>
          <div class="col-md-3">
          '.showRecordCountSelection().'
          </div>
          <div class="col-md-3">'.arranger(array('id'=>'date')).'
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend ">
                  <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="'.$cfplaceholder.'" onkeyup="SearchForms(this.value)">              </div>
            </div>
          </div>';

    $table_manager=ob_get_clean();
     $max_leads_limit=(int)get_option('qfnl_max_records_per_page');
    $total_wheels=$forms_ob->getWheelsCount();
    $forms=$forms_ob->getAllWheels($total_wheels, $max_leads_limit, $page_count);
   
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Wheel Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">Manage Wheels</div>
      </div>
  </div>
  <div class="card pb-2  br-rounded">
      <div class="card-body pb-2">
        <div class="row">
          <?=$table_manager;  ?>
          <div class="col-sm-12 nopadding">
          <?php
if(!isset($_GET['wheelstatus']))
{
            ?>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Wheel&nbsp;Name</th>
                    <th>Shortcode</th>
                    <th>Type</th>
                    <th>Addded On</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="keywordsearchresult">
                  <!-- keyword search -->
                  <?php 
      
                  $count=1;
                  if(isset($_GET['page_count']))
                  {
                     $page_count=(int)$_GET['page_count'];
                     $count=($page_count*$max_leads_limit)-$max_leads_limit;
                    ++$count;
                  }
               
                  if( count($forms) > 0 )
                  {
                      foreach( $forms as $data )
                      {
                  ?>    <tr>
                          <td><?php echo $count; ?></td>
                          <td>    <a href="index.php?page=CFSpinner_settingform&cfspinner_wheelid=<?=$data['id'] ?>">
 <?php  echo $data['cfspinnerwheel'];  ?></a></td>
    <td onclick='myFunction("<?php echo $data['id'];?>")' style='cursor:pointer'><span data-bs-toggle='tooltip' title='Copy To Clip Board'><input type='text' id='cpycfshortcode<?php echo $data['id'];?>' style='display:none;' value='[show_wheel id=<?php echo $data['id'];?>]'><strong>[show_wheel id=<?php echo $data['id'];?>]</strong></span></td>                  
                                  <td><?php if($data['cfspinner_theme'] == "cfwheelcolbg")
                                  {
                                    echo "Color Wheel";
                                  }
                                  else{
                                    echo "Image Wheel";
                                  }?>
</td>
                          <td><?php echo date('d-M-y h:ia', strtotime($data['created_at'])); ?></td>
                          <td><form method="post">
                          <a href="index.php?page=CFSpinner_allwheels&wheelstatus=wheels&wheelid=<?php echo $data['id'];?>">    <i class="fas fa-user text-primary"></i> &nbsp;</a>
  <button type='submit'  value="<?php echo $data['id'];?>"name="cfwheeldelete" class="btn unstyled-button"> <i class="fas fa-trash text-danger"></i></button></form>
                          </td>
                        </tr>
                    <?php
                    $count++;
                  }
                }
                  ?>
                        <tr>
                          <td colspan="6" class="total-data" ><center> Total Wheels: <?=$total_wheels; ?></center></td>
                        </tr>
                <!-- /keyword search -->
                </tbody>
              </table>


            </div>
            <div class="col-md-12 row nopadding">
          <div class="col-sm-6 mt-2">
            <?php
              $next_page_url="index.php?page=CFSpinner_allwheels&page_count";
              $page_count=($page_count<2)? 0:$page_count;
              echo createPager($total_wheels,$next_page_url,$page_count);
            ?> 
          </div>
          <div class="col-sm-6 text-end mt-2"> 
            <a href="index.php?page=CFSpinner_settingform"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> Create New</button></a>
          </div>  
            <?php
}
?>
      
          </div>
          









            <?php if(isset($_GET['wheelstatus']))
        {  
?>
          <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>User&nbsp;Name</th>
                <th>User Email</th>
                <th>Prize Name</th>
                <th>Addded On</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="keywordsearchresult">
              <!-- keyword search -->
              <?php 
                           $page_count=1;
    if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
    {
        $page_count=(int)$_GET['page_count'];
    }
              $count=1;
              if(isset($_GET['page_count']))
              {
                $page_count=(int)$_GET['page_count'];
                $count=($page_count*$max_leads_limit)-$max_leads_limit;
                ++$count;
              }
           
              if( count($wheels) > 0 )
              {
                  foreach( $wheels as $data )
                  {
              ?>    <tr>
                      <td><?php echo $count; ?></td>
                      <td><?php echo $data['name']; ?></td>
                      <td><?php echo $data['email']; ?></td>
                      <td><?php echo $data['winprize']; ?></td>
                      <td><?php echo date('d-M-y h:ia', strtotime($data['added_on'])); ?></td>
                          <td style="display:inline-flex;padding:10px;"><form method="post">
  <button type='submit' style="padding-right:12px;"  value="<?php echo $data['id'];?>"name="cfwheeldeleteuser" class="btn unstyled-button"> <i class="fas fa-trash text-danger"></i></button></form>
  <?php if($data['mailstatus'] == "1")
  {?>
   <form method="post"><button type="button" disabled name="cfspinsendmail" class="btn unstyled-button"><i class="fa fa-envelope text-success" style="font-size:18px;" aria-hidden="true" ></i></button></form>
   <?php }
   else{?>
  <form method="post"><button type="submit" value="<?php echo $data['id']; ?>" name="cfspinsendmail" class="btn unstyled-button"><i class="fa fa-envelope text-warning" style="font-size:18px;" aria-hidden="true"></i></button></form>
  <?php }?>
                          </td>
                     
                    </tr>
                <?php
                $count++;
              }
            }
              ?>
                    <tr>
                      <td colspan="6" class="total-data" ><center> Total User: <?=$total_user; ?></center></td>
                    </tr>
            <!-- /keyword search -->
            </tbody>
          </table>
        </div>
        <div class="col-md-12 row nopadding">
      <div class="col-sm-6 mt-2">
        <?php
          $next_page_url="index.php?page=CFSpinner_allwheels&wheelstatus=wheels&wheelid=".$_GET['wheelid']."&page_count";
          $page_count=($page_count<2)? 0:$page_count;
          echo createPager($total_user,$next_page_url,$page_count);
        ?> 
      </div>
      <div class="col-sm-6 mt-2" style="text-align: end;">
  <form  method="POST" >
                        <button type="submit" class="btn theme-button" name="cfspinner_export_csv" value="<?php echo  $_GET['wheelid']; ?>"><i class="fas fa-file-download" ></i>&nbsp;Export CSV</button>
                    </form>
             </div>
    <?php  
        }
         ?>
        </div>
        
        </div>
      </div>
  </div>  
</div>
</div>
<input type="hidden" id="cfexito_ajax" value="<?php echo get_option("install_url") ?>/index.php?page=ajax">
<script>
  document.getElementById("commoncontainerid").innerHTML="<?php echo t($headertitle); ?>";
    document.getElementById("commmonreviewid").innerHTML="<?php echo t($headercontent); ?>";
function SearchForms(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}




</script>




