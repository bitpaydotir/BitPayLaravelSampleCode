<?php
namespace App\Models;

class PaymentResult
{

    public $status;
    public $amount;
    public  $cardNum;
    public  $factorId;
    public function GetMessage()
    {
        switch ($this->status) {
            case -1:
                return "API ارسالی با نوع API تعریف شده در bitpay سازگار نیست";
            case -2:
                return "id_trans ارسال شده، داده عددي نمیباشد";
            case -3:
                return "get_id ارسال شده، داده عددي نمیباشد";
            case -4:
                return "چنین تراکنشی در پایگاه وجود ندارد و یا موفقیت آمیز نبوده است";
            case 1:
                return "خ تراکنش موفقیت آمیز بوده است";
            case 11:
                return " تراکنش از قبل وریفاي شده است";
            default:
                return "Unknown error";
        }
    }
}
