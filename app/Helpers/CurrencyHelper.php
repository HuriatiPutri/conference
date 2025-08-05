<?php
// app/Helpers/CurrencyHelper.php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Convert USD to IDR using manual exchange rate
     *
     * @param float $usdAmount
     * @param float|null $rate (default rate = 15500)
     * @return float
     */
    public static function usdToIdr(float $usdAmount, float $rate = 15500): float
    {
        return $usdAmount * $rate;
    }

    /**
     * Convert IDR to USD
     */
    public static function idrToUsd(float $idrAmount, float $rate = 15500): float
    {
        return round($idrAmount / $rate,2);
    }
}
