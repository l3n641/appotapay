<?php

namespace Appotapay;

class Notification
{
    public $data = [];
    private $secretKey;

    public function __construct($postData, $secretKey)
    {
        $tokenResult = $postData['tokenResult'];
        $card = $tokenResult['card'];
        $this->secretKey = $secretKey;

        $this->data = [
            "errorCode" => $postData["errorCode"],
            "message" => $postData["message"],
            "partnerCode" => $postData["partnerCode"],
            "apiKey" => $postData["apiKey"],
            "amount" => $postData["amount"],
            "currency" => $postData["currency"],
            "orderId" => $postData["orderId"],
            "bankCode" => $postData["bankCode"],
            "paymentMethod" => $postData["paymentMethod"],
            "paymentType" => $postData["paymentType"],
            "appotapayTransId" => $postData["appotapayTransId"],
            "transactionTs" => $postData["transactionTs"],
            "extraData" => $postData["extraData"],
            "tokenResult" => [
                "status" => $tokenResult["status"],
                "message" => $tokenResult["message"],
                "card" => [
                    "status" => $card["card"],
                    "token" => $card["token"],
                    "card_name" => $card["card_name"],
                    "card_number" => $card["card_number"],
                    "card_date" => $card["card_date"],
                    "card_type" => $card["card_type"],
                ]
            ],

            "signature" => $postData["signature"],
        ];
    }

    protected function checkSignature()
    {
        $template = "amount=%s&apiKey=%s&appotapayTransId=%s&bankCode=%s&currency=%s&errorCode=%s&extraData=%s&message=%s&orderId=%s&partnerCode=%s&paymentMethod=%s&paymentType=%s&transactionTs=%s";

        $data = sprintf($template, $this->data["amount"], $this->apiKey, $this->data["appotapayTransId"], $this->data["bankCode"],
            $this->data["currency"], $this->data["errorCode"], $this->data["extraData"], $this->data["orderId"], $this->data["partnerCode"],
            $this->data["paymentMethod"], $this->data["paymentType"], $this->data["transactionTs"]
        );

        $signature = hash_hmac("sha256", $data, $this->secretKey);

        return $this->data["signature"] === $signature;

    }

}