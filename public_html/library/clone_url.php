<?php
class Clone_url
{
    private $url;
    private $parsed_data;
    public $jsn_dir;
    private $img_upload_path;
    private $load;
    function __construct($arr)
    {
        $this->load=$arr['load'];
        $this->parsed_data=array();
        $this->jsn_dir=array(
            "asset"=>array("img"=>array(),"js"=>array(),"css"=>array("style.css"=>"")),
            "index.html"=>"",
            "remote_css"=>array(),
            "temp_images"=>array(),
        );
       
    }
    function init($url,$do="init",$page_data="")
    {
        if(filter_var($url,FILTER_VALIDATE_URL))
        {
            $this->url=$url;
            if($do=="init"||$do=="init_content")
            {
                self::sessionSite("unset");
                if($do==='init_content')
                {
                    $data=$page_data;
                }
                else
                {
                    $data=self::request(self::proto($url));
                }
                if($data !==false)
                {
                    $data=self::htmlReader($data);
                    return true;
                }
                else{return false;}
            }
            elseif($do=="download_images")
            {
                $has_session=self::sessionSite("get_array");
                if($has_session)
                {
                    self::sessionSite("set",$has_session);
                    return self::doDownloadImages();
                }
                else
                {
                    return false;
                }
            }
            elseif($do=="get_session_site")
            {
                $has_session=self::sessionSite("get_array");
                return ($has_session)? true:false;
            }
        }
        else
        {
            return false;
        }
    }
    private function buildLink($url,$current=false)
    {
        $url=str_replace(" ","",$url);
        if(strpos($url,"//")===0)
        {
            $url="http:".$url;
        }
        if(filter_var($url,FILTER_VALIDATE_URL))
        {
            return $url;
        }
        else
        {
            if(filter_var($current,FILTER_VALIDATE_URL))
            {
            $actual_url=parse_url($current);
            }
            else
            {
            $actual_url=parse_url($this->url);
            }

            $protocol=(isset($actual_url["scheme"]))? $actual_url["scheme"]."://":"http://";
            $host=$actual_url["host"];
            $path=(isset($actual_url["path"]))? $actual_url["path"]:"/";

            $url_arr=explode("/",$url);

            $path_arr=explode("/",$path);
			//print_r($path_arr);
            array_pop($path_arr);
            
            if(count($url_arr)==1)
            {
                return $protocol.$host.implode('/',$path_arr)."/".$url;
            }

            $path_arr=array_reverse($path_arr);
            $path_pointer=0;
        
            for($i=0;$i<count($url_arr);$i++)
            {
                $ua=$url_arr[$i];
                if(in_array($ua,array('.','..')))
                {
                    if($ua=="." && isset($path_arr[$path_pointer]))
                    {
                        $url_arr[$i]=$path_arr[$path_pointer];
                        $path_pointer +=1;
                    }
                    else if(isset($path_arr[$path_pointer+1]))
                    {
                        $url_arr[$i]=$path_arr[$path_pointer+1];
                        $path_pointer +=2;
                    }
                    else
                    {
                        break;
                    }
					
					for($j=0;$j<$path_pointer;$j++)
					{
						unset($path_arr[$j]);
					}
					
                }
                elseif(strlen($ua)>0)
                {
                    continue;
                }
                else
                {
					$path_arr=array();
                    unset($url_arr[$i]);
                }
            }

            for($i=0;$i<count($url_arr);$i++)
            {
                if(empty($url_arr[$i]))
                {
                    unset($url_arr[$i]);
                }
            }
            return $protocol.$host.implode("/",array_reverse($path_arr))."/".implode("/",$url_arr);
        }
    }
    private function proto($url)
    {
        return $url;
    }
    private function request($url,$type=0)
    {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        $data=curl_exec($ch);
        if(curl_errno($ch)<1)
        {
            if($type)
            {
                $info=curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
                return array($info,$data);
            }
            else
            {
                return $data;
            }
        }
        else
        {
            return false;
        }
    }
    private function sameSiteLinkReplacer($link,&$count)
    {
        if(filter_var($link,FILTER_VALIDATE_URL))
        {
            $url=$this->url;
            $url=parse_url($url);
            $link=parse_url($link);

            $is_subdomain= function()use($url, $link){
                $rev_url_host= explode('.', strrev($url["host"]));
                $rev_link_host= explode('.', strrev($link["host"]));

                if(count($rev_url_host)==3)
                {
                    $url_arr= array_slice($rev_url_host, 0, 2);
                    if(count($rev_link_host)>=2)
                    {
                        $link_arr= array_slice($rev_link_host, 0, 2);
                        if(implode('.', $url_arr)==implode('.', $link_arr))
                        {return true;}
                    }
                }
                else if((count($rev_url_host)> 3) && (count($rev_link_host)>= 3))
                {
                    $url_arr= array_slice($rev_url_host, 0, 3);

                    if(count($rev_link_host)>=3)
                    {
                        $link_arr= array_slice($rev_link_host, 0, 3);
                        if(implode('.', $url_arr)==implode('.', $link_arr))
                        {return true;}
                    }
                }

                return false;
            };


            if(($url["host"]== $link["host"]) || $is_subdomain())
            {
                    $time=time();
                    $time .=substr(str_shuffle('asdfghjkrtyuixvbchpwfADGJLZCVNMQETIP1234567890'),0,5);
                    ++$count;
                    return 'a'.$time.$count;
                
            }
            else
            {
                return false;
            }
        }
    }
    private function cssLinkReplacer($content,$current_url=false)
    {
        $cssurlptrn="/(url\()+(.)*(\))+/";
        preg_match_all($cssurlptrn,$content,$match_arr);
        // print_r($match_arr);
        if(isset($match_arr[0]) && is_array($match_arr))
        {
            $data_arr=$match_arr[0];
            for($i=0;$i<count($data_arr);$i++)
            {
                $url=$data_arr[$i];
                $backup=$url;
                $url=str_replace("url(","",$url);
                $url=str_replace(")","",$url);
                $url=str_replace("'","",$url);
                $url=str_replace("\"","",$url);

                if(strpos($url,"data:") !==false)
                {
                    //return $backup;
                    continue;
                }

                $rand=mt_rand(10,10000);
                $url=self::buildLink($url,$current_url);
                $no_differentsite=self::sameSiteLinkReplacer($url,$rand);

                $file_name=false;
                if($no_differentsite !==false)
                {
                    array_push($this->jsn_dir["temp_images"],$url);
                    $content=str_replace($backup,"url(@dbquote@@qfnlcloneimglink@".$url."---@qfnl-img-link@---@dbquote@)",$content);
                    //---@qfnl-img-link@---
                    /*
                    $data=self::request($url,1);
                    if($data)
                    {
                        $ext=self::getCachedExtension($url);
                        if($ext===false)
                        {
                            $info=explode("/",$data[0]);
                            $info[1]=explode("+",$info[1]);
                            $ext=$info[1][0];
                        }

                        if($ext !==false)
                        {
                            $file_name=$no_differentsite.".".$ext;
                            $content=str_replace($backup,"url(@qfnlcloneimglink@".$file_name.")",$content);
                            self::addToDirArr($file_name,$data[1],'img');
                        }
                    }
                    */
                }
            }
        }
       
        return $content;
    }
    private function DOMinnerHTML(DOMNode $element) 
    { 
        $innerHTML = ""; 
        $children  = $element->childNodes;
        foreach ($children as $child) 
        { 
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }
        return $innerHTML; 
    }
    private function getCachedExtension($url)
    {
        $extension=explode("/",rtrim(str_replace(" ","",$url),"/"));
        $extension=explode('.',$extension[count($extension)-1]);
        $found=false;
        if(isset($extension[count($extension)-1]))
        {
            $ext=$extension[count($extension)-1];
            $arr=array("png","jpg","jpeg","gif","svg","mp4");
            if(in_array(strtolower($ext),$arr))
            {
                $found=true;
                return $ext;
            }
        }
        return $found;
    }

