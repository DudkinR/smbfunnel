<?php
if( !defined("EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_PATH"))
	define( "EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );

if( !defined("EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL"))
    define("EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL", plugin_dir_url( __FILE__ ));

if (!class_exists('CF_Global_AutoResponder_base'))
{
    require_once('controller/controller.php');
    class CF_Global_AutoResponder_base extends CF_Global_AutoResponder_controller
    {
        var $config = false;

        function __construct()
        {
            self::getConfig();
            self::createMenu();
            self::settingFormAjax();
            self::globalRegisterAutoresponder();

            //admin_headers
			add_action('admin_head',function()
            {
				self::loadScripts();
			});

            if(isset($_GET['page']) && in_array($_GET['page'],array('cf_global_au_main', 'cf_global_au_settings') ))
            {
                add_action("admin_head", [ $this,"addStyle" ] );
				add_action( "admin_head",[ $this, "addScript" ] );
			}

            add_action('cf_footer',function(){
			});
        }

        function getConfig()
        {
            if (!$this->config)
            {
                $file = plugin_dir_path(__FILE__);
                $fp = fopen($file . 'config.json', 'r');
                $data = json_decode(fread($fp, filesize($file . 'config.json')));
                fclose($fp);
                if (isset($data->version))
                {
                    $this->config = $data;
                }
            }
        }

        function createMenu()
        {
            add_action('admin_menu', function () {
                add_menu_page('CF Universal Autoresponder Adaptor: AU Generator', 'CF Universal Autoresponder Adaptor', 'cf_global_au_main', function () {
                    require_once('view/cf_global_au_main.php');
                }, plugins_url('assets/img/logo.png', __FILE__), 'All Setting');


                if (isset($_GET['page']) && $_GET['page'] == 'cf_global_au_settings')
                {
                    add_submenu_page('cf_global_au_main', 'Add New Form', 'Add New Form', 'cf_global_au_settings', function () {
                        require_once('view/cf_global_au_settings.php');
                    });
                }
            });
        }


        function addStyle()
        {
            $v=0;
			if(isset($this->config->version))
			{
				$v=$this->config->version;
			}
			echo "
            <link rel='stylesheet' href='".EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL."assets/css/style.css'>
            <link rel='stylesheet' href='".EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL."assets/css/sweetalert2.min.css'>
            ";
        }

        function addScript()
        {
            $v=0;
			if(isset($this->config->version))
            {
				$v=$this->config->version;
			}

            echo "
            <script src='".EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL."assets/js/script.js?v=".$v."' ></script>
            <script src='".EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL."assets/js/sweetalert2.all.min.js?v=".$v."' ></script>
            <script src='".EXIT_GLOBAL_AUTORESPONDER_PLUGIN_DIR_URL."assets/js/multi_inputs.js?v=".$v."' ></script>
            ";
        }

        function loadScripts()
        {
			$valid_pages=array('cf_popup_all_forms', 'cf_new_form', 'cf_popup_created_forms');
			if(isset($_GET['page']) && in_array($_GET['page'], $valid_pages))
            {
				echo "<script src='assets/js/node_modules/js-base64/base64.js?v=".get_option('qfnl_current_version')."'></script>";

				echo "<script src='assets/js/request.js?v=".get_option('qfnl_current_version')."'></script>";
			}
		}

        function settingFormAjax()
        {
            add_action( 'cf_ajax_cfglobalajaxsettings', function()
            {
                $forms_ob=$this->load('form_controller');
                $forms_ob->getAjaxRequest( $_REQUEST );
            });
        }

        function globalRegisterAutoresponder()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref.'quick_autoresponders';
            $autoresponder_name = "cfglobalautoresponder";
            
            $qry = $mysqli->query("SELECT * FROM `".$table."` WHERE `autoresponder_name`='".$autoresponder_name."' ORDER  BY `id` DESC");
            
            if(!$qry || $qry->num_rows<1) return;
            $incr = 0;
            
            while($r=$qry->fetch_object())
            {
                $arr = json_decode($r->exf, true);
                $autoresponder_details = json_decode($r->autoresponder_detail, true);
                $id="cfautores".$autoresponder_name."_".$r->id;
                
                $title=$r->autoresponder;
                
                if($autoresponder_name=='cfglobalautoresponder')
                {
                    register_autoresponder($id,$title,function($data, $arg2){
                        $exf = $arg2[0];
                        $autoresponder_details = $arg2[1];
                        $custom_form_input_data = array();
                        foreach($exf as $key=>$value)
                        {
                            if((isset($value['form_method'])?$value['form_method']:'NO') == "POST" || (isset($value['form_method'])?$value['form_method']:'NO') == "GET")
                            {
                                $custom_form_input_data['api_url'] = $value['api_url'];
                                $custom_form_input_data['form_method'] = $value['form_method'];
                                unset($value['api_url']);
                                unset($value['form_method']);
                            }
                            if(!empty($value))
                            {
                                if($value['custom'])
                                {
                                    if(is_array($data))
                                    {
                                        foreach($data as $d_index=>$d_val)
                                        {
                                            if(str_replace('{'.$d_index.'}', $d_val, $value['title']) == $d_val) $custom_form_input_data[$value['name']] = str_replace('{'.$d_index.'}', $d_val, $value['title']);
                                        }
                                    }
                                }
                                else
                                {
                                    if(is_array($data))
									{
										if((isset($data[$value['name']])))
										{
											$custom_form_input_data[$value['name']] = $data[$value['name']];
										}
										else $custom_form_input_data[$value['name']] = $value['title'];
									}
                                }
                            }
                        }
                        $auth_details = $autoresponder_details['Authorization'];
                        if($auth_details != "") $custom_form_input_data['authRequired'] = 1;
                        else $custom_form_input_data['authRequired'] = 0;
                        
                        $forms_ob=$this->load('auto_controller');
                        $message = json_decode($forms_ob->CFGlobalAU($custom_form_input_data, $autoresponder_details), true);
                    },array($arr, $autoresponder_details));
                }
                else return false;
            }
        }
    }
    new CF_Global_AutoResponder_base();
}