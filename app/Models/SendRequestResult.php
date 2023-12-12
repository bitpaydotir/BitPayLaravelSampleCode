<?php
namespace App\Models;

class SendRequestResult
{

    public $status;
    public $redirectUrl;
    public function GetMessage()
    {
        switch ($this->status) {

            case -1:
                return "API ارسالی با نوع API تعریف شده در bitpay سازگار نیست";
            case -2:
                return "مقدار amount داده عددي نمیباشد و یا کمتر از 1000ریال است";
            case -3:
                return "مقدار redirect رشته null است";
            case -4:
                return "درگاهی با اطلاعات ارسالی شما وجود ندارد و یا در حالت\r\nانتظار میباشد";
            case -5:
                return "خطا در اتصال به درگاه، لطفا مجددا تالش کنید";

            default:
                if ($this->status > 0) {
                    return "تراکنش از قبل وریفاي شده است";
                }

                return "Unknown error";
        }
    }
}