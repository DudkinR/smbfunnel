<?php
class Multisuer
{
	var $mysqli;
  var $dbpref;
  var $load;
  var $user_ob;
  var $site_token;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
    $this->dbpref=$arr['dbpref']; 
    $this->load=$arr['load'];
    $this->user_ob=$this->load->loadUser();
    $this->site_token=get_option('site_token');
	}
        function currentIP()
    {//get current ip
        $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
      return  $ipaddress;
    }
      // function addUser($name,$email,$password) 
             function addUser($name,$email,$password,$permission,$current_user_pass) 

         {
             // $permission = implode(',', $_POST['permission']);

             $current_user_ob=$this->load->loadUser();
             $current_user_data= $current_user_ob->getUser($_SESSION['user'.$this->site_token]);
             if(!($current_user_data && password_verify($current_user_pass,$current_user_data->password)))
             {
               return false;
             }

               $mysqli=$this->mysqli;
               $pref=$this->dbpref;
                  //echo $email;
              $ip= self::currentIP();
               $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
               $date= time();

               $password = $mysqli->real_escape_string($password);
               $email = $mysqli->real_escape_string($email);
               $name = $mysqli->real_escape_string($name);
               $permission = $mysqli->real_escape_string($permission);
               // echo $permission;

               if (strpos($permission,"admin") !== false) {
                 $permission = "admin";
               }
               else{
                $permission = $permission;
               }

// $permissionupload=0;
// if(isset($permission) && (!empty($permission)))

 $fileuploaded=0;   


  if(isset($_FILES["fileToUpload"]["name"]) && (!empty($_FILES["fileToUpload"]["name"])))
  { 
  ++$fileuploaded;            
$target_dir = "assets/img/profile/";


$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
       // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        //echo "File is not an image.";
        $uploadOk = 0;
    }

// Check if file already exists
if (file_exists($target_file)) {
   // echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5120000) {
   // echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
   // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
   // echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
       // echo "Sorry, there was an error uploading your file.";
    }
}
}



if($fileuploaded>0)
{
  $fileupload=",`profile_picture`='".$target_file."'";
  $fileupload_insert=$target_file;

}
else
{
  $fileupload="";
  $fileupload_insert="";
}

               $sql="SELECT email FROM `".$pref."users` WHERE email='".$email."'";
                $result = $mysqli->query($sql);
               if ($result->num_rows > 0)
                {
                return 0;
                }
         else{
          $verifycode=time();
          $verifycode .=substr(str_shuffle('sxdcfgvhj15238sdxcfvgbhj358awsedrft'),0,5);
               $sql="INSERT INTO `".$pref."users` (`name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_lastsignin`, `ip_created`, `date_created`, `date_signin`,`permission`,`profile_picture`) VALUES ('".$name."','".$email."','".$password."','','".$verifycode."','','','".$ip."','".$date."','','".$permission."','".$fileupload_insert."')";
               $this->user_ob->isLoggedin(1);
              if ($mysqli->query($sql) === TRUE)
               {
                $insertedUserId = mysqli_insert_id($mysqli);
                $sql_adm="INSERT INTO `".$pref."administrator`(`id`, `user_id`, `type`) VALUES
                (NULL,'".$insertedUserId."','user')";
                $mysqli->query($sql_adm);
               return 1;
               }
                else 
               {
                return 0;
                }
             }
         }



          function editUser($id,$name,$email,$password,$permission,$current_user_pass,$role="user")
         {
  
          
          $current_user_ob=$this->load->loadUser();
          $current_user_data= $current_user_ob->getUser($_SESSION['user'.$this->site_token]);
          if(!($current_user_data && password_verify($current_user_pass,$current_user_data->password)))
          {
            return false;
          }
               $mysqli=$this->mysqli;
               $pref=$this->dbpref;
                  //echo $email;
              $ip= self::currentIP();
              $date= time();
              // add role parameter to add role in administrator table
              $sql_adm="UPDATE `".$pref."administrator` SET `type`='".$role."' WHERE `user_id`='".$id."'";
              $mysqli->query($sql_adm);
               $password = $mysqli->real_escape_string($password);
               $original_pass=$password;
               $email = $mysqli->real_escape_string($email);
               $name = $mysqli->real_escape_string($name);
               $id = $mysqli->real_escape_string($id);
               $permission = $mysqli->real_escape_string($permission);
               // echo $permission;

               if (strpos($permission,"admin") !== false) {
                 $permission = "admin";
          
               }
               else{
                $permission = $permission;
               }


  $fileuploaded=0;   


  if(isset($_FILES["fileToUpload"]["name"]) && (!empty($_FILES["fileToUpload"]["name"])))
  { 
  ++$fileuploaded;            
  $target_dir = "assets/img/profile/";

  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
       // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        //echo "File is not an image.";
        $uploadOk = 0;
    }

// Check if file already exists
if (file_exists($target_file)) {
   // echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5120000) {
   // echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
   // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
   // echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
       // echo "Sorry, there was an error uploading your file.";
    }
}
}



