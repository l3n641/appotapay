<?php
require_once "vendor/autoload.php";

use Appotapay\Service;
use GuzzleHttp\Client;

$secret_key = "XAonJgy14YhtePEITXhyBS2unjfJLAV3";
$partner_code = "APPOTAPAY";
$api_key = "FJcmF8uj2ISveL5FvvNk4pnp8xrhINz8";


$service = new Service($secret_key, $partner_code, $api_key, 3600, "development");

$orderData = [
    "amount" => 10000,
    "orderId" => "5f61cf4f41e2b",
    "orderInfo" => "test",
    "bankCode" => "VCB",
    "paymentMethod" => "ATM",
    "clientIp" => "103.53.171.140",
    "extraData" => "",
    "action" => "PAY",
    "notifyUrl" => "https=>//yourwebsite.com/ipn",
    "redirectUrl" => "https=>//yourwebsite.com/redirect",
];

$amount = $orderData["amount"];
$orderId = $orderData["orderId"];
$orderInfo = $orderData["orderInfo"];
$clientIp = $orderData["clientIp"];
$extraData = $orderData["extraData"];
$notifyUrl = $orderData["notifyUrl"];
$redirectUrl = $orderData["redirectUrl"];
$service->payment($amount, $orderId, $orderInfo, $clientIp, $notifyUrl, $redirectUrl);

