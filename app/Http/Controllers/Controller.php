<?php

namespace App\Http\Controllers;


use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    private $token = "adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck1244567";
    private $isTest = true;
    private $apiSendUrl = "https://bitpay.ir/payment-test/gateway-send";
    private $apiGatewayUrl = "https://bitpay.ir/payment-test/gateway-%s-get";
    private $apiPaymentResultUrl = "https://bitpay.ir/payment-test/gateway-result-second";

    public function index()
    {
        return view('index');
    }

    public function processPayment()
    {
        $amount = 10000;
        $factorId = 123;
        $name = "Test Payment";
        $email = "test@test.com";
        $description = "This is for test";

        try {
            $apiUrl = $this->isTest ? $this->apiSendUrl : str_replace("-test", "", $this->apiSendUrl);
            $redirectUrl = "http://127.0.0.1:8000/payment-result";

            $response = Http::post($apiUrl, [
                'amount' => $amount,
                'redirect_url' => $redirectUrl,
                'factor_id' => $factorId,
                'name' => $name,
                'email' => $email,
                'description' => $description,
                'token' => $this->token
            ]);

            $result = $response->json();

            if ($result['status'] > 0) {
                $goToGateway = sprintf($this->apiGatewayUrl, $result['status']);
                return redirect()->away($goToGateway);
            }

            session()->flash('msg', $result['message']);
            return view('error');
        } catch (\Exception $ex) {
            return response()->json(['error' => 'An error occurred while processing payment: ' . $ex->getMessage()], 500);
        }
    }

    public function paymentResult(Request $request)
    {
        try {
            $transId = $request->input('trans_id');
            $idGet = $request->input('id_get');
            $factorId = $request->input('factorId');

            $apiUrl = $this->isTest ? $this->apiPaymentResultUrl : str_replace("-test", "", $this->apiPaymentResultUrl);

            $response = Http::get($apiUrl, [
                'trans_id' => $transId,
                'id_get' => $idGet,
                'token' => $this->token
            ]);

            $result = $response->json();

            if ($result['status'] == 1) {
                session()->flash('msg', sprintf("Your payment with factor ID %s was successful", $factorId));
                return view('success');
            }

            session()->flash('msg', $result['message']);
            return view('error');
        } catch (\Exception $ex) {
            return response()->json(['error' => 'An error occurred while processing payment: ' . $ex->getMessage()], 500);
        }
    }
}
