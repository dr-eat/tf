<?php
/**
 * CRate - handle exchange rate logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
require_once '../models/MRate.php';
require_once '../models/MRateAct.php';
require_once '../models/MRateApi.php';

class CRate {

    /**
     * Import Exchange rates from from 3rd party service
     * @return int
     */
    public function import(): int
    {
        $cnt = 0;
        foreach (App::get()->config->exchange_currencies as $currency) {
            $mrate_api = new MRateApi();
            $cnt += $mrate_api->getExchangeRates($currency);
        }
        return $cnt;
    }
}