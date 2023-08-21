<?php
$GLOBALS['mysqli']=$mysqli;
$GLOBALS['dbpref']=$dbpref;
$GLOBALS['qfnl_global_vars']=array();
/*----------------Options starts here-------------------*/
function add_option($name,$value)
{
	$globalvars=$GLOBALS['qfnl_global_vars'];
	$mysqli=$GLOBALS['mysqli'];
	$pref=$GLOBALS['dbpref'];
	$table=$pref."options";


	$name=$mysqli->real_escape_string($name);
	$value=$mysqli->real_escape_string($value);


	if(get_option($name)===false)
	{
	$qry=$mysqli->query("insert into `".$table."` (`option_name`, `option_value`, `createdon`) values ('".$name."','".$value."','".date('d-M-Y h:i')."')");
	$globalvars[$name]=$value;
	$GLOBALS['qfnl_global_vars']=$globalvars;
	}
	else{return false;}
}
function get_option($name)
{
	$globalvars=$GLOBALS['qfnl_global_vars'];
	$mysqli=$GLOBALS['mysqli'];
	$pref=$GLOBALS['dbpref'];
	$table=$pref."options";

	$name=$mysqli->real_escape_string($name);

	if(isset($globalvars[$name]))
	{
		return $globalvars[$name];
	}
	else
	{
		$qry=$mysqli->query("select `option_value` from `".$table."` where `option_name`='".$name."'");
		if($qry->num_rows<1)
		{
			return false;
		}
		else
		{
			$data= $qry->fetch_object();
			$option_value= $data->option_value;
			if($name==='install_url')
			{
				$option_value= manageWWW($option_value);
			}
			$globalvars[$name]= $option_value;
			$GLOBALS['qfnl_global_vars']= $globalvars;
			return $option_value;
		}
	}
}
function update_option($name,$value)
{
	$globalvars=$GLOBALS['qfnl_global_vars'];
	$mysqli=$GLOBALS['mysqli'];
	$pref=$GLOBALS['dbpref'];
	$table=$pref."options";

	$name=$mysqli->real_escape_string($name);
	$value=$mysqli->real_escape_string($value);

	if(get_option($name)===false)
	{
		add_option($name,$value);
	}
	else
	{
		$mysqli->query("update `".$table."` set `option_value`='".$value."' where `option_name`='".$name."'");
	}
	$globalvars[$name]=$value;
	$GLOBALS['qfnl_global_vars']=$globalvars;
}
function delete_option($name)
{
	$globalvars=$GLOBALS['qfnl_global_vars'];
	$mysqli=$GLOBALS['mysqli'];
	$pref=$GLOBALS['dbpref'];
	$table=$pref."options";
	$mysqli->query("delete from `".$table."` where `option_name`='".$name."'");
	unset($globalvars[$name]);
	$GLOBALS['qfnl_global_vars']=$globalvars;
}
/*--------------------options ends here-----------------*/

