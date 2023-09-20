<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$notify_data['ipn_mode'] = $_POST['ipn_mode'] ?? 'hmac';
	$notify_data['HTTP_HMAC'] = $_SERVER['HTTP_HMAC'] ?? array();
	$notify_data['post_data'] = file_get_contents('php://input');
	$notify_data['merchant'] = $_POST['merchant'] ?? '';
	$notify_data['ipn_type'] = $_POST['ipn_type'] ?? 0;
	$notify_data['txn_id'] = $_POST['txn_id'] ?? 0;
	$notify_data['item_name'] = $_POST['item_name'] ?? '';
	$notify_data['item_number'] = $_POST['item_number'] ?? 0;
	$notify_data['amount1'] = $_POST['amount1'] ?? 0;
	$notify_data['amount2'] = $_POST['amount2'] ?? 0;
	$notify_data['currency1'] = $_POST['currency1'] ?? '';
	$notify_data['currency2'] = $_POST['currency2'] ?? '';
	$notify_data['status'] = intval($_POST['status'] ?? 0);
	$notify_data['status_text'] = $_POST['status_text'] ?? '';

	foreach ($notify_data as $key => $val) {
		$pfData[$key] = stripslashes($val);
	}
	$pfParamString = "";
	// Convert posted variables to a string
	foreach ($pfData as $key => $val) {
		$pfParamString .= $key . '=' . urlencode($val) . '&';
	}
	$pfParamString = substr($pfParamString, 0, -1);
	if (!empty($pfData)) {
		$myfile_url = plugin_dir_path(dirname(__FILE__)) . "coinpayment/newfile.txt";
		$myfile = fopen($myfile_url, "w+") or die("Unable to open file!");
		fwrite($myfile, $pfParamString);
		fclose($myfile);
	}
}
?>