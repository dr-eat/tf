<?php

namespace models;
use BaseTest;
use MRate;
require_once('../tests/BaseTest.php');
/**
 * MRate - handle exchange rate logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MRateTest extends BaseTest
{
    public function testCreate()
    {
        $mrate = new MRate();
        $mrate->currency_from = 'USD';
        $mrate->currency_to = 'EUR';
        $mrate->rate = 1.1;
        $rate_id = $mrate->create();
        $mrate_test = new MRate($rate_id);
        $fields = ['currency_from', 'currency_to', 'rate'];
        foreach($fields as $field) {
            $this->assertSame($mrate->$field, $mrate_test->$field, "Field: $field");
        }
    }
}