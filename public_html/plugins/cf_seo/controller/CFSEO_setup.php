<?php

if(!class_exists('cfseo_setup'))
{
  class cfseo_setup
  {
    var $pref="cfseo_";
    
    function __construct($arr)
    {
        $this->loader=$arr['loader'];
    }
	
	function getAllSetups($total_forms=false,$max_limit=false,$page=1)
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.$this->pref."setup";
	  $pref="smbf_";
	  	$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access !== 'admin' ){
			$select_funnels=" SELECT * FROM `".$pref."quick_funnels` as `funnels` 
		INNER JOIN `".$pref."user_funnel` as `uf` 
		ON `funnels`.`id` = `uf`.`funnel_id` 
		WHERE `uf`.`user_id`= ".$user_id;
		}
		else{
		$select_funnels=" SELECT * FROM `".$pref."quick_funnels` WHERE 1";
		}
		//echo $select_funnels;
		$funnels_query=$mysqli->query($select_funnels);
		$funnels="AND (";
		if($funnels_query->num_rows>0){
			while($funnel = $funnels_query->fetch_assoc()){
				if($funnels=="AND (")
				{
					$funnels .=" `page_url` LIKE '%/".$funnel[name]."/%' ";
				}
				else{
					$funnels .=" OR `page_url` LIKE '%/".$funnel[name]."/%' ";
				}
			}
		}
		$funnels .=")";
      $page=$mysqli->real_escape_string($page);
      if(!$max_limit)
      {$max_limit=$mysqli->real_escape_string($max_limit);}

      $arr=array();
      $limit="";

      if($max_limit !==false && is_numeric($max_limit) && is_numeric($page))
      {
        $page=($page*$max_limit)-$max_limit;
        $limit =" limit ".$page.','.$max_limit;
      }
          /////////////////////////////
      $search="";
      if(isset($_POST['onpage_search']))
      {
        $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
        $search=str_replace('_','[_]',$search);
        $search=str_replace('%','[%]',$search);
		// filter data with page name and page url and date
		$search=" AND `page_name` LIKE '%".$search."%' OR `page_url` LIKE '%".$search."%'  OR `added_on` LIKE '%".$search."%'";
      }
      $order_by="`id` DESC";
      if(isset($_GET['arrange_records_order']))
      {
        $order_by=base64_decode($_GET['arrange_records_order']);
	  }
      $date_between=dateBetween('added_on',null,true);
      if(strlen($date_between[0])>0)
      {
          $search .=$date_between[1];
      }
      //////////////////////////////
	  $sqlQ="SELECT * FROM `".$table."` WHERE 1".$search." ".$funnels." ORDER BY ".$order_by.$limit;
	  $qry=$mysqli->query($sqlQ);

      $arr=[];
      if($qry->num_rows>0)
      {
        while($data = $qry->fetch_assoc() )
        {
          $arr[]=$data;
        }   
	  }
      return $arr;

    }

    function getSetupsCount()
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.$this->pref.'setup';

      $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."`");

      if($qry->num_rows > 0 ){
          $r=$qry->fetch_object();
          return $r->total_setup;
      }
    }

    // this function get all data from webmaster data
    public function getWebmaster(){
      global $mysqli;
      global $dbpref;
      $table=$dbpref.$this->pref."webmaster";
      $qry = "SELECT * FROM `".$table."` ";
      $r = $mysqli->query( $qry );
      $new_data=[];
      if($r->num_rows >0 ){
        $data = $r->fetch_assoc();
        // return $data;
        $dom = new DOMDocument(); 
          
        // Load the XML

        $verification_code='';
        if(!empty($data['google'])){
		   $google_v = rtrim($data['google'],"/>");
          $verification_code.=$google_v."></meta>";
        }
        if(!empty($data['bing'])){
			$bing_v = rtrim($data['bing'],"/>");
          $verification_code.=$bing_v."></meta>";
        }
        if(!empty($data['yandex'])){
			$yandex_v = rtrim($data['yandex'],"/>");
          $verification_code.=$yandex_v."></meta>";
        }
        if(!empty($data['baidu'])){
			$baidu_v = rtrim($data['baidu'],"/>");
          $verification_code.=$baidu_v."></meta>";
		}
		print_r($verification_code);
        $dom->loadXML("<?xml version=\"1.0\"?> 
          <body> 
            ".$verification_code."
          </body>");  
        // Get all the div elements 
        $elements = $dom->getElementsByTagName('meta');
        foreach ($elements as $element) { 
          $name = $element->getAttribute('name');
          if( stristr( $name, "google" ) )
          {
            $new_data['google'] = $element->getAttribute('content');
          }
          elseif( stristr( $name, "msvalidate" ) )
          {
            $new_data['bing'] = $element->getAttribute('content');
          }
          elseif( stristr( $name, "yandex" ) )
          {
            $new_data['yandex'] = $element->getAttribute('content');
          }
          elseif( stristr( $name, "baidu" ) )
          {
            $new_data['baidu'] = $element->getAttribute('content');
          }
        }
      return $new_data; 
      }
      return [];
    }

    public function getSocialData(){
      
      global $mysqli;
      global $dbpref;
      $table=$dbpref.$this->pref."social";
      $qry = "SELECT * FROM `".$table."` ";
      $r = $mysqli->query( $qry );
      
      if($r->num_rows >0 ){
        $datas = $r->fetch_assoc();
        return $datas;
      }
      return [];

	}
	
	// this function show seo return robots file
	public function getRobotsFile(){
		$robots_file = file( plugin_dir_path(dirname(__FILE__,3)).'robots.txt' );
		return $robots_file;


	}

    public function handleFormData($form_data=null){
		global $mysqli;
		global $dbpref;
		$table=$dbpref.$this->pref."setup";
		$install_url=get_option("install_url");
		$cfseo_data=[];
		$cfseo_robots_file=$mysqli->real_escape_string($form_data['cfseo_robots_file']);
		$cfseo_custom_meta_tag=$mysqli->real_escape_string($form_data['cfseo_custom_meta_tag']);
		$cfseo_page_name=$mysqli->real_escape_string($form_data['cfseo_page_name']);
		$cfseo_schema_files=$mysqli->real_escape_string($form_data['cfseo_schema_file']);
		$cfseo_schema_file=base64_encode($cfseo_schema_files);
		$cfseo_page_url="";
		
		if(stristr($cfseo_custom_meta_tag, "<script>")){

			echo json_encode( array("status"=>0,"message"=>"Sorry script tag not allowed") );

		}else{

			$cfseo_custom_meta_tag=$cfseo_custom_meta_tag;

		}

		self::createRobotsFile($cfseo_robots_file);
		foreach ($form_data['cfseo'] as $key => $seo_value) {
		
			$key=$mysqli->real_escape_string($key);
			

			if($key=="page_id")
			{

				$pageId = json_decode(get_option("cfseo_page_ids"));
				if( !empty( $pageId ) ){
					array_push($pageId, $seo_value);
					update_option("cfseo_page_ids",json_encode($pageId));
				}else{

					add_option("cfseo_page_ids",json_encode([$seo_value]));
				}
				$page = get_page_by_id( $seo_value );

				$cfseo_page_url.=str_ireplace("@@qfnl_install_url@@",$install_url, $page['url']);
					
			}

			$cfseo_data[$key]=$mysqli->real_escape_string( $seo_value);
		}
		$cfseo_datas=json_encode($cfseo_data);
		$cfseo_dataa=base64_encode($cfseo_datas);
		$cfseo_param=$mysqli->real_escape_string($form_data['cfseo_param']);
		

		if($cfseo_param=="save_cfseo"){
			$add_row = "INSERT INTO `".$table."`( `page_name`,`page_url` ,`seo_data`, `custom_meta`,`schema_org`) VALUES ('".$cfseo_page_name."','".$cfseo_page_url."','".$cfseo_dataa."','".$cfseo_custom_meta_tag."','".$cfseo_schema_file."')";
			$ret_add = ($mysqli->query( $add_row ))?1:-1;
			// print_r($mysqli);
			if($ret_add){
				$last_id=$mysqli->insert_id;
				echo json_encode( array("status"=>1,"cfseo_id"=>$last_id,"message"=>"Page seo added succesfull") );
				die();
			}else{
				echo json_encode( array("status"=>0,"message"=>"Page seo not added succesfull") );
				die();
			}
		}

		elseif($cfseo_param=="update_cfseo"){
			$seo_id = $mysqli->real_escape_string( $form_data['cfseo_id'] );
			$update_row = "UPDATE `".$table."` SET `page_name`='".$cfseo_page_name."',`page_url`='".$cfseo_page_url."',`seo_data`='".$cfseo_dataa."',`custom_meta`='".$cfseo_custom_meta_tag."', `schema_org`='".$cfseo_schema_file."' WHERE `id`=".$seo_id;
			$ret_update=($mysqli->query( $update_row ))?1:-1;
			if($ret_update){
				echo json_encode( array("status"=>1,"cfseo_id"=>$seo_id,"message"=>"Page seo updated successfully") );
				die();
			}else{
				echo json_encode( array("status"=>0,"message"=>"Page seo not updated successfully") );
				die();
			}
		}
		die();

	}
	function DeleteSeoData( $form_data = null ){
		global $mysqli;
		global $dbpref;
		$table=$dbpref.$this->pref."setup";
		$seo_id = $mysqli->real_escape_string( $form_data['cfseo_id'] );
		$delete_row = "DELETE FROM `".$table."` WHERE `id`=".$seo_id;
		$ret_delete=($mysqli->query($delete_row))?1:-1;
		if($ret_delete){
			echo json_encode( array("status"=>1, "type"=>"delete" ,"message"=>"Page seo deleted successfully") );
			die();
		}else{
			echo json_encode( array("status"=>0,"message"=>"Page seo not deleted successfully") );
			die();
		}
		die();
	}
	function handleWebmasterData(  $webmasters=null ){
      
		global $mysqli;
		global $dbpref;
		
		$table=$dbpref.$this->pref."webmaster";
		$cfseo_webmaster_param=$mysqli->real_escape_string( $webmasters['cfseo_webmaster_param'] );
			$cfseo_google_v = $mysqli->real_escape_string( $webmasters['cfseo_google_verification'] );
			$cfseo_bing_v = $mysqli->real_escape_string( $webmasters['cfseo_bing_verification'] );
			$cfseo_yandex_v = $mysqli->real_escape_string( $webmasters['cfseo_yandex_verification'] );
			$cfseo_baidu_v = $mysqli->real_escape_string( $webmasters['cfseo_baidu_verification'] );
  
		  if( !empty($cfseo_google_v) && !stristr($cfseo_google_v, "<meta") ){
			  
			  $cfseo_google_v='<meta name="google-site-verification" content="'.$cfseo_google_v.'">';
		  }else{
			$cfseo_google_v = $cfseo_google_v;
		  }
  
		  if( !empty( $cfseo_bing_v ) && !stristr($cfseo_bing_v, "<meta") ){
			  $cfseo_bing_v='<meta name="msvalidate.01" content="'.$cfseo_bing_v.'">';
		  }else{
			$cfseo_bing_v = $cfseo_bing_v;
		  }
  
		  if( !empty( $cfseo_baidu_v )  && !stristr($cfseo_baidu_v, "<meta") ){
			  $cfseo_baidu_v='<meta name="baidu-site-verification" content="'.$cfseo_baidu_v.'">';
		  }else{
			$cfseo_baidu_v = $cfseo_baidu_v;
		  }
  
		  if( !empty($cfseo_yandex_v) && !stristr($cfseo_yandex_v, "<meta") ){
			  $cfseo_yandex_v='<meta name="yandex-verification" content="'.$cfseo_yandex_v.'">';
		  }else{
			$cfseo_yandex_v = $cfseo_yandex_v;
		  }
  
		foreach ($webmasters as $key => $webmaster) {
		  if( stristr($webmaster, "<script>") ){
			echo json_encode( array("status"=>0,"message"=>"Sorry script tag not allowed") );
			die();   
		  }
		}
		
			if( $cfseo_webmaster_param=="save_cfseo_webmaster" ){
			  $add_row = "INSERT INTO `".$table."`(`google`, `bing`, `Yandex`, `Baidu`) VALUES ('".$cfseo_google_v."','".$cfseo_bing_v."','".$cfseo_yandex_v."','".$cfseo_baidu_v."')";
			  $ret_add = ($mysqli->query( $add_row ))?1:-1;
			  if($ret_add){
					echo json_encode( array("status"=>1,"message"=>"Page seo added succesfull") );
				die();
			  }else{
					echo json_encode( array("status"=>0,"message"=>"Page seo not added succesfull") );
					die();
			  }
			}
	  
			elseif($cfseo_webmaster_param=="update_cfseo_webmaster"){
			  $qry="TRUNCATE TABLE `".$table."` ";
			  $mysqli->query($qry);
			  $update_row = "INSERT INTO `".$table."`(`google`, `bing`, `Yandex`, `Baidu`) VALUES ('".$cfseo_google_v."','".$cfseo_bing_v."','".$cfseo_yandex_v."','".$cfseo_baidu_v."')";
			  $ret_update=($mysqli->query( $update_row ))?1:-1;
			  if($ret_update){
					echo json_encode( array("status"=>1,"message"=>"Page seo updated successfully") );
					die();
			  }else{
					echo json_encode( array("status"=>0,"message"=>"Page seo not updated successfully") );
					die();
			  }
			}
			die();
	  }
	  function handleSocialAccountData(  $accounts = null ){
		
			global $mysqli;
			global $dbpref;
				$s_account = [];
				$s_account_data = [];
				$table = $dbpref.$this->pref."social";
		  
				$cfseo_social_param = $mysqli->real_escape_string( $accounts['cfseo_social_param'] );

				foreach ($accounts['cfseo'] as $s_key => $account_value) {
					if( $s_key=="pinterest_verification" && !empty($account_value) )
					{

						if( !stristr($account_value, "<meta") ){

							$account_value = '<meta name="p:domain_verify" content="'.$account_value.'">';
							$s_account_data[ $s_key ] = $mysqli->real_escape_string( $account_value );
							
						}else{
							 
							$account_value = $account_value;
							$s_account_data[$s_key] = $mysqli->real_escape_string( $account_value );
						}
						
					}
					$s_key = $mysqli->real_escape_string( $s_key );
					$s_account_data[$s_key] = $mysqli->real_escape_string( $account_value );
					
				}

				foreach ( $accounts['cfseo_accounts'] as $key => $account ) {
				  if( stristr($account, "<script>") ){
						echo json_encode( array("status"=>0,"message"=>"Sorry script tag not allowed") );
						die();   
				  }
				  else{
						$key=$mysqli->real_escape_string($key);
						$s_account[$key] = $mysqli->real_escape_string($account);
				  }
				}
		  
				$s_account = json_encode( $s_account );
				$s_account_data = json_encode($s_account_data);
		  
				if( $cfseo_social_param=="save_cfseo_social" ){
				  $add_row = "INSERT INTO `".$table."`(`accounts`,`accounts_data`) VALUES ( '".$s_account."','".$s_account_data."' )";
				  $ret_add = ($mysqli->query( $add_row ))?1:-1;
				  if($ret_add){
						echo json_encode( array("status"=>1,"message"=>"social accounts added succesfull") );
						die();
				  }else{
						echo json_encode( array("status"=>0,"message"=>"social accounts not added succesfull") );
						die();
				  }
			}
	  
			elseif($cfseo_social_param=="update_cfseo_social"){
			  $qry="TRUNCATE TABLE `".$table."` ";
			  $mysqli->query($qry);
			  $update_row = "INSERT INTO `".$table."`(`accounts`,`accounts_data`) VALUES ( '".$s_account."','".$s_account_data."' )";
			  $ret_update=($mysqli->query( $update_row ))?1:-1;

			  if($ret_update){
					echo json_encode( array("status"=>1,"message"=>"social accounts updated successfully") );
					die();
			  }else{
					echo json_encode( array("status"=>0,"message"=>"social accounts updated successfully") );
					die();
			  }
			}
			die();
		}
	
    function createRobotsFile($robots_txts=null){
    	
    	$robots_txt=explode("\\r\\n", $robots_txts);
    	$text="";
    	foreach ($robots_txt as  $txt) {
    		$text.=$txt.PHP_EOL;
    	}
    	$text=rtrim($text,PHP_EOL);
    	$file_dir=plugin_dir_path(dirname(__FILE__,3)).'robots.txt';
        $file=fopen($file_dir,"w") or die("Sorry file not exist");
        fwrite($file,$text);
        fclose($file);
    }

    function loadAllSeoSetup($config_version=0)
    {
      global $mysqli;
      global $dbpref;
	  $table=$dbpref.$this->pref.'setup';
	  $table1=$dbpref.$this->pref.'webmaster';
	  $table2=$dbpref.$this->pref.'social';
      $qry=$mysqli->query("SELECT * FROM ".$table);
      $install_url=get_option("install_url");
      if( $qry->num_rows>0 ){
		
        while($r = $qry->fetch_assoc()){
		$seos = base64_decode($r['seo_data']);
		$seo=json_decode($seos);
		$cfseo_schema_file = base64_decode($r['schema_org']);
	    
		$page_id=$seo->page_id;
        $page = get_page_by_id( $page_id );
		$page_url=str_ireplace("@@qfnl_install_url@@",$install_url, $page['url']);
		$created_date=date("Y-m-d\TH:i:sP",$page['date_created']);
		$u_date=$page['date_created']+50000;
		$update_date=date('Y-m-d\TH:i:sP',$u_date);
       	$query_url =(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')?"https://":"http://"; 
		$query_url.= $_SERVER['HTTP_HOST'];      
		$parse_url =parse_url($_SERVER['REQUEST_URI']); 
		$query_url.= $parse_url['path'];
		$url=rtrim($query_url, '/\\');

		  $qry1 = $mysqli->query("SELECT * FROM ".$table1);
		
			if($url==$page_url){
				// echo $url."<br>".$page_url;
				$prds = $collections =  false;
				if(isset($_GET['product']))
                {

                    $product= $_GET['product'];
					$prds = self::getProduct( $product );
				}
				if( isset( $_GET['sf_collection_name'] ) )
                {

                    $col_id= $_GET['sf_collection_name'];
					$collections = self::getCollection( $col_id );
				}elseif(  isset( $_GET['sf_cfilters_Collection'] )  )
				{
					$col_id= $_GET['sf_cfilters_Collection'];
					$collections = self::getCollection( $col_id );

				}
				$qry2 = $mysqli->query("SELECT * FROM ".$table2);

				require plugin_dir_path( dirname(__FILE__,1) )."/view/seo_meta.php";	
			} 
			$web_data = $qry1->fetch_assoc();
			$cfseo_google_verification = !empty( $web_data['google'] )?$web_data['google']:"";
			$cfseo_bing_verification = !empty( $web_data['bing'] )?$web_data['bing']:"";
			$cfseo_yandex_verification = !empty( $web_data['yandex'] )?$web_data['yandex']:"";
			$cfseo_baidu_verification = !empty( $web_data['baidu'] )?$web_data['baidu']:"";
			if(!empty($cfseo_google_verification)){
				echo $cfseo_google_verification.PHP_EOL;
			}
			if(!empty($cfseo_bing_verification)){
				echo $cfseo_bing_verification.PHP_EOL;
			}
			if(!empty($cfseo_yandex_verification)){
				echo $cfseo_yandex_verification.PHP_EOL;
			}
			if(!empty($cfseo_baidu_verification)){
				echo $cfseo_baidu_verification.PHP_EOL;
			}                      
      	}
    }
      else{
        return false;
      }
    }
	function getProduct( $pid )
	{
		global $app_variant;
		global $mysqli;
		global $dbpref;
		$table=$dbpref.'all_products';
		if( $app_variant == "shopfunnels" )
		{
			$prid = $mysqli->real_escape_string( $pid );
			$sql = "SELECT `title`,`description` FROM `$table` WHERE `productid`='$prid'";
			$r = $mysqli->query( $sql );
			if( $r->num_rows > 0 )
			{
				$data = $r->fetch_object();
				return $data;
			}
		}
		return false;

	}
	function getCollection( $pid )
	{
		global $app_variant;
		global $mysqli;
		global $dbpref;
		$table=$dbpref.'product_collections';
		if( $app_variant == "shopfunnels" )
		{
			$prid = $mysqli->real_escape_string( $pid );
			$sql = "SELECT `title`,`description` FROM `$table` WHERE `search_title`='$prid'";
			$r = $mysqli->query( $sql );
			if( $r->num_rows > 0 )
			{
				$data = $r->fetch_object();
				return $data;
			}
		}
		return false;
	}
  }
}