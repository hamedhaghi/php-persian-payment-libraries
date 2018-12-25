<?php
class Parsian
{
    private $pin;
    private $wsdl_url;
    public $status = false;

    public function __construct($pin = null, $url = null)
    {
        $this->pin = $pin;
        $this->wsdl_url = $url;
    }
    
    public function pay($transaction_id, $amount, $callback_url)
    {
        $params = [
            "LoginAccount" => $this->pin,
            "Amount" => $amount * 10,
            "OrderId" => $transaction_id,
            "CallBackUrl" => $callback_url
        ];
        $client = new SoapClient('https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL');
        try {
            $result = $client->SalePaymentRequest(array(
                "requestData" => $params
            ));
            if (empty($result)) {
                throw new Exception("خطای درگاه : اطلاعاتی از بانک ارسال نشده است");
            }
            $status = $result->SalePaymentRequestResult->Status;
            $token = $result->SalePaymentRequestResult->Token;
            if (!empty($token) && ($status === 0)) {
                $this->status = true;
                return $token;
            } elseif ($status != '0') {
                throw new Exception("خطای درگاه : " . $this->message($status));
            }


        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    public function redirect_to_pay($token)
    {
        header("Location: https://pec.shaparak.ir/NewIPG/?Token=" . $token);
    }


    public function verify($RRN, $status)
    {
        if ($RRN > 0 && $status == 0) {
            return TRUE;
        } elseif ($status) {
            return $this->message($status);
        } else {
            return "پاسخی از سمت بانک ارسال نشد";
        }
    }

    public function confirm($token)
    {
        $params = array(
            "LoginAccount" => $this->pin,
            "Token" => $token
        );
        $client = new SoapClient ('https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL');
        try {
            $result = $client->ConfirmPayment(array(
                "requestData" => $params
            ));
            if (empty($result)) {
                throw new Exception("خطای درگاه : اطلاعاتی از بانک ارسال نشده است");
            }
            $status = $result->ConfirmPaymentResult->Status;
            if ($status != '0') {
                throw new Exception("خطای درگاه : " . $this->message($status));
            }

            return TRUE;

        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    public function message($error_code)
    {
        $message = 'خطای درگاه بانک پارسیان';
        switch ($error_code) {
            case -32768:
                $message = 'خطای ناشناخته رخ داده است';
                break;
            case -1552 :
                $message = 'برگشت تراکنش مجاز نمی باشد';
                break;
            case -1551 :
                $message = 'برگشت تراکنش قبلا انجام شده است';
                break;
            case -1550 :
                $message = ' برگشت تراکنش در وضعیت جاری امکان پذیر نمی باشد';
                break;
            case -1549 :
                $message = 'زمان مجاز برای درخواست برگشت تراکنش به اتمام رسیده است';
                break;
            case -1548 :
                $message = 'فراخوانی سرویس درخواست پرداخت قبض ناموفق بود';
                break;
            case -1540 :
                $message = 'تایید تراکنش ناموفق می باشد';
                break;
            case -1536 :
                $message = 'فراخوانی سرویس درخواست شارژ تاپ آپ ناموفق بود';
                break;
            case -1533 :
                $message = 'تراکنش قبلاً تایید شده است';
                break;
            case -1532 :
                $message = 'تراکنش از سوی پذیرنده تایید شد';
                break;
            case -1531 :
                $message = 'تایید تراکنش ناموفق امکان پذیر نمی باشد';
                break;
            case -1530 :
                $message = 'پذیرنده مجاز به تایید این تراکنش نمی باشد';
                break;
            case -1528 :
                $message = ' اطلاعات پرداخت یافت نشد';
                break;
            case -1527 :
                $message = 'انجام عملیات درخواست پرداخت تراکنش خرید ناموفق بود';
                break;
            case -1507 :
                $message = 'تراکنش برگشت به سوئیچ ارسال شد';
                break;
            case -1505 :
                $message = ' تایید تراکنش توسط پذیرنده انجام شد';
                break;
            case -138 :
                $message = 'عملیات پرداخت توسط کاربر لغو شد';
                break;
            case -132 :
                $message = ' مبلغ تراکنش کمتر از حداقل مجاز میباشد';
                break;
            case -131 :
                $message = 'Token' . ' ' . 'نامعتبر می باشد';
                break;
            case -130 :
                $message = 'زمان' . ' ' . 'Token' . ' ' . 'منقضی شده است';
                break;
            case -128 :
                $message = 'قالب آدرس' . ' ' . 'IP' . ' ' . 'معتبر نمی باشد';
                break;
            case -127 :
                $message = 'آدرس اینترنتی معتبر نمی باشد';
                break;
            case -126 :
                $message = 'کد شناسایی پذیرنده معتبر نمی باشد';
                break;
            case -121 :
                $message = 'رشته داده شده بطور کامل عددی نمی باشد';
                break;
            case -120 :
                $message = 'طول داده ورودی معتبر نمی باشد';
                break;
            case -119 :
                $message = 'سازمان نامعتبر می باشد';
                break;
            case -118 :
                $message = 'مقدار ارسال شده عدد نمی باشد';
                break;
            case -117 :
                $message = 'طول رشته کم تر از حد مجاز می باشد';
                break;
            case  -116 :
                $message = 'طول رشته بیش از حد مجاز می باشد';
                break;
            case -115 :
                $message = 'شناسه پرداخت نامعتبر می باشد';
                break;
            case -114 :
                $message = 'شناسه قبض نامعتبر می باشد';
                break;
            case -113 :
                $message = 'پارامتر ورودی خالی می باشد';
                break;
            case -112 :
                $message = 'شماره سفارش تکراری است';
                break;
            case -111 :
                $message = 'مبلغ تراکنش بیش از حد مجاز پذیرنده می باشد';
                break;
            case -108 :
                $message = ' قابلیت برگشت تراکنش برای پذیرنده غیر فعال می باشد';
                break;
            case -107 :
                $message = ' قابلیت ارسال تاییده تراکنش برای پذیرنده غیر فعال می باشد';
                break;
            case -106 :
                $message = 'قابلیت شارژ برای پذیرنده غیر فعال می باشد';
                break;
            case -105 :
                $message = 'قابلیت تاپ آپ برای پذیرنده غیر فعال می باشد';
                break;
            case -104 :
                $message = ' قابلیت پرداخت قبض برای پذیرنده غیر فعال می باشد';
                break;
            case -103 :
                $message = ' قابلیت خرید برای پذیرنده غیر فعال می باشد';
                break;
            case -102 :
                $message = 'تراکنش با موفقیت برگشت داده شد';
                break;
            case -101 :
                $message = 'پذیرنده اهراز هویت نشد';
                break;
            case -100 :
                $message = 'پذیرنده غیرفعال می باشد';
                break;
            case -1 :
                $message = 'خطای سرور';
                break;
            case 0 :
                $message = 'عملیات موفق می باشد';
                break;
            case 1 :
                $message = 'صادرکننده ی کارت از انجام تراکنش صرف نظر کرد';
                break;
            case 2 :
                $message = 'عملیات تاییدیه این تراکنش قبلا با موفقیت صورت پذیرفته است';
                break;
            case 3 :
                $message ='پذیرنده ی فروشگاهی نامعتبر می باشد';
                break;
            case 5 :
                $message = 'از انجام تراکنش صرف نظر شد';
                break;
            case 6 :
                $message = 'بروز خطایی ناشناخته';
                break;
            case 8 :
                $message = 'باتشخیص هویت دارنده ی کارت، تراکنش موفق می باشد';
                break;
            case 9 :
                $message = ' درخواست رسیده در حال پی گیری و انجام است';
                break;
            case 10 :
                $message = ' تراکنش با مبلغی پایین تر از مبلغ درخواستی (کمبود حساب مشتری)  پذیرفته شده است';
                break;
            case 12 :
                $message = 'تراکنش نامعتبر است';
                break;
            case 13 :
                $message = 'مبلغ تراکنش نادرست است';
                break;
            case 14 :
                $message = 'شماره کارت ارسالی نامعتبر است (وجودندارد)';
                break;
            case 15 :
                $message = ' صادرکننده ی کارت نامعتبراست (وجودندارد)';
                break;
            case 17 :
                $message = 'مشتری درخواست کننده حذف شده است';
                break;
            case 20 :
                $message = 'در موقعیتی که سوئیچ جهت پذیرش تراکنش نیازمند پرس و جو از کارت است ممکن است درخواست از کارت ( ترمینال) بنماید این پیام مبین نامعتبر بودن جواب است';
                break;
            case 21 :
                $message = 'در صورتی که پاسخ به در خواست ترمینال نیازمند هیچ پاسخ خاص یا عملکردی نباشیم این پیام را خواهیم داشت';
                break;
            case 22 :
                $message = 'تراکنش مشکوك به بد عمل کردن ( کارت ، ترمینال ، دارنده کارت ) بوده است لذا پذیرفته نشده است';
                break;
            case 30 :
                $message = 'قالب پیام دارای اشکال است';
                break;
            case 31 :
                $message = 'پذیرنده توسط سوئی پشتیبانی نمی شود';
                break;
            case 32 :
                $message = 'تراکنش به صورت غیر قطعی کامل شده است . به عنوان مثال تراکنش سپرده گزاری که از دید مشتری کامل شده است ولی می بایست تکمیل گردد';
                break;
            case 33 :
                $message = 'تاریخ انقضای کارت سپری شده است';
                break;
            case 38 :
                $message = ' تعداد دفعات ورود رمزغلط بیش از حدمجاز است. کارت توسط دستگاه ضبط شود';
                break;
            case 39 :
                $message = 'کارت حساب اعتباری ندارد';
                break;
            case 40 :
                $message = 'عملیات درخواستی پشتیبانی نمی گردد';
                break;
            case 41 :
                $message = 'کارت مفقودی می باشد';
                break;
            case 43 :
                $message = 'کارت مسروقه می باشد';
                break;
            case 45 :
                $message = 'قبض قابل پرداخت نمی باشد';
                break;
            case 51 :
                $message = 'موجودی کافی نمی باشد';
                break;
            case 54 :
                $message = 'تاریخ انقضای کارت سپری شده است';
                break;
            case 55 :
                $message = 'رمز کارت نا معتبر است';
                break;
            case 56 :
                $message = 'کارت نا معتبر است';
                break;
            case 57 :
                $message = 'انجام تراکنش مربوط توسط پایانه ی انجام دهنده مجاز نمی باشد';
                break;
            case 58 :
                $message = 'انجام تراکنش مربوطه توسط پایانه ی انجام دهنده مجاز نمی باشد';
                break;
            case 59 :
                $message = 'کارت مظنون به تقلب است';
                break;
            case 61 :
                $message = 'مبلغ تراکنش بیش از حد مجاز می باشد';
                break;
            case 62 :
                $message = 'کارت محدود شده است';
                break;
            case 63 :
                $message = ' تمهیدات امنیتی نقض گردیده است';
                break;
            case 65 :
                $message = 'تعداد درخواست تراکنش بیش از حد مجاز می باشد';
                break;
            case 68 :
                $message = 'پاسخ لازم برای تکمیل یا انجام تراکنش خیلی دیر رسیده است';
                break;
            case 69 :
                $message = 'تعداد دفعات تکرار رمز از حد مجاز گذشته است';
                break;
            case 75 :
                $message = 'تعداد دفعات ورود رمزغلط بیش از حدمجاز است';
                break;
            case 78 :
                $message = 'کارت فعال نیست';
                break;
            case 79 :
                $message = ' حساب متصل به کارت نا معتبر است یا دارای اشکال است';
                break;
            case 80 :
                $message = 'درخواست تراکنش رد شده است';
                break;
            case 81 :
                $message = 'کارت پذیرفته نشد';
                break;
            case 83 :
                $message = 'سرویس دهنده سوئیچ کارت تراکنش را نپذیرفته است';
                break;
            case 84 :
                $message = ' در تراکنشهایی که انجام آن مستلزم ارتباط با صادر کننده است در صورت فعال نبودن صادر کننده این پیام در پاسخ ارسال خواهد شد';
                break;
            case 91 :
                $message = 'سیستم صدور مجوز انجام تراکنش موقتا غیر فعال است و یا زمان تعیین شده برای صدو مجوز به پایان رسیده است';
                break;
            case 92 :
                $message = 'مقصد تراکنش پیدا نشد';
                break;
            case 93 :
                $message = 'امکان تکمیل تراکنش وجود ندارد';
                break;

        }
        return $message;
    }
}