<?php
$app_variant= "cloudfunnels";
$current_app_version= '4.7.0';
/*=======Reminder: Never change comments from this file=========*/
//$_SERVER['DOCUMENT_ROOT']=rtrim($_SERVER['DOCUMENT_ROOT'],"/");
//$_SERVER['DOCUMENT_ROOT']=rtrim($_SERVER['DOCUMENT_ROOT'],"\\");

$pro_upgrade_url= "https://yournextfunnel.in/cloudfunnels2pro";
$upgrade_url= "https://getcloudfunnels.in";


$document_root=__DIR__;

$document_root=rtrim(str_replace("\\","/",$document_root),"/");
$document_root_arr=explode("/",$document_root);
array_pop($document_root_arr);
$document_root=implode("/",$document_root_arr);
$document_root=rtrim(str_replace("\\","/",$document_root),"/");

$GLOBALS["config_file"]= $document_root."/config.php";

if(!function_exists('cf_dir_exists'))
{
	function cf_dir_exists($dir)
	{
		$dir=rtrim($dir,"/");
		if(is_dir($dir))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

}

if(!function_exists('cf_fwrite'))
{
	function cf_fwrite($file,$content)
	{
		$stat=false;
		$fp=fopen($file,"w");
		if(fwrite($fp,$content))
		{
			$stat =true;
		}
		fclose($fp);
		return $stat;
	}
}

if(!function_exists('doInitRoute'))
{
	function doInitRoute()
	{
		if(function_exists('get_option') && get_option('install_url'))
		{
			$path= rtrim(str_replace('\\', '/', __DIR__), '/');

			$ins_url= parse_url(get_option('install_url'));

			$ins_path= $ins_url['host'];

			if(isset($ins_url['path']))
			{
				$p= trim($ins_url['path'], '/');
				if(strlen($p)>0)
				{
					$ins_path .= '/'.$p;
				}
			}

			$current_url= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$current_path= str_replace($ins_path, '', $current_url);
			$current_path= trim($current_path, '/');
			$current_path .='/';
			
			if(! preg_match('/^(index\.php\/)|(index\.php\?)/i', $current_path))
			{
				if(preg_match('/^(cf-admin|cf-login)\//i', $current_path))
				{
					$_GET['page']= 'login';
					$_REQUEST['page']= 'login';

					$_GET['cf-admin']= 1;
					$_REQUEST['cf-admin']= 1;
				}
				else
				{
					$p_path= "";
					$p_url= parse_url(trim($current_path, '/'));

					if(isset($p_url['path']))
					{
						$p_path= trim($p_url['path'], '/');
					}

					if(strlen(trim($p_path))>0)
					{
						$_GET['funnel_view']= 1;
						$_REQUEST['funnel_view']= 1;

						$_GET['get_funnel']= $p_path;
						$_REQUEST['get_funnel']= $p_path;
					}
				}
			}
		}
	}
}
?>