    public function doDownloadImages()
    {           
                $template=$this->jsn_dir;

                $temp_arr=array();
                foreach($this->jsn_dir["temp_images"] as $template_img_temp)
                {
                    array_push($temp_arr,$template_img_temp);
                }

                $this->jsn_dir["temp_images"]=$temp_arr;

                $image_links=$this->jsn_dir["temp_images"];

                $img_rename_count=count($image_links);

             
                if(($img_rename_count)<1)
                {
                    return "done";
                }
                else
                {

                    for($i=0;($i<10 && count($this->jsn_dir["temp_images"])>0);$i++)
                    {

                    $imglink=$image_links[$i];
                    array_shift($this->jsn_dir["temp_images"]);
                

                $salt_link=$imglink."---@qfnl-img-link@---";
               // $this->jsn_dir["index.html"]=str_replace($salt_link,$imglink,$template["index.html"]);

                $not_samesitelink=self::sameSiteLinkReplacer($imglink,$img_rename_count);
                if($not_samesitelink !==false)
                {
                    // echo $imglink;
                    $img_data=self::request($imglink,1);
                    if($img_data)
                    {
                        $ext=self::getCachedExtension($imglink);
                        if($ext===false)
                        {
                            $info=explode("/",$img_data[0]);
                            $info[1]=explode("+",$info[1]);
                            $ext=$info[1][0];
                        }
                        if($ext !==false)
                        {
                            $finalimg_link=$not_samesitelink.".".$ext;
                           // $img->setAttribute("src",$finalimg_link);
                           $this->jsn_dir["index.html"]=base64_encode( str_replace($salt_link,$finalimg_link,base64_decode($this->jsn_dir["index.html"])));
                        
                           $this->jsn_dir["asset"]["css"]["style.css"]=base64_encode(str_replace($salt_link,$finalimg_link,base64_decode($this->jsn_dir["asset"]["css"]["style.css"])));

                            self::addToDirarr($finalimg_link,$img_data[1],"img");
                        }
                    }
                }
                else
                {
                    $this->jsn_dir["index.html"]=base64_encode(str_replace($salt_link,$imglink,base64_decode($this->jsn_dir["index.html"])));

                    $this->jsn_dir["asset"]["css"]["style.css"]=base64_encode(str_replace($salt_link,$imglink,base64_decode($this->jsn_dir["asset"]["css"]["style.css"])));
                }
            }
            }

                self::sessionSite("set",$this->jsn_dir);
                return "pending";
    }
    private function htmlReader($data)
    {
        //echo $data;
        $dom=new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($data);
        $img_rename_count=0;

        //js scripts
        $js_scripts=$dom->getElementsByTagName('script');
        foreach($js_scripts as $js_script)
        {
            $js_script->parentNode->removeChild($js_script);
        }

        //get all images
        $images=$dom->getelementsByTagName("img");
        foreach($images as $img)
        {
            $imglink=$img->getAttribute("src");
            $imglink1=$img->getAttribute("data-src");
            if( strpos($imglink,"data:")===false)
            {
                $imglink=self::buildLink($imglink);
                $img->setAttribute("src",$imglink."---@qfnl-img-link@---");
                array_push($this->jsn_dir["temp_images"],$imglink);
            }elseif( strpos($imglink1,"data:")===false ){
                $imglink1=self::buildLink($imglink1);
                $img->setAttribute("src",$imglink1."---@qfnl-img-link@---");
                array_push($this->jsn_dir["temp_images"],$imglink1);
            }
        }

        //get all css
        $links=$dom->getElementsByTagName("link");
        $count=0;

        foreach($links as $link)
        {
            ++$count;
            if($link->getAttribute("rel")=="stylesheet")
            {
                    $temp_link=self::buildLink($link->getAttribute("href"));
                    $differentsite=self::sameSiteLinkReplacer($temp_link,$img_rename_count);
                    $data=self::request($temp_link);

                    if((strpos($data,'<html')>=0) && strpos($data, '</html>')>0)
                    {
                        continue;
                    }

                    //$cssurlptrn="/(url\()+(.)*(\))+/";
                    //$data=preg_replace_callback($cssurlptrn,array($this,"cssLinkReplacer"),$data);
                    $data=self::cssLinkReplacer($data,$temp_link);
                    self::addToDirArr($differentsite.".css",$data,"css");
            }
        }


        //get all styles from head section

        $head=$dom->getElementsBytagName("head");
        if($head->length>0)
        {
            $head_item=$head->item(0);
            $styles=$head_item->getElementsByTagName("style");
            $style_count=0;
            foreach($styles as $style)
            {
                ++$style_count;
                $style_content=self::DOMinnerHTML($style);
                //$cssurlptrn="/(url\()+(.)*(\))+/";
                //$style_content=preg_replace_callback($cssurlptrn,array($this,"cssLinkReplacer"),$style_content);
                $style_content=self::cssLinkReplacer($style_content);
                self::addToDirArr("style-head-".$style_count.".css",$style_content,"css");
            }
        }

        $fnal_html=$dom->saveHTML();
        $final_body=$dom->getElementsByTagName("body");
        if($final_body && 0<$final_body->length)
        {
        $bodyelement=self::DOMinnerHTML($final_body->item(0));

        $bodyelement=self::cssLinkReplacer($bodyelement);

        self::addToDirArr($name="",$bodyelement,"html");
        }

        $this->jsn_dir["asset"]["css"]["style.css"]=base64_encode($this->jsn_dir["asset"]["css"]["style.css"]);
    }
    private function addToDirArr($name="",$data,$type="html")
    {
        if($type=="html")
        {
            $data=base64_encode($data);
            $this->jsn_dir["index.html"]=$data;
        }
        elseif($type=="css")
        {
            $this->jsn_dir["asset"]["css"]["style.css"] .=$data;
        }
        elseif($type=="remote_css")
        {
            $data=base64_encode($data);
            array_push($this->jsn_dir[$type],$data);
        }
        else
        {
            $data=base64_encode($data);
            $this->jsn_dir["asset"][$type][$name]=$data;
        }
    }
    public function sessionSite($do="set",$arr=array())
    {
        //$do=set|unset|get_array|get_session

        //$ssn=get_option('sitetoken');
        $funnel_ob=$this->load->loadFunnel();
        //$ssn .="clone_url_session_site";
        $dir=$funnel_ob->getTemplateinstallationfile();

        $saved_temp_file=	get_option('temp_filename_template');
        
        $file=$dir."/".$saved_temp_file.".txt";
        

        if(strpos($do,"get_") !==false)
        {
            if(!is_file($file)){return false;}
            $fp=fopen($file,"r");
            $data=fread($fp,filesize($file));
            fclose($fp);
            if($do=="get_json")
            {
                return $data;
            }
            elseif($do=="get_array")
            {
                $data=json_decode($data);

                $ob_to_arr=function($data) use(&$ob_to_arr)
                {
                    $arr=(array) $data;
                    foreach($arr as $i=>$val)
                    {
                        if(is_object($arr[$i])){ $arr[$i]=$ob_to_arr($arr[$i]); }
                    }
                    return $arr;
                };

                return (array) $ob_to_arr($data);
            }
            else
            {
                return false;
            }
        }
        elseif($do=="set")
        {

            if(!cf_dir_exists($dir)){mkdir($dir);}

            //$fp=fopen($file,"w");
            // print_r($arr);
            cf_fwrite($file,json_encode($arr));
            $this->jsn_dir=$arr;
            //fclose($fp);
            return true;
        }
        elseif($do=="unset")
        {
            if(cf_dir_exists($dir))
            {
                $funnel_ob->delAllFilesWithDir($dir);
            }
        }
    }
    public function getSite($jsn=false)
    {
        return ($jsn)? json_encode($this->jsn_dir):$this->jsn_dir;
    }
}
?>