<?php

class Pay
{
    private $pay_url = 'https://pay.ir/payment/send';
    private $verify_url = 'https://pay.ir/payment/verify';
    public $api;
    public $amount;
    public $redirect;
    public $factor_number;    
    public function __construct($api = null, $amount = 0 , $redirect = null, $factor_number = null)
    {
        $this->api = $api;
        $this->amount = $amount * 10;
        $this->redirect = $redirect;
        $this->factor_number = $factor_number;            
    }

    public function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->pay_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api={$this->api}&amount={$this->amount}&redirect={$this->redirect}&factorNumber={$this->factor_number}");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function verify($transId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->verify_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api={$this->api}&transId=$transId");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}