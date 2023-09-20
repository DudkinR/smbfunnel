<?php

global $mysqli;
global $dbpref;

if (isset($_GET['page']) && $_GET['page'] = "cf_scarcity_jeet_all_forms") {
    $table = $dbpref . "scarcity_jeet";
    $getuserid = "select id from " . $table . " order by id desc";
    $result = $mysqli->query($getuserid);
    $existingRecordCount = $result->num_rows;
    if ($existingRecordCount > 0) {
        $user = $result->fetch_assoc();
        $row_id = $user['id'];
        $returnOptions =  $mysqli->query("SELECT * FROM `" . $table . "` WHERE `id`=" . $row_id);
        $data = $returnOptions->fetch_array();
        $apply_to = $data['apply_to'];
        $page_url = $data['cfscarcity_page_url'];
        $cfscarcity_page_url = explode("\\r\\n", rtrim($page_url, "\\r\\n"));
        $funnels = $data['funnels'];
        // $Position = $data['Position'];
        $Position = "";
        $Bar_Gradient1 = $data['Bar_Gradient1'];
        $Bar_Gradient1_1 = $data['Bar_Gradient1_1'];
        $CatchLine = $data['CatchLine'];
        $catch_line_color = $data['catch_line_color'];
        $Catch_line_font = $data['Catch_line_font'];
        $Catch_line_style = $data['Catch_line_style'];
        $show_action_button = $data['show_action_button'];
        $action_Background_color = $data['action_Background_color'];
        $action_button_text = $data['action_button_text'];
        $action_button_text_color = $data['action_button_text_color'];
        $button_link = $data['button_link'];
        $product_box_show = $data['product_box_show'];
        $product_box_image = $data['product_box_image'];
        $product_link = $data['product_link'];
        $timezone = $data['timezone'];
        $existing_end_date = $data['end_date'];
        $end_date_array = explode(" ", $existing_end_date);
        $time_value_array = explode(".", $end_date_array[1]);
        $end_date = $end_date_array[0] . 'T' . $time_value_array[0];
        $timer_text_color = $data['timer_text_color'];
        $expiry_action = $data['expiry_action'];
        $expiry_url = $data['expiry_url'];
        $effect = $data['effect'];
        $effect_delay = $data['effect_delay'];
        $effect_transition = $data['effect_transition'];
        $theme = $data['theme'];
    } else {
        $apply_to = "shortcodeOnly";
        $cfscarcity_page_url = [];
        $funnels = "";
        $Position = "";
        $Bar_Gradient1 = "#1ab2ff";
        $Bar_Gradient1_1 = "#00807d";
        $CatchLine = "This Offer Expire In :";
        $catch_line_color = "#ffffff";
        $Catch_line_font = "";
        $Catch_line_style = "";
        $show_action_button = "true";
        $action_Background_color = "#ffffff";
        $action_button_text = "Subscribe Now";
        $action_button_text_color = "#000000";
        $button_link = "";
        $product_box_show = "true";
        $product_box_image = "";
        $product_link = "";
        $timezone = "";
        $end_date = "";
        $timer_text_color = "#000000";
        $expiry_action = "";
        $expiry_url = "";
        $effect = "bounceInLeft";
        $effect_delay = "";
        $effect_transition = "";
        $theme = "theme_a";
    }
}
if (isset($_POST['Submit'])) {
    $show_action_button = '';
    $product_box_show = '';
    $end_date = '';
    $apply_to = $mysqli->real_escape_string($_POST['apply_to']);
    $cfscarcity_page_url = $mysqli->real_escape_string($_POST['cfscarcity_page_url']);
    $funnels = (isset($_POST['scarcity_funnels'])) ? implode(',', $_POST['scarcity_funnels']) : "";
    $funnels = $mysqli->real_escape_string($funnels);
    // $Position = $mysqli->real_escape_string($_POST['Position']);
    $Bar_Gradient1 = $mysqli->real_escape_string($_POST['Bar_Gradient1']);
    $Bar_Gradient1_1 = $mysqli->real_escape_string($_POST['Bar_Gradient1_1']);
    $CatchLine = $mysqli->real_escape_string($_POST['CatchLine']);
    $catch_line_color = $mysqli->real_escape_string($_POST['catch_line_color']);
    // $Catch_line_font = $mysqli->real_escape_string(($_POST['Catch_line_font']);
    // $Catch_line_style = $mysqli->real_escape_string(($_POST['Catch_line_style']);
    if (isset($_POST['show_action_button'])) {
        $show_action_button = $mysqli->real_escape_string($_POST['show_action_button']);
    };
    $action_Background_color = $mysqli->real_escape_string($_POST['action_Background_color']);
    $action_button_text = $mysqli->real_escape_string($_POST['action_button_text']);
    $action_button_text_color = $mysqli->real_escape_string($_POST['action_button_text_color']);
    $button_link = $mysqli->real_escape_string($_POST['button_link']);
    if (isset($_POST['product_box_show'])) {
        $product_box_show = $mysqli->real_escape_string($_POST['product_box_show']);
    };
    $product_box_image = $mysqli->real_escape_string($_POST['product_box_image']);
    if (isset($_POST['product_box_show'])) {
        $product_box_show = $_POST['product_box_image'] == '' ? 'false' : 'true';
    }
    $product_link = $mysqli->real_escape_string($_POST['product_link']);
    $timezone = $mysqli->real_escape_string($_POST['timezone']);



    if (isset($_POST['end_date']) && $_POST['end_date'] != '') {
        $endDateArray = explode("T", $_POST['end_date']);
        $end_date_save = $endDateArray[0] . ' ' . $endDateArray[1];
        $end_date = $mysqli->real_escape_string($end_date_save);
    };

    $timer_text_color = $mysqli->real_escape_string($_POST['timer_text_color']);
    // $expiry_action = $_POST['expiry_action'];
    // $expiry_url = $_POST['expiry_url'];
    $effect = $mysqli->real_escape_string($_POST['effect']);
    // $effect_delay = $_POST['effect_delay'];
    // $effect_transition = $_POST['effect_transition'];
    if (isset($_POST['theme'])) {
        $theme = $mysqli->real_escape_string($_POST['theme']);
    };

    global $mysqli;
    global $dbpref; // $dbpref=cloud_funnels_scarcity_jeet 
    $table = $dbpref . "scarcity_jeet";

    if ($existingRecordCount > 0) {


        $sql = "UPDATE `" . $table . "` SET apply_to='$apply_to',cfscarcity_page_url='$cfscarcity_page_url',funnels='$funnels', Position='$Position', Bar_Gradient1='$Bar_Gradient1',Bar_Gradient1_1='$Bar_Gradient1_1', CatchLine='$CatchLine', catch_line_color='$catch_line_color', Catch_line_font='$Catch_line_font', Catch_line_style='$Catch_line_style', show_action_button='$show_action_button', action_Background_color='$action_Background_color', 
 action_button_text='$action_button_text', action_button_text_color='$action_button_text_color', button_link='$button_link', product_box_show='$product_box_show', 
 product_box_image='$product_box_image',product_link='$product_link',timezone='$timezone', end_date='$end_date', 
 timer_text_color='$timer_text_color', expiry_action='$expiry_action', expiry_url='$expiry_url',effect='$effect', effect_delay='$effect_delay', 
 effect_transition='$effect_transition', theme='$theme'  WHERE id='$row_id'";

        $return_update = $mysqli->query($sql) ? 1 : -1;
        header("Refresh:0");
    } else {

        $sql = "INSERT INTO `" . $table . "`(`apply_to`,`cfscarcity_page_url`,`funnels`, `Position`, `Bar_Gradient1`, `Bar_Gradient1_1`,  `CatchLine`, `catch_line_color`, `Catch_line_font`, `Catch_line_style`,  `show_action_button`,
`action_Background_color`, `action_button_text`, `action_button_text_color`, `button_link`, `product_box_show`, `product_box_image`,`product_link`,
 `timezone`, `end_date`, `timer_text_color`,`expiry_action`, `expiry_url`,`effect`, 
   `effect_delay`, `effect_transition`, `theme`) VALUES ('" . $apply_to . "','" . $cfscarcity_page_url . "','" . $funnels . "','" . $Position . "','" . $Bar_Gradient1 . "','" . $Bar_Gradient1_1 . "', '" . $CatchLine . "', '" . $catch_line_color . "',
  '" . $Catch_line_font . "', '" . $Catch_line_style . "', '" . $show_action_button . "','" . $action_Background_color . "','" . $action_button_text . "','" . $action_button_text_color . "',
  '" . $button_link . "','" . $product_box_show . "','" . $product_box_image . "','" . $product_link . "', '" . $timezone . "', '" . $end_date . "', '" . $timer_text_color . "','" . $expiry_action . "', '" . $expiry_url . "','" . $effect . "', '" . $effect_delay . "', '" . $effect_transition . "','" . $theme . "')";

        $return_insert = $mysqli->query($sql) ? 1 : -1;
        header("Refresh:0");
    }
}
