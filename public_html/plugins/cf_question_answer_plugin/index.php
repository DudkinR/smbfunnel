<?php
if( !defined("CFQUESTION_PLUGIN_DIR_PATH")  ) {
	define( "CFQUESTION_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}

if( !defined("CFQUESTION_PLUGIN_URL") ) {
    define("CFQUESTION_PLUGIN_URL", plugin_dir_url( __FILE__ ));
}


if (!class_exists('CFQuestion_base')) {
    require_once('controller/controller.php');
    class CFQuestion_base extends CFQuestion_controller {
        var $config = false;
        var $funnel_id=false;
        function __construct()
        {
                
            self::getConfig();
            self::createMenu();
            self::Install();
            self::loadScripts();
            self::addShortCode();
            self::getAjaxRequest();
            if( isset($_GET['page']) && in_array($_GET['page'],array('cf_question_all','cf_question_setting','cf_question_all_question')) ){
                add_action("admin_head",[$this,"addStyle"]);
                add_action("admin_head",[$this,"addScript"]);
            }
            add_action('cf_head',function( $data ){
				
				$this->funnel_id = $data['funnel_id'];
				$this->loadStyles();
			});
            add_action("cf_footer",[$this,"loadUserScript"]);
        }

        public function addStyle(){
            $v=0;
            if(isset($this->config->version))
            {
                $v=$this->config->version;
            }
            echo "<link rel='stylesheet' href='".CFQUESTION_PLUGIN_URL."/assets/css/style.css?v=".$v."'>";
        }
        
        public function addScript(){
            $v=0;
            if(isset($this->config->version))
            {
                $v=$this->config->version;
            }
            $valid_pages=array('cf_question_all','cf_question_setting');
            if( isset( $_GET['page'] ) && in_array( $_GET['page'], $valid_pages ) ) {
                
                echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";
                echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
            }
            echo "<script src='".CFQUESTION_PLUGIN_URL."/assets/js/script.js?v=".$v."' ></script>";
        }

        function loadScripts() {
           
        }
        function loadStyles()
        {
            echo '<link rel="stylesheet" href="'.CFQUESTION_PLUGIN_URL.'assets/css/user.css">';
        }
        function loadUserScript()
        {
            echo '<script src="'.CFQUESTION_PLUGIN_URL.'assets/js/sweet_alert.js"></script>';
            echo '<script src="'.CFQUESTION_PLUGIN_URL.'assets/js/user.js"></script>';

        }


        function getConfig()
        {
            if (!$this->config)
            {
                $file = plugin_dir_path(__FILE__);
                $fp = fopen($file . 'config.json', 'r');
                $data = json_decode(fread($fp, filesize($file . 'config.json')));
                fclose($fp);
                if (isset($data->version)) {
                    $this->config = $data;
                }
            }
        }
        
        function createMenu()
        {
            add_action('admin_menu',function(){
                $logo_url=plugins_url('assets/img/logo.png', __FILE__);

                add_menu_page('Q&A','Q&A','cf_question_all',function(){
                    require_once('view/all_question.php');
                },$logo_url,'Q&A');
                                            
                add_submenu_page('cf_question_all','Setting','Setting','cf_question_setting',function(){
                    require_once(CFQUESTION_PLUGIN_DIR_PATH.'/view/setting.php');
                });	
            });
        }
        function Install() {
            register_activation_hook(function () {
                require_once('controller/install.php');
                cfproduct_question_install();
            });
        }

        function addShortCode()
        {
            add_shortcode( "cfquestion", function(){
                global $mysqli;
                $pid='';
                if( isset( $_GET['product'] ) )
                {
                    $pid = $mysqli->real_escape_string( trim( $_GET['product']  ) );
                }
                
                if( $pid )    
                {
                    $v=0;
                    if(isset($this->config->version)) {
                        $v=$this->config->version;
                    }
                    ob_start();
                    $forms_ob = $this -> load('form_control');
                    $forms_ob -> getFormUI($pid,$v,$this->funnel_id);
                    $data = ob_get_clean();
                    return $data;
                }
            });
        }
        function getAjaxRequest(){
                
            add_action("cf_ajax_cf_questions_ajax",function(){
                $load = $this->load("form_control");
                $load->insertFormData($_REQUEST);
            });

            add_action("cf_ajax_cf_preview_answers",function(){
                $load = $this->load("form_control");
                $load->previewData($_REQUEST);
            });

            add_action("cf_ajax_cf_insert_answer",function(){
                $load = $this->load("form_control");
                $load->insertAnswer($_REQUEST);
            });
                        
            add_action("cf_ajax_cf_edit_preview_answer",function(){
                $load = $this->load("form_control");
                $load->previewData($_REQUEST);
            });

            add_action("cf_ajax_cf_edit__answer",function(){
                $load = $this->load("form_control");
                $load->updateAnswer($_REQUEST);
            });
            add_action("cf_ajax_cf_delete_question",function(){
                $load = $this->load("form_control");
                $load->deleteData($_REQUEST);
            });

            add_action("cf_ajax_cf_insert_style_ajax",function(){
                $load = $this->load("setting_control"); 
                $load->getStyleUI($_REQUEST);      
            });
            add_action("cf_ajax_nopriv_cf_question_loadmore",function(){
              
                $load = $this->load("form_control"); 
                $load->loadMoreQuestions($_REQUEST);      
                die();
            });
            add_action("cf_ajax_cf_question_loadmore",function(){
                $load = $this->load("form_control"); 
                $load->loadMoreQuestions($_REQUEST);  
                die();    
            });
        }
    }
    new CFQuestion_base();
}