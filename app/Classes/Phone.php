<?php

namespace App\Classes;

use Illuminate\Support\Facades\Validator;
use libphonenumber\PhoneNumberUtil;

class Phone
{
    protected $proto;

    public static function number($number, $format = 0)
    {
        $phone = new static($number);

        return $phone->getUtil()->format($phone->getProto(), $format);
    }

    public static function validate($number)
    {
        $validator = Validator::make(compact('number'), ['number' => 'required|phone:PH']);
        if ($validator->passes()){

            return static::number($number);
        }

        return false;
    }

    public function __construct($mobile)
    {
        $this->proto = $this->getUtil()->parse($mobile, "PH");
    }

    protected function getProto()
    {
        return $this->proto;
    }

    protected function getUtil()
    {
        return PhoneNumberUtil::getInstance();
    }
}
