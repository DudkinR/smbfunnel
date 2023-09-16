<?php
if(!function_exists('cf_respo_dbTable')){
    function cf_respo_dbTable($pref){
        global $mysqli;
        global $dbpref;

         //table for form
        $form_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."respo_popup_form` (
            `formId` int(11) NOT NULL AUTO_INCREMENT,
            `form_name` varchar(500) NOT NULL,
            `header_text` text NOT NULL,
            `footer_text` text NOT NULL,
            `theme_id` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`formId`)
            )";
        $mysqli->query($form_table_query);

        $form_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."respo_extra_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `formId` int(11) NOT NULL,
            `formBackCol` varchar(255) NOT NULL,
            `headBackCol` varchar(255) NOT NULL,
            `headerPadding` int(11) NOT NULL,
            `headerMargin` int(11) NOT NULL,
            `footBackCol` varchar(255) NOT NULL,
            `footerPadding` int(11) NOT NULL,
            `footerMargin` int(11) NOT NULL,
            `submitBackCol` varchar(255) NOT NULL,
            `submitBtnText` varchar(255) NOT NULL,
            `submitBtnCol` varchar(255) NOT NULL,
            `errorTxtCol` varchar(255) NOT NULL,
            `button_align` varchar(255) NOT NULL,
            `form_width` int(11) NOT NULL,
            `form_appear` varchar(255) NOT NULL,
            `delay_value` int(11) NOT NULL,
            `use_as_exit` int(11) NOT NULL,
            `use_as_delay` int(11) NOT NULL,
            `custom_css` varchar(255) NOT NULL,
            `don_show` int(11) NOT NULL,
            `on_btn_click` int(11) NOT NULL,
            `allow_process_in_cf` int(11) NOT NULL,
            `display_setup` int(11) NOT NULL,
            `redirect_url` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
            )";
        $mysqli->query($form_table_query);

        //table for inputs
        $input_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."respo_popup_inputs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `form_id` int(11) NOT NULL,
            `name` varchar(500) DEFAULT NULL,
            `placeholder` varchar(500) DEFAULT NULL,
            `type` varchar(500) DEFAULT NULL,
            `title` varchar(500) DEFAULT NULL,
            `position` int(11) DEFAULT NULL,
            `required` int(11) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`ID`)
            )";
        $mysqli->query($input_table_query);

        //table for optins
        $table= $dbpref.'respo_popup_optins';
        $optin_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint not null auto_increment,
            `form_id` int(11) not null,
            `name` varchar(255) not null,
            `email` varchar(255) not null,
            `exf` text not null,
            `url` text not null,
            `ip` text not null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`ID`)
        )
        ";
        $mysqli->query($optin_table_query);
    }
}
?>