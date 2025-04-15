<?php

namespace models;
use App;
use BaseTest;
use MAccount;
use MTransaction;
use MTransactionAct;
require_once('../tests/BaseTest.php');
/**
 * MTransaction - transaction object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MTransactionActTest extends BaseTest
{
    public function testListByAccount()
    {
        App::get()->client_id = 1;
        $maccount = new MAccount();
        $maccount->name = 'test ut1';
        $maccount->client_id = 1;
        $maccount->currency = 'EUR';
        $account_1 = $maccount->create();
        $maccount->name = 'test ut2';
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
        $mtransaction_act = new MTransactionAct();
        $res = $mtransaction_act->listByAccount(0);
        $this->assertEmpty($res, print_r($res, true));

        $res = $mtransaction_act->listByAccount($account_1 - 1);
        $this->assertEmpty($res, print_r($res, true));

        $res = $mtransaction_act->listByAccount($account_1, 10);
        $this->assertSame(3, count($res), 'A11' . print_r([$trans, $res], true));
        $this->assertSame($trans[2]['id'], $res[0]['id'], 'A21' . print_r([$trans, $res], true));

        $res = $mtransaction_act->listByAccount($account_1, 2);
        $this->assertSame(2, count($res), 'A21' . print_r($res, true));
        $this->assertSame($trans[2]['id'], $res[0]['id'], 'A22' . print_r($res, true));

        $res = $mtransaction_act->listByAccount($account_1, 1);
        $this->assertSame(1, count($res), 'A31' . print_r($res, true));
        $this->assertSame($trans[2]['id'], $res[0]['id'], 'A32' . print_r($res, true));

        $res = $mtransaction_act->listByAccount($account_1, 1, 1);
        $this->assertSame(1, count($res), 'A41' . print_r($res, true));
        $this->assertSame($trans[1]['id'], $res[0]['id'], 'A42' . print_r($res, true));
    }

    public function processTransactions()
    {
        $sql = "SELECT * FROM transactions WHERE status = ?";
        $trans = $this->app->db->getAll($sql, [MTransaction::STATUS_NEW]);
        foreach ($trans as $tran) {
            $mtran = new MTransaction($tran['id']);
            $mtran->process();
        }
    }
}