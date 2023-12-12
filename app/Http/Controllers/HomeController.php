<?php

namespace App\Http\Controllers;


use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\BitPay;
use Illuminate\Support\Facades\Redirect;

class HomeController extends BaseController
{
    private $token = "adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck1244567";
    private $isTest = true;
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
        $redirectUrl = "http://127.0.0.1:8000/payment-result";
        try {
            $bitPay = new BitPay($this->token, $this->isTest);
            $result = $bitPay->Send($amount, $factorId, $redirectUrl, $name, $email, $description);

            if ($result->status > 0) {

                return Redirect::away($result->redirectUrl);
            }

            session()->flash('msg', $result->GetMessage());
            return view('success');
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

            $bitPay = new BitPay($this->token, $this->isTest);
            $result = $bitPay->Get($transId, $idGet);
            session()->flash('msg', $result);
            if ($result->status == 1) {
                session()->flash('msg', sprintf("Your payment with factor ID %s was successful", $factorId));
                return view('success');
            }

          //  session()->flash('msg', $result->GetMessage());
            return view('error');
        } catch (\Exception $ex) {
        
            return view('error');
            //return response()->json(['error' => 'An error occurred while processing payment: ' . $ex->getMessage()], 500);
        }
    }
}
