<?php
if(!function_exists('cfSeoDoInstall'))
{
    function cfSeoDoInstall($pref)
    {
        global $mysqli;
        global $dbpref;
        
        //table for seo setup
        $table= $dbpref.$pref."setup";
        $optin_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint not null auto_increment,
            `page_name` text null,
            `page_url` text null,
            `seo_data` text null,
            `custom_meta` text null,
            `schema_org` text null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
        )
        ";
        $mysqli->query($optin_table_query);
                
        //table for seo webmaster
        $table= $dbpref.$pref."webmaster";
        $webmaster_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint not null auto_increment,
            `google` text null,
            `bing` text null,
            `yandex` text null,
            `baidu` text null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
        )
        ";
        $mysqli->query($webmaster_table_query);
        
        //table for 
        $table= $dbpref.$pref."social";
        $social_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint not null auto_increment,
            `accounts` text null,
            `accounts_data` text null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
        )
        ";
        $mysqli->query($social_table_query);
    }
}
?>