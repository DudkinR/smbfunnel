<?php
if(!function_exists('cfquizDoInstall'))
{
    function cfquizDoInstall($pref)
    {
        global $mysqli;
        global $dbpref;
        
        //table for quiz
        $quiz_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."cfquiz_popup` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `quiz_name` varchar(500) NOT NULL,
            `header_text` text DEFAULT NULL,
            `footer_text` text DEFAULT NULL,
            `quiz_setup` text DEFAULT NULL,
            `quiz_css` text DEFAULT NULL,
            `is_global` int(1) not null default 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
            )";
        $mysqli->query($quiz_table_query);

        //table for inputs
        $input_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."cfquiz_popup_inputs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `quiz_id` int(11) NOT NULL,
            `name` varchar(500) DEFAULT NULL,
            `placeholder` varchar(500) DEFAULT NULL,
            `type` varchar(500) DEFAULT NULL,
            `title` varchar(500) DEFAULT NULL,
            `position` int(11) DEFAULT NULL,
            `required` int(11) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
            )";
        $mysqli->query($input_table_query);

        //table for optins
        $table= $dbpref.'cfquiz_popup_optins';
        $optin_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint not null auto_increment,
            `quiz_id` int(11) not null,
            `name` varchar(255) not null,
            `email` varchar(255) not null,
            `exf` text not null,
            `url` text not null,
            `ip` text not null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
        )
        ";
        $mysqli->query($optin_table_query);

        
        //table for questions
        //id,quiz_id,question_pos,question,options_json,added_on
        $table= $dbpref.'cfquiz_questions2';
        $questions_table_query2="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` int(11) not null auto_increment,
            `quiz_id` int(11) not null,
            `question_pos` int(11) not null,
            `question` varchar(500) not null,
            `options` varchar(1000) not null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
        )";
        $mysqli->query($questions_table_query2);


        //table for quiz response
        //id,quiz_id,user_details_json, quiz_response_json,added_on
        $table= $dbpref.'cfquiz_response';
        $response_table_query2="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` int(11) not null auto_increment,
            `quiz_id` int(11) not null,
            `user_details` varchar(2000),
            `quiz_response` varchar(2000) not null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
        )";
        $mysqli->query($response_table_query2);
    }
}
?>