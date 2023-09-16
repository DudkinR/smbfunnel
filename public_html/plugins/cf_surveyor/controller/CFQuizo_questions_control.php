<?php
if(!class_exists('CFQuizo_questions_control'))
{
    class CFQuizo_questions_control
    {
        function __construct($arr)
        {
            $this->loader=$arr['loader'];
        }
        
        //to delete a question
        function delete_a_question($cfques_id,$quiz_id)
        {         
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_questions2';
            $cfques_id=$mysqli->real_escape_string($cfques_id);
            $result=$mysqli->query("delete from `".$table."` where `id`=".$cfques_id." and `quiz_id`=".$quiz_id.";");
            return $result;
        }

        //load a question into input boxes before edit 
        function load_before_edit($cfques_id,$quiz_id)
        {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'cfquiz_questions2';
            $cond="";
            if($quiz_id)
            {
                $quiz_id=$mysqli->real_escape_string($quiz_id);
                $cond=" where `quiz_id`=".$quiz_id." and `id`=".$cfques_id;
            }  
            $qry=$mysqli->query("SELECT `id`, `quiz_id`, `question_pos`, `question`, `options`, `added_on` FROM `".$table."`".$cond." order by `id` desc");
            return $qry;
        }

      //It will insert the question into database 
      function insert_question()
      {
      $cfquizo_question=$_POST['cfquizo_question'];
      $quiz_id=$_GET['cf_quizid_ques'];
      $arr_options=array();
      foreach ($_POST as $key => $value) {
      if($key=='hold_edit_question_id'||$key=='cfquizo_question'||$key=='que_submit')
      {
      }
      else
      {
      $arr_options[$key] = base64_encode($value);
      }
      }
      $arr_options=json_encode($arr_options);
      global $mysqli;
      global $dbpref;
      $date=date('Y-m-d H:i:s');
      $question_pos=1;
      $table=$dbpref.'cfquiz_questions2';
      $quiz_id=$mysqli->real_escape_string($quiz_id);
      $question_pos=$mysqli->real_escape_string($question_pos);
      $cfquizo_question=base64_encode($cfquizo_question);
      $date=$mysqli->real_escape_string($date);
      $sql = "INSERT INTO `".$table."`(`quiz_id`, `question_pos`, `question`, `options`, `added_on`) VALUES (".$quiz_id." ,".$question_pos.",'".$cfquizo_question."','".$arr_options."','".$date."')";
      $return_insert = $mysqli->query( $sql )?1:-1;
      return $return_insert;
      }

      //when the edit question button is clicked to update..
      function edit_question()
      {
      $question_id=$_POST['hold_edit_question_id'];
      $cfquizo_question=$_POST['cfquizo_question'];
      $quiz_id=$_GET['cf_quizid_ques'];
      foreach ($_POST as $key => $value) {
          if($key=='hold_edit_question_id'||$key=='cfquizo_question'||$key=='que_edit_submit')
          {
          }
          else
          {
             $arr_options[$key] = base64_encode($value);
          }
      }
      $arr_options=json_encode($arr_options);
      $question_pos=1;
      global $mysqli;
      global $dbpref;
      $date=date('Y-m-d H:i:s');
      $table=$dbpref.'cfquiz_questions2';
      $quiz_id=$mysqli->real_escape_string($quiz_id);
      $cfquizo_question=base64_encode($cfquizo_question);
      $arr_options=$mysqli->real_escape_string($arr_options);
      $question_id=$mysqli->real_escape_string($question_id);
      $sql="UPDATE `".$table."` SET `question`='".$cfquizo_question."',`options`='".$arr_options."' WHERE `id`=".$question_id." and `quiz_id`=".$quiz_id;
      $return_update = $mysqli->query( $sql )?1:-1;
      return $return_update;
      }//edit function ends
      
      //It will show all questions at admin side
      function show_all_question($quiz_id)
      {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'cfquiz_questions2';
            $cond="";
            $quiz_id=$mysqli->real_escape_string($quiz_id);
            $cond=" where `quiz_id`=".$quiz_id;
            $qry=$mysqli->query("SELECT `id`, `quiz_id`, `question_pos`, `question`, `options`, `added_on` FROM `".$table."`".$cond." order by `id` desc");
            return $qry;
      }


    }//class ends
}//if exist class ends        
  
?>