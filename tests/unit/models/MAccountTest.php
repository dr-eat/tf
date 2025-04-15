<?php

namespace models;
use BaseTest;
use MAccount;
require_once('../tests/BaseTest.php');
/**
 * MAccount - Account object
 *
 * PHP version 8.2
 *
 * @author Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MAccountTest extends BaseTest
{
    public function testCreate()
    {
        $maccount = new MAccount();
        $maccount->name = 'test ut1';
        $maccount->client_id = 1;
        $maccount->balance = 11.2;
        $maccount->currency = 'TRY';
        $account_id = $maccount->create();
        $maccount_test = new MAccount($account_id);
        $fields = ['name', 'client_id', 'currency', 'balance'];
        foreach($fields as $field) {
            $this->assertSame($maccount->$field, $maccount_test->$field, "Field: $field");
        }
    }
}