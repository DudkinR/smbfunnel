<?php
if(!function_exists('cfExitoDoInstall'))
{
    function cfExitoDoInstall($pref)
    {
        global $mysqli;
        global $dbpref;
        
        //table for form
        $form_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."ext_popup_form` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `form_name` varchar(500) NOT NULL,
            `header_text` text DEFAULT NULL,
            `footer_text` text DEFAULT NULL,
            `form_setup` text DEFAULT NULL,
            `form_css` text DEFAULT NULL,
            `is_global` int(1) not null default 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `user_id` int(16) DEFAULT 1,
            PRIMARY KEY (`id`)
            )";
        $mysqli->query($form_table_query);

        //table for inputs
        $input_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."ext_popup_inputs` (
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
            `user_id` int(16) DEFAULT 1,
            PRIMARY KEY (`id`)
            )";
        $mysqli->query($input_table_query);

        //table for optins
        $table= $dbpref.'ext_popup_optins';
        $optin_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint not null auto_increment,
            `form_id` int(11) not null,
            `name` varchar(255) not null,
            `email` varchar(255) not null,
            `exf` text not null,
            `url` text not null,
            `ip` text not null,
            `added_on` timestamp not null default current_timestamp(),
            `user_id` int(16) DEFAULT 1,
            primary key(`id`)
        )
        ";
        $mysqli->query($optin_table_query);
    }
}
?>