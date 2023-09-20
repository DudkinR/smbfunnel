<?php
if(!function_exists('cfScarcityJeetDoInstall'))
{
    function cfScarcityJeetDoInstall($pref)
    {
        global $mysqli;
        global $dbpref;
        
        //table for form
        $form_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."scarcity_jeet` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `apply_to` varchar(50) NOT NULL,
            `cfscarcity_page_url` text DEFAULT NULL,
            `funnels` text DEFAULT NULL,
            `Position` varchar(50) NOT NULL,
            `Bar_Gradient1` varchar(55) NOT NULL,
            `Bar_Gradient1_1` varchar(55) NOT NULL,
            `CatchLine` varchar(55) NOT NULL,
            `catch_line_color` varchar(55) NOT NULL,
            `Catch_line_font` varchar(55) NOT NULL,
            `Catch_line_style` varchar(55) NOT NULL,
            `show_action_button` varchar(55) NOT NULL,
            `action_Background_color` varchar(50) NOT NULL,
            `action_button_text` varchar(50) NOT NULL,
            `action_button_text_color` varchar(50) NOT NULL,
            `button_link` varchar(50) NOT NULL,
            `product_box_show` varchar(255) NOT NULL,
            `product_box_image` varchar(255) NOT NULL,
            `product_link` varchar(50) NOT NULL,
            `timezone` varchar(255) NOT NULL,
            `end_date` datetime(6) NOT NULL,
            `timer_text_color` varchar(55) NOT NULL,            
            `expiry_action` varchar(55) NOT NULL,
            `expiry_url` varchar(55) NOT NULL,
            `effect` varchar(55) NOT NULL,
            `effect_delay` varchar(55) NOT NULL,
            `effect_transition` varchar(55) NOT NULL,
            `theme` varchar(55) NOT NULL,
            PRIMARY KEY (`id`)
            )"; 
        $mysqli->query($form_table_query);

    }
}
?>