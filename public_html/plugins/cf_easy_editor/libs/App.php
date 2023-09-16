<?php
 namespace CF_EASY_EDITOR\libs;
 class App{
     private $plugin_config;
     private $default_editor_url;
     private $current_editor_url;
     private $editor_url_path; // It will contain trailing slash
     private $editor_asset_save_url;
     private $editor_data_save_url;

     function __construct()
     {
     }
     function init(){
         self::registerPageSaveAPI();
         self::doAssignPageEditor();
         self::doToggleFromDefaultEditor();
     }
     function readPluginConfig(){
         if($this->plugin_config ===null){
            $file= $this->plugin_path.'config.json';
            $content= self::readFile($file);
            $config= json_decode($content);
         }
         else{
            $config= $this->plugin_config;
         }
         return $config;
     }
     private function doAssignPageEditor(){
        if(isset($_GET['page']) && ($_GET['page']==='page_builder'))
         {
             $this->default_editor_url= add_query_arg(array('builder_type'=>'default'), $_SERVER['REQUEST_URI']);
             $this->current_editor_url= add_query_arg(array('builder_type'=> CF_EASY_EDITOR_BUILDER_TYPE), $_SERVER['REQUEST_URI']);

             if(isset($_REQUEST['easy_editor_remember_choice']))
                {
                    update_option('default_page_editor', $_GET['builder_type']);
                }

             if(isset($_GET['builder_type'])){
                if(strtolower($_GET['builder_type'])===CF_EASY_EDITOR_BUILDER_TYPE){
                    $app_loader= $GLOBALS['loader'];

                    $editor_path= "";
                    $prs_url= parse_url($this->plugin_url);
                    if(isset($prs_url['path'])){
                        $editor_path= rtrim($prs_url['path'], '/');   
                    }
                    $editor_path .= "/assets/editor/";
                    $this->editor_url_path= $editor_path;

                    // Duplicated from the native code
                    if(isset($_GET['fid']))
                    {
                        $funnelob= $app_loader->loadFunnel();
                        $content= $funnelob->readContent($_GET['fid'],$_GET['lbl'],$_GET['abtype']);
                
                        $this_page_data= $funnelob->getPageFunnel($_GET['fid'], $_GET['abtype'], $_GET['lbl']);
                        if($this_page_data)
                        {
                            $_GET['folder']= $this_page_data->filename;
                        }
                    } 
                    $header = $app_loader->loadJSTranslator();
                    $header .= $app_loader->loadScript('request');
                
                    $footer= $app_loader->loadMediaBox();
                    // Ends here

                    require_once($this->plugin_path.'views/page_editor.php');
                    die();
                }

             }
             else{
                 self::goToSelectedTextEditor();
                 require_once($this->plugin_path.'views/editor_selector.php');
                 die();
             }
         }
     }
     private function goToSelectedTextEditor(){
         $editor_key= 'default_page_editor';
         $do_redirect= false;
         $editor_type= 'default';
         
         if(isset($_REQUEST['easy_editor_select_editor'])){
            $editor_type= $_REQUEST['easy_editor_select_editor'];
            if(isset($_REQUEST['easy_editor_remember_choice']))
            {
                update_option($editor_key, $editor_type);
            }

            $do_redirect= true;
         }
         else if(get_option($editor_key)){
             $do_redirect= true;
             $editor_type= get_option($editor_key);
         }

         if($do_redirect)
         {
            $editor_url= add_query_arg(array('builder_type'=> $editor_type), $_SERVER['REQUEST_URI']);
            header("Location: ".$editor_url);
            echo <<<REDIRECT
            <script>window.location=`$editor_url`;</script>
            REDIRECT;
            die();
         }
     }
     private function readFile($file){
         $data= "";
         if(is_file($file))
         {
            $fp= fopen($file, 'r');
            $size= filesize($file);
            if($size>0)
            { $data= fread($fp, filesize($file)); }
            fclose($fp);
         }
         return $data;
     }
     private function updateRequired($main_app_version, $saved_plugin_version, $site_token){
         global $current_app_version;
         $plugin_config= $this->readPluginConfig();
         $current_plugin_version= (isset($plugin_config->version))? $plugin_config->version:false;
         
         return (($current_app_version !==$main_app_version)||($current_plugin_version !==$saved_plugin_version)||($site_token !==get_option('site_token')))? true:false;
     }
     private function getSetFileCache($file, $doo="get", $content_to_save= []){
        //$doo= get or set
        $stat= false;
        $file= $this->plugin_path."assets/cache/".$file;

        if($doo=="get"){
            if(is_file($file)){
                $content= json_decode($this->readFile($file));
                if(isset($content->app_version)){
                    $app_version= $content->app_version;
                    $plugin_version= $content->plugin_version;
                    $site_token= cf_enc($content->site_token, "decrypt");

                    $update_required= $this->updateRequired($app_version, $plugin_version, $site_token);
                    if(!$update_required){
                        $stat= $content->data;
                    }
                }
            }
        }
        else if($doo==='set'){
            global $current_app_version;
            $plugin_config= $this->readPluginConfig();
            $current_plugin_version= (isset($plugin_config->version))? $plugin_config->version:false;
            $arr= array(
                'app_version'=> $current_app_version,
                'plugin_version'=> $current_plugin_version,
                'site_token'=> cf_enc(get_option('site_token')),
                'data'=> $content_to_save
            );
            $fp= fopen($file, 'w');
            fwrite($fp, json_encode($arr));
            fclose($fp);
            $stat= true;
        }
        return $stat;
     }
     private function getTemplateCache(){
         $data= $this->getSetFileCache("template.json");
         return $data;
     }
     private function setTemplateCache($data){
        $stat= $this->getSetFileCache("template.json", "set", $data);
        return $stat;
    }
     private function loadEditorTemplates(){
        $arr= [];
        $has_cache= self::getTemplateCache();

        if($has_cache && is_array($has_cache) && count($has_cache)>0){
            $arr= $has_cache;
        }
        else{
            $read_templates= function($temp_dir, $files_only= false){
                $template_dirs= [];
                if(cf_dir_exists($temp_dir))
                {
                    $template_dirs= array_map(function($dir)use($temp_dir, $files_only){
                        if(strlen($dir)>0 && !in_array($dir, ['.', '..']))
                        {
                            if(!cf_dir_exists($dir))
                            {
                                $dir= trim($dir, '/');
                                if(!$files_only)
                                {$dir= $temp_dir.$dir;}
                            }
                            return $dir;
                        }
                        return null;
                    }, scandir($temp_dir));

                    $template_dirs= array_filter($template_dirs, function($d){
                        return (is_string($d) && strlen($d)>0)? true: false;
                    });
                }
                return $template_dirs;
            };

            // Read template dirs
            $temp_dir= $this->plugin_path.'assets/editor/templates/';
            $template_type_dirs= $read_templates($temp_dir, true);

            $title_reg= "/<title>[^<]*<\/title>/";

            if(is_array($template_type_dirs) && count($template_type_dirs)>0)
            {
                foreach($template_type_dirs as $template_type_dir){
                    $dir= trim($template_type_dir, '/');
                    $template_type= $dir;

                    $dir= $temp_dir.$dir;

                    $template_dirs= $read_templates($dir, true);
                    foreach($template_dirs as $template_dir)
                    {
                        $template_dir= trim($template_dir, '/');
                        $template_dir_path= $dir.'/'.$template_dir;
                        $indexes= glob($dir.'/'.$template_dir.'/index.html');
                        $template_id= $template_dir;

                        if(is_array($indexes) && count($indexes)>0){
                            $index_file= $indexes[0];
                            $content= self::readFile($index_file);
                            preg_match_all($title_reg, $content, $title_arr);
                            $title= "No Title";
                            if(isset($title_arr[0]) && isset($title_arr[0][0]) && is_string($title_arr[0][0]))
                            {
                                $t= trim(preg_replace("/(<title>|(<\/title>))/i", "", $title_arr[0][0]));
                                $title= (strlen($t)>0)? $t:$title;
                            }

                            $thumb = 'thumb.svg';
                            if (!file_exists($template_dir_path.'/'.$thumb)) {
                                $thumb = 'thumb.png';
                            }

                            $thumb= $this->editor_url_path."templates/$template_type/$template_id/$thumb";
                            
                            $template_url= "@server-request-uri@&load_easy_editor_template=".base64_encode('templates/' . $template_type . '/' . $template_id);

                            $current_template= array(
                                "name"=> $title, 
                                "thumbnail"=> $thumb,
                                "url"=> $template_url
                            );

                            array_push($arr, $current_template);
                        }
                    }

                    // $single

                    // $indexes= glob($dir.'/index.html');
                    // print_r($indexes);
                }
            }
            self::setTemplateCache($arr);
        }
       
        // Read
        return $arr;
     }
     private function loadCustomWidgets(){
         $custom_widgets= array("scripts"=>[], "widget_objects"=>[], "action_objects"=>[]);
         $cache_file= "widgets.json";
         $cached_data= self::getSetFileCache($cache_file, "get");
         if($cached_data){
             if(is_object($cached_data))
             {
                $cached_data= (array) $cached_data;
             }
            $custom_widgets= $cached_data;
         }
         else{
            $widget_path= $this->plugin_path."assets/editor/custom_widgets/widgets/";
            $widget_path_url= $this->editor_url_path."custom_widgets/widgets/";

            $actions_path= $this->plugin_path."assets/editor/custom_widgets/actions/";
            $actions_path_url= $this->editor_url_path."custom_widgets/actions/";

            $widget_files= scandir($widget_path);
            $action_files= scandir($actions_path);

            $generate_widget= function($files, $path_url, $type)use(&$custom_widgets){
                if(is_array($files) && count($files)>0)
                {
                    $reg= '/^(.)+((\.js)+)$/i';
                    
                    foreach($files as $file)
                    {
                        if(preg_match($reg, $file)){
                            array_push($custom_widgets["scripts"], "<script src='$path_url$file'></script>");
                            $ob= "new ".str_replace(".js", "", $file);
                            array_push($custom_widgets[$type], $ob);
                        }
                    }
                }  
            };

            $generate_widget($widget_files, $widget_path_url, 'widget_objects');
            $generate_widget($action_files, $actions_path_url, 'action_objects');
            
            self::getSetFileCache($cache_file, 'set', $custom_widgets);
         }
         return $custom_widgets;
     }
     private function registerPageSaveAPI(){
        $this->editor_data_save_url= get_option('install_url').'/req.php';
        $key= CF_EASY_EDITOR_BUILDER_TYPE.'_save_assets';
        $this->editor_asset_save_url= get_option('install_url').'/index.php?page=callback_api&action='.$key;

        add_action('cf_api_'.$key, function(){
            $file= $this->plugin_path.'/assets/editor/asset.php';
            require_once($file);
            die();
        });
     }
     private function doToggleFromDefaultEditor(){
         if(isset($_GET['page']) && ($_GET['page']==='page_builder'))
         {
             $type= CF_EASY_EDITOR_BUILDER_TYPE;
             echo <<<DOTOGGLETOEASYEDITOR
             <script>   
                document.addEventListener('readystatechange', function(){
                        if(this.readyState==='complete'){
                            try{
                                let doc = document.querySelector(`#vvveb-builder #bottom-panel .btn-group`);
                                let btn= document.createElement("button");
                                btn.setAttribute('class', 'btn btn-sm btn-light btn-sm');
                                btn.innerHTML= "<i class='fas fa-pencil-alt'></i>&nbsp;Use Easy Editor";
                                doc.appendChild(btn);
                                btn.addEventListener('click', function(){
                                    let u= new URL(window.location.href);
                                    u.searchParams.set('builder_type', "$type");
                                    u.searchParams.append('easy_editor_remember_choice', 1);
                                    window.location= u.href;
                                })
                            }catch(err){console.log(err);}
                        }
                    });
             </script>
             DOTOGGLETOEASYEDITOR;
        }
     }
 }
?>