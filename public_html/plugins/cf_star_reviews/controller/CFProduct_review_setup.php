<?php
if(!class_exists('CFProduct_review_setup'))
{
  class CFProduct_review_setup
  {
    var $pref="cfproduct_review_";
    function __construct($arr)
    {
      $this->loader=$arr['loader'];
    }
      /*
          Get all reviews for showing on the desktop
      */ 
    function getAllReviews($total_comment=false,$max_limit=false,$page=1,$product_id=false, $rating=false, $min_rat=false, $max_rat=false, $summary=false,$filter=false)
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'cfproduct_review_records';
        $table2= $dbpref.'all_products';
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

        $search="";

        if(isset($_POST['onpage_search']))
        {
            $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
            $search=str_replace('_','[_]',$search);
            $search=str_replace('%','[%]',$search);
            $review = $mysqli->real_escape_string( htmlspecialchars( $search ) );
            $search=" AND cfr.`summary` LIKE '%".$review."%' OR cfr.`name` LIKE  '%".$search."%' OR cfc.`title` LIKE  '%".$search."%'";
            
        }

        $order_by=" cfr.`id` desc";
        if(isset($_GET['arrange_records_order']))
        {
            $order_by=base64_decode($_GET['arrange_records_order']);
        }

        $date_between=dateBetween('added_on',null,true);
        
        if(strlen($date_between[0])>0)
        {
            $date_mid = $date_between[1];
            $search .=$date_mid;
        }
        if($product_id)
        {
            $search .=" AND cfr.`product_id`=".$product_id;
        }
        // $rating=false, $min_rat=false, $max_rat=false, $summary=false
        if($rating)
        {
            $search .=" AND cfr.`rating`=".$rating;
        }
        if($summary)
        {
            $search .=" AND CHAR_LENGTH(cfr.`summary`)>=".$summary;
        }
        if( $max_rat && $min_rat )
        {
            $search .=" AND cfr.`rating` BETWEEN $min_rat AND $max_rat";
        }
        elseif( $min_rat )
        {
            $search .=" AND cfr.`rating`=".$min_rat;
        }
        elseif($max_rat)
        {
            $search .=" AND cfr.`rating`=".$max_rat;
        }
        if($filter)
        {
            if($filter=="ap"){
                $search .=" AND `approved`=1";
            }
            else if($filter=="uap"){
                $search .=" AND `approved`=0";
            }
            elseif($filter=="r"){
                $search .=" AND cfr.`readed`=1";
            }
            else if($filter=="ur"){
                $search .=" AND cfr.`readed`=0";
            }
        }
        $sql_dtebtw = "SELECT cfr.*, cfc.title FROM `".$table."` AS cfr LEFT JOIN `".$table2."` as cfc ON cfc.`id`=cfr.`product_id`  WHERE  1".$search." ORDER BY ".$order_by.$limit;
        $qry=$mysqli->query($sql_dtebtw);
        $arr=[];
        if($qry==null)
        {
            return 0;
        }
        else
        {
        if($qry->num_rows>0)
        {
            while($data = $qry->fetch_assoc() )
            {
                $arr[]=$data;
            }   
        
        }
        return $arr;
        }
    }

    //Get Total Reviews Count
    function getSetupsCount()
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.$this->pref.'records';

      $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."`");

      $r=$qry->fetch_object();
      return $r->total_setup;
    }
    /*********** Get Gravavatar Image *************/ 
    function get_gravatar_image($email=null){
      $email = $email;
      $default = 'mp';
      $size = 40;
      $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
      return $grav_url;

    }

    /****** Get Product ******/
    function getProdcuts()
    {
      global $mysqli;
      global $dbpref;
      global $app_variant;
      $app_variant = isset($app_variant)?$app_variant:"shopfunnels";
      $table= $dbpref.'all_products';
      if($app_variant=="shopfunnels")
      {
          $sql="SELECT * FROM `".$table."` WHERE `parent_product`=0";
      }else{
        $sql="SELECT * FROM `".$table."`";
      }
      $qry = $mysqli->query($sql);
      $arr=[];
      if( $qry==null )
      {
          return 0;
      }
      else
      {
      if($qry->num_rows>0)
      {
          while($data = $qry->fetch_assoc() )
          {
              $arr[]=$data;
          }   
      }
      return $arr;
      }
    }
    /********* Total Review ************/ 
    function getReviewsCount( $product_id=false )
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'cfproduct_review_records';
        $where ="";
        if( $product_id )
        {
            $where.=" `product_id` = ".$product_id." AND ";
        }
        $where .= " 1";
        $qry=$mysqli->query("SELECT COUNT(`id`) AS `total_reviews` FROM `".$table."` WHERE ".$where);

        if($qry==null)
        {
            return 0;
        }
        else
        {
        $r=$qry->fetch_object();
        return $r->total_reviews;
        }
    }
    /******** Read More Funtion ************/ 
    function readMore( $tex, $len=100 )
    {
      $string_length = strlen($tex);
      $rstr=[];
      $text = preg_replace("/<br\s*[\/]?>/i",PHP_EOL,$tex);
        if( $string_length > $len && $len!="-1")
        {
            $rstr['status']=1;
            $rstr['readmore'] = substr( $text, 0 , $len );
            $rstr['string'] = substr( $text, $len,$string_length-$len );
        }else{
            $rstr['status']=0;
            $rstr['string']=$text;
        }
        return $rstr;
    }

     /*********
     * For bulk action
     * Reviews
    **********/
    public function BulkReviewAction($allData)
    {
        global $mysqli;
        $bulkval = $mysqli->real_escape_string( $allData['bulkval'] );
        $data   = $allData['data'];
        if( $bulkval=="del" )
        {
         $this->reviewDelete( $data );
        }
        else if( $bulkval=="re" )
        {
         $this->reviewMarkasRead( $data,"r" ,false);
        }
        else if( $bulkval=="ap" )
        {
          $this->reviewApproved( $data,"a",false );
        }
        else if( $bulkval=="uap" )
        {
          $this->reviewApproved( $data,"u",false );
        }
        else if( $bulkval=="unre" )
        {
        $this->reviewMarkasRead( $data ,"ur",false);
        }
    }
     /*********
     *  Mark as read and unread Reviews function
     * Single and bulk 
     *  *************/
    public function reviewMarkasRead( $datas,$bulkac,$onlyone)
    {
        global $mysqli;
        global $dbpref;
        $table = $dbpref."cfproduct_review_records";
        $return_del=0;
        if( $onlyone === true ) {

            $id =(int)$mysqli->real_escape_string( trim($datas['id'] ));
            $read = $mysqli->real_escape_string($datas['read']);
            $readed = $read==1?0:1;
            $sql ="UPDATE  `".$table."` SET `readed`=$readed WHERE `id`=".$id;
            $return_del = $mysqli->query($sql)?1:-1;

        }else{
            $data = array_reverse(explode(",",$datas[0]));
            $len = count($data);
            $return_del=0;
            for( $i=1; $i < $len; $i++ )
            {
                $id = (int)$mysqli->real_escape_string( trim( $data[$i] ));
                if($bulkac=="r")
                {
                    $sql ="UPDATE  `".$table."` SET `readed`=1 WHERE `id`=".$id;
                    $return_del = $mysqli->query($sql)?1:-1;
                }
                else if($bulkac=="ur")
                {
                    
                    $sql = "UPDATE  `".$table."` SET `readed`=0 WHERE `id`=".$id;
                    $return_del = $mysqli->query($sql)?1:-1;
                }
            }
        }
        if( $return_del ){
            if($bulkac=="r")
            {
                echo json_encode(array("msg"=>t("All reviews have been read successfully"),"status"=>"1"));
            }
            elseif($bulkac=="ur")
            {
                echo json_encode(array("msg"=>t("All reviews have not been read successfully"),"status"=>"1"));
            }
            else
            {
                echo json_encode(array("msg"=>t("All reviews have not been read successfully"),"status"=>"1"));
            }
        }else{

            if( $bulkac=="r" )
            {
                echo json_encode(array("msg"=>t("All reviews have not been read successfully"),"status"=>"0"));
            }
            elseif( $bulkac=="ur" )
            {
                echo json_encode(array("msg"=>t("All reviews have not been read successfully"),"status"=>"0"));
            }
            else
            {
                echo json_encode(array("msg"=>t("All reviews have not been read successfully"),"status"=>"0"));
            }
        }
        die();

    }
     /*********
     *  Delete Reviews function
     * Single and bulk 
     *  *************/
    public function reviewDelete( $datas,$onlyone=false )
    {
        global $mysqli;
        global $dbpref;
        $table = $dbpref."cfproduct_review_records";
        $return_del=0;
        
        if($onlyone===true)
        {   
            $id =  $mysqli->real_escape_string( trim( $datas['id'] )); 
            $sql = "DELETE FROM  `".$table."` WHERE `id`=".$id;
            $return_del = $mysqli->query($sql)?1:-1;

        }else{
            $data = array_reverse(explode(",",$datas[0]));
            $len = count($data);
            for( $i=1; $i < $len; $i++ )
            {  
                $id =  $mysqli->real_escape_string( trim( $data[$i] ) );
                $sql = "DELETE FROM  `".$table."` WHERE `id`=".$id;
                $return_del = $mysqli->query($sql)?1:-1;
            }
        }
        if($return_del){

            echo json_encode(array("msg"=>"deleted","status"=>"1"));
        
        }else{

            echo json_encode(array("msg"=>"not deleted","status"=>"0"));
        }
        die();
    }
    /********
     *Marked as approved 
     * *************/ 
    public function reviewApproved($datas,$bulkac,$onlyone=false)
    {
        global $mysqli;
        global $dbpref;
        $table = $dbpref."cfproduct_review_records";
        $return_del=0;
        if($onlyone===true){

            $id =$mysqli->real_escape_string( trim($datas['id'] ) );
            $appr = $mysqli->real_escape_string($datas['appr']);
            $approved = $appr==0?1:0;
            $sql ="UPDATE  `".$table."` SET `approved`=$approved WHERE `id`=".$id;
            $return_del = $mysqli->query($sql)?1:0;

        }else{
            
            $data = array_reverse(explode(",",$datas[0]));
            $len = count($data);
            for( $i=1; $i < $len; $i++ )
            {
                $id =  $mysqli->real_escape_string( trim( $data[$i] ) );
                if($bulkac=="a")
                {
                    $sql ="UPDATE  `".$table."` SET `approved`=1 WHERE `id`=".$id;
                    $return_del = $mysqli->query($sql)?1:0;
                }
                else if($bulkac=="u")
                {      
                    $sql = "UPDATE  `".$table."` SET `approved`=0 WHERE `id`=".$id;
                    $return_del = $mysqli->query($sql)?1:0;
                }
            }
        }
        if($return_del){
            if($bulkac=="a")
            {
                echo json_encode(array("msg"=>t("Approved all reviews successfully"),"status"=>"1"));
            }
            elseif($bulkac=="u")
            {
                echo json_encode(array("msg"=>t("Unapproved all reviews successfully"),"status"=>"1"));
            }else{
                echo json_encode(array("msg"=>t("Unapproved all reviews successfully"),"status"=>"1"));
            }
        }else{

            if($bulkac=="a")
            {
                echo json_encode(array("msg"=>t("Not approved all reviews successfully"),"status"=>"0"));
            }
            elseif($bulkac=="u")
            {
                echo json_encode(array("msg"=>t("Not unapproved all reviews successfully"),"status"=>"0"));
            }else{
                echo json_encode(array("msg"=>t("Unapproved all reviews successfully"),"status"=>"0"));
            }
        }
        die();

    }
    public function getProdcutsPrimaryKeyId($id)
    {
        global $mysqli;
        global $dbpref;
        $table  = $dbpref."all_products";
        $sql="SELECT `id` FROM `$table` WHERE `productid`='$id'";
        $row = $mysqli->query($sql);
        if( $row->num_rows > 0 )
        {
            $r=$row->fetch_assoc();
            return $r['id'];
        }else{
            return false;
        }

    }
    //******* Reviews shortcode ********* */
    public function getAllReviewUI($pid, $v,$funnel_id,$come_from_get, $show=false, $read=false )
    {
        global $mysqli;
        global $dbpref;
        
        $table  = $dbpref."cfproduct_review_records";
        $setting_ob=$this->loader->load('setting');
        $settings = $setting_ob->getSettings();
        
        if( isset($_GET['cfpro_review_page'] )  && $_GET['cfpro_review_page'] > 1 )
        {
            $pageid = (int)$mysqli->real_escape_string( $_GET['cfpro_review_page'] );
            $start  = floor( ($pageid-1)*10 );
        }else{
            $start = 0;
        }

        $con = '';
        if( $show && $show == "approved" ){
            $con .= " AND `approved`=1";
        }else{
            $con.="";
        }
        
        if( $read && $read == "yes" ){
            $con .= " AND `readed`=1";
        }else{
            $con .= "";
        }

        if($come_from_get)
        {
            $id = $this->getProdcutsPrimaryKeyId($pid);
        }else{
            $id=$pid;

        }
        
        $all = "SELECT * FROM `".$table."` WHERE `product_id`=".$id." $con ";
        $all_data = "SELECT * FROM `".$table."` WHERE `product_id`=$id $con  ORDER BY `id` DESC  LIMIT $start, 10";
        $sum_sql = $mysqli->query("SELECT  ROUND( AVG (`rating`) , 2 ) as 'avgrating',COUNT(id) as 'totalreviewer'  FROM `".$table."` WHERE `product_id`=".$id." $con");
        if( $mysqli->affected_rows <= 0 )
        {
            die();
        }
        $row = $mysqli->query( $all );
        $row_all = $mysqli->query( $all_data );
        $avg_sql  = $sum_sql->fetch_assoc();
        $avg_rating = $avg_sql['avgrating'];
        $totalreviewer = $avg_sql['totalreviewer'];
        
        $pages = ceil( $totalreviewer / 10 );
        $first_page = 0;
        $last_page  = 6;

        $onestar=0;
        $twostar=0;
        $threestar=0;
        $fourstar=0;
        $fivestar=0;

        if( $row ->num_rows > 0 )
        {
            while( $r = $row->fetch_assoc() )
            {
                $rating = $r['rating'];
                if($rating==5)
                {
                    $fivestar+=1;
                }else if($rating==4)
                {
                    $fourstar+=1;
                }else if($rating==3) 
                {
                    $threestar+=1;
                }else if($rating==2)
                {
                    $twostar+=1;
                }else if($rating==1)
                {
                    $onestar+=1;
                }
    
            }
            $fiveStarRatingPercent = round(($fivestar/$totalreviewer)*100);
            $fiveStarRatingPercent = !empty($fiveStarRatingPercent)?$fiveStarRatingPercent.'%':'0%';  
            
            $fourStarRatingPercent = round(($fourstar/$totalreviewer)*100);
            $fourStarRatingPercent = !empty($fourStarRatingPercent)?$fourStarRatingPercent.'%':'0%';
            
            $threeStarRatingPercent = round(($threestar/$totalreviewer)*100);
            $threeStarRatingPercent = !empty($threeStarRatingPercent)?$threeStarRatingPercent.'%':'0%';
            
            $twoStarRatingPercent = round(($twostar/$totalreviewer)*100);
            $twoStarRatingPercent = !empty($twoStarRatingPercent)?$twoStarRatingPercent.'%':'0%';
            
            $oneStarRatingPercent = round(($onestar/$totalreviewer)*100);
            $oneStarRatingPercent = !empty($oneStarRatingPercent)?$oneStarRatingPercent.'%':'0%';
          
        }else{
            $fiveStarRatingPercent="0%";
            $fourStarRatingPercent="0%";
            $threeStarRatingPercent="0%";
            $twoStarRatingPercent="0%";
            $oneStarRatingPercent="0%";
            $totalreviewer=0;
            $avg_rating=0;
        }

        require plugin_dir_path( dirname(__FILE__,1) )."/views/review_shortcode.php";
    }

    //***********Show total like and dislike******************* */

    //********* Pagination for reviews in userside *********** */
    public function getPagination( $pages,$last_page=1,$first_page=1,$next="&raquo;",$prev="&laquo;" )
    {
        $p = '<div class="cfpro-rev-pagination">';
        ob_start();
        if( $pages >= 7)
            if( isset( $_GET['cfpro_review_page'] ) && $_GET['cfpro_review_page'] > 1 )
            {
                $first_page = $first_page+1;
                $current_page = $_GET['cfpro_review_page'];
                if( $current_page >= ( $pages) ){
                    $first_page= $current_page - 5;
                    $last_page=$current_page;
                    $i=$first_page;
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($first_page-1).'" >'.$prev.'</a>';
                    while( $i <= $last_page   ){
                        if( $current_page == $i ){
                            echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link active" data-page="'.$i.'"  >'.$i.'</a>';
                        }
                        else{  
                            echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.$i.'"   >'.$i.'</a>';
                        }    
                        $i++;
                    }
                }
                elseif( $current_page > 1){
                    if( $current_page >= 7 )
                    {
                        $first_page= $current_page -5;
                        $last_page=$current_page;
                    }else{
                        $first_page=2;
                        $last_page=6;
                    }
                    $i=$first_page;
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($first_page-1).'" >'.$prev.'</a>';
                    while( $i <= $last_page   ){
                        if( $current_page == $i ){
                            echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link active" data-page="'.$i.'"  >'.$i.'</a>';
                        }
                        else{  
                            echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.$i.'"   >'.$i.'</a>';
                        }    
                        $i++;
                    }
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($last_page+1).'" >'.$next.'</a>';
                }
            }else{
                $i=1;
                $last_page=7;
                while( $i <= 6   ){
                    if( 1 == $i ){
                        echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link active" data-page="'.$i.'"  >'.$i.'</a>';
                    }else{  
                        echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.$i.'"   >'.$i.'</a>';
                    }
                    $i++;
                }
                echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($last_page).'" >'.$next.'</a>';
            }
        else if( $pages >= 1 ){
            if( isset( $_GET['cfpro_review_page'] ) && $_GET['cfpro_review_page'] > 1 ){
                $i=1;

                $current_page = $_GET['cfpro_review_page'];
                if($current_page < $pages)
                {
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link " data-page="'.($current_page-1).'" >'.$prev.'</a>';
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link active" data-page="'.$current_page.'"   >'.$current_page.'</a>';
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($current_page+1).'" >'.$next.'</a>';
                }else{
                    $current_page=$pages;
                    if($current_page==1){
                        $current_page=$current_page+1;
                    }
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link " data-page="'.($current_page-1).'" >'.$prev.'</a>';
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link active" data-page="'.$current_page.'"   >'.$current_page.'</a>';
                    echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($current_page).'" >'.$next.'</a>';
                }
            }else{
                $i=1;
                echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link active" data-page="'.$i.'"   >'.$i.'</a>';
                echo '<a href="javascript:void(0)" class="cfpro-rev-pagination-link" data-page="'.($i+1).'" >'.$next.'</a>';
            }
        }
        $data=ob_get_clean();
        $p .=$data;
        $p .= '</div>';
        return $p;
    }
  }
}
?>