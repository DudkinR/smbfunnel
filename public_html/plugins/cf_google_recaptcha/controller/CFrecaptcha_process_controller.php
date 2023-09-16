<?php
if (!class_exists('CFrecaptcha_process_controller')) {

class CFrecaptcha_process_controller
{
    var $pref;
    
    function __construct($arr)
    {
       
        $this->loader = $arr['loader'];

        
    }
    
   

    function getFormUI($id = null, $config_version = 0)
        {
            
            $form_data = self::getFormSetup($id);
            if ($form_data) {
                echo "
                <!-- CF  starts here -->";
                require plugin_dir_path(dirname(__FILE__, 1)) ."view/shortcode_v2.php";
                

                echo "
                <!-- CF  ends here -->
                ";
            } else {
                echo "";
            }
        }
        function getFormUIv3($id = null, $config_version = 0)
        {
            
            $form_data = self::getFormSetup($id);
            if ($form_data) {
                echo "
                <!-- CF  starts here -->";
                require plugin_dir_path(dirname(__FILE__, 1)) ."view/shortcode_v3.php";

                echo "
                <!-- CF  ends here -->
                ";
            } else {
                echo "";
            }
        }
        function getFormSetup($form_id = null)

        {
            
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'google_recaptcha';
            $form_id = trim($mysqli->real_escape_string($form_id));

            $r = $mysqli->query("SELECT * FROM `" . $table . "` WHERE `id`=" . $form_id);

            if ($r->num_rows > 0) {
                $data = $r->fetch_assoc();
                return $data;
            }
            return 0;
        }

      
    
   
}
}
?>