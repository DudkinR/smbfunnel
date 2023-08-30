<?php
    class Media_control
    {
        var $mysqli;
	    var $dbpref;
        var $load;
        var $base_dir;
        var $media_dir;
        var $media_dir_url;
        var $table;
        var $user_id;
        var $access;
        var $where_data;
        var $add_user;
        var $add_vue;

        
        
        function __construct($arr)
        {

            $this->mysqli=$arr['mysqli'];
            $this->dbpref=$arr['dbpref'];
            $this->load=$arr['load'];

            $this->table= $this->dbpref.'media';
        $this->user_id = $_SESSION['user' . get_option('site_token')];
		$this->access = $_SESSION['access' . get_option('site_token')];
		if($this->access == 'admin')
		{
			$this->where_data = " where 1";
			$this->add_user = " ";
            $this->add_vue = " ";

		}
		else
		{
			$this->where_data = " where `user_id` = '".$this->user_id."'";
			$this->add_user = " and `user_id` = '".$this->user_id."'";
            $this->add_vue = " `user_id` = '".$this->user_id."' and ";
		}

            if(isset($arr['base_dir']))
            {
                $this->base_dir= $arr['base_dir'];
            }

            $media_folder= '/assets/media';
            $this->media_dir=  $this->base_dir.$media_folder;

            $this->media_dir_url= get_option('install_url').$media_folder;
            if(!cf_dir_exists($this->media_dir))
            {
                mkdir($this->media_dir);
            }
        }
        function hasFile($file)
        {
            $file= $this->mysqli->real_escape_string($file);
            $main_file=$this->media_dir.'/'.$file;
            $exists= (is_file($main_file))? true: false;
            $exists_in_db=false;
            $qry=$this->mysqli->query("select `id` from `".$this->table."` where ".$this->add_vue." `file`='".$file."' limit 1");
            if($qry->num_rows>0)
            {
                $exists_in_db= true;
            }
            if($exists && $exists_in_db)
            {return true;}
            else if(!$exists && !$exists_in_db)
            {return false;}
            else
            {
                if($exists_in_db)
                {
                    $this->mysqli->query("delete from `".$this->table."` where ".$this->add_vue."  `file`='".$file."'");
                }
                else
                {
                    unlink($main_file);
                }
                return false;
            }
        }
        function getTypes()
        {
            return array(
                'image'=> array('jpg', 'jpeg' , 'png', 'gif', 'svg'),
                'audio'=> array('mp3', 'wma', 'aac', 'wav', 'flac','ogv'),
                'video'=> array('flv', 'mp4', 'm3u8', 'ts', '3gp', 'mov', 'avi', 'wmv'),
                'document'=> array('doc', 'docx', 'html', 'htm', 'odt', 'pdf', 'xls', 'xlsx', 'ods', 'ppt', 'pptx', 'txt', 'csv', 'zip', 'tar', 'rar'),
            );
        }
        function doInitInFrontend()
        {
            $types=array_keys(self::getTypes());
            $types=array_merge(array('all'), $types, array('others'));
            $arr=array(
                'media_url'=> $this->media_dir_url,
                'types'=> $types,
                'files'=>self::getAssets(),
                'max_files_per_page'=> (int)get_option('qfnl_max_records_per_page')
            );
            return $arr;
        }
        function getFileType($file)
        {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            $type_arr= self::getTypes();
            
            $type='other';
            foreach($type_arr as $index=>$types)
            {
                if(in_array($ext, $types))
                {
                    $type= $index;
                    break;
                }
            }
            return $type;
        }
        function uploadAsset($file)
        {
            $title=trim($this->mysqli->real_escape_string($file['name']));
            $main_type= $file['type'];
            $size= $file['size'];
            $tmp_file=$file['tmp_name'];
            $name=str_replace(' ','_', $title);
            $count=1;
            $user_id = $_SESSION['user' . get_option('site_token')];

            $temp_main_name=$name;
            lbl:
            if(self::hasFile($name))
            {
                ++$count;
                $name_arr=explode('.', $temp_main_name);
                if(count($name_arr)>1)
                {
                    $name_arr[count($name_arr)-2] .="(".$count.")"; 
                }
                $name= implode('.', $name_arr);
                goto lbl;
            }

            $destin=$this->media_dir.'/'.$name;
            $moved=move_uploaded_file($tmp_file, $destin);
            if($moved)
            {
                $type=self::getFileType($destin);
                $in=$this->mysqli->query("insert into `".$this->table."` (`title`, `file`, `type`,`file_type`, `size`, `description`, `added_on`, `updated_on`,`user_id`) values ('".$title."', '".$name."', '".$type."','".$main_type."', '".$size."', '', '".time()."', '".time()."', '".$user_id."')");
                if($in)
                {return 1;}
            }
            return 0;
        }
        function getAssets($by='all', $page=1)
        {
            $mysqli= $this->mysqli;
            $by= $mysqli->real_escape_string($by);
            $page=$mysqli->real_escape_string($page);

            $max= 10 /*(int)get_option('qfnl_max_records_per_page')*/;
            $page= (int)$page;
            $page =($page*$max)-$max;

            $limit_txt= " limit ".$page.",".$max."";
            $search="";
            if(isset($_POST['do_search']))
            {
                $limit_txt="";
                $search=$mysqli->real_escape_string(trim($_POST['do_search']));
                $search =str_replace("%", "[%]", $search);
                $search= str_replace('_', '[_]', $search);
                $search =" and `title` like '%".$search."%' or `file` like '".$search."'";
            }

            if(isset($_POST['select_order']))
            {
                $order=$mysqli->real_escape_string(trim($_POST['select_order']));
                $limit_txt= "  order by `id` ".$order.$limit_txt;
            }
            $by=($by=='all')? 1: "`type` ='".$by."'";
            $sql_text="select * from `".$this->table."` where  ".$this->add_vue." ".$by.$search.$limit_txt;
            $qry=$mysqli->query($sql_text);

            $arr=array();

            while($r= $qry->fetch_object())
            {
                $file=$r->file;
                unset($r->file);
                $r->added_on= date('d-M-Y h:ia', $r->added_on);
                $r->updated_on= date('d-M-Y h:ia', $r->updated_on);
                $arr[$file]= $r;
            }

            return $arr;
        }
        function deleteAsset($file)
        {
            $mysqli=$this->mysqli;
            $file= $mysqli->real_escape_string($file);
            unlink($this->media_dir.'/'.$file);
            $mysqli->query("delete from `".$this->table."` where ".$this->add_vue."  `file`='".$file."'");
        }
        function updateFileBasicData($file, $title, $description)
        {
            $mysqli= $this->mysqli;
            $table= $this->table;
            $file= $mysqli->real_escape_string($file);
            $title= $mysqli->real_escape_string($title);
            $description= $mysqli->real_escape_string($description);

            $up=$mysqli->query("update `".$table."` set `title`='".$title."', `description`= '".$description."' where  ".$this->add_vue." `file`='".$file."'");
            echo (($up)? 1:0);
        }
    }
?>