<?php
$sales_ob = $this->load('form_controller');

$url2 = plugins_url();
$url2 = explode('/', $url2);
$count = count($url2);
$folder = $url2[$count - 3];
$url = $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$pos = array_search($folder, $url);
$funnel_name = $url[$pos + 1];
global $mysqli;
global $dbpref;
$table = $dbpref . 'quick_funnels';
$id = mysqli_fetch_assoc($mysqli->query("SELECT `id` FROM  `" . $table . "` WHERE  BINARY LOWER(`name`) = '".$funnel_name."'"));
$shipping_options = $sales_ob->shipping_options($id['id']);
$flag = 0;
?>
<div id="delivery_type">
    <div id='delivery_options'>
        <?php
        if (!empty($shipping_options)) {
            foreach ($shipping_options as $methods) {
                if ($flag  == 0) {
                    echo '<input type="radio" style="margin-right: 0.5rem;" checked name="delivery_type" id="radio1" delivery="' . $methods->name . '" cost="' . $methods->cost . '">' . $methods->name . '&nbsp;(' . number_format((float)$methods->cost,2,'.','') . '&nbsp;USD)' . '<br>';
                } else {
                    echo '<input type="radio" style="margin-right: 0.5rem;" name="delivery_type" id="radio1" delivery="' . $methods->name . '" cost="' . $methods->cost . '">' . $methods->name . '&nbsp;(' . number_format((float)$methods->cost,2,'.','') . '&nbsp;USD)' . '<br>';
                }
                $flag++;
            }
        } ?>
    </div>
</div>