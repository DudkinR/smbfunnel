<?php
  $header_content= $quiz_data['header_text'];
  $quiz_name= $quiz_data['quiz_name'];
  $footer_content= $quiz_data['footer_text'];
  $quiz_setup= json_decode($quiz_data['quiz_setup']);
  $fid= $quiz_data['id'];
  $custom_css=$quiz_data['quiz_css'];
  $div_id=time();
  $div_id .=$fid;
  $div_id =str_shuffle($div_id.'sdfghjkvbnijh');

  $delay_time =(int)(( isset( $quiz_setup->cfquizo_delay_time ) && is_numeric($quiz_setup->cfquizo_delay_time) ) ? $quiz_setup->cfquizo_delay_time : 1);

  $header_b_color = ( !empty( $quiz_setup->cfquizo_header_b_color) ) ? $quiz_setup->cfquizo_header_b_color: "#2400eab";

  $footer_b_color = ( !empty( $quiz_setup->cfquizo_footer_b_color) )  ? $quiz_setup->cfquizo_footer_b_color: "#333333";  

  $success_b_color = ( !empty( $quiz_setup->cfquizo_success_b_color ) ) ? $quiz_setup->cfquizo_success_b_color : "#ff0000";

  $success_t_color = ( !empty( $quiz_setup->cfquizo_success_t_color ) ) ? $quiz_setup->cfquizo_success_t_color : "#ff88ff";

  $submit_b_color = ( !empty( $quiz_setup->cfquizo_submit_b_color ) ) ? $quiz_setup->cfquizo_submit_b_color : "#ff0000";

  $submit_t_color = ( !empty( $quiz_setup->cfquizo_submit_t_color ) ) ? $quiz_setup->cfquizo_submit_t_color : "#ffffff";

  $submit_text = ( !empty( $quiz_setup->cfquizo_submit_text ) ) ? $quiz_setup->cfquizo_submit_text: "Submit Quiz";

  $allow_process_in_cf = ( !empty( $quiz_setup->allow_process_in_cf) ) ? $quiz_setup->allow_process_in_cf : "";

  $redirect_url = ( !empty( $quiz_setup->redirect_url) ) ? $quiz_setup->redirect_url : "";

  $bckground = ( !empty( $quiz_setup->cfquizo_bckground ) ) ? $quiz_setup->cfquizo_bckground : "no_bckground";

  $custom_bg_url_holder = ( !empty( $quiz_setup->custom_bg_url_holder ) ) ? $quiz_setup->custom_bg_url_holder : "";  

  $theme = ( !empty( $quiz_setup->cfquizo_theme ) ) ? $quiz_setup->cfquizo_theme : "theme_a";

  if($theme=="theme_a")
  {
  $theme="theme_question_a";  
  }
  if($theme=="theme_b")
  {
  $theme="theme_question_b";  
  }
  if($theme=="theme_c")
  {
  $theme="theme_question_c";  
  }
  if($theme=="theme_d")
  {
  $theme="theme_question_d";  
  }

