<?php

namespace models;
use MAccount;
use BaseTest;
use MTransaction;
use MRate;
use App;
require_once('../tests/BaseTest.php');
/**
 * MTransaction - transaction object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MTransactionTest extends BaseTest
{
    public function testIsNotEmpty(): void
    {
        $mtransaction = new MTransaction();
        $mtransaction->amount = 10;
        $mtransaction->currency = 'USD';
        $mtransaction->account_id_from = 1;
        $mtransaction->account_id_to = 2;

        $res = self::callMethod($mtransaction, 'isNotEmpty');
        $this->assertTrue($res, "Valid object failed");

        $fields = ['amount' => 0, 'currency' => '', 'account_id_from' => 0, 'account_id_to' => 0];
        foreach ($fields as $field => $empty_value) {
            $orig_val = $mtransaction->$field;
            $mtransaction->$field = $empty_value;
            $mtransaction->invalid_fields = [];
            $res = self::callMethod($mtransaction, 'isNotEmpty');
            $this->assertFalse($res, "Empty $field passed");
            $mtransaction->field = $orig_val;
        }
    }

    public function createAccount($name, $client_id, $balance, $currency): int
    {
        $maccount = new MAccount();
        $maccount->name = $name;
        $maccount->client_id = $client_id;
        $maccount->balance = $balance;
        $maccount->currency = $currency;
        return $maccount->create();
    }

    public function createRate($currency_from, $currency_to, $rate): void
    {
        $mrate = new MRate();
        $mrate->currency_from = $currency_from;
        $mrate->currency_to = $currency_to;
        $mrate->rate = $rate;
        $mrate->replace();
    }

    public function testIsValidAccounts(): void
    {
        $account1_id = $this->createAccount('test ut1', 1, 1, 'EUR');
        $account2_id = $this->createAccount('test ut2', 1, 1, 'USD');

        $mtransaction = new MTransaction();
        $mtransaction->amount = 10;
        $mtransaction->currency = 'USD';
        $mtransaction->account_id_from = $account1_id;
        $mtransaction->account_id_to = $account2_id;

        $this->app->client_id = 1;

        $res = self::callMethod($mtransaction, 'isValidAccounts');
        $this->assertTrue($res, "Valid object failed");

        $fields = ['account_id_to' => 0, 'account_id_from' => 0, 'currency' => 'GBP'];
        foreach ($fields as $field => $empty_value) {
            $orig_val = $mtransaction->$field;
            $mtransaction->$field = $empty_value;
            $mtransaction->invalid_fields = [];
            $res = self::callMethod($mtransaction, 'isValidAccounts');
            $this->assertFalse($res, "Wrong $field passed");
            $mtransaction->field = $orig_val;
        }

        $this->app->client_id = 0;
        $mtransaction->invalid_fields = [];
        $res = self::callMethod($mtransaction, 'isValidAccounts');
        $this->assertFalse($res, "Unauthorized transaction passed");
    }

    public function testIsValidAmount(): void
    {
        $account1_id = $this->createAccount('test ut1', 1, 1, 'EUR');
        $account2_id = $this->createAccount('test ut2', 1, 2, 'USD');
        $account3_id = $this->createAccount('test ut3', 1, 0, 'EUR');
        $this->app->db->query("delete from rates");
        $mrate = new MRate();
        $mrate->currency_from = 'USD';
        $mrate->currency_to = 'EUR';
        $mrate->rate = 0.9;
        $mrate->create();

        $mtransaction = new MTransaction();
        $mtransaction->amount = 1;
        $mtransaction->currency = 'USD';
        $mtransaction->account_id_from = $account1_id;
        $mtransaction->account_id_to = $account2_id;

        $this->app->client_id = 1;

        $mtransaction->invalid_fields = [];
        $res = self::callMethod($mtransaction, 'isValidAmount');
        $this->assertTrue($res, "Valid object failed(1)" . print_r($mtransaction->invalid_fields, true));

        $mtransaction->account_id_from = $account3_id;
        $mtransaction->invalid_fields = [];
        $res = self::callMethod($mtransaction, 'isValidAmount');
        $this->assertFalse($res, "Insufficient balance accepted");

        $mtransaction->account_id_from = $account1_id;
        $mrate->delete();
        $mtransaction->invalid_fields = [];
        $res = self::callMethod($mtransaction, 'isValidAmount');
        $this->assertFalse($res, "Empty rate accepted");

        $mrate->replace();
        $mtransaction->invalid_fields = [];
        $res = self::callMethod($mtransaction, 'isValidAmount');
        $this->assertTrue($res, "Valid object failed(2)" . print_r($mtransaction->invalid_fields, true));
    }

    public function testProcess(): void
    {
        $account1_id = $this->createAccount('test ut1', 1, 3, 'EUR');
        $account2_id = $this->createAccount('test ut2', 1, 2, 'USD');
        $account3_id = $this->createAccount('test ut3', 1, 0, 'PLN');
        $this->createRate('EUR', 'USD', 1.1);
        $this->createRate('EUR', 'PLN', 4);

        App::get()->config->transaction_process_immediate = false;
        $mtransaction = new MTransaction();
        $mtransaction->amount = 1;
        $mtransaction->currency = 'USD';
        $mtransaction->account_id_from = $account1_id;
        $mtransaction->account_id_to = $account2_id;
        $trans1_id = $mtransaction->create();

        $mtransaction = new MTransaction();
        $mtransaction->amount = 2;
        $mtransaction->currency = 'PLN';
        $mtransaction->account_id_from = $account1_id;
        $mtransaction->account_id_to = $account3_id;
        $trans2_id = $mtransaction->create();

        $mtransaction = new MTransaction();
        $mtransaction->amount = 0.5;
        $mtransaction->currency = 'USD';
        $mtransaction->account_id_from = $account3_id;
        $mtransaction->account_id_to = $account2_id;
        $trans3_id = $mtransaction->create();

        $mtransaction = new MTransaction($trans1_id);
        $mtransaction->amount = 1000;
        $res = $mtransaction->process();
        $this->assertFalse($res, "Wrong amount accepted");

        $mtransaction->amount = 1;
        $res = $mtransaction->process();
        $this->assertTrue($res, "Valid transaction rejected");
        $maccount1 = new MAccount($account1_id);
        $maccount2 = new MAccount($account2_id);
        $this->assertSame(2.09, $maccount1->balance, '0.9 EUR from Acc1');
        $this->assertSame(3.0, $maccount2->balance, '1 USD to Acc2');

        $mtransaction = new MTransaction($trans2_id);
        $res = $mtransaction->process();
        $this->assertTrue($res, "Valid transaction rejected");
        $maccount1 = new MAccount($account1_id);
        $maccount3 = new MAccount($account3_id);
        $this->assertSame(1.59, $maccount1->balance, '2 PLN from Acc1');
        $this->assertSame(2.0, $maccount3->balance, '2 PLN to Acc3');

        $mtransaction = new MTransaction($trans3_id);
        $res = $mtransaction->process();
        $this->assertTrue($res, "Valid transaction rejected");
        $maccount3 = new MAccount($account3_id);
        $maccount2 = new MAccount($account2_id);
        $this->assertSame(0.18, $maccount3->balance, '0.5 USD from Acc3');
        $this->assertSame(3.5, $maccount2->balance, '0.5 USD to Acc2');
    }
}