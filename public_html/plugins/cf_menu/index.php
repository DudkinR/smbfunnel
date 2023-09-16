<?php
if( !defined("EXIT_FORM_CFMENU_PLUGIN_DIR_PATH")  ) {
	define( "EXIT_FORM_CFMENU_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );
}

if( !defined("EXIT_FORM_CFMENU_PLUGIN_URL") ) {
    define("EXIT_FORM_CFMENU_PLUGIN_URL", plugin_dir_url( __FILE__ ));
}

if (!class_exists('CFMenu_base')) {
    require_once('controller/controller.php');
    class CFMenu_base extends CFMenu_controller {
        var $config = false;

        function __construct()
        {
            self::getConfig();
            self::createMenu();
            self::settingFormAjax();
            self::doTableInstall();
            self::addLanguage();
			self::generateLanguage();

            if(isset($_GET['page']) && in_array($_GET['page'],array('cfmenu_allforms', 'cfmenu_form_details', 'cfmenu_basicdetails') ))
            {
                add_action("admin_head", [ $this,"addStyle" ] );
                add_action("admin_head", [ $this,"addScript" ] );
            }
           
            
            self::addShortCode();

        }
        function addLanguage()
		{
			$file = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/hindi_hi.json";
			$file1 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/arabic_ar.json";
			$file2 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/danish_da.json";
			$file4 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/dutch_nl.json";
			$file5 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/english_en.json";
			$file6 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/french_fr.json";
			$file7 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/german_de.json";
			$file8 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/greek_gr.json";
			$file9 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/italian_itl.json";
			$file10 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/japanese_ja.json";
			$file11 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/korean_ko.json";
			$file12 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/malay_ml.json";
			$file13 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/norwegian_no.json";
			$file14 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/polish_pl.json";
			$file15 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/portuguese_po.json";
			$file16 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/romanian_ro.json";
			$file17 = EXIT_FORM_CFMENU_PLUGIN_DIR_PATH."/assets/lang/spanish_sp.json";
			
			register_custom_lang(array(
				"lang_hindi_hi"=>array('file'=>$file),
				"lang_arabic_ar"=>array('file'=>$file1),
				"lang_danish_da"=>array('file'=>$file2),
				"lang_dutch_nl"=>array('file'=>$file4),
				"lang_english_en"=>array('file'=>$file5),
				"lang_french_fr"=>array('file'=>$file6),
				"lang_german_de"=>array('file'=>$file7),
				"lang_greek_gr"=>array('file'=>$file8),
				"lang_italian_itl"=>array('file'=>$file9),
				"lang_japanese_ja"=>array('file'=>$file10),
				"lang_korean_ko"=>array('file'=>$file11),
				"lang_malay_ml"=>array('file'=>$file12),
				"lang_norwegian_no"=>array('file'=>$file13),
				"lang_polish_pl"=>array('file'=>$file14),
				"lang_portuguese_po"=>array('file'=>$file15),
				"lang_romanian_ro"=>array('file'=>$file16),
				"lang_spanish_sp"=>array('file'=>$file17)
			),function($stat, $par){
			});
		}
		function generateLanguage()
		{
			if(!get_option("sf_custom_language_init"))
			{
				$current= get_option('app_language');
				if($current && strlen(trim($current))>0)
				{
					generate_custom_lang();
					add_option("sf_custom_language_init",1);
				}
			}
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
            add_action('admin_menu', function ()
            {
                add_menu_page(t('CF Menu: Menu Builder'), t('CF Menu'), 'cfmenu_allforms', function (){
                    require_once('view/creativemenu_all_forms.php');
                }, plugins_url('assets/img/logo.png', __FILE__), t('All Menus'));


                if (isset($_GET['page']) && $_GET['page'] == 'cfmenu_basicdetails')
                {
                    add_submenu_page('cfmenu_allforms', t('Create New Menu'), t('Add New Menu'), 'cfmenu_basicdetails', function () {
                        require_once('view/creativemenu_settings_form.php');
                    });
                }

                if (isset($_GET['page']) && $_GET['page'] == 'cfmenu_form_details')
                {
                    add_submenu_page('cfmenu_allforms', t('Form Details'), t('Navbar Details'), 'cfmenu_form_details', function () {
                        require_once('view/creativemenu_settings_form.php');
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
            <link rel='stylesheet' href='".EXIT_FORM_CFMENU_PLUGIN_URL."assets/css/style.css'>
            <link rel='stylesheet' href='".EXIT_FORM_CFMENU_PLUGIN_URL."assets/css/sweetalert2.min.css'>
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
            <script type='text/javascript' src='assets/js/jscolor.js'></script>
            <script type='text/javascript' src='".EXIT_FORM_CFMENU_PLUGIN_URL."assets/js/script.js'></script>
            <script type='text/javascript' src='".EXIT_FORM_CFMENU_PLUGIN_URL."assets/js/sweetalert2.all.min.js'></script>
            <script type='text/javascript' src='".EXIT_FORM_CFMENU_PLUGIN_URL."assets/js/multi_inputs.js'></script>
            <script type='text/javascript' src='".EXIT_FORM_CFMENU_PLUGIN_URL."assets/js/drag_n_drop.js'></script>
            ";
        }

        function settingFormAjax()
        {
            add_action( 'cf_ajax_cfmenufunnelsajaxsettings', function()
            {
                $forms_ob=$this->load('form_controller');
                $forms_ob->getFunnelsAjaxRequest( $_REQUEST );
            });
            add_action( 'cf_ajax_cfmenufunnelspageajaxsettings', function()
            {
                $forms_ob=$this->load('form_controller');
                $forms_ob->getPagesNavDetails( $_REQUEST );
            });
            add_action( 'cf_ajax_cfmenuajaxsettingsdata', function()
            {
                $forms_ob=$this->load('form_controller');
                $forms_ob->createUpdateForm( $_REQUEST );
            });
            add_action( 'cf_ajax_cfmenushowfunnelsajaxsettings', function()
            {
                $forms_ob=$this->load('form_controller');
                $forms_ob->getFunnelsData( $_REQUEST );
            });
        }

        function doTableInstall() {
            register_activation_hook(function () {
                require_once('controller/table_install.php');
                cfcreative_install_table();
            });
        }

        function addShortCode()
        {
            add_shortcode( "cfmenu", function($params){
				if(isset($params['id'])) {
                    $v=0;
				    if(isset($this->config->version)) {
				    	$v=$this->config->version;
				    }

				    $id = $params['id'];
				    ob_start();
				    $forms_ob = $this -> load('form_controller');
				    $forms_ob -> getFormUI($id,$v);
				    $data = ob_get_clean();
				    return $data;
				}
			});
        }
    }
    new CFMenu_base();
}