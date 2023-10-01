

<?php
global $mysqli;
if(isset($_POST['autodelete']))
{ global $mysqli;
    global $dbpref;
    $table= $dbpref.'cfsendfox_autoresponders';
    $idd = $_POST['autodelete'];
    $delete ="delete from `".$table."` where id='".$idd."'";
 $mysqli->query($delete);
}

$total_autoresponders=0;
$forms_ob=$this->load('forms_control');

    $page_count=1;
    if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
    {
        $page_count=(int)$_GET['page_count'];
    }
    ob_start();
   
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
                <input type="text" class="form-control form-control-sm" placeholder="Enter Title" onkeyup="SearchForms(this.value)">              </div>
            </div>
          </div>';

    $table_manager=ob_get_clean();
     $max_leads_limit=(int)get_option('qfnl_max_records_per_page');
    $total_autoresponders=$forms_ob->getAutoresponderCount();
    $forms=$forms_ob->getAllautores($total_autoresponders, $max_leads_limit, $page_count);
   
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Sendfox  Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">Manage  Sendfox</div>
      </div>
  </div>
  <div class="card pb-2  br-rounded">
      <div class="card-body pb-2">
        <div class="row">
          <?=$table_manager;  ?>
          <div class="col-sm-12 nopadding">
         
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
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
              <td> 
<?php  echo $data['title'];  ?></a></td>
<td><?php echo $data['type'];?></td>
                   

              <td><?php echo date('d-M-y h:ia', strtotime($data['date_created'])); ?></td>
              <td><form method="post">
              <a href="index.php?page=cfsendfox_Setting&autoid=<?php echo $data['id'];?>">    <i class="fas fa-edit text-primary"></i> &nbsp;</a>
<button type='submit'  value="<?php echo $data['id'];?>"name="autodelete" class="btn unstyled-button"> <i class="fas fa-trash text-danger"></i></button></form>
              </td>
            </tr>
        <?php
        $count++;
      }
    }
      ?>
                
                        <tr>
                          <td colspan="6" class="total-data" ><center> Total Autoresponders: <?php echo $total_autoresponders;?></center></td>
                        </tr>
                <!-- /keyword search -->
                </tbody>
              </table>

            </div>
            <!-- <div class="col-md-12 row nopadding">
          <div class="col-sm-6 mt-2"> -->
          <div class="row mt-4">
                        <div class="col">
            <?php
              $next_page_url="index.php?page=cfsendfox_setups&page_count";
              $page_count=($page_count<2)? 0:$page_count;
              echo createPager($total_autoresponders,$next_page_url,$page_count);
            ?> 
          </div>
          <div class="col text-end">
                            <a href="index.php?page=cfsendfox_Setting"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i>&nbsp;Create New</button></a>
                        </div>
            <?php

?>
      
          </div>
          
</div>
</div></div></div></div>







          
<script>

function SearchForms(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}




</script>