/*--------------------add_query_arg function------------*/
function add_query_arg($arg1,$arg2,$arg3="")
{
	$create_url=function($url,$args){
		$url_arr=parse_url($url);
		$params="";
		$all_args=array();
		if(isset($url_arr['query']))
		{
			parse_str($url_arr['query'],$temp_all_args);
			$all_args=$temp_all_args;
		}
		
		foreach($args as $index=>$arg)
		{
			$all_args[$index]=$arg;
		}

		$new_arr=array();
		foreach($all_args as $index=>$arg)
		{
			array_push($new_arr,$index."=".$arg);
		}
		$params="?";
		$params .=implode("&",$new_arr);


		$new_url="";
		if(isset($url_arr['scheme']))
		{
			$new_url .=$url_arr['scheme']."://";
		}
		if(isset($url_arr['host']))
		{
			$new_url .=$url_arr['host'];
		}
		if(isset($url_arr['path']))
		{
			$new_url .=$url_arr['path'];
		}
		return $new_url.$params;
	};
	if(is_array($arg1))
	{
		$url=$arg2;
		return $create_url($url,$arg1);
	}
	else
	{
		$url=$arg3;
		$arr=array($arg1=>$arg2);
		return $create_url($url,$arr);
	}
}
function showRecordCountSelection()
{
	if(isset($_POST['qfnl_max_records_per_page']) && is_numeric($_POST['qfnl_max_records_per_page']))
	{
		update_option('qfnl_max_records_per_page',$_POST['qfnl_max_records_per_page']);
	}
	$options="";
	$range_arr=array(10, 25, 50, 100, 250, 500, get_option('qfnl_max_countable_rows'));

	$got_selection=false;

	for($i=0;$i<count($range_arr);$i++)
	{
		$selected="";
		if($range_arr[$i]==get_option('qfnl_max_records_per_page'))
		{
			$selected="selected";
			$got_selection=true;
		}
		elseif(($i==(count($range_arr)-1)) && (!$got_selection))
		{
			$selected="selected";
		}
		$selected=($range_arr[$i]==get_option('qfnl_max_records_per_page'))? "selected":"";
		$opt_text=(($i==(count($range_arr)-1)))? 'All':$range_arr[$i];
		$options .="<option value='".$range_arr[$i]."' ".$selected.">".t($opt_text)."</option>";
	}

	return "<div class='mb-3'><form action='' method='post'>
	<div class='input-group input-group-sm'>
	<div class='input-group-prepend'>
	<span class='input-group-text'>".t('Number of items per page')."</span>
	</div>
	<select name='qfnl_max_records_per_page' class='form-select qfnl_max_records_per_page form-select-sm'>
	".$options."
	</select>
	</div>
	<input type='submit' class='qfnl_max_records_per_page_btn' style='display:none;'>
	</form></div>";	
}


