<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Expr\Cast\Double;
use App\Models\PaymentResult;
use App\Models\SendRequestResult;

class BitPay
{
    private $token;
    private $apiSendUrl = "https://bitpay.ir/payment-test/gateway-send";
    private $apiGatewayUrl = "https://bitpay.ir/payment-test/gateway-%s-get";
    private $apiPaymentResultUrl = "https://bitpay.ir/payment-test/gateway-result-second";
    function __construct(string $token, bool $isTest)
    {
        if (!$isTest) {
            $this->apiSendUrl =  str_replace("-test", "", $this->apiSendUrl);
            $this->apiGatewayUrl =  str_replace("-test", "", $this->apiGatewayUrl);
            $this->apiPaymentResultUrl =  str_replace("-test", "", $this->apiPaymentResultUrl);
        }
        $this->token = $token;
    }
    public  function Send($amount, int $factorId, string $redirectUrl, string $name = "", string $email = "", string $description)
    {


        $redirectUrlEcnode = urlencode($redirectUrl);

        $postdata = [
            'amount' => $amount,
            'redirect' => $redirectUrlEcnode,
            'factorId' => $factorId,
            'name' => $name,
            'email' => $email,
            'description' => $description,
            'api' => $this->token
        ];
        $requestResult = new SendRequestResult();
        $requestResult->status = 0;
        $response = Http::asForm()->post($this->apiSendUrl, $postdata);
        if ($response->successful()) {
            // Handle a successful response
            $result = $response->json();
            $requestResult->status = $result;

            if ($result > 0) {
                $requestResult->redirectUrl = sprintf($this->apiGatewayUrl, $result);
            }
        }
        return $requestResult;
    }
    public function Get($transId, $idGet)
    {

        $result = new PaymentResult();
        $response = Http::asForm()->post($this->apiPaymentResultUrl, [
            'trans_id' => $transId,
            'id_get' => $idGet,
            'api' => $this->token,
            'json' => 1
        ]);

        $jsonResult = $response->json();

        $result->amount = $jsonResult['amount'] ?? null; // Access array keys using []
        $result->status = $jsonResult['status'] ?? null;
        $result->cardNum = $jsonResult['cardNum'] ?? null;
        $result->factorId = $jsonResult['factorId'] ?? null;
        return $result;
    }
}
