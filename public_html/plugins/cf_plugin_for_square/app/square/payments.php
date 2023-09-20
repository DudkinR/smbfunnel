<?php
require 'square/square-php-sdk/example-autoload.php';

use Square\SquareClient;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;
use Square\Models\createPayment;
use Square\LocationsApi;
session_start();



$data = json_decode(file_get_contents('php://input'), true);
if ($_SESSION['environment'] == 0) {
	$square_client = new SquareClient([
		'accessToken' =>  $_SESSION['access_token'],
		'environment' => Environment::SANDBOX,
	]);
} else {
	$square_client = new SquareClient([
		'accessToken' =>  $_SESSION['access_token'],
		'environment' => Environment::PRODUCTION,
	]);
}

$payments_api = $square_client->getPaymentsApi();

$money = new Money();
$money->setAmount(strval( $_SESSION['total_paid'] ));
$money->setCurrency($_SESSION['payment_currency']);

$uuid = rand(1000, 9999);
$create_payment_request = new CreatePaymentRequest($data['sourceId'], $uuid, $money);

$response = $payments_api->createPayment($create_payment_request);
if ($response->isSuccess()) {
	echo json_encode($response->getResult());
} else {
	echo json_encode($response->getErrors());
}
