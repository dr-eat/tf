<?php

namespace models;
use BaseTest;
use MRate;
use MRateAct;
require_once('../tests/BaseTest.php');
/**
 * MRate - handle exchange rate logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MRateActTest extends BaseTest {

    public function setUp(): void
    {
        parent::setUp();
        $rates = [['EUR', 'USD', 1.1], ['USD', 'EUR', 0.9], ['EUR', 'PLN', 4], ['EUR', 'NOK', 16]];
        $mrate = new MRate();
        foreach ($rates as $rate) {
            $mrate->currency_from = $rate[0];
            $mrate->currency_to = $rate[1];
            $mrate->rate = $rate[2];
            $mrate->replace();
        }
    }


    /**
     *
     * @return void
     * @dataProvider rateList
     */
    public function testGet($currency_from, $currency_to, $total) {
        $mrate_act = new MRateAct();
        $rate = $mrate_act->get($currency_from, $currency_to);
        $this->assertSame($total, $rate, "$currency_from -> $currency_to");
    }

    public function rateList(): array
    {
        return [
            ['EUR', 'EUR', 1],
            ['EUR', '', 0],
            ['EUR', 'USD', 1.1],
            ['PLN', 'EUR', 0.25],
            ['PLN', 'NOK', 4.0],
            ['NOK', 'PLN', 0.25],
        ];

    }
}