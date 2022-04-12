<?php

namespace Appotapay;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class Service
{
    /**
     * @var int|mixed
     */
    private $secretKey;
    private $partnerCode;
    private $apiKey;
    private $liver;
    private $client;

    public function __construct($secretKey, $partnerCode, $apiKey, $liver = 3600, $env = "production", $timeout = 30)
    {
        if ($env == "production") {
            $base_url = "https://payment.appotapay.com";
        } else {
            $base_url = "https://payment.dev.appotapay.com";
        }


        $this->client = new Client([
            'base_uri' => $base_url,
            'timeout' => $timeout,
        ]);
        $this->secretKey = $secretKey;
        $this->partnerCode = $partnerCode;
        $this->apiKey = $apiKey;
        $this->liver = $liver;

    }

    public function signatureData($data)
    {
        $query = http_build_query($data);
        return hash_hmac("sha256", $query, $this->secretKey);
    }

    protected function generateToken()
    {
        $head = [
            "typ" => "JWT",
            "alg" => "HS256",
            "cty" => "appotapay-api;v=1"
        ];

        $current_time_stamp = time();
        $payload = [
            "iss" => $this->partnerCode,
            "jti" => $this->apiKey . "-" . $current_time_stamp, // (ex time: 1614225624)
            "api_key" => $this->apiKey,
            "exp" => $current_time_stamp + $this->liver
        ];


        return JWT::encode($payload, $this->secretKey, 'HS256', null, $head);
    }

    /**
     * 创建支付单
     * @param $amount numeric
     * @param $orderId string
     * @param $orderInfo string
     * @param $clientIp string
     * @param $notifyUrl string
     * @param $redirectUrl string
     * @param $bankCode string
     * @param $paymentMethod string
     * @param $extraData string
     * @param $action string
     * @param $token string
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payment($amount, $orderId, $orderInfo, $clientIp, $notifyUrl, $redirectUrl,
                            $bankCode = "", $paymentMethod = "", $extraData = "", $action = "PAY", $token = "")
    {
        $uri = "/api/v1.1/orders/payment/bank";

        $jwtToken = $this->generateToken();
        $headers = [
            "X-APPOTAPAY-AUTH" => "Bearer $jwtToken",
            "Content-Type" => "application/json"
        ];


        $data = [
            "amount" => $amount,
            "orderId" => $orderId,
            "orderInfo" => $orderInfo,
            "bankCode" => $bankCode,
            "paymentMethod" => $paymentMethod,
            "clientIp" => $clientIp,
            "extraData" => $extraData,
            "action" => $action,
            "notifyUrl" => $notifyUrl,
            "redirectUrl" => $redirectUrl,
            "token" => $token,
        ];

        $dataStr = "amount={$amount}&bankCode={$bankCode}&clientIp={$clientIp}&extraData={$extraData}&notifyUrl={$notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}&paymentMethod={$paymentMethod}&redirectUrl={$redirectUrl}";
        $signature = hash_hmac("sha256", $dataStr, $this->secretKey);;
        $data["signature"] = $signature;

        $response = $this->client->request('POST', $uri, [
            'json' => $data,
            'headers' => $headers
        ]);


        return $response;

    }

    /**查看订单状态
     * @param $orderId
     * @return mixed
     */
    public function checkTransactionStatus($orderId)
    {

        $uri = "/api/v1.1/orders/transaction/bank/status";

        $jwtToken = $this->generateToken();
        $headers = [
            "X-APPOTAPAY-AUTH" => "Bearer $jwtToken",
            "Content-Type" => "application/json"
        ];


        $data = [
            "orderId" => $orderId,
        ];

        $signature = $this->signatureData($data);
        $data["signature"] = $signature;

        $response = $this->client->request('POST', $uri, [
            'json' => $data,
            'headers' => $headers
        ]);


        return $response;

    }
}