<?php
/**
 * CTransaction - handle transaction logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
require_once '../models/MTransaction.php';
require_once '../models/MTransactionAct.php';
class CTransaction extends CBase {

    /**
     * Prepare list of Transaction for the specified accounts
     * GET: account_id, limit, offset
     * @return array
     */
    public function list(): array
    {
        $account_id = $this->getVar('account_id');
        if (empty($account_id)) {
            return [];
        }
        $offset = $this->getVar('offset', 0);
        $limit = $this->getVar('limit', App::get()->config->transaction_list_limit);
        if ($limit > App::get()->config->transaction_list_limit) {
            $limit = App::get()->config->transaction_list_limit;
        }
        $maccount = new MAccount($account_id);
        if ($maccount->client_id != App::get()->client_id) {
            return [];
        }
        $mtransaction = new MTransactionAct();
        return $mtransaction->listByAccount($account_id, $limit, $offset);
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

    /**
     * Create new Transaction
     * GET: account_id_from, account_id_to, amount, currency
     * @return array
     */
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
    }
}