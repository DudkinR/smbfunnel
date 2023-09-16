 <div class="row bg-white shadow p-0 m-0 mb-3 " style="width:100%;">
    <div class="col-lg-12 mt-4">
        <h4 class="text-primary  p-2 px-4">All Responses</h4>
            <div class="p-4 py-0 border mb-4">
                <div class="mb-3 py-1"> 
<?php  
$optins=array();
$has_quiz_id=false;
$total_leads=0;
$optin_ob=$this->load('optin_control');

if(isset($_POST['cfquizo_del_optin']))
{
    $optin_ob->deleteResponse($_POST['cfquizo_del_optin']);
}
  global $mysqli;
  global $dbpref;
  $table=$dbpref.'cfquiz_response';
  $cond="";
  
if(isset($_GET['cf_quizid_resp']))
{
$quiz_id=$_GET['cf_quizid_resp'];
$page_count=1;
$has_quiz_id=true;
    if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
    {
        $page_count=(int)$_GET['page_count'];
    } 

    ob_start();
    echo '<div class="row">
                    <div class="col-md-2  mb-2">
                    '.createSearchBoxBydate().'
                    </div>
                    <div class="col-md-3">
                    '.showRecordCountSelection().'
                    </div>
                    <div class="col-md-3">'.arranger(array('id'=>'date')).'
                    </div>
                    <div class="col-md-4">
                    <div class="quiz-group">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend ">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                             <input type="text" class="quiz-control quiz-control-sm" placeholder="'.t('Enter title, type or credentilas').'" onkeyup="searchPaymentMethods(this.value)">
                        </div>
                    </div>
                    </div>
                </div>';
    $table_manager=ob_get_clean();

    $max_leads_limit=(int)get_option('qfnl_max_records_per_page');
    $optins=$optin_ob->getLeads22($_GET['cf_quizid_resp'], $max_leads_limit, $page_count);
    $qry=$optins;
    $total_leads=$optin_ob->getLeadsCount($_GET['cf_quizid_resp']);

      
      
      $quiz_name = $optin_ob->getQuizName($quiz_id);
      echo "Survey Name: ".$quiz_name;
      //print_r($quiz_name);

            if($quiz_id)
            {
                $quiz_id=$mysqli->real_escape_string($quiz_id);
                $cond=" where `quiz_id`=".$quiz_id;
            }  
           if($mysqli->affected_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Response Found</h1>
                    </div>';
           }
           else
           {
            echo $table_manager;
            ?>
            <div id="keywordsearchresult">
              <!-- keyword search -->   
            <?php
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             echo '
                   <div class="mb-3 py-0">';

            echo "<br>&nbsp;Response &nbsp;".$counter."&nbsp;";
             $user_details=json_decode($r->user_details);
echo '<div class="  ">';
echo '<div class="card bg-primary" style="width: 100%;">
  <div class="card-body">
    <h5 class="card-title text-white">User Details</h5>
  </div>
  <div>
  <ul class="list-group list-group-flush">';


            foreach($user_details as $key => $value) {
echo '<li class="list-group-item">';
            echo ucfirst($key).": ".$value."</li>";
             }
             echo "<li class='list-group-item'>Added on: ".$r->added_on; 
             $arr_opt=json_decode($r->quiz_response);
             echo "</li>
             </ul>
</div>
             </div>";
             $i=0;
           
echo '<div class="card bg-primary " style="width: 100%;">
  <div class="card-body d-flex" data-bs-toggle="collapse" href="#cow'.$counter.'">
    <h5 class="card-title text-white" >Surveyor Response</h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-eye" aria-hidden="true"></i>
  </div>
  <ul class="list-group list-group-flush collapse" id="cow'.$counter.'">';

             foreach($arr_opt as $res => $res_value) {
             $i++;
             echo '<li class="list-group-item">';
             echo 'Question '.$i.' :';
                      echo htmlentities(base64_decode($res_value));  
                      echo '</li>';}

             echo '</ul></div>';
                    
echo "</div>";


echo "<form action='' method='POST' onsubmit='return confirm(\"Are you sure you want to delete?\");'><button class='btn unstyled-button' name='cfquizo_del_optin' value='".$r->id."'><i class='fas fa-trash text-danger'></i></button></form>";
             echo "</div>";
           }//while loop over here
           ?>
           <!-- /keyword search -->
           <?php
           echo '</div>
           <p class="text-center" colspan=10>Total Survey Responses: '.$total_leads.'</p>';
          }//else part now over 
?>

    <div class="row mt-4">
                <div class="col">
                    <?php
                        $next_page_url="index.php?page=cfquiz_all_optins&cf_quizid_resp=".$_GET['cf_quizid_resp']."&page_count";
                       $page_count=($page_count<2)? 0:$page_count;
                  
                        echo createPager($total_leads,$next_page_url,$page_count);
                    ?>
                </div>
                <div class="col text-end">
                    <form action="" method="POST" target="_BLANK">
                        <button type="submit" class="btn theme-button" name="cfquiz_export_csv" value="<?php echo $_GET['cf_quizid_resp']; ?>"><i class="fas fa-file-download"></i>&nbsp;Export CSV</button>
                    </form>
                </div>
            </div>
<?php 
}
else
{
  ?>
   <div class="row justify-content-center align-items-center">
                        <div class="col-sm-4">
                            <div class="card pnl">
                                <div class="card-header">Select Survey</div>
                                <div class="card-body" style="max-height:300px; overflow: auto;">
                                    <?php
                                        $quizs_ob=$this->load('quizs_control');
                                        $quizs=$quizs_ob->getMiniquizs();
                                        //print_r($quizs);
                                        if($quizs!=0)
                                        {
                                        foreach($quizs as $quiz_id=>$quiz_name)
                                        {
                                            echo "<a href='index.php?page=cfquiz_all_optins&cf_quizid_resp=".$quiz_id."'><button class='btn btn-light btn-block mb-2'>".$quiz_name."</button></a>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
<?php 
}
?>
                 </div>
            </div>
    </div>
</div>
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>