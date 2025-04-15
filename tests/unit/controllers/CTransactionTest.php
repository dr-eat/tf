<?php
declare(strict_types=1);
namespace controllers;

use App;
use BaseTest;
use CTransaction;
use MAccount;
use MTransaction;
use MTransactionAct;

/**
 * CTransaction - handle client logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
require_once('../tests/BaseTest.php');
class CTransactionTest extends BaseTest
{
    public function testList(): void
    {
        App::get()->client_id = 1;
        $maccount = new MAccount();
        $maccount->name = 'test ut1';
        $maccount->client_id = 1;
        $maccount->currency = 'EUR';
        $account_1 = $maccount->create();
        $maccount->name = 'test ut2';
        $maccount->client_id = 1;
        $maccount->currency = 'USD';
        $account_2 = $maccount->create();

        $trans = [
            ['amount' => 1, 'currency' => 'USD', 'rate' => 1.1, 'account_id_from' => $account_1, 'account_id_to' => $account_2, 'status' => MTransaction::STATUS_NEW],
            ['amount' => 10, 'currency' => 'EUR', 'rate' => 0.9, 'account_id_from' => $account_2, 'account_id_to' => $account_1, 'status' => MTransaction::STATUS_COMPLETED],
            ['amount' => 20, 'currency' => 'USD', 'rate' => 1.1, 'account_id_from' => $account_1, 'account_id_to' => $account_2, 'status' => MTransaction::STATUS_COMPLETED]
        ];
        App::get()->config->transaction_process_immediate = false;
        foreach ($trans as $n => $tran) {
            $mtransaction = new MTransaction();
            foreach ($tran as $k => $v) {
                $mtransaction->$k = $v;
            }
            $trans[$n]['id'] = $mtransaction->create();
        }
        $ctransaction = new CTransaction();
        $res = $ctransaction->list();
        $this->assertEmpty($res, print_r($res, true));

        $_GET['account_id'] = $account_1 - 1;
        $res = $ctransaction->list();
        $this->assertEmpty($res, print_r($res, true));

        $_GET['account_id'] = $account_1;
        App::get()->config->transaction_list_limit = 10;
        $res = $ctransaction->list();
        $this->assertSame(3, count($res), 'A11' . print_r($res, true));
        $this->assertSame($trans[2]['id'], $res[0]['id'], 'A21' . print_r([$res, $trans], true));

        App::get()->config->transaction_list_limit = 2;
        $res = $ctransaction->list();
        $this->assertSame(2, count($res), 'A21' . print_r($res, true));
        $this->assertSame($trans[2]['id'], $res[0]['id'], 'A22' . print_r($res, true));

        $_GET['limit'] = 1;
        $res = $ctransaction->list();
        $this->assertSame(1, count($res), 'A31' . print_r($res, true));
        $this->assertSame($trans[2]['id'], $res[0]['id'], 'A32' . print_r($res, true));

        $_GET['offset'] = 1;
        $res = $ctransaction->list();
        $this->assertSame(1, count($res), 'A41' . print_r($res, true));
        $this->assertSame($trans[1]['id'], $res[0]['id'], 'A42' . print_r($res, true));
    }

// account_id 1,2,5,6 - test1 client
// account_id 1-EUR,2-USD,3-EUR,4-GBP,5-PLN,6-RUB
// T /transaction/create?amount=1&currency=EUR&account_id_from=1&account_id_to=3&login=test1&pwd=test1
// T /transaction/create?amount=1&currency=USD&account_id_from=1&account_id_to=2&login=test1&pwd=test1
// T /transaction/create?amount=100&currency=RUB&account_id_from=5&account_id_to=6&login=test1&pwd=test1
// T /transaction/create?amount=4&currency=PLN&account_id_from=6&account_id_to=5&login=test1&pwd=test1
//
// F /transaction/create?amount=1&currency=USD&account_id_from=1&account_id_to=20&login=test1&pwd=test1
// F /transaction/create?amount=1&currency=ZZZ&account_id_from=1&account_id_to=2&login=test1&pwd=test1
// F /transaction/create?amount=0&currency=USD&account_id_from=1&account_id_to=2&login=test1&pwd=test1
// F /transaction/create?amount=1&currency=EUR&account_id_from=1&account_id_to=2&login=test1&pwd=test1
// F /transaction/create?amount=1&currency=EUR&account_id_from=3&account_id_to=1&login=test1&pwd=test1
// F /transaction/create?amount=100&currency=EUR&account_id_from=1&account_id_to=3&login=test1&pwd=test1
/*
    public function create(): array
    {
        $res = 0;
        $mtransaction = new MTransaction();
        $mtransaction->amount = $this->getVar("amount", 0);
        $mtransaction->currency = strtoupper($this->getVar("currency", ''));
        $mtransaction->account_id_from = $this->getVar("account_id_from", 0);
        $mtransaction->account_id_to = $this->getVar("account_id_to", 0);
        if ($mtransaction->isValid()) {
            $res = $mtransaction->create();
        }
        return ['result' => $res, 'wrong_fields' => $res ? [] : $mtransaction->invalid_fields];
    } */
}