function arrayIndexToStr($text,$arr)
{
	if(is_array($arr))
	{
		foreach($arr as $index=>$data)
		{
			if(is_array($data)||is_object($data)){continue;}
			$text=str_replace("{".$index."}",$data,$text);
		}
	}
	return $text;
}
function linkBuilderAccordingCurrentURL($current)
{
		$url_arr=parse_url($current);
		$query_arr=array();
		if(isset($url_arr['query']))
		{
		parse_str($url_arr['query'],$arr);
		$query_arr=$arr;
		}

		$current_get=(isset($_GET))? $_GET:array();
		foreach($current_get as $index=>$data)
		{
			if(!isset($query_arr[$index]))
			{
				$query_arr[$index]=$data;
			}
		}
		
		$query="";
		foreach($query_arr as $index=>$data)
		{
			$query .=$index."=".$data."&";
		}
		
		$query=rtrim($query,"&");
		return $url_arr['path']."?".$query;
}
function createPager($total,$nextpageurl="",$page_count=0,$lastid=0)
{
	ob_start();
	echo '<ul class="pagination qfnlpagination" style="cursor:pointer;">';

	if($page_count>0)
	{
	echo '<li class="page-item"><a class="page-link" id="historyback">'.t('Previous').'</a></li>';
	}
	$gotnextactive=0;
	$gotactive=0;
	if(is_numeric($total))
	{
	$pagescount=ceil($total/get_option('qfnl_max_records_per_page'));
	if($pagescount>=1)
	{
		 $dotshow=0;
		for($i=1;$i<=$pagescount;$i++)
		{

     if(!($i==1||$i==2||$i==$pagescount||$i==$pagescount-1||$i==$page_count||$i==$page_count-1||$i==$page_count+1))
		 {
			 ++$dotshow;
       if($dotshow==1){echo "<li class='page-item'><a class='page-link'>...</a></li>";}
			 continue;
		 }

		 $dotshow=0;

			$activeli="";
			if($page_count>0)
			{
			if($i==$page_count)
			{
			$activeli="active";
			++$gotactive;
			goto lbl;
			}
			if($gotactive==1)
			{
				$gotnextactive=$i;
		  	$gotactive=0;
			}
			}
	  lbl:
			$nextexecutable_page=$nextpageurl.'='.$i;
			$nextexecutable_page=linkBuilderAccordingCurrentURL($nextexecutable_page);
			echo '<li class="page-item '.$activeli.'"><a class="page-link" href="'.$nextexecutable_page.'">'.t($i).'</a></li>';
			$lastid=$i+1;
		}
		if($gotnextactive==0){$gotnextactive=2;}
	}
    }

	if($gotnextactive>0)
	{
		echo '<li class="page-item"><a class="page-link" href="'.$nextpageurl.'='.$gotnextactive.'">'.t('Next').'</a></li>';
	}
	echo '</ul>';
	$content=ob_get_contents();
	ob_end_clean();
	return $content;
}
function createSearchBoxBydate()
{
	ob_start();
 $today=date('d-m-Y');

	$select='<select class="form-select form-select-sm " name="fromdays"><option value=0>'.t('Select Days').'</option><option value=1>'.t('All Days').'</option><option value="'.$today.'">'.t('Today').'</option><option value="'.date('d-m-Y h:ia',strtotime($today." -7 days")).'">'.t('Last 7 days').'</option><option value="'.date('d-m-Y h:ia',strtotime($today." -15 days")).'">'.t('Last 15 days').'</option><option value="'.date('d-m-Y h:ia',strtotime($today." -30 days")).'">'.t('Last 30 days').'</option><option value="'.date('d-m-Y h:ia',strtotime($today." -60 days")).'">'.t('Last 60 days').'</option>
	<option value="'.date('d-m-Y h:ia',strtotime($today." -90 days")).'">'.t('Last 90 days').'</option><option value="'.date('d-m-Y h:ia',strtotime($today." -180 days")).'">'.t('Last 180 days').'</option><option value="'.date('d-m-Y h:ia',strtotime($today." -365 days")).'">'.t('Last 365 days').'</option></select>';

	$hiddeninputs="";

	foreach($_GET as $index=>$data)
	{
		if(!in_array($index,array('fromdays','fromdate','todate')))
		{
			$hiddeninputs.="<input type='hidden' name='".$index."' value='".$data."'>";
		}
	}

	echo '<div class="srchcontainer" style=""><button class="btn  dropdown-toggle btn-sm btn-block" style="">'.t('Search By Date').'</button><div
	 class="datesearchformdata"><div class="mb-3">
	 <form action="" method="GET" onsubmit="return validateDateField()">
	 '.$hiddeninputs.'
	 	 <span id="searchspancontainer">
	 '.$select.'<label class="text-white">'.t('Search By Date').'</label>
	 <div class="input-group input-group-sm mb-2"><div class="input-group-prepend"><p class="input-group-text">'.t('From').' </p></div><input type="date" name="fromdate" class="form-control form-control-sm" value="'.date('Y-m-d').'"></div>
	 <div class="input-group input-group-sm mb-2"><div class="input-group-prepend"><p class="input-group-text">'.t('To').' </p></div><input type="date" class="form-control form-control-sm" value="'.date('Y-m-d').'" name="todate"></div>
	 </span>
	 <button type="submit" class="form-control btn theme-button" style="margin-top:5px;" id="srchdatebtn"> '.t('Search').'</button>
	 </form>
	</div></div></div>';
	$data=ob_get_contents();
	ob_end_clean();
	return $data;
}
function dateBetween($search,$table=null,$date_time_format=false)
{
	//get sql between str for search....[0] for all [1] and
	$datebetween="";
	$datebetween_all="";

	$search=($table===null)? "`".$search."`":"`".$table."`.".$search."";
	
	if(isset($_GET['fromdate']))
	{
		if(strlen($_GET['fromdate'])>2)
		{
		$getfromdate=array_reverse(explode("-",$_GET['fromdate']));
		$fromdate=implode('-',$getfromdate);
		$fromdate=strtotime($fromdate);

		$gettodate=array_reverse(explode("-",$_GET['todate']));
		$todate=implode('-',$gettodate);
		$todate.=" 11:59pm";
		$todate=strtotime($todate);

		if($date_time_format)
		{
			$fromdate="'".date('Y-m-d H:i:s', $fromdate)."'";
			$todate="'".date('Y-m-d H:i:s', $todate)."'";
		}
		
		$datebetween=" and ".$search." between ".$fromdate." and ".$todate."";
		$datebetween_all=" ".$search." between ".$fromdate." and ".$todate."";
		}
	}
	if(isset($_GET['fromdays']))
	{
		if(strlen($_GET['fromdays'])>2)
		{
			$fromdate=strtotime($_GET['fromdays']);
			$todate=time();

			if($date_time_format)
			{
				$fromdate="'".date('Y-m-d H:i:s', $fromdate)."'";
				$todate="'".date('Y-m-d H:i:s', $todate)."'";
			}

			$datebetween=" and ".$search." between ".$fromdate." and ".$todate."";
			$datebetween_all=" ".$search." between ".$fromdate." and ".$todate."";
		}
	}
	return array($datebetween_all,$datebetween);
}
function timeConvert($format='s',$time='',$do=0)
{
	//time convert between different zone
  	//$do=0 default to another|$do=1 another to default
	//$format  s for unix time
	if($do==0)
	{
	$default="UTC";
	$convert=get_option('time_zone');
	}
	if($do==1)
	{
		$convert="UTC";
		$default=get_option('time_zone');
	}
	date_default_timezone_set($default);
	if(is_numeric($time)){$time=date("d-M-Y h:ia",$time);}
	$date = new DateTime($time, new DateTimeZone($default));

	$date->setTimezone(new DateTimeZone($convert));
	if($format=='s')
	{$time=strtotime($date->format('d-M-Y h:ia'));}
	else {$time=$date->format($format);}
	date_default_timezone_set("UTC");
	return $time;
}