/********************************************************************************************************************
*  This block is starts at 62 ends at line no. 257 this will show the theme_question_a related design.              *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_question_a")
{  

?>

<link rel="stylesheet" href="<?php echo plugins_url('../assets/css/'.$theme.'.css?v='.$config_version,__FILE__) ?>"/> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<div>
            <div class="header_text" style="background-color:#<?=$header_b_color?>">
              <?=$header_content; ?>
            </div>
</div>
<form action="" method="post" id="regForm">


<div class="row mb-3" id="show_questions" >


	<div class="col-lg-6">
	   <center> <h3 class="p-2 px-4 quiz_label">Select Your Answer Below:</h3></center>
     	     
<div class="mb-3 py-1">
  <div class="progress" id="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progbr">
    </div>
  </div>
<?php  
          $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="p-4 py-0 border tab">
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
           
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3 mx-5" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" >
                      
                      &nbsp;&nbsp;<input type="radio" style="opacity: 1;" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />
                      <div>'.htmlentities(base64_decode($opt_value)).'</div></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
?>
<div class="border tab" id="form_design"><center>
<input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
<input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                $header_count=0;
                echo "<h3>See your results</h3><br>";
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php

                  }
                  echo "<br><br>";
                }
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php } ?><br>
              <input type="hidden" name='submit_button' value='submit_button' >
                <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
</center>
</div>
				</div>
			
	</div>
  <?php
  if(strlen(($custom_bg_url_holder))>17   &&  $bckground=='custom_bg_url')
{ 
 
?>
 <div  class="col-lg-6 d-flex justify-content-center"><img id="imgctrl"  src="<?php echo $custom_bg_url_holder ?>"></div>
<?php

}
else
{

      if($bckground=='no_bckground')
      {
        echo '<div  class="col-lg-6 mt-5 d-flex justify-content-center align-items-center"><h1>No Background</h1></div>';
      }
      else
      { ?>
 <div  class="col-lg-6 mt-5"><img id="imgctrl" src="<?php echo plugins_url('../assets/image/'.$bckground.'.jpg',__FILE__) ?>"></div>

<?php
}

}
?>
</div>

           
            </form>
<div>
            <div class="footer_text" style="background-color:#<?=$footer_b_color?>"   >
              <?=$footer_content; ?>
            </div>
</div>

<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  //echo $data;
}

}//theme theme_question_a.css ends here.. 

/********************************************************************************************************************
*  This block is starts at 263 ends at line no. 444 this will show the theme_question_b related design.             * 
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_question_b")
{  

?>

<link rel="stylesheet" href="<?php echo plugins_url('../assets/css/'.$theme.'.css?v='.$config_version,__FILE__) ?>"/> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<div>
            <div class="header_text" style="background-color:#<?=$header_b_color?>">
              <?=$header_content; ?>
            </div>
</div>
<form action="" method="post" id="regForm">

<div class="row shadow mb-3" id="show_questions"  >

  <div class="col-lg-12">
     <center> <h3 class="p-2 px-4 quiz_label">Answer these simple questions.</h3></center>

        <div class="mb-3 py-1">
          <div class="progress" id="progress">
             <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progbr">
          </div>
        </div>
<?php  
          $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="p-4 py-0 border tab">
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
           
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" >
                      
                      &nbsp;&nbsp;<input type="radio" style="opacity: 1;" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />
                      &nbsp<div>'.htmlentities(base64_decode($opt_value)).'</div></center></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
      
?>

<div class="border tab" id="form_design"><center>
<input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
<input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                $header_count=0;
                echo "<h3 style='color:white;'>See your results</h3><br>";
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php } ?><br>
              <input type="hidden" name='submit_button' value='submit_button' >
                <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
                 </center>
            </div>
          </div>      
        </div>   
      </div>
           
            </form>
<div>
            <div class="footer_text" style="background-color:#<?=$footer_b_color?>"   >
              <?=$footer_content; ?>
            </div>
</div>

<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  //echo $data;
}

}//theme theme_question_b.css ends here.. 

/********************************************************************************************************************
*    This block is starts at 451 ends at line no. 625 this will show the theme_question_c related design.           *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_question_c")
{  

?>

<link rel="stylesheet" href="<?php echo plugins_url('../assets/css/'.$theme.'.css?v='.$config_version,__FILE__) ?>"/> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<div>
            <div class="header_text" style="background-color:#<?=$header_b_color?>">
              <?=$header_content; ?>
            </div>
</div>
<form action="" method="post" id="regForm">


<div class="row shadow mb-3" id="show_questions"  >

  <div class="col-lg-12">
     <center> <h3 class="p-2 px-4 quiz_label">Answer these simple questions.</h3></center>

              <div class="mb-3 py-1">
                <div class="progress" id="progress">
                   <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progbr">
                </div>
              </div>
<?php  
          $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="p-4 py-0 border tab">
                   <div class="mb-3" id="question"><center><b>';
             echo base64_decode($r->question);
             echo "</b></center></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" >
                      <center><input type="radio" style="opacity: 0;" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />
                      &nbsp<div>'.htmlentities(base64_decode($opt_value)).'</div></center></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
?>
<div class="border tab" id="form_design"><center>
<input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
<input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                $header_count=0;
                echo "<h3 style='color:white;'>See your results</h3><br>";
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php } ?><br>
              <input type="hidden" name='submit_button' value='submit_button' >
                <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
              </center>
            </div>
        </div>
    </div>
</div>
           
            </form>
<div>
            <div class="footer_text" style="background-color:#<?=$footer_b_color?>"   >
              <?=$footer_content; ?>
            </div>
</div>

<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  //echo $data;
}

}//theme theme_question_c.css ends here.. 

/********************************************************************************************************************
*    This block is starts at 625 ends at line no. 800 this will show the theme_question_d related design.           *
*                                                                                                                   *
*                                                                                                                   *
********************************************************************************************************************/
if($theme=="theme_question_d")
{

?>

<link rel="stylesheet" href="<?php echo plugins_url('../assets/css/'.$theme.'.css?v='.$config_version,__FILE__) ?>"/> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<div>
            <div class="header_text" style="background-color:#<?=$header_b_color?>">
              <?=$header_content; ?>
            </div>
</div>
<form action="" method="post" id="regForm">


<div class="row shadow mb-3" id="show_questions">

  <div class="col-lg-12">
     <center> <h3 class="p-2 px-4 quiz_label">Answer these simple questions.</h3></center>

       
<?php  
          $qry=$this->select_question_ui();
           if($qry->num_rows==0)
           {
              echo '<div class="col-sm-12">
                    <h1 class="text-center" style="opacity:0.8;">No Questions Found</h1>
                    </div>';
           }
           else
           {
            $counter=0;           
            while($r=$qry->fetch_object())
            {    
             $counter++;
             
             echo '<div class="p-4 py-0 tab">
<span id="counter_nu">'.$counter.'/'.$qry->num_rows.'</span>
                   <div class="mb-3" id="question"><b>';
             echo base64_decode($r->question);
             echo "</b></div>";
             $arr_opt=array();
             $arr_opt=json_decode($r->options);
             $i=0;
           
             foreach($arr_opt as $opt => $opt_value) {
             $i++;
             echo '<div class="mb-3" id="options" onclick="fun(lbel_for'.$counter.$i.'),nextPrev(1)" >
                      
                      &nbsp;&nbsp;&nbsp;&nbsp;'.$i.'&nbsp;&nbsp;<input type="radio" id=lbel_for'.$counter.$i.' name=opt@'.$counter.' value="'.htmlentities(base64_decode($opt_value)).'"  />
                      &nbsp<div>'.htmlentities(base64_decode($opt_value)).'</div></div>';
                    }
             echo "</div>"; 
           }//while loop over here
          }//else part now over 
      
?>

<div class="border tab" id="form_design"><center>
<input type="hidden" name="allow_process_in_cf" value="<?=$allow_process_in_cf; ?>"  >
<input type="hidden" name="redirect_url" value="<?=$redirect_url; ?>"  >
              <?php
                $fetch_data= $this->cfquizoGetquizInput( $fid );
                $header_count=0;
                echo "<h3 style='color:white;'>See your results</h3><br>";
                while ( $data  = $fetch_data->fetch_assoc() ) {
                  
                  if(in_array($data['type'], array('text', 'number', 'password', 'email')))
                  {
                    ?>
                
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"  
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />

                    <?php
                  }
                  else if(in_array(trim($data['type']), array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p')))
                  {
                    ++$header_count;
                    echo "<".trim($data['type'])." class='cfquizo-header cfquizo-header-".$header_count."'>".$data['title']."</".trim($data['type']).">";
                  }

                  else if( $data['type'] == "textarea" )
                  {
                      ?>
                
                    <textarea class="form-control"
                    name="user@<?=$data['name'];  ?>" 
                    placeholder="<?=$data['placeholder']; ?>"
                    title="<?=$data['title'] ?>"
                    <?php  if(  $data['required']  == 1 ){ echo "required"; } ?>
                    ></textarea> 
                    <?php
                  }
                  else if( $data['type'] == "radio" )
                  {
                      ?>
                
                    <label class='lbl-radio'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>"
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                    <?=$data['placeholder']; ?></label>
                    
                    <?php
                  }
                  else if( $data['type'] == "checkbox" )
                  {
                      ?>
                    <label class='lbl-checkbox'>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      value="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />&nbsp;
                        <?=$data['placeholder']; ?></label>
                    <?php
                  }
                  else {
                      ?>
                    <input class="form-control"
                      type="<?= $data['type'] ?>" 
                      name="user@<?=$data['name']  ?>" 
                      placeholder="<?=$data['placeholder'] ?>"
                      title="<?=$data['title'] ?>"   
                      <?php  if(  $data['required']  == 1 ){ echo "required"; } ?> 
                    />
                    <?php
                  }
                  echo "<br><br>";
                }
               ?>
              <?php if(isset($show_err) && strlen($show_err)>0){ ?>
              <center><p style="color: #e6005c;margin:1px;"><?php echo $show_err; ?></p></center>
              <?php } ?><br>
              <input type="hidden" name='submit_button' value='submit_button' >
                <button class="form-control" style="background-color:#<?=$submit_b_color;?>;color:#<?=$submit_t_color;?>;"  ><?php echo $submit_text; ?></button> 
              </center>
            </div>
        </div>
     </div>
</div>
           
            </form>
<div>
            <div class="footer_text" style="background-color:#<?=$footer_b_color?>"   >
              <?=$footer_content; ?>
            </div>
</div>

<script src="<?php echo plugins_url('../assets/js/user_question_script.js?v='.$config_version,__FILE__); ?>"></script>
<?php
if(strlen($custom_css)>0)
{
  $data="<style>".$custom_css."</style>";
  $data=str_replace('.this-quiz', '.cfquizo-modal-'.$div_id, $data);
  //echo $data;
}

}//theme theme_question_d.css ends here.. 


?>