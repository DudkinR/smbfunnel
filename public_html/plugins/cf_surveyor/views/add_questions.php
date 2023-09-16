<?php 
$questions_ob=$this->load('questions_control');

//to delete a questions
if(isset($_POST['cfquizo_del_question']))
{
  $cfques_id=$_POST['cfquizo_del_question'];
  $quiz_id=$_GET['cf_quizid_ques'];
  $res=$questions_ob->delete_a_question($cfques_id,$quiz_id);
  if($res==1)
  {
    echo "<script>alert('Deleted successfully');</script>";
  }
  else 
  {
    echo "<script>alert('Could not delete the question due to Some Error');</script>";
  }
}

//load a question into input boxes before edit 
if(isset($_POST['cfquizo_edit_question']))
{
  $cfques_id=$_POST['cfquizo_edit_question'];
  $quiz_id=$_GET['cf_quizid_ques'];
  $qry=$questions_ob->load_before_edit($cfques_id,$quiz_id);
     if($qry->num_rows==0)
     {
        echo '<div class="col-sm-12">
              <h1 class="text-center" style="opacity:0.8;">Some Error, Please Try Again</h1>
              </div>';
     }
     elseif($qry->num_rows==1)
     { $counter=0;      
        while($r=$qry->fetch_object())
        {    $counter++;
             $edit_ques=$r->question;
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $arr_counter=0;
             $edit_ques=base64_decode($edit_ques);
             $edit_ques=addslashes($edit_ques);
        echo "
              <script>
              $(document).ready(function(){
              document.getElementById('que_submit').style.display = 'none';
              document.getElementById('que_edit_submit').style.display = 'block';             
              document.getElementById('add_edit').innerHTML = 'Edit Question';
              document.getElementById('cfquizo_ques').value = '".$edit_ques."';
              document.getElementById('hold_edit_question_id').value = '".$cfques_id."';
              });
              </script>";

        foreach($arr_opt as $opt => $opt_value) {
          $arr_counter++;
          $opt_value=base64_decode($opt_value);  
          $opt_value=addslashes($opt_value);
          echo "<script>
          $(document).ready(function(){
          var i=".$arr_counter.";
          var cfoptions=document.getElementById('options');
          var cfdiv = document.createElement('DIV');
          cfdiv.setAttribute('id','cfopt'+i);
          cfdiv.setAttribute('class','mb-3 py-0 mx-1');
          var cflabel=document.createElement('LABEL');
          cflabel.innerHTML='Option '+i;
          cfdiv.appendChild(cflabel);
          var cross=document.createElement('I');
          cross.setAttribute('class','fas fa-times-circle delinp text-end ms-5');
          cross.setAttribute('counter',i);
          cross.setAttribute('onclick','delete_option('+i+');');
          cfdiv.appendChild(cross);
          cfdiv.appendChild(document.createElement('BR'));
          var cfinp = document.createElement('INPUT');
          cfinp.setAttribute('type', 'text'); 
          cfinp.setAttribute('counter',i);
          cfinp.setAttribute('id','opt'+i);
          cfinp.setAttribute('name','opt'+i);
          cfinp.setAttribute('class','form-control');
          cfinp.setAttribute('value','$opt_value');
          cfdiv.appendChild(cfinp);
          cfoptions.appendChild(cfdiv);
          var number=document.getElementById('number');
          number.value=".$arr_counter.";
          });
          </script>";
        }
          echo "</div>";
        }    

     }
}

//Nobody can see this page if quiz id is not set.. 
if(isset($_GET['cf_quizid_ques']))
{
  $quiz_id=$_GET['cf_quizid_ques'];
}
else
{
  echo "Some Error";
  header("Location: index.php?page=cfquiz_all_quizs");
}

//It will insert the question into database 
 if(isset($_POST['que_submit']))
 {
    $return_insert=$questions_ob->insert_question();     
      if( $return_insert == 1 ){
         echo "<script>alert('Question Inserted Successfully');</script>";
      }
      else 
      {
         echo "<script>alert('Question Not Inserted - Some Error');</script>";
      }
 }      

//when the edit question button is clicked to update..
if(isset($_POST['que_edit_submit']))
{  
      $return_update=$questions_ob->edit_question();     
      if( $return_update == 1 ){
      echo "<script>alert('Question Updated Successfully');</script>";
      }
      else 
      {
      echo "<script>alert('Question Not Updated - Some Error');</script>";
      }               
}

?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Surveyor Questions Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
          <div class="d-flex justify-content-end align-items-center">Create, Edit, Manage Survey Questions</div>
      </div>
  </div>  
    <form  action="" method="post" id="cfquizo_AddQuestion"  autocomplete="off">
        <div class="row bg-white shadow p-0 m-0 mb-3 ">
          <div class="col-lg-12 mt-4">
            <h4 class="text-primary" id="add_edit" class="p-2 px-4">Add Questions</h4>
              <div class="p-4 py-0 border">
                <div class="mb-3 py-1">
                  <input type="hidden" id="hold_edit_question_id" name="hold_edit_question_id">
                  <label for="">Enter Question   </label>
                  <input type="text" name="cfquizo_question" required id="cfquizo_ques" class="form-control" placeholder="Enter Question">
                </div>
                  <p onclick="add_options()" id="hide_option" class="btn btn-primary">Add Options</p>                
                <div id="options">
                  <input type="hidden" id="number" value="0"/>
                </div><!-- options div ends-->
       
                <center>    
                  <input type="submit" value="Add Question" name="que_submit" id="que_submit">
                  <input type="submit" value="Edit Question" name="que_edit_submit" id="que_edit_submit" style="display:none;">
                </center>
            </div>
           <br>
          </div>
        </div>
    </form>
   
<div class="row bg-white shadow p-0 m-0 mb-3 ">
	<div class="col-lg-12 mt-4">
	    <h4 class="text-primary  p-2 px-4">All Questions</h4>
	     	<div class="p-4 py-0 border">
	        <div class="mb-3 py-1">
          <?php  
           if($quiz_id)
           {
           $qry=$questions_ob->show_all_question($quiz_id);

           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found, Lets Add Some Quiz Questions</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             echo '<div class="p-4 py-0 border">
                   <div class="mb-3 py-0">';
             echo "<br>&nbsp;Question &nbsp;".$counter."&nbsp;:&nbsp; "; 
             echo base64_decode($r->question);
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             echo "</div>";
             $i=0;
           
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3 py-0 mx-1">
                      <label>Option '.$i.'  </label>
                      <input type="text"  required  class="form-control" value="';
                      echo htmlentities(base64_decode($opt_value));  
                      echo '" readonly></div>';
                    }

             echo "<div class='d-inline-flex'>";
             echo "<form action='' method='POST'><button class='btn unstyled-button' name='cfquizo_edit_question' value='".$r->id."'><i class='fas fa-edit text-primary'></i></button></form>";
             echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
             echo "<form action='' method='POST' onsubmit='return confirm(\"Are you sure you want to delete?\");'><button class='btn unstyled-button' name='cfquizo_del_question' value='".$r->id."'><i class='fas fa-trash text-danger'></i></button></form></div>";
             echo "</div>";
           }//while loop over here
          }//else part now over 
        }
?>
				</div>
			</div>
	</div>
</div>
</div>
<script src="<?php echo plugins_url('../assets/js/user_question_script.js',__FILE__); ?>"></script>