function cfLoopCreator($type,$html)
{
	$hasbody=0;
	if(strpos($html,"<body>")>=0)
	{
		++$hasbody;
	}
$dom=new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
$xpath=new DOMXPath($dom);
$qry=$xpath->query("//*[@cf-loop='".$type."']");
foreach($qry as $data)
{
	$inner_html=cfLoopInnerHTML($data);
	$inner_html="{".$type."}".$inner_html."{/".$type."}";
	while($data->childNodes->length)
    {
        $data->removeChild($data->firstChild);
    }
	$fragment = $data->ownerDocument->createDocumentFragment();
    $fragment->appendXML($inner_html);
	$data->appendChild($fragment);
}
$html=$dom->saveHTML();
if($hasbody<1)
{
$start=strpos($html,"<body>")+6;
$end=strpos($html,"</body>")-$start;
$html=substr($html,$start,$end);
}
return $html;
}

function cfLoopInnerHTML(DOMNode $element) 
{ 
    $innerHTML = ""; 
    $children  = $element->childNodes;

    foreach ($children as $child) 
    { 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML; 
}

function getProtocol()
	{
	//get current protocol	
	if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {

		return "https://";
	}
	return "http://";
	}
function getOS()
{
//get current visitor operating system	
	$user_agent = (isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT']:'Unknown';
    $os_platform  = "Unknown OS Platform";
    $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function getBrowser() 
{
  //get current visitor browser
  $user_agent = (isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT']: 'Unknown';
  $browser        = "Unknown Browser";
  $browser_array = array(
                            '/msie/i'      => 'Internet Explorer',
                            '/firefox/i'   => 'Firefox',
                            '/safari/i'    => 'Safari',
                            '/chrome/i'    => 'Chrome',
                            '/edge/i'      => 'Edge',
                            '/opera/i'     => 'Opera',
                            '/netscape/i'  => 'Netscape',
                            '/maxthon/i'   => 'Maxthon',
                            '/konqueror/i' => 'Konqueror',
                            '/mobile/i'    => 'Mobile Browser'
                     );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}

function getIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function getLocation($ip=false)
{
  //get current visitor location
  if($ip===false)
  {
	  $ip=getIP();
  }
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, "https://pro.ip-api.com/json/".$ip."?key=hKTTGTDeZib1VzK");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  $data=json_decode($data,true);
  return $data;
}
 function getDevice()
 {
 	require_once("mobile_detect.php");
	$detect = new Mobile_Detect;
	// DETECTION ENGINE
	$data= "DESKTOP";
	if($detect->isMobile()) 
	{
		$data= "MOBILE";
	}
	elseif($detect->isTablet())
	{
		$data="TABLET";
	}
	else
	{
		$data="DESKTOP";
	}

return $data;
 }
function arranger($type_arr=array(),$get_parameter="arrange_records_order")
{
	ob_start();
	$hiddeninputs="";
	foreach($_GET as $index=>$data)
	{
		if($index==$get_parameter){continue;}
		$hiddeninputs .="<input type='hidden' name='".$index."' value='".$data."'>";
	}
	echo '<form action="" methoed="get"><div class="mb-3"><div class="input-group input-group-sm"><div class="input-group-prepend">
	<span class="input-group-text">'.t('Arrange By').' </span>
	</div>
	'.$hiddeninputs.'
	<select name="'.$get_parameter.'" class="form-select form-select-sm" onchange="document.getElementById(\'constarrengerorder\').click()">';
	foreach($type_arr as $index=>$data)
	{
		$selected_asc="";
		$selected_desc="";
		if(isset($_GET[$get_parameter]))
		{
			if($_GET[$get_parameter]==base64_encode($index." asc"))
			{
				$selected_asc="selected";
			}
			elseif($_GET[$get_parameter]==base64_encode($index." desc"))
			{
				$selected_desc="selected";
			}
		}

		echo "
		<option value='".base64_encode($index." desc")."' ".$selected_desc.">".t("\${1} Descending",array(ucwords($data)))."</option>
		<option value='".base64_encode($index." asc")."' ".$selected_asc.">".t("\${1} Ascending", array(ucwords($data)) )."</option>
		";
	}
	echo '
	</select>
	<button type="submit" id="constarrengerorder" style="display:none;"></button>
	</div></div></form>';
	$content=ob_get_contents();
	ob_end_clean();
	return $content;
}
function modifyHtaccess($doo="create",$dir='')
{
   $dir=str_replace("\\","/",$dir);
   $file=$dir."/".".htaccess";
   $data="#cf-qfnl-rewrite-start\n";
   $data .="RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteRule ^(.*)$ index.php [L]";
   $data .="\n#cf-qfnl-rewrite-end\n";

   if($doo=="create")
   {
	if(is_file($file) && !get_option('backup_htaccess') && filesize($file)>0)
	{
		$fr=fopen($file,'r');
		add_option('backup_htaccess',fread($fr,filesize($file)));
		fclose($fr);
	}
	$ht_data=$data;
	if(file_exists($file))
	{
		$fp=fopen($file,'r');
		$current_ht_data=fread($fp,filesize($file));
		if(strpos($current_ht_data,$data) ===false)
		{
			$ht_data .="\n".$current_ht_data;
		}
		else
		{
			$ht_data=$current_ht_data;
		}
		fclose($fp);
	}
   $fp=fopen($file,"w+");
   fwrite($fp,$ht_data);
   fclose($fp);
   }
   elseif(is_file($file) && $doo=="delete" && filesize($file)>0)
   {
	   $fpp=fopen($file,"rb");
	   $file_data=fread($fpp,filesize($file));
	   fclose($fpp);
	   $fpw=fopen($file,"w");
	   $file_data=str_replace($data,"",$file_data);
	   fwrite($fpw,$file_data);
	   fclose($fpw);
   }
}
function cf_enc($string,$do="encrypt")
{
	$token=get_option('site_token');
	$token .="cloudfunnels can do it";
	
	$method = "AES-256-CBC";
	$key= hash("sha256",$token);
	$iv=substr(hash('sha256','--'.$token),0,16);
	
	if($do=="encrypt")
	{
		$data=base64_encode(openssl_encrypt($string,$method,$key,0,$iv));
	}
	elseif($do=="decrypt")
	{
		$data=openssl_decrypt(base64_decode($string),$method,$key,0,$iv);
	}
	else
	{
		$data=$string;
	}
	return $data;
}
function getBsSixtyFourLogos($type="logo")
{
	if($type=="logo")
	{
		return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAoCAYAAAC8cqlMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA25pVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpFQ0Q1NDE5RUJGRTQxMUU5OTE1N0Q5NEE5NjExNDAzMyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCNEMwQ0MxNzJBOEQxMUVDODI4QkFBRkYyQTlGMEI3MiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCNEMwQ0MxNjJBOEQxMUVDODI4QkFBRkYyQTlGMEI3MiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpkNjBlY2Y1Yy02N2MyLTUyNGUtYjk4NC0xZTNlNDg4MDE2MWEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RUNENTQxOUVCRkU0MTFFOTkxNTdEOTRBOTYxMTQwMzMiLz4gPC9yZGY6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";
	}
	elseif($type=="logo-text")
	{
		return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAAAWCAYAAAAisWU6AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ppVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpFQ0Q1NDE5RUJGRTQxMUU5OTE1N0Q5NEE5NjExNDAzMyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFN0RBRUYzMDMwQTcxMUVDQUU3M0Q0RkE1OTc2QTZDQiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFN0RBRUYyRjMwQTcxMUVDQUU3M0Q0RkE1OTc2QTZDQiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpEQzY0QUM4NjJBOTAxMUVDQURERThCNUI1RDk2OEVDQyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpEQzY0QUM4NzJBOTAxMUVDQURERThCNUI1RDk2OEVDQyIvPiA8L3JkZjpEZXNj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";
	}
}
function cf_rmdir($dir)
    {
         if (cf_dir_exists($dir))
   		{
        	$objects = scandir($dir);

			foreach ($objects as $object)
			{
				if ($object != "." && $object != "..")
				{
					if (!is_file($dir . "/" . $object))
					{
						cf_rmdir($dir . "/" . $object);
					}
					else
					{
						unlink($dir . "/" . $object);
					}
				}
			}

			reset($objects);
        	rmdir($dir);
    	}
	}
	function register_tiny_editor($selector)
	{
		$script="";
		if(!isset($GLOBALS['tiny_asset_loaded']))
		{
			$ins_url= get_option('install_url');

			$script .='<script type="text/javascript" src="'.$ins_url.'/assets/js/jscolor.js"></script>
			<script type="text/javascript" src="'.$ins_url.'/assets/js/tinymce/jquery.tinymce.min.js"></script>
			<script type="text/javascript" src="'.$ins_url.'/assets/js/tinymce/tinymce.min.js"></script>
			<script type="text/javascript" src="'.$ins_url.'/assets/js/load_tiny.js"></script>
			';
			$GLOBALS['tiny_asset_loaded']=1;
		}
		if(is_array($selector))
		{  foreach($selector as $sel)
			{
				$script .="<script>cfLoadTinyMceEditor(`".$sel."`);</script>";
			}
		}
		else
		{
			$script .="<script>cfLoadTinyMceEditor(`".$selector."`);</script>";
		}
		echo $script;
	}
	if ( ! function_exists('build_from_parsed_url'))
	{
		function build_from_parsed_url(array $parts)
		{
			$scheme   = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';

			$host     = $parts['host'] ?? '';
			$port     = isset($parts['port']) ? (':' . $parts['port']) : '';

			$user     = $parts['user'] ?? '';
			$pass     = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
			$pass     = ($user || $pass) ? ($pass . '@') : '';

			$path     = $parts['path'] ?? '';

			$query    = empty($parts['query']) ? '' : ('?' . $parts['query']);

			$fragment = empty($parts['fragment']) ? '' : ('#' . $parts['fragment']);

			return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
		}
	}
	function manageWWW($saved){
		$res= $saved;
		$current= $_SERVER['HTTP_HOST'];
		$parsed_saved= parse_url($saved);

		if(isset ($parsed_saved['host']))
		{
			if(strtolower($current) !== strtolower($parsed_saved['host']))
			{
				$current_domain= strtolower(preg_replace("/^(www\.)/", "", $current));
				$saved_domain=  strtolower(preg_replace("/^(www\.)/", "", $parsed_saved['host']));

				if($current_domain===$saved_domain)
				{
					$parsed_saved['host']= $current;
					$res= build_from_parsed_url($parsed_saved);
				}
			}
		}

		return $res;
	}
?>