if($fileuploaded>0)
{

    $fileupload_insert=$target_file;  
$fileupload=",`profile_picture`='".$fileupload_insert."'";
}
else
{
  $fileupload="";
}
               //    //echo $email;
               // $sql="UPDATE `".$pref."users` SET `name`='".$name."',`email`='".$email."',`password`='".$password."' ,`verified`='',`verifycode`='1',`date_verifycodegen`='',`ip_lastsignin`='',`ip_created`='".$ip."',`date_created`='".$date."',`date_signin`='',`permission`='".$permission."'".$$fileupload."  WHERE id=".$id;
               $sql="SELECT email FROM `".$pref."users` WHERE email='".$email."' and `id` not in(".$id.")";
               $result = $mysqli->query($sql);
              if ($result->num_rows > 0)
               {
               return 0;
               }

               if(strlen($original_pass)>0)
               {
                $verifycode=time();
                $verifycode .=substr(str_shuffle('sxdcfgvhj15238sdxcfvgbhj358awsedrft'),0,5);
                $password = password_hash($original_pass,PASSWORD_DEFAULT);
                $sql="UPDATE `".$pref."users` SET `name`='".$name."',`email`='".$email."',`password`='". $password."' ,`verified`='',`verifycode`='".$verifycode."',`date_verifycodegen`='',`ip_lastsignin`='',`ip_created`='".$ip."',`date_created`='".$date."',`date_signin`='',`permission`='".$permission."'".$fileupload." WHERE id='".$id."'";
               }
               else
               {
                $sql="UPDATE `".$pref."users` SET `name`='".$name."',`email`='".$email."',`verified`='',`verifycode`='1',`date_verifycodegen`='',`ip_lastsignin`='',`ip_created`='".$ip."',`date_created`='".$date."',`date_signin`='',`permission`='".$permission."'".$fileupload." WHERE id='".$id."'";
               }
                 // echo $sql;      
          if ($mysqli->query($sql) === TRUE) 
          {
            $this->user_ob->isLoggedin(1); 
               return 1;
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
        $selectmax = "select max(id) from `".$pref."users`";
      $sel = $mysqli->query($selectmax);
      // print_r($sel);
      while($resul = $sel->fetch_assoc()){
        $gettheid = $resul['max(id)'];
        return $gettheid;
      }


       }
function check($email,$password,$permission)
       //  function check($email,$password)
{
  // echo $email;

          $mysqli=$this->mysqli; 
          $pref=$this->dbpref;
          $table="`".$pref."users`";

          $email = $mysqli->real_escape_string($email);
          $password = $mysqli->real_escape_string($password);

          $sql=$mysqli->query("SELECT id,password,permission FROM ".$table." WHERE email='".$email."'");
   if($sql->num_rows >0)
    {
      $data=$sql->fetch_object();
      $arrayy = explode(",", $data->permission);
      if(password_verify($password, $data->password))
      {
        // echo  "fffffffffffffffffffffffsssssssssssss" .$data->email;
         $focus=explode(",",$permission);
       // print_r($focus);
        foreach ($focus as $permission)
         {
         // echo $permission;
          if (in_array($permission, $arrayy))
           {
            echo "match";
              header("Location: index.php?page=".$permission);  
          }
          else{
            echo "not match";
          }
        }
    
     // echo"Success login";
      //  echo $data->permission;

      }
      else
      {
        return 0;
      }
    }
    else
   {
     return 0;
    }
}
}
    ?>