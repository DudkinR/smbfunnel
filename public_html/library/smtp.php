<?php

class Smtp
{
	var $mysqli;
	var $dbpref;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
	}

    
   
      function insert($title,$fromemail,$hostname,$port,$username,$password,$encryption,$fromname,$replyname,$replyemail)

{
               $mysqli=$this->mysqli;
               $pref=$this->dbpref;
               $date=time();
 $sql="INSERT INTO `".$pref."quick_smtp_setting`(title,hostname,port,encryption,fromname,fromemail,username,password,replyname,replyemail,created_at)VALUES('".$title."','".$hostname."','".$port."','".$encryption."','".$fromname."','".$fromemail."','".$username."','".$password."','".$replyname."','".$replyemail."','".$date."')";

           if ($mysqli->query($sql) === TRUE)
           {
             // echo"insert"; 
              return 1; 
            }
            else{
             // echo"exist";  
    	           return 0;
                 }

             // }
       }



       function edit($id,$title,$fromemail,$hostname,$port,$username,$password,$encryption,$fromname,$replyname,$replyemail)
       {
                  $mysqli=$this->mysqli;
                   $pref=$this->dbpref;
                   $date= date("d-M-y h:ia");
          
                   $sql="UPDATE `".$pref."quick_smtp_setting` SET title='".$title."',fromemail='".$fromemail."',hostname='".$hostname."',port='".$port."',username='".$username."',password='".$password."' ,encryption='".$encryption."',fromname='".$fromname."',replyname='".$replyname."',replyemail='".$replyemail."'  WHERE id=".$id;
                  // echo $sql;
          if ($mysqli->query($sql) === TRUE) 
          {
               return 1;
               //echo "updated";
          }
          else
          {
              return 0;
          }
       }

       function getLastId()
       {
        $mysqli = $this->mysqli;
        $pref = $this->dbpref;
        $selectmax = "select max(id) from `".$pref."quick_smtp_setting`";
        $sel = $mysqli->query($selectmax);
        // print_r($sel);
        while($resul = $sel->fetch_assoc()){
          $gettheid = $resul['max(id)'];
          return $gettheid;
        }
       }
}
?>