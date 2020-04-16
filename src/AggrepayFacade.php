<?php

namespace ssi\aggrepaypayment;
use Illuminate\Support\Facades\Facade;

class AggrepayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Aggrepay::class;
    }
}